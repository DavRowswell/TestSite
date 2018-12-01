<?php 
require_once ('FileMaker.php');
require_once ('db.php');

$fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

$layouts = $fm->listLayouts();

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

// $findCommand = $fm->newFindCommand($layouts[2]);
// echo $layouts[2];
// if (isset($_GET['Genus'])) {
//     $identificationID = $_GET['Genus'];
// }
// echo $identificationID;
// $findCommand->addFindCriterion('Genus', $identificationID);
// $result = $findCommand->execute();
if(FileMaker::isError($result)) {
    // echo "nothing found";
    $findAllRec = [];
} else {
    $findAllRec = $result->getRecords();
}

// $result = $compoundFind->execute(); 
?>