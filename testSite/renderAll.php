<!DOCTYPE html>
<html>
<head>
    <?php
    session_start();
    require_once ('FileMaker.php');
    require_once ('partials/header.php');
    // require_once ('../partials/navbar.php');
    require_once ('DatabaseSearch.php');
    
    // list databases
    // $databases = ['algae', 'avian', 'bryophytes', 'entomology', 'fish', 
    // 'fossil', 'fungi', 'herpetology', 'lichen', 'mammal', 'mi', 
    // 'miw', 'vwsp'];

    $databases = ['avian', 'entomology', 'fish', 
    'fossil', 'herpetology', 'mammal', 'mi', 
    'miw'];

    // $databases = ['avian', 'entomology'];

    /*
      Country
      Province or State
      Locality
      Elevation/Depth
      Phylum
      Class
      Family
      Genus
      Species
      Collector
      Collection Date
      Year
      Month
      Day
    */

    $searchDatabases = [];

    foreach ($databases as $db) {
        require_once ('databases/'.$db.'db.php');
        // echo "$FM_FILE <br>";
        $fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);
        if (FileMaker::isError($fm->listLayouts())) {
          // echo $FM_FILE;

          continue;
        }
        $databaseSearch = new DatabaseSearch($fm, $db);
        array_push($searchDatabases, $databaseSearch);
    }
    // exit;
    ?>
    <style>
    .collapsible {
  background-color: #777;
  color: white;
  cursor: pointer;
  padding: 18px;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 15px;
}

.active, .collapsible:hover {
  background-color: #555;
}

