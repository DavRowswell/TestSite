<?php 
require_once ('FileMaker.php');
require_once ('databases/db.php');

$fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

// echo $FM_HOST . "<br>";
// echo $FM_FILE . "<br>";
// echo $FM_USER . "<br>";
// echo $FM_PASS . "<br>";

$layouts = $fm->listLayouts();
$findCommand = $fm->newFindCommand($layouts[2]);
$result = $findCommand->execute();
$findAllRec = $result->getRecords();
$recFields = $result->getFields();

if (FileMaker::isError($layouts)) {
    echo $layouts;
}

// CompoundFind on all inputs with values
$findCommand = $fm->newFindCommand($layouts[2]);

foreach ($recFields as $rf) {
    $field = explode(' ',trim($rf))[0];
    if (isset($_GET[$field]) && $_GET[$field] !== '') {
    // echo "accession";
    $findCommand->addFindCriterion($rf, $_GET[$field]);
}
}


// if (isset($_GET['Genus']) && $_GET['Genus'] !== '') {
//     // echo "genus";
//     $findCommand->addFindCriterion('Genus', $_GET['Genus']);
// }
// if (isset($_GET['Species']) && $_GET['Species'] !== '') {
//     // echo "species";
//     $findCommand->addFindCriterion('Species', $_GET['Species']);
// }
// if (isset($_GET['Location']) && $_GET['Location'] !== '') {
//     // echo "location";
//     $findCommand->addFindCriterion('Location', $_GET['Location']);
// }

$result = $findCommand->execute();

if(FileMaker::isError($result)) {
    $findAllRec = [];
} else {
    $findAllRec = $result->getRecords();
}

?>