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
</style>
<?php
 session_start();
  require_once ('FileMaker.php');
  require_once ('partials/header.php');
  require_once ('functions.php');

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

  // echo $_GET['AccessionNo'];

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
        $findCommand->addFindCriterion('Accession No.', '=='.$_GET['AccessionNo']);
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
  ?>
</head>

<body>
  <div class="container-fluid">
  <?php
  require_once ('partials/navbar.php');
  // Check if layout exists, and get fields of layout
  If(FileMaker::isError($result)){
    $_SESSION['error'] = $result;
    header('Location: error.php');
    exit;
  } else {
    $recFields = $result->getFields();
  ?>
  <!-- construct table for given layout and fields -->
  <table class="table">
    <tbody>
      <?php foreach($recFields as $i){
        if ($i === "Photographs::photoContainer" || $i === "Photographs::stableURL") continue;?> 
      <tr>
        <th scope="col"><?php echo htmlspecialchars(formatField($i)) ?></th>
        <td 
        <?php if (formatField($i) === "Latitude") {echo "id='Latitude'"; $lat = $findAllRec[0]->getField($i);}
              if (formatField($i) === "Longitude") {echo "id='Longitude'"; $long = $findAllRec[0]->getField($i);}?>>
              <?php echo htmlspecialchars($findAllRec[0]->getField($i)) ?>
        </td>
      </tr>
      <?php }?>
    </tbody>
  </table>   
  <?php } ?>
  <div class = "col-sm-6">
  <?php
  if($lat != "" && $long != ""){?>
  <span style = "font-style: normal; font-size: 0.8em;"> 
    *Should there be a map it is still in heavy development.
  </span>
  <div class="row">  
    <div class="col-sm-5">
      <div id="viewDiv" style="height: 300px;"></div>
    </div>
  </div>
  <script src="https://herbweb.botany.ubc.ca/arcgis_js_api/library/4.10/dojo/dojo.js"></script>
  <script src="js/map.js"></script>
  <?php }?>
  </div>
  <div class = "col-sm-6">
  
 
    <?php
        if ($_GET['Database'] === 'vwsp') {
          $url = "http://herbweb.botany.ubc.ca/herbarium/images/vwsp_images/Large_web/".$_GET['AccessionNo'].".jpg";
          if(@ getimagesize($url)){
            echo '<img src ="'. $url.'">';
          }
        }
        /* if ($_GET['Database'] === 'avian') {
        echo $fm->getContainerData(urlencode($findAllRec[0]->getField("Photographs::photoContainer")));
        
          echo '<img src="'.$fm->
        getContainerDataURL($findAllRec[0]->getField('Photographs::photoContainer')) .'">';

        
      
        echo $fm->getContainerDataURL($findAllRec[0]->getField("Photographs::photoContainer"));
       
        } */
        /* if ($_GET['Database'] === 'entomology') {
            //check if image url actually exists
            $url = 'http://www.zoology.ubc.ca/entomology/main/Lepidoptera/Crambidae/Crambus%20unistriatellus%20(1dorsal).jpg';
            if(@ getimagesize($url)){
              echo '<img src ="'. $url.'">';
            }
        } */
    ?>
  </div>
  <?php require_once("partials/footer.php");?>
  </div>
</body>
</html>