.content {
  padding: 0 18px;
  display: none;
  overflow: hidden;
  background-color: #f1f1f1;
}
</style>
    </head>
    <body class="container-fluid">
    <?php

    require_once ('partials/navbar.php');
    // generate results from FileMaker query
    foreach ($searchDatabases as $sd) {
        // determine search and results layouts for given database
        setLayouts($sd);
        generateTable($sd);
    }

    function setLayouts($sd) {
        
        $fm_db = $sd->getFM();
        $layouts = $fm_db->listLayouts();
        $searchLayout = "";
        $resultLayout = "";

        foreach ($layouts as $l) {
            if ($sd->getDatabase() === 'mi') {  // mi and miw layouts get mixed up so this check is necessary to get the mi layouts coorrectly
                if ($l == 'search-MI') {
                    $searchLayout = $l;
                } else if ($l == 'results-MI') {
                    $resultLayout = $l;
                }
            } else { // go through layouts and find the search and results layouts
                if (strpos($l, 'search') !== false) {
                    $searchLayout = $l;
                } else if (strpos($l, 'results') !== false) {
                    $resultLayout = $l;
                }
            }
        }
        
        $sd->setSearchLayout($searchLayout);
        $sd->setResultLayout($resultLayout);

    }

    function generateTable($sd) {
        $numRes = 50;
        require_once ('functions.php');
        $fm = $sd->getFM();
        $resultLayout = $sd->getResultLayout();
        $fmResultLayout = $fm->getLayout($resultLayout);
        $findCommand = $fm->newFindCommand($resultLayout);
        $layoutFields = $fmResultLayout->listFields();
        // echo 'Database: ' . $sd->getDatabase() . "<br>";
        $database = $sd->getDatabase();
        ?>
        
        <button class="collapsible">
        <?php if($database === "mi" || $database === "miw") { ?>
          <h1><b><?php if($database === "mi"){echo "Dry Marine Invertebrate";}else{echo "Wet Marine Invertebrate";} ?> Results</b></h1>
        <?php } else { ?>
        <h1><b><?php echo ucfirst($database); ?> Results</b></h1>
        <?php }?>
        </button>
        <div class="content"> 
      <?php
        foreach(array_keys($_GET) as $field) {
          if (!addFindCriterionIfSet($field, $layoutFields, $findCommand)) {
            echo 'No records found.<br>';
            
        ?>
        </div>
      <?php
            return;
          }
        }
        // $findCommand->addFindCriterion("Location::country", "Canada");
        $findCommand->setRange(0, $numRes);
        $result = $findCommand->execute();
        // require_once ('partials/pageController.php');
        setResultPageRange($findCommand, $numRes, $database);
        // $findCommand->setRange(0, $numResults);
        $result = $findCommand->execute();
        // If(FileMaker::isError($result)){
        //     $_SESSION['error'] = $result->getMessage();
        //     header('Location: error.php');
        //     exit;
        // }
        if (FileMaker::isError($result)) {
          echo $result->getMessage() . "<br>";
          // echo 'hello';
          return;
        }
        // echo "<br>";
        $findAllRec = $result->getRecords();
        require ('partials/allPageController.php');
        printTable($database, $findAllRec, $fmResultLayout);
        require ('partials/allPageController.php');
        ?>
          </div>
        <?php
    }

    function setResultPageRange($findCommand, $numRes, $database) {
      if (isset($_GET[$database.'Page']) && $_GET[$database.'Page'] !== '') {
        $numPages = $_GET[$database.'Page'];
        $findCommand->setRange(($numPages-1) * $numRes, $numRes);
      } else {
        $findCommand->setRange(0, $numRes);
      }
    }

    function addFindCriterionIfSet($field, $layoutFields, $findCommand) {
      if (fieldIsSet($field, $layoutFields)) {
        addFindCommand($field, $layoutFields, $findCommand);
        // $findCommand->addFindCriterion($field, $_GET[mapField($field)]);
        return true;
      } else {
        return false;
      }
    }

    function fieldIsSet($field, $layoutFields) {
      // echo 'field: ' . $field . '<br>';
      foreach ($layoutFields as $lf) {
        // echo formatField($lf) . '<br>';
        if (formatField($lf) === "Phylum") {
          return true;
        }
      }
      return false;
      // return isset($_GET[$field]);
    }

    function addFindCommand($field, $layoutFields, $findCommand) {
      foreach ($layoutFields as $lf) {
        if ($field === formatField($lf)) {
          $findCommand->addFindCriterion($lf, $_GET[$field]);
        }
      }
    }

    function printTable($database, $findAllRec, $resultLayout) {
        $recFields = $resultLayout->listFields();
    ?>
      <div class="row">
  </div>
        <table class="table table-hover table-striped table-condensed tasks-table" style="position:relative; top:16px">
            <thead>
                <tr>
                    <?php foreach($recFields as $i){
                        if ($i === 'SortNum' || $i === 'Accession Numerical'  || $i === 'Photographs::photoFileName') continue;?>
                        <th id = <?php echo htmlspecialchars(formatField($i)) ?> scope="col">
                            <!-- <a style="padding: 0px;" href= -->
                            <?php echo '<b>'.htmlspecialchars(formatField($i)) ?>
                            <!-- </a> -->
                        </th>
                    <?php }?>
                </tr>
            </thead>
            <tbody>
      <?php foreach($findAllRec as $i){
        ?>
      
      <tr>
        <?php foreach($recFields as $j){
          
          if ($j === 'SortNum' || $j === 'Accession Numerical'  || $j === 'Photographs::photoFileName'  ) continue;
          if(formatField($j) == 'Accession Number' || $j === 'SEM #'){
            ?>
            <td id="data">
              <a style="padding: 0px;"
                href="details.php?Database=<?php echo htmlspecialchars($database). 
                  '&AccessionNo='.htmlspecialchars($i->getField($j)) 
                ?>"
              >
              <?php
              $photoExists = $i->getField("Photographs::photoFileName");
            
              if (($database === 'mammal' || $database === 'avian' || $database === 'herpetology')
              &&  $photoExists !== "") {
              ?>
                <div class="row">
                  <div class="col"> <b><?php echo htmlspecialchars(trim($i->getField($j))) ?></b></div>
                  <div class="col"> <span style="display:inline" id = "icon"  class="fas fa-image"></span></div> 
                </div>
              <?php
              }  else {
              ?>
                <b><?php echo htmlspecialchars(trim($i->getField($j))) ?></b>
              <?php
              }        
              ?>
              </a>
            </td>
          <?php
          }
          else if (formatField($j) == 'Genus' || formatField($j) == 'Species'){
            echo '<td id="data" style="font-style:italic;">'. htmlspecialchars($i->getField($j)).'</td>';
          }
          else {
            echo '<td id="data">'. htmlspecialchars($i->getField($j)).'</td>';
          }
        }?>
      </tr>
      <?php }?>
    </tbody>
  </table>  
        <?php
    }
    include_once ('partials/footer.php');
    ?>
    
<script src="js/process.js"> </script>
</body>
</html>