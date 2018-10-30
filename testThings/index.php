<?php
require_once ('FileMaker.php');

$fm = new FileMaker();
include('db.php');

$connected = $fm->listLayouts();
If(FileMaker::isError($connected)){
        echo "out of luck ".$connected;
} else {
        echo "connected <br>";
        foreach($connected as $i){
                echo $i . "<br>";
        }
        echo "crystal is here <br>";
        echo "david is here <br>";
}

?>