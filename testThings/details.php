<div class="container">

<?php
require_once ('FileMaker.php');
require_once ('partials/header.php');
require_once ('db.php');

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
    <tbody>
        <?php foreach($recFields as $i){?>
      <tr>
          <th scope="col"><?php echo $i ?></th>
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