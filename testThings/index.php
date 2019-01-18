<?php 
require_once ('FileMaker.php');
require_once ('partials/header.php');
require_once ('db.php');

$fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

// echo "FM_FILE: $FM_FILE <br>
//       FM_HOST: $FM_HOST <br>
//       FM_USER: $FM_USER <br>
//       FM_PASS: $FM_PASS <br>";

$layouts = $fm->listLayouts();

if (FileMaker::isError($layouts)) {
  echo $layouts;
}

$layout = $layouts[0];

foreach ($layouts as $l) {
  if (strpos($l, 'search') !== false) {
    $layout = $l;
  }
}
$fmLayout = $fm->getLayout($layout);
$layoutFields = $fmLayout->listFields();
function mapField($field) {
  switch( strtolower($field)) {
    case 'specificepithet':  
      return 'Specific Epithet';
      break;
    case 'infraspecificepithet':
      return 'Infraspecific Epithet';
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

?>

<body class="container">
  <form action="render.php" method="get">
    <div class="form-group">
      <input type="text" name="Database" style="display:none;" 
      value=<?php if (isset($_GET['Database'])) echo $_GET['Database']; ?>>
    </div>
    <?php foreach ($layoutFields as $rf) { ?>
    <div class="row">
      <div class="col-sm-2">
      <label><?php echo formatField($rf) ?></label>
      </div>
      <div class="col-sm-2">
      <input type="text" name=<?php echo $rf ?>>
      </div>
    </div> 
    <?php } ?>
      <input class="btn btn-primary" type="submit">
    </div>
  </form>
</body>
