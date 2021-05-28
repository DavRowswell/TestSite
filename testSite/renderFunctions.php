<?php

use JetBrains\PhpStorm\Pure;

function generateTable($sd, $numRes) {
    //require_once ('functions.php');
    $fm = $sd->getFM();
    $resultLayout = $sd->getResultLayout();
    $fmResultLayout = $fm->getLayout($resultLayout);
    $findCommand = $fm->newFindCommand($resultLayout);
    $layoutFields = $fmResultLayout->listFields();
    $database = $sd->getDatabase();


    echo '<button class="collapsible">';
    echo '<h1><b>';

    if($database === "mi" || $database === "miw") {
        if($database === "mi"){ echo "Dry Marine Invertebrate"; }
        else{ echo "Wet Marine Invertebrate"; }
    } else {
        echo ucfirst($database);
    }
    echo 'Results</b></h1>';
    echo '</button>';
    echo '<div class="content">';
    foreach(array_keys($_GET) as $field) {
        if (!addFindCriterionIfSet($field, $layoutFields, $findCommand)) {
            echo 'No records found.<br>';
            echo '</div>';
            return;
        }
    }

    $findCommand->setRange(0, $numRes);
    $result = $findCommand->execute();
    setResultPageRange($findCommand, $numRes, $database);
    $result = $findCommand->execute();
    if (FileMaker::isError($result)) {
        echo $result->getMessage();
        return;
    }
    $findAllRec = $result->getRecords();
    require ('partials/allPageController.php');
    printTable($database, $findAllRec, $fmResultLayout);
    require ('partials/allPageController.php');
    echo '</div>';
}

/**
 * Helper for generateTable()
 */
function setResultPageRange($findCommand, $numRes, $database) {
    if (isset($_GET[$database.'Page']) && $_GET[$database.'Page'] !== '') {
        $numPages = $_GET[$database.'Page'];
        $findCommand->setRange(($numPages-1) * $numRes, $numRes);
    } else {
        $findCommand->setRange(0, $numRes);
    }
}
/**
 * Helper for generateTable()
 */
function printTable($database, $findAllRec, $resultLayout) {
    $recFields = $resultLayout->listFields();


    echo '<div class="row">';
    echo '</div>';
    echo '<table class="table table-hover table-striped table-condensed tasks-table" style="position:relative; top:16px">';
    echo '<thead>';
    echo '<tr>';

    foreach($recFields as $i){
        if ($i === 'SortNum' || $i === 'Accession Numerical'  || $i === 'Photographs::photoFileName'){ continue; }

        echo '<th id = '.htmlspecialchars(formatField($i)).'scope="col">';
        echo '<b>'.htmlspecialchars(formatField($i));
        echo '</th>';
    }
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    foreach($findAllRec as $i){
        echo '<tr>';
        foreach($recFields as $j){
            if ($j === 'SortNum' || $j === 'Accession Numerical'  || $j === 'Photographs::photoFileName'  ){ continue; }
            if(formatField($j) == 'Accession Number' || $j === 'SEM #'){
                echo '<td id="data">';
                echo '<a style="padding: 0px;" href="details.php?Database='.htmlspecialchars($database).
                    '&AccessionNo='.htmlspecialchars($i->getField($j)).'">';
                $photoExists = $i->getField("Photographs::photoFileName");
                if (($database === 'mammal' || $database === 'avian' || $database === 'herpetology') &&  $photoExists !== "") {
                    echo '<div class="row">';
                    echo '<div class="col"><b>'. htmlspecialchars(trim($i->getField($j))).'</b></div>';
                    echo '<div class="col"> <span style="display:inline" id = "icon"  class="fas fa-image"></span></div>';
                    echo '</div>';
                } else {
                    echo '<b>'.htmlspecialchars(trim($i->getField($j))).'</b>';
                }
                echo '</a>';
                echo '</td>';
            } else if (formatField($j) == 'Genus' || formatField($j) == 'Species') {
                echo '<td id="data" style="font-style:italic;">'. htmlspecialchars($i->getField($j)).'</td>';
            } else {
                echo '<td id="data">'. htmlspecialchars($i->getField($j)).'</td>';
            }
        }
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}
/**
 * Helper for generateTable()
 */
function addFindCriterionIfSet($field, $layoutFields, $findCommand): bool {
    if (fieldIsSet($field, $layoutFields)) {
        addFindCommand($field, $layoutFields, $findCommand);
        return true;
    } else {
        return false;
    }
}

/**
 * Helper for addFindCriterionIfSet()
 */
#[Pure] function fieldIsSet($field, $layoutFields): bool {
    foreach ($layoutFields as $lf) {
        if (formatField($lf) === "Phylum") {
            return true;
        }
    }
    return false;
}
/**
 * Helper for addFindCriterionIfSet()
 */
