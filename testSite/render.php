<!DOCTYPE html>
<html>
<head>
  <?php
    session_start();
    //set_time_limit(0);
    $_SESSION['error'] = "";
    require_once ('FileMaker.php');
    require_once ('partials/header.php');
    require_once ('functions.php');
    $numRes = 100;
    $layouts = $fm->listLayouts();
    $layout = "";
    $formatLayout = "";
    foreach ($layouts as $l) {
      if ($_GET['Database'] === 'mi') {
        if ($l == 'search-MI') {
          $layout = $l;
      
        }
        else if ($l == 'results-MI') {
          $formatLayout = $l;
        }
      }
      else {
      if (strpos($l, 'search') !== false) {
        $layout = $l;
      }
      else if (strpos($l, 'results') !== false) {
        $formatLayout = $l;
      }
    }
    }

    function shouldDescend($field) {
      if (!isset($_GET['SortOrder']) || $_GET['SortOrder'] === '') return false;
      if (isset($_GET['Sort']) && $_GET['Sort'] === $field && isset($_GET['SortOrder']) && $_GET['SortOrder'] === 'Ascend') return true;
      return false;
    }

    $fmLayout = $fm->getLayout($layout);
    $layoutFields = $fmLayout->listFields();

    if (FileMaker::isError($layouts)) {
      $_SESSION['error'] = $layouts->getMessage();
      header('Location: error.php');
      exit;
    }

    // Find on all inputs with values
    $findCommand = $fm->newFindCommand($layout);
    foreach ($layoutFields as $rf) {
      
      $field = str_replace(" ", "_",$rf);
      
      if (isset($_GET[$field]) && $_GET[$field] !== '') {
        if ($field == 'Accession_Number' and ($_GET['Database'] == 'vwsp' or $_GET['Database'] == 'bryophytes' or 
              $_GET['Database'] == 'fungi' or $_GET['Database'] == 'lichen' or $_GET['Database'] == 'algae')) {
                if ( is_numeric($_GET[$field][0])) {
                  $findCommand->addFindCriterion("Accession Numerical", $_GET[$field]);
                }
              }
          else if ($field == 'catalogNumber' && ($_GET['Database'] == 'fossil' || 
           $_GET['Database'] == 'avian' || $_GET['Database'] == 'herpetology' || $_GET['Database'] == 'mammal' )) {
              if ( is_numeric($_GET[$field][0])) {
                $findCommand->addFindCriterion("SortNum", $_GET[$field]);
              }
           }
           else if ($field == 'Accession_No' && ($_GET['Database'] == 'mi' || $_GET['Database'] == 'miw' )) {
            if ( is_numeric($_GET[$field][0])) {
              $findCommand->addFindCriterion("SortNum", $_GET[$field]);
            }
           }
            else {      
              $findCommand->addFindCriterion($rf, $_GET[$field]);
            }
      }
    }
    if($_GET['Database'] === 'fish'){
      $findCommand->addFindCriterion('Ref Type','MC');
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
    } else {
        $findCommand->setRange(0, $numRes);
    }

    $result = $findCommand->execute();

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
  <style>
      th {
        font-size: 14px;
      }
      span {
        text-indent:-0.6em;
      }
      }
  </style>
