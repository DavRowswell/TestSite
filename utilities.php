<?php

use airmoi\FileMaker\FileMakerException;
use airmoi\FileMaker\Object\Record;
require_once ('constants.php');

/**
 * Maps the database name to a usable name.
 * @param $database
 * @return string
 */
function getDatabaseName($database): string
{
    return match ($database) {
        "mi" => "Dry Marine Invertebrate",
        "miw" => "Wet Marine Invertebrate",
        "vwsp" => "Vascular",
        default => ucfirst($database)
    };
}

/**
 * Will clean out a url from a variable using regex.
 * Kudos to https://stackoverflow.com/questions/1251582/beautiful-way-to-remove-get-variables-with-php
 * @param string $url full url to remove var from
 * @param string $varname url var name to remove
 * @return string
 */
function removeUrlVar(string $url, string $varname): string
{
    return preg_replace('/([?&])'.$varname.'=[^&]+(&|$)/','',$url);
}

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
    else if (!in_array($databaseFieldValue, kDATABASES)) {
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
    } catch (FileMakerException $e) {
        return '#';
    }
}

/**
 * Creates a url to point to the images. The url depends on the databse in use.
 * @param string $identifier usually the accession number or ID
 * @param string $database the database name
 * @return string|null url to image
 */
function getPhotoUrl(string $identifier, string $database): ?string
{
  if ($database === 'vwsp') {
    return "https://herbweb.botany.ubc.ca/herbarium/images/vwsp_images/Large_web/".$identifier.".jpg";
  }
  else if ($database === 'algae') {
    return "https://herbweb.botany.ubc.ca/herbarium/images/ubcalgae_images/Large_web/".$identifier.".jpg";
  }
  else if ($database === 'lichen') {
    return "https://herbweb.botany.ubc.ca/herbarium/images/lichen_images/Large_web/".$identifier.".jpg";
  }
  else if ($database === 'fungi') {
    return "https://herbweb.botany.ubc.ca/herbarium/images/fungi_images/Large_web/".$identifier.".jpg";
  }
  else if ($database === 'bryophytes') {
    return "https://herbweb.botany.ubc.ca/herbarium/images/bryophytes_images/Large_web/".$identifier.".jpg";
  }
  else if ($database === 'mammal') {
    return 'https://collections.zoology.ubc.ca/fmi/xml/cnt/data.JPG?-db=Mammal%20Research%20Collection&-lay=mammal_details&-recid='
    .htmlspecialchars($identifier).'&-field=Photographs::photoContainer(1)';
  }
  else if ($database === 'avian') {
    return 'https://collections.zoology.ubc.ca/fmi/xml/cnt/data.JPG?-db=Avian%20Research%20Collection&-lay=details-avian&-recid='
    .htmlspecialchars($identifier).'&-field=Photographs::photoContainer(1)';
  }
  else if ($database === 'herpetology') {
    return 'https://collections.zoology.ubc.ca/fmi/xml/cnt/data.JPG?-db=Herpetology%20Research%20Collection&-lay=herp_details&-recid='
    .htmlspecialchars($identifier).'&-field=Photographs::photoContainer(1)';
  } else {
      return null;
  }
}

