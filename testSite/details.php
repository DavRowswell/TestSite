<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://herbweb.botany.ubc.ca/arcgis_js_api/library/4.10/esri/css/main.css">
<style>
  html, body, #map {
    height: 100%;
    width: 100%;
    margin: 0;
    padding: 0;
  }
  .minHeight{
    min-height: 300px;
  }
  

</style>
<?php
 session_start();
  require_once ('FileMaker.php');
  require_once ('partials/header.php');
  require_once ('functions.php');
  require_once ('lib/simple_html_dom.php');
  require_once ('db.php');

  $fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

  $layouts = $fm->listLayouts();
  $layout = $layouts[0];
  foreach ($layouts as $l) {
    if ($_GET['Database'] === 'mi') {
      if (strpos($l, 'details') !== false) {
        $layout = $l;
        break;
      }
    }
    else if (strpos($l, 'details') !== false) {
      $layout = $l;
    }
  }

  $findCommand = $fm->newFindCommand($layout);
  if (isset($_GET['AccessionNo']) && $_GET['AccessionNo'] !== '') {
      if ($_GET['Database'] == 'vwsp' or $_GET['Database'] == 'bryophytes' or 
          $_GET['Database'] == 'fungi' or $_GET['Database'] == 'lichen' or $_GET['Database'] == 'algae'){
        $findCommand->addFindCriterion('Accession Number', '=='.$_GET['AccessionNo']);
      }
      else if ($_GET['Database'] == 'fossil' || $_GET['Database'] == 'avian' || $_GET['Database'] == 'herpetology' || $_GET['Database'] == 'mammal') {
        $findCommand->addFindCriterion('catalogNumber', '=='.$_GET['AccessionNo']);
      }
      else if ($_GET['Database'] == 'fish'){   
        $findCommand->addFindCriterion('Accession Number', '=='.$_GET['AccessionNo']);  
      }
      else if ($_GET['Database'] == 'entomology'){
        $findCommand->addFindCriterion('SEM #', '=='.$_GET['AccessionNo']);
      }
      else {
        $findCommand->addFindCriterion('Accession No', '=='.$_GET['AccessionNo']);
      }
  }

  $result = $findCommand->execute();

  if(FileMaker::isError($result)) {
    $_SESSION['error'] = $result->getMessage();
    header('Location: error.php');
    exit;
  } else {
    $findAllRec = $result->getRecords();
  }
  $lat="";
  $long="";
  ?>
</head>

<body class="d-flex flex-column">
  <?php
  require_once ('partials/navbar.php');
  // Check if layout exists, and get fields of layout
  if(FileMaker::isError($result)){
    $_SESSION['error'] = $result;
    header('Location: error.php');
    exit;
  }
  $recFields = $result->getFields();
  ?>
