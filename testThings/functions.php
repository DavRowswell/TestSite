<?php 
require_once ('FileMaker.php');
require_once ('db.php');

$fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

$layouts = $fm->listLayouts();
$findCommand = $fm->newFindCommand($layouts[2]);
$identificationID = $_GET["name"];
$findCommand->addFindCriterion('Accession No.', $identificationID);
$result = $findCommand->execute(); 
$findAllRec = $result->getRecords();

?>