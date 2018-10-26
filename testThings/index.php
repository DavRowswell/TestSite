<?php
require_once ('FileMaker.php');

$fm = new FileMaker();
$fm->setProperty('database', 'Avian Research Collection.fmp12');
$fm->setProperty('hostspec', 'https://collections.zoology.ubc.ca');
$fm->setProperty('username', 'admin');
$fm->setProperty('password', 'admin');

$connected = $fm->listLayouts();
If(FileMaker::isError($connected)){
        echo "out of luck ".$connected;
} else {
        echo "connected <br>";
        foreach($connected as $i){
                echo $i . "<br>";
        }
        echo "crystal is here <br>";
}

?>