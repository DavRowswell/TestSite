<?php 
require_once ('FileMaker.php');
require_once ('db.php');

$fm = new FileMaker(FM_FILE, FM_HOST, FM_USER, FM_PASS);

$layouts = $fm->listLayouts();

// CompoundFind on all inputs with values
// $compoundFind = $fm->newCompoundFindCommand($layouts[2]);

// $accessionFindRequest = $fm->newFindRequest($layouts[2]);
// $accessionFindRequest->addFindCriterion('Accession No.', '=='.$_GET['Accession No.']);

// $genusFindRequest = $fm->newFindRequest($layouts[2]);
// $genusFindRequest->addFindCriterion('Genus', '=='.$_GET['Genus']);

// $speciesFindRequest = $fm->newFindRequest($layouts[2]);
// $speciesFindRequest->addFindCriterion('Species', '=='.$_GET['Species']);

// $locationFindRequest = $fm->newFindRequest($layouts[2]);
// $locationFindRequest->addFindCriterion('Location', '=='.$_GET['Location']);

// $compoundFind->add($accessionFindRequest);
// $compoundFind->add($genusFindRequest);
// $compoundFind->add($speciesFindRequest);
// $compoundFind->add($locationFindRequest);

// $compoundFind->addSortRule('Accession No.', 1, FILEMAKER_SORT_DESCEND);
$findCommand = $fm->newFindCommand($layouts[2]);
if (isset($_GET['Genus'])) {
    $identificationID = $_GET['Genus'];
}
$findCommand->addFindCriterion('Accession No.', $identificationID);
$result = $findCommand->execute();

// $result = $compoundFind->execute(); 
$findAllRec = $result->getRecords();
?>