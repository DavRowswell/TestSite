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

<!--- Print table start--->
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
  <!-- construct table for given layout and fields -->
  <div class="row">
    <div class="col">
      <table class="table table-hover table-striped table-condensed tasks-table" id="table">
        <thead>
          <tr>
            <?php 
            foreach($recFields as $i){
              $ignoreValues = ['SortNum', 'Accession Numerical', 'Imaged', 'IIFRNo', 'Photographs::photoFileName', 'Event::eventDate', 'card01', 'Has Image', 'imaged'];
              if (in_array($i, $ignoreValues)) continue;?>
              <th id = <?php echo htmlspecialchars(formatField($i)) ?>>
                <a style="padding: 0px; white-space:nowrap;" href=
                <?php 
                  if(isset($_GET['Page'])){
                    $page = $_GET['Page'];
                  }
                  else {
                    $page = '1';
                  }
                  if (shouldDescend($i)) {
                    echo htmlspecialchars(replaceURIElement(
                      replaceURIElement(
                        replaceURIElement(
                          $_SERVER['REQUEST_URI'], 'Sort', str_replace('#','%23',replaceSpace($i)))
                          , 'SortOrder', 'Descend')
                          , 'Page', $page));
                  } else {
                    echo htmlspecialchars(replaceURIElement(
                      replaceURIElement(
                        replaceURIElement(
                          $_SERVER['REQUEST_URI'], 'Sort', str_replace('#','%23',replaceSpace($i)))
                          , 'SortOrder', 'Ascend')
                          , 'Page', $page));
                  }
                ?>>
                <?php if(isset($_GET['SortOrder']) && $_GET['SortOrder'] == 'Descend'){ ?>
                <span style="display:inline" id = "icon"  class="oi oi-sort-descending"></span>
                <?php } else {?>
                <span style="display:inline" id = "icon"  class="oi oi-sort-ascending"></span>
                <?php } ?>
                <b><?php echo htmlspecialchars(formatField($i)) ?></b>
                </a>
              </th>
            <?php }?>
          </tr>
        </thead>
        <tbody>
          <?php foreach($findAllRec as $i){ ?>
            <tr>
              <?php foreach($recFields as $j){
                if (in_array($j, $ignoreValues)) continue;
                if(formatField($j) == 'Accession Number' || $j === 'SEM #'){
              ?>
              <td id="data">
                <a style="padding: 0px;"
                  href="details.php?Database=<?php echo htmlspecialchars($_GET['Database']). 
                    '&AccessionNo='.htmlspecialchars($i->getField($j)) ?>">
                <?php
                  $vertebrateHasPicture = ($_GET['Database'] === 'mammal' || $_GET['Database'] === 'avian' || $_GET['Database'] === 'herpetology')
                                          &&  $i->getField("Photographs::photoFileName") !== "";
                  $fishHasPicture = ($_GET['Database'] === 'fish' && $i->getField("imaged") === "Yes");
                  $herbHasPicture = ($_GET['Database'] == 'vwsp' or $_GET['Database'] == 'bryophytes' or 
                                    $_GET['Database'] == 'fungi' or $_GET['Database'] == 'lichen' or 
                                    $_GET['Database'] == 'algae') && $i->getField("Imaged") === "Yes";
                 
                  $entomologyHasPicture = false;
                  
                  if ($_GET['Database'] === 'entomology') {
                    if($i->getField("Imaged") === "Photographed") {
                      $entomologyHasPicture = true;
                    }
                }
                ?>
                <?php                                             
                  if ($vertebrateHasPicture || $fishHasPicture || $herbHasPicture || $entomologyHasPicture) {
                ?>
                  <b><?php echo htmlspecialchars(trim($i->getField($j))) ?></b>
                  <span style="display:inline" id = "icon"  class="oi oi-image"></span>
                <?php }  else { ?>
                  <b><?php echo htmlspecialchars(trim($i->getField($j))) ?></b>
                <?php } ?>
                </a>
              </td>
              <?php }
                else if (formatField($j) == 'Genus' || formatField($j) == 'Species'){
                  echo '<td id="data" style="font-style:italic;">'. htmlspecialchars($i->getField($j)).'</td>';
                }
                else {
                  echo '<td id="data">'. $i->getField($j).'</td>';
                }
              }?>
            </tr>
          <?php }?>
        </tbody>
      </table>
    </div>
  </div>
  <?php } ?>
  <?php require ('partials/pageController.php'); ?>
</div>
<!--- end print table --->
<?php require_once("partials/footer.php");?>
</body>
</html>