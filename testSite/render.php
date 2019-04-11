<!DOCTYPE html>
<html>
<head>
  <?php
    session_start();
    //set_time_limit(0);
    $_SESSION['error'] = "";
    require_once ('FileMaker.php');
    require_once ('partials/header.php');
    require_once ('functions.php')
    require_once ('db.php');
    $fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);  
    require_once ('lib/simple_html_dom.php');
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
    if (isset($_GET['type']) && $_GET['type'] == 'or'){ $findCommand->setLogicalOperator('or');}
    foreach ($layoutFields as $rf) {
      $field = str_replace(" ", "_",$rf);
      if ($rf == 'Photographs::photoFileName' || $rf == 'IIFRNo' || $rf == 'Imaged') {
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
          }
          else {
            $findCommand->addFindCriterion($rf, $_GET[$field]);
          }
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
      if($_GET['Database'] == 'entomology') {
        $sortBy = 'SEM #';
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
      line-height: 14px;
    }
    span {
      text-indent:-0.6em;
    }
  </style>
</head>
<body>
<div class="container-fluid">
  <?php require_once ('partials/navbar.php'); ?>
  <div class="row">
    <div class="col">
        <h1><b><?php echo ucfirst($_GET['Database']); ?> Results</b></h1>
    </div>
  </div>
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
        <button type="submit" value = "Submit" class="btn btn-primary">Modify Search</button> 
      </form>
    </div>
  </div>
  <!-- construct table for given layout and fields -->
  <div class="row">
    <div class="col">
      <table class="table table-hover table-striped table-condensed tasks-table">
        <thead>
          <tr>
            <?php foreach($recFields as $i){
              $ignoreValues = ['SortNum', 'AccessionNumerical', 'Photographs::photoFileName', 'IIFRNo', 'Imaged'];
              if (in_array($i, $ignoreValues)) continue;?>
              <th id = <?php echo htmlspecialchars(formatField($i)) ?>>
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
                  $fishHasPicture = ($_GET['Database'] === 'fish' && $i->getField("IIFRNo") !== "");
                  $herbHasPicture = ($_GET['Database'] == 'vwsp' or $_GET['Database'] == 'bryophytes' or 
                                    $_GET['Database'] == 'fungi' or $_GET['Database'] == 'lichen' or 
                                    $_GET['Database'] == 'algae') && $i->getField("Imaged") === "Yes";
                  
                  $entomologyHasPicture = false;
                  if ($_GET['Database'] === 'entomology') {
                    //check if image url actually exists
                    $genusPage = getGenusPage($findAllRec[0]);
                    $genusSpecies = getGenusSpecies($findAllRec[0]);
                    $html = file_get_html($genusPage);
                    $species = $html->find('.speciesentry');

                    foreach($species as $spec) {
                      $speciesName = $spec->innertext;
                      if (strpos($speciesName, $genusSpecies) !== false ) {
                        $entomologyHasPicture = true;
                        break;
                      }
                    }
                }
                                                  
                  if ($vertebrateHasPicture || $fishHasPicture || $herbHasPicture || $entomologyHasPicture) {
                ?>
                <div class="row">
                  <div class="col">
                    <b><?php echo htmlspecialchars(trim($i->getField($j))) ?></b>
                  </div>
                  <div class="col">
                    <span style="display:inline" id = "icon"  class="oi oi-image"></span>
                  </div> 
                </div>
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
                  echo '<td id="data">'. htmlspecialchars($i->getField($j)).'</td>';
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
<?php require_once("partials/footer.php");?>
</body>
</html>