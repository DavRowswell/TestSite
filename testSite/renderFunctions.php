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

function generateOneTable($sd, $numRes) {
  $fm = $sd->getFM();
  $layout = $sd->getSearchLayout();
  $formatLayout = $sd->getResultLayout();
  $fmLayout = $fm->getLayout($sd->getSearchLayout());
  $layoutFields = $fmLayout->listFields();
  // Find on all inputs with values
  $findCommand = $fm->newFindCommand($layout);
  if (isset($_GET['type']) && $_GET['type'] == 'or'){ $findCommand->setLogicalOperator('or');}
  foreach ($layoutFields as $rf) {
    $field = str_replace(" ", "_",$rf);
    if ($rf == 'Photographs::photoFileName' || $rf == 'Imaged') {
      $field = 'hasImage';
    }
    if (isset($_GET[$field]) && $_GET[$field] !== '') {
      if ($field == 'Accession_Number' and ($_GET['Database'] == 'vwsp' or $_GET['Database'] == 'bryophytes' or
            $_GET['Database'] == 'fungi' or $_GET['Database'] == 'lichen' or $_GET['Database'] == 'algae')) {
        if ( is_numeric($_GET[$field][0])) {
          $findCommand->addFindCriterion("Accession Numerical", $_GET[$field]);
        }
        else {
          $findCommand->addFindCriterion("Accession Number", $_GET[$field]);
        }
      }
      else if ($field == 'catalogNumber' && ($_GET['Database'] == 'fossil' ||
        $_GET['Database'] == 'avian' || $_GET['Database'] == 'herpetology' || $_GET['Database'] == 'mammal' )) {
          if ( is_numeric($_GET[$field][0])) {
            $findCommand->addFindCriterion("SortNum", $_GET[$field]);
          }
          else {
            $findCommand->addFindCriterion("catalogNumber", $_GET[$field]);
          }
      }
      else if ($field == 'Accession_No' && ($_GET['Database'] == 'mi' || $_GET['Database'] == 'miw' )) {
        if ( is_numeric($_GET[$field][0])) {
          $findCommand->addFindCriterion("SortNum", $_GET[$field]);
        }
        else {
          $findCommand->addFindCriterion("Accession No", $_GET[$field]);
        }
      }
      else {
        if ($field == 'hasImage') {
          $findCommand->addFindCriterion($rf, '*');
          if ($_GET['Database'] == 'entomology') {
            $findCommand->addFindCriterion($rf, 'Photographed');
          }
        }
        else {
          $findCommand->addFindCriterion($rf, $_GET[$field]);
        }
      }
    }
  }
  if (isset($_GET['Sort']) && $_GET['Sort'] != '') {
    $sortField = str_replace('+', ' ', $_GET['Sort']);
    $fieldSplit = explode(' ', $sortField);
    $sortBy = $_GET['Sort'];
    if (mapField($sortBy) === 'Accession Number') {
      if ($_GET['Database'] == 'vwsp' or $_GET['Database'] == 'bryophytes' or
          $_GET['Database'] == 'fungi' or $_GET['Database'] == 'lichen' or $_GET['Database'] == 'algae') {
        $sortBy = 'Accession Numerical';
      }
      else {
        $sortBy = 'sortNum';
      }
    }
    if($_GET['Database'] == 'entomology') {
      $sortBy = 'SEM #';
    }
    if($_GET['Database'] == 'fish') {
      $sortBy = 'accessionNo';
    }
    if ($_GET['SortOrder'] === 'Descend') {
      // echo 'Descending';
      $findCommand->addSortRule(str_replace('+', ' ', $sortBy), 1, FILEMAKER_SORT_DESCEND);
    } else {
      // echo 'Ascending';
      $findCommand->addSortRule(str_replace('+', ' ', $sortBy), 1, FILEMAKER_SORT_ASCEND);
    }
  }
  if (isset($_GET['Page']) && $_GET['Page'] != '') {
    $findCommand->setRange(($_GET['Page'] - 1) * $numRes, $numRes);
  }
  else {
    $findCommand->setRange(0, $numRes);
  }
  $result = $findCommand->execute();
  return $result;
}

