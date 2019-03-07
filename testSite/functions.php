<?php 
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
      case 'accession no.':
      case 'catalognumber':
      case 'id':
        return 'Accession Number';
      case 'sem #':
        return 'SEM Number';
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
        return 'Location';
      case 'verbatimelevation':
        return 'Elevation';
      case 'verbatimdepth':
        return 'Depth';
      case 'geo_longdecimal':
      case 'decimallongitude':
        return 'Longitude';
      case 'geo_latdecimal':
      case 'decimallatitude':
        return 'Latitude';
      case 'date collected':
      case 'collection date 1':
      case 'verbatimeventdate':
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
      case 'recordnumber':
        return 'Collection Number';
      case 'previousidentifications':
        return 'Previous Identifications';
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

// $numRes = 100;
// $layouts = $fm->listLayouts();
// $layout = "";
// foreach ($layouts as $l) {

//   if ($_GET['Database'] === 'mi') {
//     if (strpos($l, 'results') !== false) {
//       $layout = $l;
//       break;
//     }
//   }
//   else if (strpos($l, 'results') !== false) {
//     $layout = $l;
//   }
// }

// $fmLayout = $fm->getLayout($layout);
// $layoutFields = $fmLayout->listFields();

// if (FileMaker::isError($layouts)) {
//     echo $layouts->message;
//     exit;
// }

// // Find on all inputs with values
// $findCommand = $fm->newFindCommand($layout);

// foreach ($layoutFields as $rf) {
//   // echo $rf;
//     $field = explode(' ',trim($rf))[0];
//     if (isset($_GET[$field]) && $_GET[$field] !== '') {
//         $findCommand->addFindCriterion($rf, $_GET[$field]);
//     }
// }

// if (isset($_GET['Sort']) && $_GET['Sort'] != '') {
//     $sortField = str_replace('+', ' ', $_GET['Sort']);
//     $fieldSplit = explode(' ', $sortField);
//     if (!isset($_GET[$fieldSplit[0]]) || $_GET[$fieldSplit[0]] == '') {
//       $findCommand->addFindCriterion($sortField, '*');
//     }
//     $findCommand->addSortRule(str_replace('+', ' ', $_GET['Sort']), 1, FILEMAKER_SORT_ASCEND);
// }



// if (isset($_GET['Page']) && $_GET['Page'] != '') {
//     $findCommand->setRange(($_GET['Page'] - 1) * $numRes, $numRes);
// } else {
//     $findCommand->setRange(0, $numRes);
// }

// $result = $findCommand->execute();

// if(FileMaker::isError($result)) {
//     $findAllRec = [];
// } else {
//     $findAllRec = $result->getRecords();
// }

?>