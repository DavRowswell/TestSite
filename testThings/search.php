<?php

require_once ('FileMaker.php');
require_once ('db.php');
// $fm = new FileMaker();
// require_once 'db.php';

$fm = new FileMaker(FM_FILE, FM_HOST, FM_USER, FM_PASS);

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
$findCommand = $fm->newFindCommand($layouts[0]);
$findCommand->addFindCriterion('identificationID', $_GET["name"]);
$result = $findCommand->execute(); 
$findAllRec = $result->getRecords();
If(FileMaker::isError($layouts)){
    echo "out of luck ".$layouts;
} else {
    $recFields = $result->getFields();
    // echo "connected <br>";
    foreach($findAllRec as $i){
        if (strlen($i.getField("identificationID")) == $_GET["name"]) {
            foreach($recFields as $j){
                echo $i->getField($j) . " ";
            }
                echo "<br>";
        }
    }
    // foreach($fm->listScripts() as $db){
    //         echo $db . "<br>";
    // }
}
?>
</body>
</html>