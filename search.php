<?php

use airmoi\FileMaker\FileMakerException;

require_once ('credentials_controller.php');
require_once ('utilities.php');
require_once ('constants.php');
require_once ('DatabaseSearch.php');

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

$allFieldNames = array_keys($databaseSearch->getSearchLayout()->getFields());

# filter the layouts to those we only want
$ignoreValues = ['SortNum', 'Accession Numerical', 'Imaged', 'IIFRNo', 'Photographs::photoFileName', 'Event::eventDate', 'card01', 'Has Image', 'imaged'];

define("FIELDS", array_diff($allFieldNames, $ignoreValues));

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="https://herbweb.botany.ubc.ca/arcgis_js_api/library/4.10/esri/css/main.css">
        <?php
          require_once('partials/conditionalCSS.php');
          require_once('partials/widgets.php');

          HeaderWidget('Search');
        ?>
        <link rel="stylesheet" href="public/css/search.css">

        <!-- scripts -->
        <script type="text/javascript" src="public/js/process.js"></script>
    </head>

    <body class="container-fluid no-padding">
        <?php Navbar(); ?>

        <!-- Page title below navbar -->
        <?php TitleBanner(databaseName: DATABASE); ?>

        <div class="container-fluid">
            <form action="render.php" method="get" id="submit-form">
                <div class ="row">

                    <!-- form elements -->
                    <div id="form" class = "col-sm-6">
                        <!-- hidden text field containing the database name -->
                        <input type="text" hidden name="Database"
                               value=<?php echo htmlspecialchars(DATABASE); ?>>

                        <!-- submit button -->
                        <div class="form-group">
                            <input id="form" class="btn btn-custom" type="button" value="Submit" onclick="submitForm()">
                        </div>

                        <?php
                            list($layoutFields1, $layoutFields2) = array_chunk(FIELDS, ceil(count(FIELDS) / 2));
                            $count = 0;
                            foreach ($layoutFields1 as $layoutField) : ?>
                        <div class="row">
                            <!--- Section that is one label and one search box --->
                            <div class="col-sm-3">
                                <label for="field-<?php echo $layoutField?>">
                                    <?php echo htmlspecialchars(formatField($layoutField)) ?>
                                </label>
                            </div>

                            <div class="col-sm-3">
                                <input type="text" id="field-<?php echo $layoutField?>"
                                    <?php
                                    if (isset($_POST[str_replace(' ', '_', $layoutField)]))
                                      echo "value=".htmlspecialchars($_POST[str_replace(' ', '_', $layoutField)]);
                                    ?>
                                    name="<?php echo htmlspecialchars($layoutField) ?>"
                                    class="form-control"
                                >
                            </div>

                            <!--- End of a single label, input instance --->
                            <?php if($count < sizeof($layoutFields2)) : ?>

                            <!--- Section that is one label and one search box --->
                            <div class="col-sm-3">
                                <label for="field-<?php echo $layoutFields2[$count]?>">
                                    <?php echo htmlspecialchars(formatField($layoutFields2[$count])) ?>
                                </label>
                            </div>

                            <div class="col-sm-3">
                                <input type="text" id="field-<?php echo $layoutFields2[$count]?>"
                                    <?php
                                        if (isset($_POST[str_replace(' ', '_', $layoutFields2[$count])]))
                                          echo "value=".htmlspecialchars($_POST[str_replace(' ', '_', $layoutFields2[$count])]);
                                    ?>
                                    name="<?php echo htmlspecialchars($layoutFields2[$count]) ?>"
                                    class="form-control"
                                >
                            </div>

                            <!--- End of a single label, input instance --->
                            <?php endif; ?>

                        </div>
                        <?php $count++; endforeach; ?>
                    </div>

                    <!-- search ops, images, maps, etc -->
                    <div class="border col-sm-6 px-0">
                        <!-- special entomology title -->
                        <div>
                            <?php
                            if(DATABASE === 'entomology'){
                                echo '
                                    <div id="entoSite" class="row no-gutters">
                                        <div class="col-sm-12" style="background: url(public/images/entomologyBannerImages/rotator.php) no-repeat center center; background-size: 100% auto; text-align: center; color: white;">
                                            <div style ="margin-top:30px;margin-bottom:30px;">
                                                <a href="https://www.zoology.ubc.ca/entomology/" style="text-decoration: none; color: white;">
                                                    <p>Welcome to the</p><h3>SPENCER ENTOMOLOGICAL COLLECTION</h3>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                ';
                            }
                            ?>
                        </div>

                        <!--- start of accordion collapsible--->
                        <div class="panel-group" id="accordion">
                        <div class="panel">
                          <div class="panel-heading">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">
                            <h4 class="panel-title" > SEARCH OPERATORS
                            <span id="icon" class="oi oi-plus"> </span>
                           </h4></a>
                          </div>
                          <div id="collapse1" class="panel-collapse collapse in">
                            <div class="panel-body">
                              <div class="row">
                                <div class="col-sm-1"> == </div>
                                <div class="col-sm-11"> match entire field exactly </div>
                              </div>
                              <div class="row">
                                <div class="col-sm-1"> &lt </div>
                                <div class="col-sm-11"> find records with values less than to the one specified </div>
                              </div>
                              <div class="row">
                                <div class="col-sm-1"> &lt= </div>
                                <div class="col-sm-11">  find records with values less than or equal to the one specified </div>
                              </div>
                              <div class="row">
                                <div class="col-sm-1"> &gt </div>
                                <div class="col-sm-11">  find records with values greater than to the one specified </div>
                              </div>
                              <div class="row">
                                <div class="col-sm-1"> &gt= </div>
                                <div class="col-sm-11">  find records with values greater than or equal to the one specified </div>
                              </div>
                              <div class="row">
                                <div class="col-sm-1"> ... </div>
                                <div class="col-sm-11">  find records with values in a range (Ex. 10...20) </div>
                              </div>
                              <div class="row">
                                <div class="col-sm-1"> * </div>
                                <div class="col-sm-11">  match zero or more characters </div>
                              </div>
                              <div class="row">
                                <div class="col-sm-1"> \ </div>
                                <div class="col-sm-11">  escape any character </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        </div>

                        <!-- search operators -->
                        <div class="form-group">
                            <h4>Search By</h4>
                            <div class = "btn-group btn-group-toggle" data-toggle="buttons" >
                                <!-- AND -->
                                <label class = "btn btn-custom active" id="andLabel">
                                    <input type="radio"  id = "and" autocomplete="off" checked> AND
                                </label>

                                <!-- OR -->
                                <label class = "btn btn-custom" id="orLabel">
                                    <input type="radio" id="or" autocomplete="off" > OR
                                </label>
                            </div>
                        </div>

                        <!-- all records button -->
                        <div class="form-group">
                            <a href="render.php?Database=<?php echo htmlspecialchars(DATABASE)?>"
                               role="button" class="btn btn-custom">Show All Records</a>
                        </div>

                        <!-- only image select -->
                        <div class="form-group">
                            <?php if (in_array(DATABASE, kDATABASES_WITH_IMAGES)) : ?>
                                    <div class="col">
                                        <input type="checkbox" id="imageCheck">
                                        <label for="imageCheck">
                                            Only show records that contain an image
                                        </label>
                                    </div>

                                    <!-- Used to set data for the form with the Process() function in js/process.js TODO remove this -->
                                    <input type="hidden" name="hasImage" id="hasImage">
                            <?php endif; ?>
                            <!-- also used to set data for the form with the Process() TODO remove this -->
                            <input type="hidden" name="type" id="type">
                        </div>

                        <!-- example, shows a different example every time -->
                        <div>
                            <?php
                            if (in_array(DATABASE, kDATABASES_WITH_EXAMPLES)) :

                                $getSampleScript = $databaseSearch->getFileMaker()->newPerformScriptCommand('examples', 'Search Page Sample Selection');
                                $result = $getSampleScript->execute();
                                $record = $result->getRecords()[0];
                                $id = 'accessionNumber';
                                $lat = 'Geo_LatDecimal';
                                $lng = 'Geo_LongDecimal';
                                $genus = 'Genus';
                                $species = 'Species';
                                $url = '';

                                if (DATABASE == 'avian' || DATABASE == 'mammal') {
                                    //$url = getPhotoUrl($record->getRecordID());
                                    $url = "https://collections.zoology.ubc.ca".$record->getRelatedSet('Photographs')[0]->getField('Photographs::photoContainer');
                                    $id = 'catalogNumber';
                                    $lat = 'Geolocation::decimalLatitude';
                                    $lng = 'Geolocation::decimalLongitude';
                                    $genus = 'Taxon::genus';
                                    $species = 'Taxon::specificEpithet';
                                }
                                else if (DATABASE == 'vwsp' || DATABASE == 'bryophytes' || DATABASE == 'fungi'
                                    || DATABASE == 'lichen' || DATABASE == 'algae') {
                                    $url = getPhotoUrl($record->getField('Accession Number'), DATABASE);
                                    $id = 'Accession Number';
                                    $lat = 'Geo_LatDecimal';
                                    $lng = 'Geo_LongDecimal';
                                    $genus = 'Genus';
                                    $species = 'Species';
                                }
                                else if (DATABASE == 'entomology') {
                                    $id = 'SEM #';
                                    $lat = 'Latitude';
                                    $lng = 'Longitude';
                                    $genus = 'Genus';
                                    $species = 'Species';

                                }
                                else if (DATABASE == 'fish') {
                                    $id = 'accessionNo';
                                    $lat = 'decimalLatitude';
                                    $lng = 'decimalLongitude';
                                    $genus = 'nomenNoun';
                                    $species = 'specificEpithet';
                                }

                                ?>
                                <div class = "jumbotron jumbotron-fluid" id = "jumbotron">
                                    <div class="container-fluid">
                                        <div class = "row sample">
                                            <div class = "col d-flex justify-content-center">
                                                <a id = "catalogNumber" href  =  "details.php?Database=<?php echo htmlspecialchars(DATABASE).
                                                    '&AccessionNo='.htmlspecialchars($record->getField($id)) ?>">
                                                    <h4><b><?php echo $record->getField($id)?></b></h4></a>
                                            </div>
                                        </div>
                                        <div class = "row">
                                            <div class = "col d-flex justify-content-center" id = "taxon">
                                                <a id = "taxonInfo" href = "render.php?Database=<?php echo htmlspecialchars(DATABASE).
                                                    '&'.$genus.'='.htmlspecialchars($record->getField($genus)).
                                                    '&'.$species.'='.htmlspecialchars($record->getField($species)) ?>">
                                                    <h5><?php echo $record->getField($genus).' '.$record->getField($species);?></h5>
                                                </a>
                                            </div>
                                        </div>
                                        <div class = "row">
                                            <div id = "sample-img" class = "col-xl-6 d-flex justify-content-center">
                                                <?php
                                                if (DATABASE == 'entomology') {
                                                    try {
                                                        $genusPage = getGenusPage($record);
                                                    } catch (FileMakerException $e) {
                                                        $_SESSION['error'] = $e->getMessage();
                                                        header('Location: error.php');
                                                        exit;
                                                    }
                                                    try {
                                                        $genusSpecies = getGenusSpecies($record);
                                                    } catch (FileMakerException $e) {
                                                        $_SESSION['error'] = $e->getMessage();
                                                        header('Location: error.php');
                                                        exit;
                                                    }
                                                    $semnumber = $record->getField('SEM #');
                                                    $foundImage = false;
                                                }
                                                else if (DATABASE == 'fish') {
                                                    $url = 'https://open.library.ubc.ca/media/download/jpg/fisheries/'.$record->getField("card01").'/0';
                                                    $linkToWebsite = 'https://open.library.ubc.ca/collections/fisheries/items/'.$record->getField("card01");
                                                    echo '<a href ='. htmlspecialchars($linkToWebsite).' target="_blank" rel="noopener noreferrer">'.'<img id="fish-sample" class="minHeight" src="'.htmlspecialchars($url) .'" alt="Sample Image"></a>';
                                                }
                                                else {
                                                    echo '<a href ='. $url.' target="_blank" rel="noopener noreferrer">'.'<img id="sample" class="minHeight" src="'.$url .'" alt="Sample Image"></a>';
                                                }
                                                echo '<div hidden = true id = "Latitude">'. $record->getField($lat).'</div>';
                                                echo '<div hidden = true id = "Longitude">'. $record->getField($lng).'</div>';
                                                ?>
                                            </div>
                                            <div id = "sample-map" class = "col-xl-6 d-flex justify-content-center">
                                                <div id="viewDiv"></div>
                                                <script src="https://herbweb.botany.ubc.ca/arcgis_js_api/library/4.10/dojo/dojo.js"></script>
                                                <script src="public/js/map.js"></script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </form>
        </div>

        <!-- footer -->
        <?php FooterWidget(imgSrc: 'public/images/beatyLogo.png'); ?>
    </body>
</html>
