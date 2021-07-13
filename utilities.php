<?php

use airmoi\FileMaker\FileMakerException;
use airmoi\FileMaker\Object\Record;

/**
 * Checks the value to see if its a valid database.
 * With wrong databases value a error.php page is shown.
 * @param string|null $databaseFieldValue
 * @param bool $includeAll should the 'all' value be included as valid
 */
function checkDatabaseField(?string $databaseFieldValue, bool $includeAll = false) {
    # Check to make sure the database file is loaded or send to error.php
    if (!isset($databaseFieldValue) or $databaseFieldValue == ''){
        $_SESSION['error'] = "No database given";
        header('Location: error.php');
        exit;
    }
    # also check to make sure we dont have the 'all' database tag, if its not desired
    else if (!$includeAll and $databaseFieldValue == 'all') {
        $_SESSION['error'] = "Wrong page for database given";
        header('Location: error.php');
        exit;
    }
    # search in database list
    else if (!in_array($databaseFieldValue, DatabaseSearch::$ValidNames)) {
        $_SESSION['error'] = "Invalid database value given!";
        header('Location: error.php');
        exit;
    }
}

/**
 * Special entomology function to create the records genus page for their website.
 * @param Record $record
 * @return string
 */
function getGenusPage(Record $record): string
{
    try {
        $order = $record->getField('Order');
        $family = $record->getField('Family');
        return 'https://www.zoology.ubc.ca/entomology/main/'.$order.'/'.$family.'/';
    } catch (FileMakerException) {
        return '#';
    }
}
