<!DOCTYPE html>
<html>
<head>
  <?php
    require_once ('FileMaker.php');
    require_once ('partials/header.php');
    require_once ('functions.php');

    $numRes = 100;
    $layouts = $fm->listLayouts();
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

    foreach ($layoutFields as $rf) {
      // echo $rf;
        $field = explode(' ',trim($rf))[0];
        if (isset($_GET[$field]) && $_GET[$field] !== '') {
            $findCommand->addFindCriterion($rf, $_GET[$field]);
        }
    }

    if (isset($_GET['Sort']) && $_GET['Sort'] != '') {
        $sortField = str_replace('+', ' ', $_GET['Sort']);
        $fieldSplit = explode(' ', $sortField);
        if (!isset($_GET[$fieldSplit[0]]) || $_GET[$fieldSplit[0]] == '') {
          $findCommand->addFindCriterion($sortField, '*');
        }
        $sortBy = $_GET['Sort'];
        if (mapField($sortBy) === 'Accession Number') {
          $sortBy = 'SortNum';
          $findCommand->addFindCriterion($sortBy, '*');
          if ($_GET['Database'] === 'avian') {
            $findCommand->addFindCriterion('catalogNumber', '=B*');
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

    if(FileMaker::isError($result)) {
        $findAllRec = [];
    } else {
        $findAllRec = $result->getRecords();
    }

    // echo __LINE__;
    // Check if layout exists, and get fields of layout
    If(FileMaker::isError($result)){
      // echo $result->message;
      echo 'No Records Found';
      // echo __LINE__;
      exit;
    } else {
      // echo __LINE__;
      $recFields = $result->getFields();
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
  <?php require_once ('partials/navbar.php');
  require_once ('partials/pageController.php'); ?>
  <!-- construct table for given layout and fields -->
  <table class="table table-hover table-striped table-condensed tasks-table">
    <thead>
      <tr>
        <?php foreach($recFields as $i){?>
        <th id = <?php echo formatField($i) ?> scope="col">
          <a style="padding: 0px;" href=
          <?php 
            if (!isset($_GET['SortOrder']) || $_GET['SortOrder'] === '' || $_GET['SortOrder'] === 'Descend' || $i !== $_GET['Sort']) {
              echo replaceURIElement(
                replaceURIElement(
                  replaceURIElement(
                    $_SERVER['REQUEST_URI'], 'Sort', replaceSpace($i))
                    , 'Page', '1')
                    , 'SortOrder', 'Ascend');
              // replaceURIElement(replaceURIElement($_SERVER['REQUEST_URI'], 'Sort', 
              // replaceSpace($i)), 'Page', '1');
            } else {
              echo replaceURIElement(
                replaceURIElement(
                  replaceURIElement(
                    $_SERVER['REQUEST_URI'], 'Sort', replaceSpace($i))
                    , 'Page', '1')
                    , 'SortOrder', 'Descend');
            }
          ?>>
          <span id = "icon" class="fas fa-sort"><?php echo formatField($i) ?> </span>
          </a>
        </th>
        <?php }?>
      </tr>
    </thead>
    <tbody>
      <?php foreach($findAllRec as $i){?>
      <tr>
        <?php foreach($recFields as $j){
          if(formatField($j) == 'Accession No.' || formatField($j) == 'Accession Number' || $j == 'ID'){
            echo '<td id="data"><a style="padding: 0px;" href=\'details.php?Database=' . $_GET['Database'] . '&AccessionNo='.$i->getField($j).'\'>'.trim($i->getField($j)).'</a></td>';
          }
          else {
            echo '<td id="data">'.$i->getField($j).'</td>';
          }
        }?>
      </tr>
      <?php }?>
    </tbody>
  </table>  
  <?php }
  require ('partials/pageController.php');
  ?>
</div>
</body>
</html>
