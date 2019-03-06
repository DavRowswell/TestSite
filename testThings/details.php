<!DOCTYPE html>
<html>
<head>

<link rel="stylesheet" href="https://js.arcgis.com/3.27/esri/css/esri.css">
    <style>
      html, body, #map {
        /* height: 100%; */
        /* width: 50; */
        margin: 0;
        padding: 0;
      }
    </style>


<?php
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
      $_GET['Database'] == 'fungi' or $_GET['Database'] == 'lichen' or $_GET['Database'] == 'ubcalgae'){
        $findCommand->addFindCriterion('Accession Number', '=='.$_GET['AccessionNo']);
      }
      else if ($_GET['Database'] == 'fossil' || $_GET['Database'] == 'avian' || $_GET['Database'] == 'herpetology' || $_GET['Database'] == 'mammal') {

        $findCommand->addFindCriterion('catalogNumber', '=='.$_GET['AccessionNo']);
      }
      else if ($_GET['Database'] == 'fish'){
        
        $findCommand->addFindCriterion('ID', '=='.$_GET['AccessionNo']);
    
      }
      else {
        $findCommand->addFindCriterion('Accession No.', '=='.$_GET['AccessionNo']);
      }
  }

  $result = $findCommand->execute();

  if(FileMaker::isError($result)) {
      $findAllRec = [];
  } else {
      $findAllRec = $result->getRecords();
  }
  ?>
</head>

<body>

  <?php
  require_once ('partials/navbar.php');
  // Check if layout exists, and get fields of layout
  If(FileMaker::isError($result)){
    echo $result->getMessage();
  } else {
    $recFields = $result->getFields();
  ?>
  <!-- construct table for given layout and fields -->
  <table class="table">
    <tbody>
      <?php foreach($recFields as $i){?>
      <tr>
        <th scope="col"><?php echo formatField($i) ?></th>
        <td 
        <?php if (formatField($i) === "Latitude") {echo "id='Latitude'";}
              if (formatField($i) === "Longitude") {echo "id='Longitude'";}?>>
              <?php echo $findAllRec[0]->getField($i) ?></td>
      </tr>
      <?php }?>
    </tbody>
  </table>   
  <?php } ?>
  <div class="container-fluid">
</div>

<div id="map"></div>

<script src="https://js.arcgis.com/3.27/"></script>
    <script>
      var map;
        console.log(document.getElementById("Longitude").innerHTML);

      require(["esri/map", "dojo/domReady!"], function(Map) {
        map = new Map("map", {
          basemap: "topo",  //For full list of pre-defined basemaps, navigate to http://arcg.is/1JVo6Wd
          center: [document.getElementById("Longitude").innerHTML, document.getElementById("Latitude").innerHTML], // longitude, latitude
          zoom: 13
        });
      });
    </script>

</body>
</html>