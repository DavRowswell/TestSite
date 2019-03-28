<!DOCTYPE html>
<html>
<head>
  <style>
    #submit {
      padding-left: 5px;
      padding-right:5px;
    }
  </style>
  <?php
    session_start();
    require_once ('FileMaker.php');
    require_once ('partials/header.php');
    require_once ('functions.php');

    // echo "FM_FILE: $FM_FILE <br>
    //       FM_HOST: $FM_HOST <br>
    //       FM_USER: $FM_USER <br>
    //       FM_PASS: $FM_PASS <br>";

    $layouts = $fm->listLayouts();

    if (FileMaker::isError($layouts)) {
      $_SESSION['error'] = $layouts->getMessage();
      header('Location: error.php');
      exit;
    }

    $layout = $layouts[0];

    foreach ($layouts as $l) {
      //get current database name
      $page = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '=') + 1);
      if ($page == 'mi') {
        if (strpos($l, 'search') !== false) {
          $layout = $l;
          break;
        }
      }
      else if (strpos($l, 'search') !== false) {
        $layout = $l;
      }
    }
    $fmLayout = $fm->getLayout($layout);
    $layoutFields = $fmLayout->listFields();
  ?>
</head>
<body class="container-fluid">
 <?php require_once ('partials/navbar.php'); ?>
 <div class ="row">
  <div id="form" class = "col-sm-4"  >
  <form action="render.php" method="get" id = "submit-form">
    <div class="form-group">
      <input type="text" name="Database" style="display:none;" 
      value=<?php if (isset($_GET['Database'])) echo htmlspecialchars($_GET['Database']); ?>>
    </div>
    
    <div class="row">
    <div id = 'submit'>
      <input id="form" class="btn btn-primary" type="button" value="Submit"  style = "font-size:12px;" onclick="Process(clearURL())">    
    </div>     
  </div>

  <?php if ($_GET['Database'] == 'fish' || $_GET['Database'] == 'avian' ||$_GET['Database'] == 'herpetology' || $_GET['Database'] == 'mammal'
  || $_GET['Database'] == 'vwsp' || $_GET['Database'] == 'bryophytes' || 
  $_GET['Database'] == 'fungi' || $_GET['Database'] == 'lichen' || $_GET['Database'] == 'algae') { ?>
  <div class="col-sm-12 form-check">
        <input class="form-check-input" type="checkbox" value="" id="imageCheck">
        <label class="form-check-label" for="imageCheck">
            Only show records that contain an image
         </label>
  </div>
  <input type="hidden" name = "hasImage" id = "hasImage">
  <?php } ?>
  <input type="hidden" name = "type" id = "type">
            
  <div style="position:relative; top:12px">
    <?php 
    foreach ($layoutFields as $rf) {
      //echo $rf;
      $ignoreValues = ['SortNum', 'AccessionNumerical', 'Imaged', 'IIFRNo', 'Photographs::photoFileName'];
      if (in_array($rf, $ignoreValues)) continue; ?>
    <div class="row">
      <div class="col">
        <label style="position:relative; top:6px" for="field-<?php echo $rf?>">
          <?php echo htmlspecialchars(formatField($rf)) ?>
        </label>
      </div>
      <div class="col">   
        <input type="text" id="field-<?php echo $rf?>" 
          <?php
            if (isset($_POST[str_replace(' ', '_', $rf)]))
              echo "value=".htmlspecialchars($_POST[str_replace(' ', '_', $rf)]);
          ?> 
          name="<?php echo htmlspecialchars($rf) ?>"
          class="form-control">
      </div>
    </div> 
    <?php } ?>

    <div class = "row" style="padding-top:15px">
    <div class = "col" style ="font-size:13px"> Search By
  
      <div class = "btn-group btn-group-toggle" data-toggle="buttons" >
        <label class = "btn btn-primary active" style="font-size:12px;">
          <input type="radio"  id = "and" autocomplete="off"  checked> AND 
        </label>
        <label class = "btn btn-primary" style="font-size:12px;">
          <input type="radio" id = "or" autocomplete="off" > <span style="visibility: hidden">&nbsp;</span>OR<span style="visibility: hidden">&nbsp;</span>
        </label> 
      </div>
      </div>
      <div class="col">
       <a href="render.php?Database=<?php echo htmlspecialchars($_GET['Database'])?>" 
          role="button" class="btn btn-primary" 
          style="font-size:12px; text-align:left; padding-left:2px; padding-right:2px;">Show All Records</a>   
      </div>
      </div>
  </form>
  </div>
  </div>
  <div id="legend" class="border col-sm-5 offset-sm-2" style="position:relative; top:6px; padding-top:14px"> 
      <?php
      if($_GET['Database'] === 'entomology'){
        echo '<div class="row">';
          echo '<div class="col-sm-12">';
            echo '<a href="https://www.zoology.ubc.ca/entomology/"><img width="100%" src="images/entomology-link-image.jpg"></a>';
          echo '</div>';
        echo '</div>';
      }
      ?>
      <header style="padding-bottom:12px"> Search Operators </header>
      <div class="row">
        <div class="col-sm-1"> = </div>
        <div class="col-sm-11"> match a whole word (or match empty) </div>
      </div>
      <div class="row">
        <div class="col-sm-1"> == </div>
        <div class="col-sm-11"> match entire field exactly </div>
      </div>
      <div class="row">
        <div class="col-sm-1"> ! </div>
        <div class="col-sm-11"> find duplicate values </div>
      </div>
      <div class="row">
        <div class="col-sm-1"> &lt </div>
        <div class="col-sm-11"> find records with values less than to the one specified </div>
      </div>
      <div class="row">
        <div class="col-sm-1"> &lt= </div>
        <div class="col-sm-11">  find records with values less than or equal to the one specified </div>
      </div>
      <div class="row">
        <div class="col-sm-1"> &gt </div>
        <div class="col-sm-11">  find records with values greater than to the one specified </div>
      </div>
      <div class="row">
        <div class="col-sm-1"> &gt= </div>
        <div class="col-sm-11">  find records with values greater than or equal to the one specified </div>
      </div>
      <div class="row">
        <div class="col-sm-1"> ... </div>
        <div class="col-sm-11">  find records with values in a range (Ex. 10...20) </div>
      </div>
      <div class="row">
        <div class="col-sm-1"> &frasl;&frasl; </div>
        <div class="col-sm-11">  find records with today's date </div>
      </div>
      <div class="row">
        <div class="col-sm-1"> ? </div>
        <div class="col-sm-11">  find records invalid date and time </div>
      </div>
      <div class="row">
        <div class="col-sm-1"> @ </div>
        <div class="col-sm-11">  match any one character </div>
      </div>
      <div class="row">
        <div class="col-sm-1"> # </div>
        <div class="col-sm-11">  match any digit </div>
      </div>
      <div class="row">
        <div class="col-sm-1"> * </div>
        <div class="col-sm-11">  match zero or more characters </div>
      </div>
      <div class="row">
        <div class="col-sm-1"> \ </div>
        <div class="col-sm-11">  escape any character </div>
      </div>
      <div class="row">
        <div class="col-sm-1"> &#34&#34 </div>
        <div class="col-sm-11">  match phrase from word start </div>
      </div>
      <div class="row">
        <div class="col-sm-1"> *&#34&#34 </div>
        <div class="col-sm-11">  match phrase from anywhere </div>
      </div>
   </div>
   </div>
   </div>
  <?php require_once("partials/footer.php");?>
  <script src="js/process.js"> </script>
</body>
</html>
