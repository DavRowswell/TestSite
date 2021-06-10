<?php

use JetBrains\PhpStorm\Pure;

/**
 * These functions are all used for the all_render.php file. A feature we will not be working on for a while.
 */

/**
 * Generates the all_render collapsible and tables within those collapsables
 * @param DatabaseSearch $databaseSearch
 * @param int $numRes
 */
function generateTable(DatabaseSearch $databaseSearch, int $numRes) {

    $fm = $databaseSearch->getFileMaker();
    $resultLayout = $databaseSearch->getResultLayout();
    $findCommand = $fm->newFindCommand($resultLayout->getName());
    $layoutFields = $resultLayout->listFields();
    $database = $databaseSearch->getName();


    echo '<button class="collapsible">';
    echo '<h1><b>';

    if($database === "mi") {
        echo "Dry Marine Invertebrate";
    } else if ($database === "miw") {
        echo "Wet Marine Invertebrate";
    } else {
        echo ucfirst($database);
    }

    echo ' Results</b></h1>';
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
        setResultPageRange($findCommand, $numRes, $database);
        $result = $findCommand->execute();

        if (FileMaker::isError($result)) {
            echo $result->getMessage();
            return;
        }

        $findAllRec = $result->getRecords();
        require('partials/all_pageController.php');
        printTable($database, $findAllRec, $resultLayout);
        require('partials/all_pageController.php');
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