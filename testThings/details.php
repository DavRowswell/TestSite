<div class="container-fluid">

<?php
require_once ('FileMaker.php');
require_once ('partials/header.php');
require_once ('functions.php');
require_once ('db.php');

$fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

$layouts = $fm->listLayouts();
$layout = $layouts[0];

// foreach ($layouts as $l) {
//   if (strpos($l, 'search') !== false) {
//     $layout = $l;
//   }
// } 

foreach ($layouts as $l) {

  if ($_GET['Database'] === 'mi') {
    if (strpos($l, 'details') !== false) {
      $layout = $l;
      break;
    }
  }
  else if (strpos($l, 'details') !== false) {
    $layout = $l;
  }
}

$findCommand = $fm->newFindCommand($layout);

if (isset($_GET['AccessionNo']) && $_GET['AccessionNo'] !== '') {
    // echo "accession";
   
    if ($_GET['Database'] == 'vwsp' or $_GET['Database'] == 'bryophytes' or 
    $_GET['Database'] == 'fungi' or $_GET['Database'] == 'lichen' or $_GET['Database'] == 'ubcalgae'){
      $findCommand->addFindCriterion('Accession Number', '=='.$_GET['AccessionNo']);
    }
    else if ($_GET['Database'] == 'fossils' || $_GET['Database'] == 'avian' || $_GET['Database'] == 'herpetology' || $_GET['Database'] == 'mammals') {
      $findCommand->addFindCriterion('catalogNumber', '=='.$_GET['AccessionNo']);
    }
    else if ($_GET['Database'] == 'fish'){
      
      $findCommand->addFindCriterion('ID', '=='.$_GET['AccessionNo']);
  
    }
    else {
      $findCommand->addFindCriterion('Accession No.', '=='.$_GET['AccessionNo']);
    }
}

$result = $findCommand->execute();

if(FileMaker::isError($result)) {
    // echo "nothing found";
    $findAllRec = [];
} else {
    $findAllRec = $result->getRecords();
}

?>

<html>
  <body>
  <?php
  // Check if layout exists, and get fields of layout
  If(FileMaker::isError($result)){
    echo $result->getMessage();
  } else {
    $recFields = $result->getFields();
  ?>

  <!-- construct table for given layout and fields -->
  <table class="table">
    <tbody>
        <?php foreach($recFields as $i){?>
      <tr>
          <th scope="col"><?php echo formatField($i) ?></th>
          <td><?php echo $findAllRec[0]->getField($i) ?></td>
      </tr>
        <?php }?>
    </tbody>
  </table>
    
  <?php
  }
  ?>
  </body>
</html>