</head>
<body>
<div class="container-fluid">
  <?php require_once ('partials/navbar.php'); ?>
  <div class = "row align-items-start">
      <div class = "col-sm-2">
        <?php require_once ('partials/pageController.php'); ?>
      </div>
      <div class = "col-sm-10" style="vertical-align:bottom;display: inline-block;; float:none">  
        <form method=post action=<?php echo "search.php"."?Database=".htmlspecialchars($_GET['Database']);?>>
          <?php
            $db = $_GET['Database'];
            foreach ($_GET as $key=>$value) {
              if (in_array($key, $layoutFields) || (in_array(str_replace('_', ' ', $key), $layoutFields)))
                echo "<input  type=hidden value=".htmlspecialchars($value)." name=".htmlspecialchars($key).">";
            }
          ?>
          <button type="submit" value = "Submit" class="btn btn-primary" style="float:right; margin-top:4px">Modify Search</button> 
        </form>
      </div>
  </div>
  <!-- construct table for given layout and fields -->
  <table class="table table-hover table-striped table-condensed tasks-table" style="position:relative; top:16px">
    <thead>
      <tr>
        <?php foreach($recFields as $i){
          if ($i === 'SortNum' || $i === 'Accession Numerical') continue;?>
          <th id = <?php echo htmlspecialchars(formatField($i)) ?> scope="col">
          <a style="padding: 0px;" href=
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
                    $_SERVER['REQUEST_URI'], 'Sort', replaceSpace($i))
                    , 'SortOrder', 'Descend')
                    , 'Page', $page));
            } else {
              echo htmlspecialchars(replaceURIElement(
                replaceURIElement(
                  replaceURIElement(
                    $_SERVER['REQUEST_URI'], 'Sort', replaceSpace($i))
                    , 'SortOrder', 'Ascend')
                    , 'Page', $page));
            }
          ?>>
          <span id = "icon" class="fas fa-sort"><?php echo htmlspecialchars(formatField($i)) ?> </span>
          </a>
        </th>
        <?php }?>
      </tr>
    </thead>
    <tbody>
      <?php foreach($findAllRec as $i){?>
      <tr>
        <?php foreach($recFields as $j){
          if ($j === 'SortNum' || $j === 'Accession Numerical') continue;
          if(formatField($j) == 'Accession Number' || $j === 'SEM #'){
            ?>
            <td id="data">
              <a style="padding: 0px;"
                href="details.php?Database=<?php echo htmlspecialchars($_GET['Database']). 
                  '&AccessionNo='.htmlspecialchars($i->getField($j)) 
                ?>"
              >
              <b><?php echo htmlspecialchars(trim($i->getField($j))) ?></b>
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
  <?php } ?>
  <div class = "row">
    <div class = "col-sm-2">
      <?php require ('partials/pageController.php'); ?>
    </div>
  </div>
</div>
<?php require_once("partials/footer.php");?>
</body>
<?php 
 /*   $layouts = $fm->listLayouts();
    $layout = "";
    foreach ($layouts as $l) {
      if ($_GET['Database'] === 'mi') {
        if (strpos($l, 'results') !== false) {
          $layout = $l;
          break;
        }
      }
      else if (strpos($l, 'results') !== false) {
        $layout = $l;
      }
    }

    $fmLayout = $fm->getLayout($layout);
    $layoutFields = $fmLayout->listFields();

    if (FileMaker::isError($layouts)) {
        echo $layouts->message;
        exit;
    }

    // Find on all inputs with values
    $findCommand = $fm->newFindCommand($layout);
    // echo $layout;
    foreach ($layoutFields as $rf) {
      // echo $rf;
        $field = explode(' ',trim($rf))[0];
        if (isset($_GET[$field]) && $_GET[$field] !== '') {
            // echo $_GET[$field];
            // echo $rf;
            $findCommand->addFindCriterion($rf, $_GET[$field]);
        }
    }

    $findCommand->setRange(0, $found - 2000);

    $AllResults = $findCommand->execute();

    if (FileMaker::isError($AllResults)) {
      $error = 'FileMaker Find Error  (' . $AllResults->getMessage() . ')';
      echo $error;                          
    }

    $recFields = $AllResults->getFields();

    $allRecords = $AllResults->getRecords();

    $recordMatrix = [];
    foreach ($allRecords as $record) {
      // echo "hello";
      $recordInfo = [];
      foreach ($recFields as $rf) {
        $recordInfo[] = $record->getField($rf);
        // echo $recordInfo[0];
      }
      $recordMatrix[] = $recordInfo;
    }

    $_SESSION['recordMatrix'] = $recordMatrix;
    echo '<br>' . $_SESSION['recordMatrix'][1][0];*/
  ?>
</html>