function addFindCommand($field, $layoutFields, $findCommand) {
    foreach ($layoutFields as $lf) {
        if ($field === formatField($lf)) {
            $findCommand->addFindCriterion($lf, $_GET[$field]);
        }
    }
}


/**
 * A widget like function, will echo back a table for the given records and fields.
 *
 * @param string $database
 * @param FileMaker_Record[] $allRecords
 * @param string[] $recordFields
 */
function echoDataTable(string $database, array $allRecords, array $recordFields) {

    # filter out unnecessary fields
    $ignoredFields = ['SortNum', 'Accession Numerical', 'Imaged', 'IIFRNo', 'Photographs::photoFileName',
        'Event::eventDate', 'card01', 'Has Image', 'imaged'];
    $usefulFields = array_diff($recordFields, $ignoredFields);

    # echos the table heads for a list of elements
    # helper function
    function echoTableHeads($fields) {
        $page = $_GET['Page'] ?? 1;

        foreach($fields as $field) {
            $id = htmlspecialchars(formatField($field));

            $payloadList = [
                'Database' => $_GET['Database'],
                'Sort' => $field,
                'SortOrder' => $_GET['SortOrder'] ?? 'Descend' == 'Descend' ? 'Ascend' : 'Descend',
                'Page' => $page,
            ];

            $href = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?')) . '?' . http_build_query($payloadList);
            $href = str_replace('%3A', ':', $href);

            $icon_class = $_GET['SortOrder'] ?? '' === 'Descend' ? 'oi-sort-descending' : 'oi-sort-ascending';

            echo "
                <th scope='col' id=$id>
                    <a style='padding: 0; white-space:nowrap;' href=$href>
                        <!-- order icon -->
                        <span id='icon'  class='oi $icon_class'></span>
                        <!-- field name -->
                        <b>$id</b>
                    </a>
                </th>
            ";
        }
    }

    # echos the table rows
    # helper function
    function echoTableRows($records, $fields, $database) {
        foreach ($records as $record) {
            # each record has its table row
            echo '<tr>';
            foreach ($fields as $field) {
                # ID field logic
                if (formatField($field) === 'Accession Number' or $field === 'SEM #') {
                    $url = htmlspecialchars($database). '&AccessionNo='.htmlspecialchars($record->getField($field));
                    $id = htmlspecialchars(trim($record->getField($field)));

                    $hasImage = false;
                    if($database === 'entomology' and $record->getField("Imaged") === "Photographed") $hasImage = true;
                    else if ($database === 'fish' and $record->getField("imaged") === "Yes") $hasImage = true;
                    else if ($database === 'mammal' or $database === 'avian' or $database === 'herpetology') {
                        if ($record->getField("Photographs::photoFileName") !== "") $hasImage = true;
                    }
                    # for vwsp lichen bryophytes fungi algae
                    else if ($record->getField("Imaged") === "Yes") $hasImage = true;

                    echo "
                        <th scope='row' id='data'>
                            <a href='details.php?Database=$url'>
                            " . ($hasImage ? '<span style="display:inline" id = "icon"  class="oi oi-image"></span>' : '') . "
                            <b>$id</b>
                        </th>
                    ";
                }
                # genus or species field for special style
                else if (formatField($field) == 'Genus' || formatField($field) == 'Species') {
                    echo '<td id="data" style="font-style:italic;">' . htmlspecialchars($record->getField($field)) . '</td>';
                }
                else {
                    echo '<td id="data">'. $record->getField($field) . '</td>';
                }
            }
            echo '</tr>';
        }
    }

    # basic table setup with helper functions
    echo '
         <!-- construct table for given layout and fields -->
        <div class="container-fluid no-padding">
            <!-- id table for special color -->
            <table class="table table-hover table-striped table-responsive" id="table">
                <thead>
                    <tr>
                        ';
    echoTableHeads($usefulFields);
    echo '
                    </tr>
                </thead>
                <tbody>
                    ';
    echoTableRows($allRecords, $usefulFields, $database);
    echo '
                </tbody>
            </table>
        </div>
    ';
}