<!DOCTYPE html>
<html>
<head>
  <?php
    session_start();
    require_once ('FileMaker.php');
    require_once ('partials/header.php');
    // require_once ('functions.php');

    // echo "FM_FILE: $FM_FILE <br>
    //       FM_HOST: $FM_HOST <br>
    //       FM_USER: $FM_USER <br>
    //       FM_PASS: $FM_PASS <br>";


    require_once ('DatabaseSearch.php');
    
    // list databases
    // $databases = ['algae', 'avian', 'bryophytes', 'entomology', 'fish', 
    // 'fossil', 'fungi', 'herpetology', 'lichen', 'mammal', 'mi', 
    // 'miw', 'vwsp'];

    $databases = ['avian', 'entomology', 'fish', 
    'fossil', 'herpetology', 'mammal', 'mi', 
    'miw'];

    // $databases = ['avian'];

    /*
      Country
      Province or State
      Locality
      Elevation/Depth
      
    */

    $allLayoutFields = [
        'Country',
        'Province or State',
        'Locality',
        'Elevation',
        'Depth',
        'Phylum',
        'Class',
        'Family',
        'Genus',
        'Species',
        'Collector',
        'Collection Date',
        'Year',
        'Month',
        'Day',
    ];

    // foreach ($databases as $db) {
    //     require_once ('databases/'.$db.'db.php');
    //     echo "$FM_FILE <br>";
    //     $fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);
    //     // $databaseSearch = new DatabaseSearch($fm, $db);
        
    //     $layouts = $fm->listLayouts();
    //     foreach ($layouts as $l) {
    //     //get current database name
    //     $page = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '=') + 1);
    //     if ($page == 'mi') {
    //         if (strpos($l, 'search') !== false) {
    //         $layout = $l;
    //         break;
    //         }
    //     }
    //     else if (strpos($l, 'search') !== false) {
    //         $layout = $l;
    //     }
    //     }
    //     $fmLayout = $fm->getLayout($layout);
    //     $layoutFields = $fmLayout->listFields();

    //     foreach ($layoutFields as $lf) {
    //         if (!in_array($lf, $allLayoutFields)) {
    //             array_push($allLayoutFields, $lf);
    //         }
    //     }
    // }

    


    // $layouts = $fm->listLayouts();

    // if (FileMaker::isError($layouts)) {
    //   $_SESSION['error'] = $layouts->getMessage();
    //   header('Location: error.php');
    //   exit;
    // }
    
    require_once ('functions.php');

  ?>
</head>
<body class="container-fluid">
 <?php require_once ('partials/navbar.php'); ?>
 <div class ="row">
  <div id="form" class = "col-sm-4"  >
  <p>Many functions do not work yet and this is a prototype. Must click submit.</p>
  <form action="renderAll.php" method="get" id = "submit-form">
    <div class="form-group">
      <input type="text" name="Database" style="display:none;" 
      value=<?php if (isset($_GET['Database'])) echo htmlspecialchars($_GET['Database']); ?>>
    </div>
    <div class="row">
      <div class="col-sm-5">
       <a href="renderAll.php" 
          role="button" class="btn btn-primary" 
          style="font-size:12px; text-align:left; padding-left:1px; padding-right:1px;">Show All Records</a>   
      </div>
  </div>
  <div style="position:relative; top:12px">
    <?php 
    foreach ($allLayoutFields as $rf) {
      //echo $rf;
      if ($rf === 'SortNum' || $rf === 'Ref Type' || $rf === 'Photographs::photoFileName') continue; ?>
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
    <div class = "col" style="position:relative; top:8px">
      <input id="form" class="btn btn-primary" type="button" value="Submit" onclick="allProcess(clearURL())">    
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
   <br>
  <?php require_once("partials/footer.php");?>
  <script src="js/process.js"> </script>
</body>
</html>
