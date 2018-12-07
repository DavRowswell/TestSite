<?php 
require_once ('FileMaker.php');
require_once ('databases/db.php');

$fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

echo $FM_HOST . "<br>";
echo $FM_FILE . "<br>";
echo $FM_USER . "<br>";
echo $FM_PASS . "<br>";

$layouts = $fm->listLayouts();

if (FileMaker::isError($layouts)) {
    echo $layouts;
}

// CompoundFind on all inputs with values
$findCommand = $fm->newFindCommand($layouts[2]);

if (isset($_GET['AccessionNo']) && $_GET['AccessionNo'] !== '') {
    // echo "accession";
    $findCommand->addFindCriterion('Accession No.', $_GET['AccessionNo']);
}
if (isset($_GET['Genus']) && $_GET['Genus'] !== '') {
    // echo "genus";
    $findCommand->addFindCriterion('Genus', $_GET['Genus']);
}
if (isset($_GET['Species']) && $_GET['Species'] !== '') {
    // echo "species";
    $findCommand->addFindCriterion('Species', $_GET['Species']);
}
if (isset($_GET['Location']) && $_GET['Location'] !== '') {
    // echo "location";
    $findCommand->addFindCriterion('Location', $_GET['Location']);
}

$result = $findCommand->execute();

if(FileMaker::isError($result)) {
    $findAllRec = [];
} else {
    $findAllRec = $result->getRecords();
}

?>