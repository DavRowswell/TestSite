<?php 
require_once ('FileMaker.php');
require_once ('db.php');

$fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

$numRes = 50;
$layouts = $fm->listLayouts();
$layout = "";
foreach ($layouts as $l) {
    if (strpos($l, 'search') !== false) {
        $layout = $l;
    }
}

$fmLayout = $fm->getLayout($layout);
$layoutFields = $fmLayout->listFields();

if (FileMaker::isError($layouts)) {
    echo $layouts->message;
    exit;
}

// Find on all inputs with values
$findCommand = $fm->newFindCommand($layout);

foreach ($layoutFields as $rf) {
    $field = explode(' ',trim($rf))[0];
    if (isset($_GET[$field]) && $_GET[$field] !== '') {
        $findCommand->addFindCriterion($rf, $_GET[$field]);
    }
}

if (isset($_GET['Page'])) {
    $findCommand->setRange(($_GET['Page'] - 1) * $numRes, $numRes);
} else {
    $findCommand->setRange(0, $numRes);
}

$result = $findCommand->execute();

if(FileMaker::isError($result)) {
    $findAllRec = [];
} else {
    $findAllRec = $result->getRecords();
}

?>