<?php

use airmoi\FileMaker\FileMakerException;
use airmoi\FileMaker\Object\Field;

require_once('utilities.php');
require_once ('DatabaseSearch.php');
require_once ('credentials_controller.php');
require_once ('TableData.php');
require_once ('TableRow.php');

session_set_cookie_params(0,'/','.ubc.ca',isset($_SERVER["HTTPS"]), true);
session_start();

define("DATABASE", $_GET['Database'] ?? null);

checkDatabaseField(DATABASE);

try {
    $databaseSearch = DatabaseSearch::fromDatabaseName(DATABASE);
} catch (FileMakerException $e) {
    $_SESSION['error'] = 'Unsupported database given';
    header('Location: error.php');
    exit;
}

# get the fields for the search and result layout
$searchLayoutFieldNames = $databaseSearch->getSearchLayout()->listFields();
$resultLayoutFieldNames = $databaseSearch->getResultLayout()->listFields();

$searchLayoutFields = $databaseSearch->getSearchLayout()->getFields();

$maxResponses = 30;

# remove any empty get fields
$usefulGETFields = array_filter($_GET);

if ($_GET['taxon-search'] ?? null) {
    try {
        $result = $databaseSearch->queryTaxonSearch($_GET['taxon-search'], $maxResponses, $_GET['Page'] ?? 1);
    } catch (FileMakerException $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: error.php');
        exit;
    }
} else {
    # since we are diffing by keys, we need to set dummy values
    $unUsedGETFields = ['operator' => '', 'Sort' => '', 'Page' => '', 'SortOrder' => '', 'Database' => ''];
    $usefulGETFields = array_diff_key($usefulGETFields, $unUsedGETFields);

    try {
        $result = $databaseSearch->queryForResults($maxResponses, $usefulGETFields, $_GET['operator'] ?? 'and',
            $_GET['Sort'] ?? null, $_GET['Page'] ?? 1, $_GET['SortOrder'] ?? null);
    } catch (FileMakerException $e) {
        $_SESSION['error'] = $e->getMessage();
        header('Location: error.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
            require_once('partials/widgets.php');
            HeaderWidget('Search Table');
            require_once('partials/conditionalCSS.php');
        ?>
    </head>

    <body>

        <!-- navbar -->
        <?php Navbar(); ?>

        <!-- Page title below navbar -->
        <?php TitleBannerRender(database: DATABASE, recordNumber: $result->getFoundSetCount()); ?>

        <!-- main body with table and its widgets -->
        <div class="container-fluid flex-grow-1">

            <!-- menu buttons for render table -->
            <div class="d-flex flex-wrap flex-row justify-content-evenly align-items-center py-2 px-1 p-md-4 gap-4">
                <!-- review search parameters -->
                <button type="button" data-bs-toggle="collapse" data-bs-target="#advancedSearchDiv"
                        class="btn btn-outline-secondary conditional-outline-background"
                        >Review Search Parameters</button>

                <!-- edit table columns -->
                <button type="button" data-bs-toggle="collapse" data-bs-target="#tableColumnFilterDiv"
                        class="btn btn-outline-secondary conditional-outline-background">Hide/Show Columns</button>

                <!-- enable/disable images -->
                <div class="btn-group">
                    <span class="input-group-text">View Images:</span>
                    <input type="radio" name="viewImages" id="noImages"
                           class="btn-check radio-conditional-background" value="no" checked>
                    <label for="noImages" class="btn btn-outline-secondary">No</label>

                    <input type="radio" name="viewImages" id="yesImage"
                           class="btn-check radio-conditional-background" value="yes">
                    <label for="yesImage" class="btn btn-outline-secondary">Yes</label>
                </div>

                <!-- download data -->
                    <a href="#" type="button"
                       class="btn btn-outline-secondary conditional-outline-background">Download Data</a>

                <!-- start a new search -->
                <a href="search.php?Database=<?=DATABASE?>" type="button"
                   class="btn btn-outline-secondary conditional-outline-background">New Search</a>
            </div>

            <!-- edit advanced search collapsible -->
            <div class="collapse w-100" id="advancedSearchDiv">
                <div class="d-flex justify-content-around align-items-center px-5 py-3">
                    <form action="render.php" method="get" id="submit-form">
                        <!-- hidden text field containing the database name -->
                        <label>
                            <input type="text" hidden id="Database" name="Database" value=<?php echo htmlspecialchars(DATABASE); ?>>
                        </label>
                        <!--
                            form elements,
                            using flex and media queries, we have one, two or three columns
                            refer to the view css to media queries, we followed bootstrap cutoffs
                         -->
                        <div class="d-flex flex-column flex-md-row flex-md-wrap justify-content-center align-items-start align-items-md-end">
                            <?php
                            # Loop over all fields and create a field element in the form for each!
                            $count = 0;
                            /** @var string $fieldName
                              * @var Field $field */
                            foreach ($searchLayoutFields as $fieldName => $field) : ?>

                                <div class="px-3 py-2 py-md-1 flex-fill responsive-columns">
                                    <!-- field name and input -->
                                    <div class="input-group">
                                        <a data-bs-toggle="collapse" href="#collapsable<?php echo $count?>" role="button">
                                            <label class="input-group-text conditional-background-light"
                                                   for="field-<?php echo htmlspecialchars($fieldName)?>">
                                                <?php echo htmlspecialchars(formatField($fieldName)) ?>
                                            </label>
                                        </a>
                                        <?php
                                        # Try to get a list of options, if error (aka none available) then no datalist
                                        try {
                                            $fieldValues = $field->getValueList();
                                        } catch (FileMakerException $e) { /* Do nothing */ }

                                        if (isset($fieldValues)) : ?>
                                            <input class="form-control" list="datalistOptions"
                                                   placeholder="Type to search" id="field-<?php echo htmlspecialchars($fieldName)?>"
                                                   name="<?php echo htmlspecialchars($fieldName)?>">
                                            <datalist id="datalistOptions">
                                                <?php foreach ($fieldValues as $fieldValue): ?>
                                                    <option value="<?=$fieldValue?>"></option>
                                                <?php endforeach; ?>
                                            </datalist>
                                        <?php else:
                                            $value = array_key_exists($fieldName, $_GET) ? $_GET[$fieldName] : null;
                                            ?>
                                            <input class="form-control" type="<?php echo $field->getResult() ?>"
                                                   id="field-<?php echo htmlspecialchars($fieldName)?>"
                                                   name="<?php echo htmlspecialchars($fieldName)?>"
                                                   value="<?=$value?>">
                                        <?php endif; ?>
                                    </div>
                                    <!-- field information -->
                                    <div class="collapse" id="collapsable<?php echo $count?>">
                                        <div class="card card-body">
                                            This is some information for field <?=$fieldName?>!
                                        </div>
                                    </div>
                                </div>
                                <?php $count++; endforeach; ?>
                        </div>

                        <!-- search ops and submit button -->
                        <div class="d-inline-flex justify-content-evenly align-items-center py-4 w-100">

                            <!-- radio inputs have same name, so that only one can be enabled, and is used in render.php -->
                            <div class="btn-group">
                                <span class="input-group-text"> Search with: </span>
                                <!-- we go with checked if operator does not exist since that's the default -->
                                <input type="radio" class="btn-check radio-conditional-background"
                                       name="operator" id="and" value="and"
                                       <?php echo array_key_exists('operator', $_GET) ? $_GET['operator'] == 'and' ? 'checked' : '' : 'checked' ?>
                                       autocomplete="off">
                                <label class="btn btn-outline-secondary" for="and"> AND </label>

                                <input type="radio" class="btn-check radio-conditional-background"
                                       name="operator" id="or" value="or"
                                       <?php echo array_key_exists('operator', $_GET) ? $_GET['operator'] == 'or' ? 'checked' : '' : '' ?>
                                       autocomplete="off">
                                <label class="btn btn-outline-secondary" for="or"> OR </label>
                            </div>

                            <!-- only with image select, tooltip to explain why disabled -->
                            <div class="form-check form-switch" <?php if (!in_array(DATABASE, kDATABASES_WITH_IMAGES)) echo 'data-bs-toggle="tooltip" title="No images available"' ?>>
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input checkbox-conditional-background"
                                           <?php if (!in_array(DATABASE, kDATABASES_WITH_IMAGES)) echo 'disabled' ?>
                                           <?php echo array_key_exists('hasImage', $_GET) ? $_GET['hasImage'] == 'on' ? 'checked' : '' : '' ?>
                                           name="hasImage">
                                    Only show records that contain an image
                                </label>
                            </div>

                            <!-- submit button -->
                            <div class="form-group">
                                <button type="submit" onclick="submitForm()" class="btn btn-outline-primary conditional-background"> Advanced Search </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- edit table columns -->
            <div class="collapse w-100" id="tableColumnFilterDiv">
                <div class="d-flex flex-wrap flex-row justify-content-around px-5 py-3 gap-3">
                    <?php foreach ($resultLayoutFieldNames as $fieldName): ?>
                        <div class="btn-group me-auto">
                            <span class="input-group-text"><?=htmlspecialchars(formatField($fieldName))?></span>
                            <input type="radio" name="view<?=htmlspecialchars(formatField($fieldName))?>" id="show<?=htmlspecialchars(formatField($fieldName))?>"
                                   class="btn-check radio-conditional-background" value="show" checked>
                            <label for="show<?=htmlspecialchars(formatField($fieldName))?>" class="btn btn-outline-secondary">Show</label>

                            <input type="radio" name="view<?=htmlspecialchars(formatField($fieldName))?>" id="hide<?=htmlspecialchars(formatField($fieldName))?>"
                                   class="btn-check radio-conditional-background" value="Hide">
                            <label for="hide<?=htmlspecialchars(formatField($fieldName))?>" class="btn btn-outline-secondary">Hide</label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php $tableData = new TableData($result, $databaseSearch->getResultLayout()->listFields()) ?>

            <!-- render table with data -->
            <div class="table-responsive">
                <!-- id used to js -->
                <table class="table table-hover table-striped" id="table">
                    <thead>
                        <tr>
                            <?php foreach ($tableData->getTableHeads(page: $_GET['Page'] ?? 1, databaseName: DATABASE, requestUri: $_SERVER['REQUEST_URI']) as $id => $href): ?>
                                <th scope="col" id="<?= $id ?>" class="text-center">
                                    <a href="<?= $href ?>" class="table-col-header conditional-text-color" role="button">
                                        <!-- field name -->
                                        <b><?= $id ?></b>
                                    </a>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tableData->getTableRows(DATABASE) as $tableRow): ?>
                            <tr class="conditional-hover-background">
                                <!-- row header with link to go to specimen page -->
                                <th scope="row">
                                    <a href="details.php?Database=<?=$tableRow->getUrl()?>" role="button" class="conditional-text-color">
                                        <?php if ($tableRow->isHasImage()): ?>
                                            <span class="oi oi-image"></span>
                                        <?php endif; ?>
                                        <b><?= $tableRow->getId() ?></b>
                                    </a>
                                </th>

                                <!-- all other row columns with data -->
                                <?php foreach ($tableRow->getFields() as $field): ?>
                                    <td class="text-center" id="data"><?= $field ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- table controller -->
            <div class="p-3">
                <?php TableControllerWidget($maxResponses, $result); ?>
            </div>
        </div>

        <?php FooterWidget(imgSrc: 'public/images/beatyLogo.png'); ?>

        <!-- scripts -->
        <script type="text/javascript" src="public/js/advanced-search.js"></script>
        <script type="text/javascript" src="public/js/hide-show-columns.js"></script>
    </body>
</html>