<?php 
// require_once ('credentials_controller.php');

// $fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

function replaceURIElement($URI, $element, $input) {
  if (isset($_GET[$element])) {
    $elementLeft = strpos($URI, $element);
    $elementRight = strpos($URI, '&', $elementLeft);
    $stringRight = "";
    if ($elementRight) {
      $stringRight = substr($URI, $elementRight, strlen($URI));
    }
    return substr($URI, 0, $elementLeft) 
    . 
    $element . '=' . $input . $stringRight;
  } else {
    return $URI . '&' . $element . '=' . $input;
  }
}

function replaceSpace($element) {
  return str_replace(" ", "+", $element);
}

function mapField($field) {
    switch( strtolower($field)) {
      case 'accession no':
      case 'catalognumber':
      case 'accessionno':
      case 'id':
        return 'Accession Number';
      case 'sem #':
        return 'SEM Number';
      case 'nomennoun':
        return 'Genus';
      case 'specificepithet':  
        return 'Species';
      case 'sub sp.':
        return 'Subspecies';
      case 'infraspecificepithet':
        return 'Infraspecies';
      case 'taxonrank': 
        return 'Taxon Rank';
      case 'provincestate':
      case 'stateprovince':
      case 'prov/st';
        return 'Province or State';
      case 'location 1':
      case 'verbatimlocality':
      case 'location':
        return 'Locality';
      case 'verbatimelevation':
        return 'Elevation';
      case 'verbatimdepth':
      case 'depth below water':
        return 'Depth';
      case 'geo_longdecimal':
      case 'decimallongitude':
      case 'longitudedecimal':
        return 'Longitude';
      case 'geo_latdecimal':
      case 'decimallatitude':
      case 'latitudedecimal':
        return 'Latitude';
      case 'date collected':
      case 'collection date 1':
      case 'verbatimeventdate':
      case 'eventdate':
        return 'Collection Date';
      case 'year 1':
        return 'Year';
      case 'month 1':
        return 'Month';
      case 'day 1':
        return 'Day';
      case 'identifiedby':
        return 'Identified By';
      case 'typestatus':
        return 'Type Status';
      case 'comments':
      case 'occurrenceremarks':
      case 'fieldnotes':
        return 'Field Notes';
      case 'samplingprotocol':
        return 'Capture Method';
      case 'recordnumber':
        return 'Collection Number';
      case 'previousidentifications':
        return 'Prev. Identifications';
      case 'det by':
        return 'Determined By';
      case 'mushroomobserver':
        return 'Mushroom Observer';
      case 'citations':
      case 'associatedreferences':
        return 'Associated References';
      case 'associatedsequences':
        return 'Associated Sequences';
      case 'reproductivecondition':
        return 'Reproductive Condition';
      case 'organismremark':
        return 'Organism Remark';
      case 'vernacularname':
        return 'Vernacular Name';
      case 'recordedby':
      case 'collected by':
        return 'Collector';
      case 'photofilename': 
      case 'iifrno':
      case 'imaged':
        return 'Has Image';
      default:
        return ucwords($field);
      }
  }
  
function formatField($field) {
  $colonPosition = strrpos($field, ":");
  if ($colonPosition) {
    $field = substr($field, $colonPosition + 1);
  }
  return mapField($field);
}

function getGenusPage($record) {
  $order = $record->getField('Order');
  $family = $record->getField('Family');
  $subfamily = $record->getField('Subfamily');
  $genusPage = 'https://www.zoology.ubc.ca/entomology/main/'.$order.'/'.$family.'/';
  $html = file_get_html($genusPage);
  $species = $html->find('.speciesentry');
  if(count($species) ==0) {
    $genusPage = 'https://www.zoology.ubc.ca/entomology/main/'.$order.'/'.$family.'/'.$subfamily.'/';
  }
  return $genusPage;
}

function getGenusSpecies($record) {
  $genus = $record->getField('Genus');
  $species = $record->getField('Species');
  $genusSpecies = $genus . ' ' . $species ;
  return $genusSpecies;
}

function getPhotoUrl($identifier) {
  if ($_GET['Database'] === 'vwsp') {
    return "https://herbweb.botany.ubc.ca/herbarium/images/vwsp_images/Large_web/".$identifier.".jpg";
  }
  else if ($_GET['Database'] === 'algae') {
    return "https://herbweb.botany.ubc.ca/herbarium/images/ubcalgae_images/Large_web/".$identifier.".jpg";
  }
  else if ($_GET['Database'] === 'lichen') {
    return "https://herbweb.botany.ubc.ca/herbarium/images/lichen_images/Large_web/".$identifier.".jpg";
  }
  else if ($_GET['Database'] === 'fungi') {
    return "https://herbweb.botany.ubc.ca/herbarium/images/fungi_images/Large_web/".$identifier.".jpg";
  }
  else if ($_GET['Database'] === 'bryophytes') {
    return "https://herbweb.botany.ubc.ca/herbarium/images/bryophytes_images/Large_web/".$identifier.".jpg";
  }
  else if ($_GET['Database'] === 'mammal') {
    return 'https://collections.zoology.ubc.ca/fmi/xml/cnt/data.JPG?-db=Mammal%20Research%20Collection&-lay=mammal_details&-recid='
    .htmlspecialchars($identifier).'&-field=Photographs::photoContainer(1)';
  }
  else if ($_GET['Database'] === 'avian') {
    return 'https://collections.zoology.ubc.ca/fmi/xml/cnt/data.JPG?-db=Avian%20Research%20Collection&-lay=details-avian&-recid='
    .htmlspecialchars($identifier).'&-field=Photographs::photoContainer(1)';
  }
  else if ($_GET['Database'] === 'herpetology') {
    return 'https://collections.zoology.ubc.ca/fmi/xml/cnt/data.JPG?-db=Herpetology%20Research%20Collection&-lay=herp_details&-recid='
    .htmlspecialchars($identifier).'&-field=Photographs::photoContainer(1)';
  }
}

function shouldDescend($field) {
  if (!isset($_GET['SortOrder']) || $_GET['SortOrder'] === '') return false;
  if (isset($_GET['Sort']) && $_GET['Sort'] === $field && isset($_GET['SortOrder']) && $_GET['SortOrder'] === 'Ascend') return true;
  return false;
}

require_once('renderFunctions.php');


