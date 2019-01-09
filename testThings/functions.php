<?php 
require_once ('FileMaker.php');
require_once ('db.php');

$fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

$layouts = $fm->listLayouts();
$layout = "";
foreach ($layouts as $l) {
    if (strpos($l, 'search') !== false) {
        $layout = $l;
      }
}

$fmLayout = $fm->getLayout($layout);
$layoutFields = $fmLayout->listFields();

// $findCommand = $fm->newFindCommand($layout);
// $result = $findCommand->execute();
// $findAllRec = $result->getRecords();
// $recFields = $result->getFields();

if (FileMaker::isError($layouts)) {
    echo $layouts;
}

// CompoundFind on all inputs with values
$findCommand = $fm->newFindCommand($layout);

foreach ($layoutFields as $rf) {
    $field = explode(' ',trim($rf))[0];
    if (isset($_GET[$field]) && $_GET[$field] !== '') {
    // echo "accession";
    $findCommand->addFindCriterion($rf, $_GET[$field]);
}
}

$result = $findCommand->execute();

if(FileMaker::isError($result)) {
    $findAllRec = [];
} else {
    $findAllRec = $result->getRecords();
}

?>