function generateTable($sd, $numRes) {
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

function addFindCriterionIfSet($field, $layoutFields, $findCommand): bool {
  if (fieldIsSet($field, $layoutFields)) {
    addFindCommand($field, $layoutFields, $findCommand);
    return true;
  } else {
    return false;
  }
}

function fieldIsSet($field, $layoutFields): bool {
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

# Prints out a table with data for render.php
function echoDataTable($database, $allRecords, $recordFields) {

    # filter function
    function filterIgnoredFields($var): bool {
        # fields we are not going to use
        $ignoredFields = ['SortNum', 'Accession Numerical', 'Imaged', 'IIFRNo', 'Photographs::photoFileName', 'Event::eventDate', 'card01', 'Has Image', 'imaged'];

        return !in_array($var, $ignoredFields);
    }

    $usefulFields = array_filter($recordFields, callback: 'filterIgnoredFields');

    # echos the table heads for a list of elements
    function echoTableHeads($fields) {
        $page = $_GET['Page'] ?? 1;

        foreach($fields as $field) {
            $id = htmlspecialchars(formatField($field));

            $href = htmlspecialchars(
                replaceURIElement(
                    replaceURIElement(
                        replaceURIElement(
                            $_SERVER['REQUEST_URI'], 'Sort', str_replace('#','%23',replaceSpace($field)))
                        ,'SortOrder', shouldDescend($field) ? 'Descend' : 'Ascend')
                    ,'Page', $page)
            );

            $icon_class = $_GET['SortOrder'] ?? '' === 'Descend' ? 'oi-sort-descending' : 'oi-sort-ascending';

            echo "
                <th scope='col' id=$id>
                    <a style='padding: 0; white-space:nowrap;' href=$href>
                        <!-- order icon -->
                        <span id='icon'  class='oi $icon_class'></span>
                        <!-- field name -->
                        <b>$id</b>
                    </a>
                </th>
            ";
        }
    }

    # echos the table rows
    function echoTableRows($records, $fields) {
        foreach ($records as $record) {
            # each record has its table row
            echo '<tr>';
            foreach ($fields as $field) {
                # ID field logic
                if (formatField($field) === 'Accession Number' or $field === 'SEM #') {
                    $url = htmlspecialchars($_GET['Database']). '&AccessionNo='.htmlspecialchars($record->getField($field));
                    $id = htmlspecialchars(trim($record->getField($field)));

                    $hasImage = false;
                    if($_GET['Database'] === 'entomology' and $record->getField("Imaged") === "Photographed") $hasImage = true;
                    else if ($_GET['Database'] === 'fish' and $record->getField("imaged") === "Yes") $hasImage = true;
                    else if ($_GET['Database'] === 'mammal' or $_GET['Database'] === 'avian' or $_GET['Database'] === 'herpetology') {
                        if ($record->getField("Photographs::photoFileName") !== "") $hasImage = true;
                    }
                    # for vwsp lichen bryophytes fungi algae
                    else if ($record->getField("Imaged") === "Yes") $hasImage = true;

                    echo "
                        <th scope='row' id='data'>
                            <a href='details.php?Database=$url'>
                            " . ($hasImage ? '<span style="display:inline" id = "icon"  class="oi oi-image"></span>' : '') . "
                            <b>$id</b>
                        </th>
                    ";
                }
                # genus or species field for special style
                else if (formatField($field) == 'Genus' || formatField($field) == 'Species') {
                    echo '<td id="data" style="font-style:italic;">' . htmlspecialchars($record->getField($field)) . '</td>';
                }
                else {
                    echo '<td id="data">'. $record->getField($field) . '</td>';
                }
            }
            echo '</tr>';
        }
    }

    echo '
         <!-- construct table for given layout and fields -->
        <div class="container-fluid no-padding">
            <!-- id table for special color -->
            <table class="table table-hover table-striped table-responsive" id="table">
                <thead>
                    <tr>
                        ';
    echoTableHeads($usefulFields);
    echo '
                    </tr>
                </thead>
                <tbody>
                    ';
    echoTableRows($allRecords, $usefulFields);
    echo '
                </tbody>
            </table>
        </div>
    ';
}