<?php

use airmoi\FileMaker\FileMakerException;
use airmoi\FileMaker\Object\Record;
require_once ('constants.php');

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
 * Maps the database field to a more readable field for the web app to use.
 * @param string $field
 * @return string
 */
function mapField(string $field): string
{
    return match (strtolower($field)) {
        'accession no', 'catalognumber', 'accessionno', 'id' => 'Accession Number',
        'sem #' => 'SEM Number',
        'nomennoun' => 'Genus',
        'specificepithet' => 'Species',
        'sub sp.' => 'Subspecies',
        'infraspecificepithet' => 'Infraspecies',
        'taxonrank' => 'Taxon Rank',
        'provincestate', 'stateprovince', 'prov/st' => 'Province or State',
        'location 1', 'verbatimlocality', 'location' => 'Locality',
        'verbatimelevation' => 'Elevation',
        'verbatimdepth', 'depth below water' => 'Depth',
        'geo_longdecimal', 'decimallongitude', 'longitudedecimal' => 'Longitude',
        'geo_latdecimal', 'decimallatitude', 'latitudedecimal' => 'Latitude',
        'date collected', 'collection date 1', 'verbatimeventdate', 'eventdate' => 'Collection Date',
        'year 1' => 'Year',
        'month 1' => 'Month',
        'day 1' => 'Day',
        'identifiedby' => 'Identified By',
        'typestatus' => 'Type Status',
        'comments', 'occurrenceremarks', 'fieldnotes' => 'Field Notes',
        'samplingprotocol' => 'Capture Method',
        'recordnumber' => 'Collection Number',
        'previousidentifications' => 'Prev. Identifications',
        'det by' => 'Determined By',
        'mushroomobserver' => 'Mushroom Observer',
        'citations', 'associatedreferences' => 'Associated References',
        'associatedsequences' => 'Associated Sequences',
        'reproductivecondition' => 'Reproductive Condition',
        'organismremark' => 'Organism Remark',
        'vernacularname' => 'Vernacular Name',
        'recordedby', 'collected by' => 'Collector',
        'photofilename', 'iifrno', 'imaged' => 'Has Image',
        default => ucwords($field),
    };
  }

/**
 * With databases using different field naming conventions, some databases
 * use the following format, Taxon::family or Event::year. This function will remove
 * everything before the second semicolon including it, if it exists.
 * It will also map the field using mapField.
 * @param string $field
 * @return string Taxon::family->Family
 */
function formatField(string $field): string
{
    $colonPosition = strrpos($field, ":");
    if ($colonPosition) {
        $field = substr($field, $colonPosition + 1);
    }
    return mapField($field);
}

/**
 * Special entomology function to create the records genus page for their website.
 * @param Record $record
 * @return string
 * @throws FileMakerException
 */
function getGenusPage(Record $record): string
{
    $order = $record->getField('Order');
    $family = $record->getField('Family');
    return 'https://www.zoology.ubc.ca/entomology/main/'.$order.'/'.$family.'/';
}

/**
 * Special entomology function to create the genus and specie name.
 * @param Record $record
 * @return string
 * @throws FileMakerException
 */
function getGenusSpecies(Record $record): string
{
    $genus = $record->getField('Genus');
    $species = $record->getField('Species');
    return $genus . ' ' . $species;
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

