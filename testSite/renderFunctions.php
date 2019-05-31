<?php
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
        //require_once ('functions.php');
        $fm = $sd->getFM();
        $resultLayout = $sd->getResultLayout();
        $fmResultLayout = $fm->getLayout($resultLayout);
        $findCommand = $fm->newFindCommand($resultLayout);
        $layoutFields = $fmResultLayout->listFields();
        $database = $sd->getDatabase();
        
        
        echo '<button class="collapsible">';
        echo '<h1><b>';
        if($database === "mi" || $database === "miw") {
          if($database === "mi"){echo "Dry Marine Invertebrate";}
          else{echo "Wet Marine Invertebrate";}
        } else {
          echo ucfirst($database);
        }
        echo 'Results</b></h1>';
        echo '</button>';
        echo '<div class="content">';
        foreach(array_keys($_GET) as $field) {
          if (!addFindCriterionIfSet($field, $layoutFields, $findCommand)) {
            echo 'No records found.<br>';
            echo '</div>';
            return;
          }
        }

        $findCommand->setRange(0, $numRes);
        $result = $findCommand->execute();
        setResultPageRange($findCommand, $numRes, $database);
        $result = $findCommand->execute();
        if (FileMaker::isError($result)) {
          echo $result->getMessage();
          return;
        }
        $findAllRec = $result->getRecords();
        require ('partials/allPageController.php');
        printTable($database, $findAllRec, $fmResultLayout);
        require ('partials/allPageController.php');
        echo '</div>';
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
        return true;
      } else {
        return false;
      }
    }

    function fieldIsSet($field, $layoutFields) {
      foreach ($layoutFields as $lf) {
        if (formatField($lf) === "Phylum") {
          return true;
        }
      }
      return false;
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
      echo '<div class="row">';
      echo '</div>';
      echo '<table class="table table-hover table-striped table-condensed tasks-table" style="position:relative; top:16px">';
      echo '<thead>';
      echo '<tr>';
      foreach($recFields as $i){
        if ($i === 'SortNum' || $i === 'Accession Numerical'  || $i === 'Photographs::photoFileName'){continue;}
        echo '<th id = '.htmlspecialchars(formatField($i)).'scope="col">';
        echo '<b>'.htmlspecialchars(formatField($i));
        echo '</th>';
      }
      echo '</tr>';
      echo '</thead>';
      echo '<tbody>';
      foreach($findAllRec as $i){
        echo '<tr>';
        foreach($recFields as $j){
          if ($j === 'SortNum' || $j === 'Accession Numerical'  || $j === 'Photographs::photoFileName'  ){continue;}
          if(formatField($j) == 'Accession Number' || $j === 'SEM #'){
            echo '<td id="data">';
            echo '<a style="padding: 0px;" href="details.php?Database='.htmlspecialchars($database). 
              '&AccessionNo='.htmlspecialchars($i->getField($j)).'">';
            $photoExists = $i->getField("Photographs::photoFileName");
            if (($database === 'mammal' || $database === 'avian' || $database === 'herpetology') &&  $photoExists !== "") {
              echo '<div class="row">';
              echo '<div class="col"><b>'. htmlspecialchars(trim($i->getField($j))).'</b></div>';
              echo '<div class="col"> <span style="display:inline" id = "icon"  class="fas fa-image"></span></div>';
              echo '</div>';
            }  else {
              echo '<b>'.htmlspecialchars(trim($i->getField($j))).'</b>';
            }
            echo '</a>';
            echo '</td>';
          }
          else if (formatField($j) == 'Genus' || formatField($j) == 'Species'){
            echo '<td id="data" style="font-style:italic;">'. htmlspecialchars($i->getField($j)).'</td>';
          }
          else {
            echo '<td id="data">'. htmlspecialchars($i->getField($j)).'</td>';
          }
        }
        echo '</tr>';
      }
      echo '</tbody>';
      echo '</table>';
    } 

?>