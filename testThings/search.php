<!-- Bootstrap CSS -->
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
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
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">HerbariumDB</a>
    </div>
    <ul class="nav navbar-nav">
      <li class="active"><a href="#">Home</a></li>
      <li><a href="#">Page 1</a></li>
      <li><a href="#">Page 2</a></li>
      <li><a href="#">Page 3</a></li>
    </ul>
  </div>
</nav>


<!-- identificationID: <?php echo $_GET["name"]; ?><br> -->
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