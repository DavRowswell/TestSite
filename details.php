<?php

use airmoi\FileMaker\FileMakerException;

require_once('utilities.php');
require_once ('credentials_controller.php');
require_once ('constants.php');
require_once ('DatabaseSearch.php');
require_once ('Specimen.php');

session_set_cookie_params(0,'/','.ubc.ca',isset($_SERVER["HTTPS"]), true);
session_start();

define('DATABASE', $_GET['Database'] ?? null);
define('ACCESSIONNUMBER', $_GET['AccessionNo'] ?? null);

checkDatabaseField(DATABASE);

try {
    $databaseSearch = DatabaseSearch::fromDatabaseName(DATABASE);
} catch (FileMakerException $e) {
    $_SESSION['error'] = 'Unsupported database given';
    header('Location: error.php');
    exit;
}

try {
    $specimen = new Specimen(ACCESSIONNUMBER, $databaseSearch);
} catch (ErrorException | FileMakerException $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: error.php');
    exit;
}

# kudos to https://stackoverflow.com/questions/2548566/go-back-to-previous-page/42143843
$previous = "javascript:history.go(-1)";
if(isset($_SERVER['HTTP_REFERER'])) {
    $previous = $_SERVER['HTTP_REFERER'];
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
            require_once ('partials/widgets.php');

            HeaderWidget('Specie Details');

            require_once('partials/conditionalCSS.php');
        ?>
    </head>

    <body>
        <?php Navbar(); ?>

        <?php TitleBannerDetail(DATABASE, ACCESSIONNUMBER, $previous); ?>

        <div class="container-fluid flex-grow-1">
            <!-- basic info plus images -->
            <div class="row px-1 py-3">
                <!-- information pane -->
                <div class="col-8 d-flex flex-column flex-md-row flex-md-wrap justify-content-center align-items-start align-items-md-end">
                    <?php
                    $count = 0;
                    foreach ($specimen->getFieldData() as $fieldName => $fieldValue): ?>
                        <div class="px-3 py-2 py-md-2 flex-fill responsive-columns-2">
                            <!-- field name and value -->
                            <div class="input-group">
                                <!-- field name with a to open collapsed info -->
                                <a data-bs-toggle="collapse" href="#collapsable<?php echo $count?>" role="button">
                                    <label class="input-group-text conditional-background-light"
                                           for="field-<?php echo htmlspecialchars($fieldName)?>">
                                        <?php echo htmlspecialchars(Specimen::FormatFieldName($fieldName)) ?>
                                    </label>
                                </a>

                                <!-- field value -->
                                <input class="form-control" type="text"
                                       id="field-<?php echo htmlspecialchars($fieldName)?>"
                                       name="<?php echo htmlspecialchars($fieldName)?>"
                                       readonly disabled value="<?php echo $fieldValue == '' ? '---' : $fieldValue ?>" >
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

                <!-- image slideshow -->
                <div class="col pe-3 ps-0">
                    <div class="rounded rounded-3 border border-3 conditional-background-light-no-hover-25 p-3">
                        <?php if (sizeof($specimen->getImages()) > 0): ?>
                            <h3 class="display-6 mb-0 conditional-color">Images:</h3>
                            <hr class="conditional-color mt-1">
                            <div id="imageCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-indicators">
                                    <!-- loop over each image to add a button -->
                                    <?php foreach ($specimen->getImages() as $index => $image): ?>
                                        <button type="button" data-bs-target="#imageCarousel" data-bs-slide-to="<?=$index?>" class="<?php if ($index == 0) echo 'active' ?>"></button>
                                    <?php endforeach; ?>
                                </div>
                                <div class="carousel-inner rounded rounded-2">
                                    <!-- loop over each image to add it as a carousel-item -->
                                    <?php foreach ($specimen->getImages() as $index => $image): ?>
                                        <div class="carousel-item <?php if ($index == 0) echo 'active' ?>">
                                            <a href="<?=$image->getHref()?>">
                                                <img src="<?=$image->getUrl()?>" class="d-block w-100" alt="<?=$image->getAlt()?>">
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <!-- back button -->
                                <button class="carousel-control-prev" type="button" data-bs-target="#imageCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <!-- forward button -->
                                <button class="carousel-control-next" type="button" data-bs-target="#imageCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>

                            <div class="form-text conditional-color px-3">
                                You can click on the images to see them in full screen and download them!
                            </div>

                            <?php if (DATABASE == 'entomology'): ?>
                                <div class='text-center px-2 py-3'>
                                    <!-- rel='noopener' for security reasons -->
                                    <a href="<?=getGenusPage($specimen->getRecord())?>" class='text-center' target='_blank' rel="noopener">
                                        <button class='btn conditional-background'> See more of <?=$specimen->getFieldData()['Family']?> here! </button>
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <h3 class="display-6 mb-0 conditional-color text-center">No images available for this specimen!</h3>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- location map and information -->
            <div class="row px-1 py-3">
                <!-- map -->
                <div class="col-5">
                    <div class="rounded rounded-3 border border-3 conditional-background-light-no-hover-25 p-3">
                        <?php if($specimen->getLatitude() !== null && $specimen->getLongitude() !== null) : ?>
                            <h3 class="display-6 mb-0 conditional-color">Map:</h3>
                            <hr class="conditional-color mt-1">
                            <div id="viewDiv" class="arcgis-map"></div>

                            <link rel="stylesheet" href="https://js.arcgis.com/4.19/esri/themes/light/main.css">
                            <script src="https://js.arcgis.com/4.19/"></script>

                            <input hidden id="map-latitude" value="<?=$specimen->getLatitude()?>">
                            <input hidden id="map-longitude" value="<?=$specimen->getLongitude()?>">
                            <script type="text/javascript" src="public/js/map.js"></script>

                        <?php else: ?>
                            <h3 class="display-6 mb-0 conditional-color text-center">No coordinates for this record!</h3>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- information pane -->
                <div class="col d-flex flex-column flex-md-row flex-md-wrap justify-content-center align-items-start align-items-md-end align-content-start">
                    <?php
                    foreach ($specimen->getLocationData() as $fieldName => $fieldValue): ?>
                        <div class="px-3 py-2 py-md-2 flex-fill responsive-columns-2">
                            <!-- field name and value -->
                            <div class="input-group">
                                <!-- field name with a to open collapsed info -->
                                <a data-bs-toggle="collapse" href="#collapsable<?php echo $count?>" role="button">
                                    <label class="input-group-text conditional-background-light"
                                           for="field-<?php echo htmlspecialchars($fieldName)?>">
                                        <?php echo htmlspecialchars(Specimen::FormatFieldName($fieldName)) ?>
                                    </label>
                                </a>

                                <!-- field value -->
                                <input class="form-control" type="text"
                                       id="field-<?php echo htmlspecialchars($fieldName)?>"
                                       name="<?php echo htmlspecialchars($fieldName)?>"
                                       readonly disabled value="<?php echo $fieldValue == '' ? '---' : $fieldValue ?>" >
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
            </div>
        </div>

        <?php FooterWidget('public/images/beatyLogo.png') ;?>

    </body>
</html>