<?php


use airmoi\FileMaker\FileMakerException;
use airmoi\FileMaker\Object\Record;

require_once ('my_autoloader.php');

class Specimen
{

    /**
     * @var Image[]
     */
    private array $images;

    private ?string $latitude = null;
    private ?string $longitude = null;

    private Record $record;

    /**
     * All the regular fields that go in the main location.
     * Field Name => Field Value
     * @var string[]
     */
    private array $fieldData;

    /**
     * All the fields that are location related and thus
     * can be placed beside the map.
     * Field Name => Field Value
     * @var string[]
     */
    private array $locationData;

    /**
     * Specimen constructor.
     * @param string $id
     * @param DatabaseSearch $database
     * @throws ErrorException
     * @throws FileMakerException
     */
    public function __construct(private string $id, private DatabaseSearch $database)
    {
        # Find the record in FMP from the database and accession number (id)
        $findCommand = $database->getFileMaker()->newFindCommand($database->getDetailLayout()->getName());

        # add a search param to the query to exactly '==' equal the accession number
        # EDIT: == does not work with some since there are extra spaces
        # Need to use '=' to match whole word not field
        if ($id !== '') {
            $findCommand->addFindCriterion($database->getIDFieldName(), '=' . $id);
        } else {
            throw new AssertionError(message: "Empty ID was given!");
        }

        $result = $findCommand->execute();
        $allRecordsFound = $result->getRecords();

        if (sizeof($allRecordsFound) != 1) {
            # TODO remove this for production
            $debug = implode(array_map(function(Record $record) {
                $id = $record->getField('accessionNo');
                return " - $id - ";
            }, $allRecordsFound));
            throw new ErrorException(message: "No records or more than one records found. This is an internal error. Please contact the admin! Id: $id. Records: $debug");
        }

        $this->record = $allRecordsFound[0];

        $this->images = array();
        $this->fieldData = array();
        $this->locationData = array();

        $this->produceImageUrl();
        $this->produceFieldData();
    }

    /**
     * Adds all the record field names and values to the object list.
     * Also sets the latitude and longitude data if available.
     * @throws FileMakerException
     */
    private function produceFieldData() {

        # get location fields
        $locationFieldNames = match ($this->database->getName()) {
            "entomology" => array('Country', 'Province', 'Location', 'Elevation', 'Latitude', 'Longitude'),
            "algae", "bryophytes", "fungi", "lichen", "vwsp" => array('Country', 'ProvinceState', 'Location', 'Altitude', 'Depth', 'Geo_LatDecimal', 'Geo_LongDecimal'),
            "avian", "herpetology", "mammal" => array("Location::country", "Location::stateProvince", "Location::locality", "Geolocation::verbatimElevation", "Geolocation::decimalLatitude", "Geolocation::decimalLongitude"),
            "fish" => array('country', 'stateProvince', 'verbatimLocality', 'decimalLatitude', 'decimalLongitude'),
            "miw", "mi" => array('country', 'stateProvince', 'Location', 'verbatimDepth', 'DecimalLatitude', 'DecimalLongitude', "Depth below water"),
            "fossils" => array('Country', 'Province/State', 'City', 'Locality Information'),
            default => array(),
        };

        foreach ($this->record->getFields() as $fieldName) {

            if (in_array($fieldName, $locationFieldNames)) {
                $this->locationData[$fieldName] = $this->record->getField($fieldName);

                if (Specimen::formatFieldName($fieldName) === "Latitude") {$this->latitude = $this->record->getField($fieldName);}
                if (Specimen::formatFieldName($fieldName) === "Longitude") {$this->longitude = $this->record->getField($fieldName);}
            } else {
                $this->fieldData[$fieldName] = $this->record->getField($fieldName);
            }
        }
    }


