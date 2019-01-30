<?php 
require_once ('FileMaker.php');
require_once ('db.php');

$fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

function replaceURIElement($URI, $element, $input) {
  // if (isset($_GET[$element])) return "http://localhost/TestSite/testThings/";
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
      case 'specificepithet':  
        return 'Specific Epithet';
        break;
      case 'infraspecificepithet':
        return 'Infraspecies';
        //return 'Infraspecific Epithet';
        break;
      case 'taxonrank': 
        return 'Taxon Rank';
        break;
      case 'provincestate':
        return 'Province/State';
        break;
      case 'stateprovince':
        return 'Province/State';
        break;
      case 'verbatimelevation':
        return 'Elevation';
        break;
      case 'verbatimdepth':
        return 'Depth';
        break;
      case 'decimallongitude':
        return 'Decimal Longitude';
        break;
      case 'decimallatitude':
        return 'Decimal Latitude';
        break;
      case 'verbatimeventdate':
        return 'Collection Date';
        break;
      case 'identifiedby':
        return 'Identified By';
        break;
      case 'typestatus':
        return 'Type Status';
        break;
      case 'occurrenceremarks':
        return 'Field Notes';
        break;
        case 'fieldnotes':
        return 'Field Notes';
        break;
      case 'recordnumber':
        return 'Record Number';
        break;
      case 'previousidentifications':
        return 'Previous Identifications';
        break;
      case 'mushroomobserver':
        return 'Mushroom Observer';
        break;
      case 'catalognumber':
        return 'Accession Number';
      default:
        return ucwords($field);
        break;
      }
  }
  
  function formatField($field) {
    $colonPosition = strrpos($field, ":");
    if ($colonPosition) {
      $field = substr($field, $colonPosition + 1);
    }
    return mapField($field);
  }

$numRes = 100;
$layouts = $fm->listLayouts();
$layout = "";
foreach ($layouts as $l) {
    if (strpos($l, 'search') !== false) {
        $layout = $l;
    }
}

$fmLayout = $fm->getLayout($layout);
$layoutFields = $fmLayout->listFields();

if (FileMaker::isError($layouts)) {
    echo $layouts->message;
    exit;
}

// Find on all inputs with values
$findCommand = $fm->newFindCommand($layout);

foreach ($layoutFields as $rf) {
  // echo $rf;
    $field = explode(' ',trim($rf))[0];
    if (isset($_GET[$field]) && $_GET[$field] !== '') {
        $findCommand->addFindCriterion($rf, $_GET[$field]);
    }
}

if (isset($_GET['Sort']) && $_GET['Sort'] != '') {
    $sortField = str_replace('+', ' ', $_GET['Sort']);
    $fieldSplit = explode(' ', $sortField);
    if (!isset($_GET[$fieldSplit[0]]) || $_GET[$fieldSplit[0]] == '') {
      $findCommand->addFindCriterion($sortField, '*');
    }
    $findCommand->addSortRule(str_replace('+', ' ', $_GET['Sort']), 1, FILEMAKER_SORT_ASCEND);
}



if (isset($_GET['Page']) && $_GET['Page'] != '') {
    $findCommand->setRange(($_GET['Page'] - 1) * $numRes, $numRes);
} else {
    $findCommand->setRange(0, $numRes);
}

$result = $findCommand->execute();

if(FileMaker::isError($result)) {
    $findAllRec = [];
} else {
    $findAllRec = $result->getRecords();
}

?>