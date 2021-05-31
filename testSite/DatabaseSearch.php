<?php

/**
 * Class DatabaseSearch
 * Represents a database the client is currently searching in.
 */
class DatabaseSearch {

    /**
     * The FileMaker instance conencted to this databse
     * @var FileMaker
     */
    private FileMaker $fileMaker;

    /**
     * The name of this databse
     * @var string 
     */
    private string $name;

    private FileMaker_Layout $search_layout;
    private FileMaker_Layout $result_layout;
    private FileMaker_Layout $detail_layout;

    function __construct($fileMaker, $database) {
        $this->fileMaker = $fileMaker;
        $this->name = $database;

        $this->setLayouts();
    }

    /**
     * Creates a object from the database name
     * @param string $databaseName
     * @return DatabaseSearch|false
     */
    public static function fromDatabaseName(string $databaseName): bool|DatabaseSearch
    {
        list($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS) = getDBCredentials($databaseName);

        if (!$FM_PASS or !$FM_FILE or !$FM_HOST or !$FM_USER) {
            return false;
        }

        $fileMaker = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

        return new self($fileMaker, $databaseName);
    }

    function getFileMaker(): FileMaker
    {
        return $this->fileMaker;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getSearchLayout(): FileMaker_Layout
    {
        return $this->search_layout;
    }

    function getResultLayout(): FileMaker_Layout
    {
        return $this->result_layout;
    }

    public function getDetailLayout(): FileMaker_Layout
    {
        return $this->detail_layout;
    }

    /**
     * Searches all available layouts from FMP and sets the correct Search and
     * Result layout for this DatabaseSearch.
     *
     * All databases have a search, results and details layout. However, the MIW and MI databases have two of each
     * one for MI and one for MIW on the same database. Therefore the extra if statement.
     */
    private function setLayouts() {
        # list of layout names!
        $availableLayouts = $this->fileMaker->listLayouts();

        foreach ($availableLayouts as $layoutName){
            if ($this->name === 'mi' or $this->name === 'miw') {
                if ($layoutName == 'search-'.strtoupper($this->name)) {
                    $this->search_layout = $this->fileMaker->getLayout($layoutName);
                } else if ($layoutName == 'results-'.strtoupper($this->name)) {
                    $this->result_layout = $this->fileMaker->getLayout($layoutName);
                } else if ($layoutName == 'details-'.strtoupper($this->name)) {
                    $this->detail_layout = $this->fileMaker->getLayout($layoutName);
                }
            } else {
                if (str_contains($layoutName, 'search')) {
                    $this->search_layout = $this->fileMaker->getLayout($layoutName);
                } else if (str_contains($layoutName, 'results')) {
                    $this->result_layout = $this->fileMaker->getLayout($layoutName);
                } else if (str_contains($layoutName, 'details')) {
                    $this->detail_layout = $this->fileMaker->getLayout($layoutName);
                }
            }
        }
    }

    /**
     * Will query the FileMakerPro API for a result item. The query takes query fields,
     * and also deals with:
     * - data pagination with the pageNumber field
     * - sorting the data with the sortType and sortQuery
     *
     * @param int $maxResponseAmount amount of responses to query for
     * @param string[] $getFields query fields to use, must be not empty
     * @param string $logicalOperator on of 'or' or 'and'
     * @param string|null $sortQuery
     * @param int $pageNumber used to calculate a multiplier of maxResponseAmount for pagination
     * @param string|null $sortType one of ascend or descend
     * @return FileMaker_Result|FileMaker_Error Result or Error (Error if no entries for query too)
     */
    function queryForResults(int $maxResponseAmount, array $getFields, string $logicalOperator, ?string $sortQuery,
                             int $pageNumber, ?string $sortType): FileMaker_Result|FileMaker_Error
    {

        // Find on all inputs with values
        $findCommand = $this->fileMaker->newFindCommand($this->search_layout->getName());

        $findCommand->setLogicalOperator(operator: strtoupper($logicalOperator));

        /**
         * TODO Fix the Fossils collection, searching is not working! Not even in deployed app.
         */

        # The different strings used in FMP to mean the access number or ID
        $accessionNumberOptions = ['Accession_Number', 'catalogNumber', 'Accession_No'];

        # handle all regular search fields
        foreach ($getFields as $fieldName => $fieldValue) {

            $layoutField = str_replace("_", " ", $fieldName);

            # handle image field
            if ($fieldName == 'hasImage') {
                $layoutField = 'Photographs::photoFileName' or 'Imaged'; # TODO fix this!

                $findCommand->addFindCriterion(
                    fieldname: $layoutField,
                    testvalue: $this->name == 'entomology' ? 'Photographed' : '*'
                );
            }
            # handle accession number 'ID' field
            else if (in_array($fieldName, $accessionNumberOptions)) {

                switch ($this->name) {
                    case 'vwsp'; case 'bryophytes';
                    case 'fungi'; case 'lichen'; case 'algae':
                        $findCommand->addFindCriterion(
                            fieldname: is_numeric($fieldValue) ? "Accession Numerical" : "Accession Number",
                            testvalue: $fieldValue
                        );
                        break;
                    case 'fossil'; case 'avian';
                    case 'herpetology'; case 'mammal':
                        $findCommand->addFindCriterion(
                            fieldname: is_numeric($fieldValue) ? "SortNum" : "catalogNumber",
                            testvalue: $fieldValue
                        );
                        break;
                    case 'mi'; case 'miw':
                        $findCommand->addFindCriterion(
                            fieldname: is_numeric($fieldValue) ? "SortNum" : 'Accession No',
                            testvalue: $fieldValue,
                        );
                        break;
                    case 'fish':
                        $findCommand->addFindCriterion(
                            fieldname: 'accessionNo',
                            testvalue: $fieldValue,
                        );
                        break;
                    case 'entomology':
                        $findCommand->addFindCriterion(
                            fieldname: 'SEM #',
                            testvalue: $fieldValue,
                        );
                        break;
                }
            }

            # all other fields just go in as they come
            else {
                $findCommand->addFindCriterion($layoutField, $fieldValue);
            }
        }

        # handle the sort property
        if ($sortQuery and $sortQuery != '') {

            # preliminary sort query, will only change in certain situations
            $sortBy = $sortQuery;

            # accession number sort is different for databases, handle it here
            if (mapField($sortQuery) === 'Accession Number') {
                if ($this->name == 'vwsp' or $this->name == 'bryophytes' or
                    $this->name == 'fungi' or $this->name == 'lichen' or $this->name == 'algae') {
                    $sortBy = 'Accession Numerical';
                }
                else {
                    $sortBy = 'sortNum';
                }
            }

            # for entomology and fish we can only sort by accession number? TODO check this!
            if($this->name == 'entomology') {
                $sortBy = 'SEM #';
            }
            if($this->name == 'fish') {
                $sortBy = 'accessionNo';
            }

            # handles the order of the sort
            $findCommand->addSortRule(fieldname:  str_replace('+', ' ', $sortBy), precedence: 1,
                order: $sortType === 'Descend' ? FILEMAKER_SORT_DESCEND : FILEMAKER_SORT_ASCEND);
        }

        # handle different table pages
        if ($pageNumber) {
            $findCommand->setRange(skip: ($pageNumber - 1) * $maxResponseAmount, max: $maxResponseAmount);
        }

        return $findCommand->execute();
    }

    /**
     * Echos a data table to show the results for this database search.
     * @param FileMaker_Result $result
     */
    function echoDataTable(FileMaker_Result $result) {

        # filter out unnecessary fields
        $ignoredFields = ['SortNum', 'Accession Numerical', 'Imaged', 'IIFRNo', 'Photographs::photoFileName',
            'Event::eventDate', 'card01', 'Has Image', 'imaged'];
        $usefulFields = array_diff($this->result_layout->listFields(), $ignoredFields);

        # basic table setup with helper functions
        echo '
         <!-- construct table for given layout and fields -->
        <div class="container-fluid no-padding">
            <!-- id table for special color -->
            <table class="table table-hover table-striped table-responsive" id="table">
                <thead>
                    <tr>
                        ';
        $this->echoTableHeads($usefulFields);
        echo '
                    </tr>
                </thead>
                <tbody>
                    ';
        $this->echoTableRows($result->getRecords(), $usefulFields);
        echo '
                </tbody>
            </table>
        </div>
    ';
    }
    /**
     * Echos all rows for a table given the records and fields in use.
     * @param $records
     * @param $fields
     */
    private function echoTableRows($records, $fields) {
        foreach ($records as $record) {
            # each record has its table row
            echo '<tr>';
            foreach ($fields as $field) {
                # ID field logic
                if (formatField($field) === 'Accession Number' or $field === 'SEM #') {
                    $url = htmlspecialchars($this->name). '&AccessionNo='.htmlspecialchars($record->getField($field));
                    $id = htmlspecialchars(trim($record->getField($field)));

                    $hasImage = false;
                    if($this->name === 'entomology' and $record->getField("Imaged") === "Photographed") $hasImage = true;
                    else if ($this->name === 'fish' and $record->getField("imaged") === "Yes") $hasImage = true;
                    else if ($this->name === 'mammal' or $this->name === 'avian' or $this->name === 'herpetology') {
                        if ($record->getField("Photographs::photoFileName") !== "") $hasImage = true;
                    }
                    # for vwsp lichen bryophytes fungi algae
                    else if ($record->getField("Imaged") === "Yes") $hasImage = true;

                    echo "
                        <th scope='row' id='data'>
                            <a href='details.php?Database=$url'>
                            " . ($hasImage ? '<span style="display:inline" id = "icon"  class="oi oi-image"></span>' : '') . "
                            <b>$id</b>
                        </th>
                    ";
                }
                # genus or species field for special style
                else if (formatField($field) == 'Genus' || formatField($field) == 'Species') {
                    echo '<td id="data" style="font-style:italic;">' . htmlspecialchars($record->getField($field)) . '</td>';
                }
                else {
                    echo '<td id="data">'. $record->getField($field) . '</td>';
                }
            }
            echo '</tr>';
        }
    }
    /**
     * Echos the table heads given the fields in use.
     * Will also link the heads with sort urls.
     * TODO remove all $_GET and $_SERVER calls
     * @param $fields
     */
    private function echoTableHeads($fields) {
        $page = $_GET['Page'] ?? 1;

        foreach($fields as $field) {
            $id = htmlspecialchars(formatField($field));

            $payloadList = [
                'Database' => $this->name,
                'Sort' => $field,
                'SortOrder' => $_GET['SortOrder'] ?? 'Descend' == 'Descend' ? 'Ascend' : 'Descend',
                'Page' => $page,
            ];

            $href = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?')) . '?' . http_build_query($payloadList);
            $href = str_replace('%3A', ':', $href);

            $icon_class = $_GET['SortOrder'] ?? '' === 'Descend' ? 'oi-sort-descending' : 'oi-sort-ascending';

            echo "
                <th scope='col' id=$id>
                    <a style='padding: 0; white-space:nowrap;' href=$href>
                        <!-- order icon -->
                        <span id='icon'  class='oi $icon_class'></span>
                        <!-- field name -->
                        <b>$id</b>
                    </a>
                </th>
            ";
        }
    }

    /**
     * Returns the ID or accession number field name, it is different for different databases.
     * Optional $isNumeric field will change the name used for some databases.
     * @param bool $isNumeric
     * @return string
     */
    function getIDFieldName(bool $isNumeric = false): string
    {
        return match ($this->name) {
            'vwsp', 'bryophytes', 'fungi', 'lichen', 'algae' => $isNumeric ? 'Accession Numerical' : 'Accession Number',
            'fossil', 'avian', 'herpetology', 'mammal' => $isNumeric ? 'SortNum' : 'catalogNumber',
            'mi', 'miw' => $isNumeric ? 'SortNum' : 'Accession No',
            'fish' => 'accessionNo',
            'entomology' => 'SEM #',
        };
    }
}