    /**
     * Depending on the database in use, will try to get specimen images and add them
     * to the objects image list.
     */
    private function produceImageUrl() {
        match ($this->database->getName()) {
            "fish" => $this->_fishImageSetup(),
            "entomology" => $this->_entomologyImageSetup(),
            "avian", "herpetology", "mammal" => $this->_vertebrateImageSetup(),
            "vwsp", "bryophytes", "fungi", "lichen", "algae" => $this->_herbariumImageSetup(),
            default => '',
        };
    }
    private function _fishImageSetup() {
        # get the image urls from the cards TODO ask what are these cards?
        try { $numOfCards = $this->record->getField("iffrCardNb"); }
        catch (FileMakerException) { return; }


        for ($num = 1; $num <= $numOfCards; $num++) {
            $num_padded = sprintf("%02d", $num);
            $cardName = "card".$num_padded;

            try {
                $cardFieldValue = $this->record->getField($cardName);
            } catch (FileMakerException) { continue; }

            $url =  'https://open.library.ubc.ca/media/download/jpg/fisheries/'.$cardFieldValue.'/0';
            $linkToWebsite =  'https://open.library.ubc.ca/collections/fisheries/items/'.$cardFieldValue;

            array_push($this->images, new Image(url: $url, href: $linkToWebsite, alt: 'Fish Image'));
        }
    }
    private function _entomologyImageSetup() {
        try {
            $familyUrl = getGenusPage($this->record);
            $genus = $this->record->getField('Genus');
            $specie = $this->record->getField('Species');
        } catch (FileMakerException) {
            return;
        }

        # scrap the entomology website for images
        # source https://www.ostraining.com/blog/coding/extract-image-php/
        $html = file_get_contents($familyUrl);
        preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i',$html, $matches);
        $rawImageNameList = $matches[1];

        # only use those images with the genus and specie name in it
        $imageNames = array_filter(
            $rawImageNameList,
            function ($imgUrl) use($genus, $specie) {
                return str_contains($imgUrl, $genus) and str_contains($imgUrl,  $specie);
            }
        );

        foreach ($imageNames as $imageName) {
            $imageUrl = $familyUrl . $imageName;
            array_push($this->images, new Image(url: $imageUrl, href: $imageUrl, alt: "Image for $genus - $specie"));
        }
    }
    private function _vertebrateImageSetup() {
        try { $tableNamesObj = $this->record->getRelatedSet('Photographs'); }
        catch (FileMakerException) { return; }

        // if images, type = 'array'; else 'object'
        if (gettype($tableNamesObj) == 'array') {
            foreach ($tableNamesObj as $relatedRow) {

                try { $possible_answer = $relatedRow->getField('Photographs::photoContainer'); }
                catch (FileMakerException) { continue; }

                if (str_contains(strtolower($possible_answer), "jpg")) { // delete this if later
                    $image_url = "https://collections.zoology.ubc.ca" . $possible_answer;
                    array_push($this->images,
                        new Image(url: $image_url, href: $image_url, alt: "Species image"));
                }
            }
        }
    }
    private function _herbariumImageSetup() {
        $url = getPhotoUrl(ACCESSIONNUMBER, DATABASE);
        if (@getimagesize($url)[0] > 0 && @getimagesize($url)[1] > 0) {
            array_push($this->images, new Image(url:$url, href: $url, alt: "Species image"));
        }
    }


    /**
     * @return Image[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @return string|null
     */
    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    /**
     * @return string|null
     */
    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    /**
     * @return Record
     */
    public function getRecord(): Record
    {
        return $this->record;
    }

    /**
     * @return string[]
     */
    public function getFieldData(): array
    {
        return $this->fieldData;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return DatabaseSearch
     */
    public function getDatabase(): DatabaseSearch
    {
        return $this->database;
    }

    /**
     * @return string[]
     */
    public function getLocationData(): array
    {
        return $this->locationData;
    }



    /**
     * With databases using different field naming conventions, some databases
     * use the following format, Taxon::family or Event::year. This function will remove
     * everything before the second semicolon including it, if it exists.
     * It will also map the field using mapField.
     * @param string $fieldName
     * @return string Taxon::family->Family
     */
    static function formatFieldName(string $fieldName): string
    {
        if (str_contains($fieldName, "::")) {
            $newFieldName = substr($fieldName, strrpos($fieldName, ":") + 1);
        } else {
            $newFieldName = $fieldName;
        }

        return Specimen::mapFieldName($newFieldName);
    }

    /**
     * Maps the database field to a more readable field for the web app to use.
     * @param string $field
     * @return string
     */
    static function mapFieldName(string $field): string
    {
        return match (strtolower($field)) {
            'accession no', 'catalognumber', 'accessionno', 'catalogue number' => 'Accession Number',
            'sem #' => 'SEM Number',
            'nomennoun' => 'Genus',
            'specificepithet' => 'Species',
            'sub sp.' => 'Subspecies',
            'infraspecificepithet' => 'Infraspecies',
            'taxonrank' => 'Taxon Rank',
            'provincestate', 'stateprovince', 'prov/st' => 'Province or State',
            'location 1', 'verbatimlocality', 'location' => 'Locality',
            'verbatimelevation' => 'Elevation',
            'verbatimdepth', 'depth below water' => 'Depth',
            'geo_longdecimal', 'decimallongitude', 'longitudedecimal' => 'Longitude',
            'geo_latdecimal', 'decimallatitude', 'latitudedecimal' => 'Latitude',
            'date collected', 'collection date 1', 'verbatimeventdate', 'eventdate' => 'Collection Date',
            'year 1' => 'Year',
            'month 1' => 'Month',
            'day 1' => 'Day',
            'identifiedby' => 'Identified By',
            'typestatus' => 'Type Status',
            'comments', 'occurrenceremarks', 'fieldnotes' => 'Field Notes',
            'samplingprotocol' => 'Capture Method',
            'recordnumber' => 'Collection Number',
            'previousidentifications' => 'Prev. Identifications',
            'det by' => 'Determined By',
            'mushroomobserver' => 'Mushroom Observer',
            'citations', 'associatedreferences' => 'Associated References',
            'associatedsequences' => 'Associated Sequences',
            'reproductivecondition' => 'Reproductive Condition',
            'organismremark' => 'Organism Remark',
            'vernacularname' => 'Vernacular Name',
            'recordedby', 'collected by' => 'Collector',
            'photofilename', 'iifrno', 'imaged' => 'Has Image',
            default => ucwords($field),
        };
    }

}