<?php

use airmoi\FileMaker\FileMakerException;
use airmoi\FileMaker\Object\Field;

require_once ('utilities.php');
require_once ('constants.php');
require_once ('my_autoloader.php');

session_set_cookie_params(0,'/','.ubc.ca',isset($_SERVER["HTTPS"]), true);
session_start();

define("DATABASE", $_GET['Database'] ?? null);

checkDatabaseField(DATABASE);

if (isset($_SESSION['databaseSearch']) and ($_SESSION['databaseSearch'])->getName() == DATABASE) {
    $databaseSearch = $_SESSION['databaseSearch'];
} else {
    try {
        $databaseSearch = DatabaseSearch::fromDatabaseName(DATABASE);
        $_SESSION['databaseSearch'] = $databaseSearch;
    } catch (FileMakerException $e) {
        $_SESSION['error'] = 'Unsupported database given';
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
            HeaderWidget('Search');
            require_once('partials/conditionalCSS.php');
        ?>
    </head>

    <body>
        <?php Navbar(); ?>

        <!-- Page title below navbar -->
        <?php TitleBannerSearch(database: DATABASE, paddingIndex: 3); ?>

        <div class="container-fluid flex-grow-1">
            <!-- search or show all -->
            <div class="d-flex flex-wrap flex-column flex-md-row justify-content-evenly align-items-center px-1 py-4">
                <!-- search or advanced search -->
                    <div class="flex-grow-1 px-sm-5 mb-4 mb-md-0" style="max-width: 75%">
                        <!-- small form for taxon search -->
                        <form action="render.php" method="get" id="taxon-search" class="d-inline">
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary order-1 order-md-0 conditional-outline-background" data-bs-toggle="collapse" data-bs-target="#advancedSearchDiv">Advanced Search</button>
                                <input type="text" class="form-control form-control-lg order-0 order-md-1" style="min-width: 225px" placeholder="Start a taxon search" name="taxon-search">
                                <button type="submit" class="btn btn-outline-primary conditional-background order-2 flex-grow-1 flex-md-grow-0"> Search </button>
                                <!-- hidden text field containing the database name -->
                                <input type="text" hidden id="Database" name="Database" value=<?php echo htmlspecialchars(DATABASE); ?>>
                            </div>
                            <div class="form-text">You can search for phylum, class, order, family, etc... </div>
                        </form>
                    </div>

                <!-- show all button, add mb-4 to align button to search bar -->
                <div class="mb-4">
                    <form action="render.php" method="get">
                        <input type="text" hidden id="Database" name="Database" value=<?php echo htmlspecialchars(DATABASE); ?>>
                        <button id="form" type="submit" value="submit" class="btn btn-primary btn-lg conditional-background">Show All Records</button>
                    </form>
                </div>
            </div>

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
                            foreach ($databaseSearch->getSearchFields() as $fieldName => $field) : ?>

                                <div class="px-3 py-2 py-md-1 flex-fill responsive-columns-3">
                                    <!-- field name and input -->
                                    <div class="input-group">
                                        <!-- field name with a to open collapsed info -->
                                        <a data-bs-toggle="collapse" href="#collapsable<?php echo $count?>" role="button">
                                            <label class="input-group-text conditional-background-light"
                                                   for="field-<?php echo htmlspecialchars($fieldName)?>">
                                                <?php echo htmlspecialchars(Specimen::FormatFieldName($fieldName)) ?>
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
                                        <?php else: ?>
                                            <input class="form-control" type="<?php echo $field->getResult() ?>"
                                                   id="field-<?php echo htmlspecialchars($fieldName)?>"
                                                   name="<?php echo htmlspecialchars($fieldName)?>">
                                        <?php endif; ?>
                                    </div>
                                    <!-- field information -->
                                    <div class="collapse" id="collapsable<?=$count?>">
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
                                <input type="radio" class="btn-check radio-conditional-background" name="operator" id="and" value="and" checked autocomplete="off">
                                <label class="btn btn-outline-secondary" for="and"> AND </label>

                                <input type="radio" class="btn-check radio-conditional-background" name="operator" id="or" value="or" autocomplete="off">
                                <label class="btn btn-outline-secondary" for="or"> OR </label>
                            </div>

                            <!-- only with image select, tooltip to explain why disabled -->
                            <div class="form-check form-switch" <?php if (!in_array(DATABASE, kDATABASES_WITH_IMAGES)) echo 'data-bs-toggle="tooltip" title="No images available"' ?>>
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input checkbox-conditional-background" name="hasImage" <?php if (!in_array(DATABASE, kDATABASES_WITH_IMAGES)) echo 'disabled' ?>>
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
        </div>

        <!-- footer -->
        <?php FooterWidget(imgSrc: 'public/images/beatyLogo.png'); ?>

        <!-- Script to enable tooltips -->
        <script>
            let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        </script>
        <!-- scripts for advanced search section -->
        <script type="text/javascript" src="public/js/advanced-search.js"></script>
    </body>
</html>
