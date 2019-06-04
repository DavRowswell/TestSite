<?php
  session_set_cookie_params(0,'/','.ubc.ca',isset($_SERVER["HTTPS"]), true);
  session_start();
  $_SESSION['error'] = "";
  if (isset($_GET['Database'])){}
  else {
    $_SESSION['error'] = "No database given";
    header('Location: error.php');
    exit;
  }

  require_once ('FileMaker.php');
  require_once ('functions.php');
  require_once ('lib/simple_html_dom.php');
  require_once ('DatabaseSearch.php');
  
  if($_GET['Database'] == "all") {
    // list databases
    // $databases = ['algae', 'avian', 'bryophytes', 'entomology', 'fish', 
    // 'fossil', 'fungi', 'herpetology', 'lichen', 'mammal', 'mi', 
    // 'miw', 'vwsp'];
    $databases = ['avian', 'entomology', 'fish', 
    'fossil', 'herpetology', 'mammal', 'mi', 
    'miw'];
  } else {
    $databases = [$_GET['Database']];
  }

  $searchDatabases =[];
  foreach ($databases as $db) {
    require_once ('databases/'.$db.'db.php');
    $fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);
    if (FileMaker::isError($fm->listLayouts())) {
      continue;
    }
    $databaseSearch = new DatabaseSearch($fm, $db);
    array_push($searchDatabases, $databaseSearch);
  }


  if(sizeOf($searchDatabases)==1) {
    $fm = $searchDatabases[0]->getFM(); 
    setLayouts($searchDatabases[0]);
    $layout = $searchDatabases[0]->getSearchLayout();
    $formatLayout = $searchDatabases[0]->getResultLayout();
    
    $fmLayout = $fm->getLayout($searchDatabases[0]->getSearchLayout());
    $layoutFields = $fmLayout->listFields();

    $numRes = 100;
    $result = generateOneTable($searchDatabases[0], $numRes);
  } else {
    foreach ($searchDatabases as $sd) {
      $fm = $sd->getFM(); 
      setLayouts($sd);
      $layout = $sd->getSearchLayout();
      $formatLayout = $sd->getResultLayout();
      
      $fmLayout = $fm->getLayout($sd->getSearchLayout());
      $layoutFields = $fmLayout->listFields();

      $numRes = 50;
      $result = generateTable($sd, $numRes);
    }
  }
  // Check if layout exists, and get fields of layout
  If(FileMaker::isError($result)){
    $_SESSION['error'] = $result->getMessage();
    header('Location: error.php');
    exit;
  } else {
    $findAllRec = $result->getRecords();
    $fmFormatLayout = $fm->getLayout($formatLayout);
    $recFields = $fmFormatLayout->listFields();
?>
<!DOCTYPE html>
<html>
<head>
  <?php
    require_once ('partials/cssDecision.php');
    require_once ('partials/header.php');
  ?>
  <link rel="stylesheet" href="css/rendercss.css">
</head>
<body class="d-flex flex-column">
<?php require_once ('partials/navbar.php'); ?>
<div class="row">
  <div class="col">
    <?php if($_GET['Database'] === "mi" || $_GET['Database'] === "miw" || $_GET['Database'] === "vwsp") { ?>
      <h1><b><?php 
                if($_GET['Database'] === "mi"){echo "Dry Marine Invertebrate";}
                else if($_GET['Database'] === "vwsp"){echo "Vascular";}
                else{echo "Wet Marine Invertebrate";} 
              ?> Results</b>
      </h1>
    <?php } else { ?>
    <h1><b><?php echo ucfirst($_GET['Database']); ?> Results</b></h1>
    <?php }?>
    <div id="column-divider"></div>
  </div>
  </div>
  <div class="container-fluid">
  <?php require_once ('partials/pageController.php'); ?>
  <div class="row">
  <div class = "col" style="padding-bottom:5px;">  
    <form method=post action=<?php echo "search.php"."?Database=".htmlspecialchars($_GET['Database']);?>>
      <?php
        $db = $_GET['Database'];
        foreach ($_GET as $key=>$value) {
          if (in_array($key, $layoutFields) || (in_array(str_replace('_', ' ', $key), $layoutFields))) {
            echo "<input  type=hidden value=".htmlspecialchars($value)." name=".htmlspecialchars($key).">";
          }
        }
      ?>
      <button type="submit" value = "Submit" class="btn btn-custom">Modify Search</button> 
    </form>
  </div>
</div> 
<?php printOneTable($searchDatabases[0]->getDatabase(),$findAllRec,$recFields);
?>

<!--- Print table start--->

  <?php } ?>
  <?php require ('partials/pageController.php'); ?>
</div>
<!--- end print table --->
<?php require_once("partials/footer.php");?>
</body>
</html>