<div class="container-fluid">
  <div class="row">
    <div class="col">
      <?php if($_GET['Database'] === "mi" || $_GET['Database'] === "miw" || $_GET['Database'] === "vwsp") { ?>
        <h1><b><?php 
                  if($_GET['Database'] === "mi"){echo "Dry Marine Invertebrate";}
                  else if($_GET['Database'] === "vwsp"){echo "Vascular";}
                  else{echo "Wet Marine Invertebrate";} 
                ?> Search</b>
        </h1>
      <?php } else { ?>
      <h1><b><?php echo ucfirst($_GET['Database']); ?> Search</b></h1>
      <?php }?>
    </div>
  </div>
  <div class="row">
    <div class="col-sm-9">
      <!-- construct table for given layout and fields -->
      <table class="table">
        <tbody>
          <?php foreach($recFields as $i){
            if ($i === "Photographs::photoContainer" || $i === "IIFRNo") continue;?> 
            <tr>
              <th scope="col-sm-2"><?php echo htmlspecialchars(formatField($i)) ?></th>
              <?php if(formatField($i) == "Genus" || formatField($i) == "Species") { ?>
              <td scope="col-sm-10" style="font-style:italic;"
              <?php } else { ?>
              <td scope="col-sm-10"
              <?php }
                if (formatField($i) === "Latitude") {echo "id='Latitude'"; $lat = $findAllRec[0]->getField($i);}
                if (formatField($i) === "Longitude") {echo "id='Longitude'"; $long = $findAllRec[0]->getField($i);}
              ?>>
              <?php echo htmlspecialchars($findAllRec[0]->getField($i)) ?>
              </td>
            </tr>
          <?php }?>
        </tbody>
      </table>
    </div>
    <div class="col-sm-3">
      <div class="row">
        <div class="col">
          <?php   
            if($lat != "" && $long != ""){
          ?> 
          <div id="viewDiv" style="height: 300px;"></div> 
          <script src="https://herbweb.botany.ubc.ca/arcgis_js_api/library/4.10/dojo/dojo.js"></script>
          <script src="js/map.js"></script>
          <?php } else {?>
          <div style="height: 300px; text-align:center; line-height:300px;">
            <span style="">No coordinates for this record</span>
          </div>
          <?php } ?>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <?php            
            if ($_GET['Database'] === 'fish') {

              $url = 'https://open.library.ubc.ca/media/download/jpg/fisheries/'.$findAllRec[0]->getField("IIFRNo").'/0';
              $linkToWebsite = 'https://open.library.ubc.ca/collections/fisheries/items/1.021095'.$findAllRec[0]->getField("IIFRNo");
              if (@getimagesize($url)[0] >0 && @getimagesize($url)[1] > 0) {
                echo '<a href ='. htmlspecialchars($linkToWebsite).' target="_blank">'.'<img id = "fish" class="img-fluid minHeight" src="'.htmlspecialchars($url) .'"></a>';
              } else {
                echo '<div style="height: 300px; text-align:center; line-height:300px;">';
                  echo '<span style="">No picture found for this record</span>';
                echo '</div>';
              }
            }
            else if ($_GET['Database'] === 'entomology') {
              //check if image url actually exists
              $genusPage = getGenusPage($findAllRec[0]);
              $genusSpecies = getGenusSpecies($findAllRec[0]);
              $html = file_get_html($genusPage);
              $species = $html->find('.speciesentry');
              $foundImage = false;

              foreach($species as $spec) {
                $speciesName = $spec->innertext;
                if (strpos($speciesName, $genusSpecies) !== false ) {
                  $foundImage = true;
                  $images = $spec->find('a');
                  $link = $images[0]->href;
                  $url = str_replace('http','https',$genusPage);
                  echo '<a href="'.$url.'" target="_blank"><figure><img class="img-fluid minHeight" src ="'.$url.$link.'"><figcaption style="text-align:center;">See more images here</figcaption></figure></a>';
                  break;
                }
              }
              if($foundImage==false) {
                echo '<div style="height: 300px; text-align:center; line-height:300px;">';
                  echo '<span style="">No picture found for this record</span>';
                echo '</div>';
              }
            }
            else {
              $validDb = false;
              if ($_GET['Database'] == 'avian' ||$_GET['Database'] == 'herpetology' || $_GET['Database'] == 'mammal') {
                $url = getPhotoUrl($findAllRec[0]->getRecordID());
                $validDb = true;
              }
              else if ($_GET['Database'] == 'vwsp' || $_GET['Database'] == 'bryophytes' || $_GET['Database'] == 'fungi' 
              || $_GET['Database'] == 'lichen' || $_GET['Database'] == 'algae') {
                $url = getPhotoUrl($_GET['AccessionNo']);
                $validDb = true;
              }
              if ($validDb) {
                if (@getimagesize($url)[0] >0 && @getimagesize($url)[1] > 0) {
                  echo '<a href ='. htmlspecialchars($url).' target="_blank">'.'<img class="img-fluid minHeight" src="'.htmlspecialchars($url) .'"></a>';
                } else {
                  echo '<div style="height: 300px; text-align:center; line-height:300px;">';
                    echo '<span style="">No picture found for this record</span>';
                  echo '</div>';
                }
              }
            } 
          ?>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once("partials/footer.php");?>
</body>
</html>