<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
<div class="container">
<?php

require_once ('FileMaker.php');
require_once ('db.php');
// $fm = new FileMaker();
// require_once 'db.php';

$fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

$layouts = $fm->listLayouts();
// $findAllObject = $fm->newFindAllCommand($layouts[0]);
// // echo $layouts[0] . "<br>";
// $findAllRes = $findAllObject->execute();
// $findAllRec = $findAllRes->getRecords();
// If(FileMaker::isError($layouts)){
//         echo "out of luck ".$layouts;
// } else {
//         $recFields = $findAllRes->getFields();
//         // echo "connected <br>";
//         foreach($findAllRec as $i){
//                 foreach($recFields as $j){
//                         echo $i->getField($j) . " ";
//                 }
//                 echo "<br>";
//         }
//         // foreach($fm->listScripts() as $db){
//         //         echo $db . "<br>";
//         // }
// }

?>

<html>
<body>

identificationID: <?php echo $_GET["name"]; ?><br>
<!-- Your email address is: <?php // echo $_GET["email"]; ?> -->

<?php 
$findCommand = $fm->newFindCommand($layouts[2]);
$identificationID = $_GET["name"];
$findCommand->addFindCriterion('Accession No.', $identificationID);
$result = $findCommand->execute(); 
$findAllRec = $result->getRecords();
If(FileMaker::isError($layouts)){
    echo "out of luck ".$layouts;
} else {
    $recFields = $result->getFields();
    ?>

    <table class="table">
  <thead>
    <tr>
    <?php foreach($recFields as $i){?>
      <th scope="col"><?php echo $i ?></th>
    <?php }?>
    </tr>
  </thead>
  <tbody>
    <?php foreach($findAllRec as $i){
        if (strlen($_GET['name']) == strlen($i->getField('Accession No.'))) {
    ?>
    <tr>
      <?php foreach($recFields as $j){?>
      <td><?php echo $i->getField($j) ?></td>
      <?php }?>
    </tr>
      <?php }}?>
  </tbody>
</table>
    
    <?php



    // $recFields = $result->getFields();

    // foreach($recFields as $l){
    //     echo $l . " ";
    // }
    // echo "<br>";

    // echo "connected <br>";
    // foreach($findAllRec as $i){
    //     if (strlen($_GET['name']) == strlen($i->getField('identificationID'))) {
    //         foreach($recFields as $j){
    //             echo $i->getField($j) . " ";
    //         }
    //             echo "<br>";
    //     }
    // }
    // foreach($fm->listScripts() as $db){
    //         echo $db . "<br>";
    // }
}
?>
</body>
</html>

  <!-- Content here -->
  </div>