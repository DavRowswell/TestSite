<div class="container">

<?php
require_once ('FileMaker.php');
require_once ('db.php');
require_once ('partials/header.php');

$fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

$layouts = $fm->listLayouts();

// CompoundFind on all inputs with values
$findCommand = $fm->newFindCommand($layouts[2]);

if (isset($_GET['AccessionNo']) && $_GET['AccessionNo'] !== '') {
    // echo "accession";
    $findCommand->addFindCriterion('Accession No.', '=='.$_GET['AccessionNo']);
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
    echo $result;
  } else {
    $recFields = $result->getFields();
  ?>

  <!-- construct table for given layout and fields -->
  <table class="table">
    <thead>
        <?php foreach($recFields as $i){?>
      <tr>
          <th scope="col"><?php echo $i ?></th>
      </tr>
        <?php }?>
    </thead>
    <tbody>
      <?php foreach($findAllRec as $i){
      ?>
      <tr>
        <?php foreach($recFields as $j){?>
          <td><?php echo $i->getField($j) ?></td>
        <?php }?>
      </tr>
      <?php }?>
    </tbody>
  </table>
    
  <?php
  }
  ?>
  </body>
</html>