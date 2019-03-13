<!DOCTYPE html>
<html>
<head>
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
      <div class="col-sm-5">
       <a href="render.php?Database=<?php echo htmlspecialchars($_GET['Database'])?>" 
          role="button" class="btn btn-primary" 
          style="font-size:12px; text-align:left; padding-left:1px; padding-right:1px;">Show All Records</a>   
      </div>
  </div>
  <div style="position:relative; top:12px">
    <?php 
    foreach ($layoutFields as $rf) {
      if ($rf === 'SortNum' || $rf === 'Ref Type') continue; ?>
    <div class="row">
      <div class="col">
        <label style="position:relative; top:6px" for="field-<?php echo $rf?>">
          <?php echo htmlspecialchars(formatField($rf)) ?>
        </label>
      </div>
      <div class="col">
        <input type="text" id="field-<?php echo $rf?>" 
          <?php
            if (isset($_POST[$rf]))
              echo "value=".htmlspecialchars($_POST[$rf]);
          ?> 
          name="<?php echo htmlspecialchars($rf) ?>"
          class="form-control">
      </div>
    </div> 
    <?php } ?>
    <div class = "col" style="position:relative; top:8px">
      <input id="form" class="btn btn-primary" type="button" value="Submit" onclick="Process(clearURL())">    
    </div>
  </form>
  </div>
  </div>
  <div id="legend" class="border col-sm-5 offset-sm-2" style="position:relative; top:6px; padding-top:14px"> 
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
