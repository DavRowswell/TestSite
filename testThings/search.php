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
$findCommand = $fm->newFindCommand($layouts[2]);
$identificationID = $_GET["name"];
$findCommand->addFindCriterion('Accession No.', $identificationID);
$result = $findCommand->execute(); 
$findAllRec = $result->getRecords();
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
      <li><a href="#">Bryophytes</a></li>
      <li><a href="#">Fungi</a></li>
      <li><a href="#">Lichen</a></li>
      <li><a href="#">Algae</a></li>
      <li><a href="#">Vascular Plants</a></li>
    </ul>
  </div>
</nav>


<!-- identificationID: <?php echo $_GET["name"]; ?><br> -->
<!-- Your email address is: <?php // echo $_GET["email"]; ?> -->

<?php 
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
}
?>
</body>
</html>

  <!-- Content here -->
  </div>