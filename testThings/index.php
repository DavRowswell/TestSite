<?php
require_once ('FileMaker.php');
require_once ('db.php');
// $fm = new FileMaker();
// require_once 'db.php';

$fm = new FileMaker(FM_FILE, FM_HOST, FM_USER, FM_PASS);

$connected = $fm->listLayouts();
$findAllObject = $fm->newFindAllCommand($connected[0]);
echo $connected[0] . "<br>";
$findAllRes = $findAllObject->execute();
$findAllRec = $findAllRes->getRecords();
If(FileMaker::isError($connected)){
        echo "out of luck ".$connected;
// } else {
//         $recFields = $findAllRes->getFields();
//         echo "connected <br>";
//         foreach($findAllRec as $i){
//                 foreach($recFields as $j){
//                         echo $i->getField($j) . " ";
//                 }
//                 echo "<br>";
//         }
        // foreach($fm->listScripts() as $db){
        //         echo $db . "<br>";
        // }
}

?>

<html>
<body>

<form action="search.php" method="get">
Name: <input type="text" name="name"><br>
E-mail: <input type="text" name="email"><br>
<input type="submit">
</form>

</body>
</html>
