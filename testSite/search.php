<?php
    session_set_cookie_params(0,'/','.ubc.ca',isset($_SERVER["HTTPS"]), true);
    session_start();

    $_SESSION['error'] = "";

    # Check to make sure the database file is loaded or send to error.php
    if (!isset($_GET['Database'])){
        $_SESSION['error'] = "No database given";
        header('Location: error.php');
        exit;
    }

    require_once ('FileMaker.php');
    require_once ('db.php');
    require_once ('functions.php');
    require_once ('lib/simple_html_dom.php');

    $layoutFields = [
    'Country',
    'Province or State',
    'Locality',
    'Elevation',
    'Depth',
    'Phylum',
    'Class',
    'Family',
    'Genus',
    'Species',
    'Collector',
    'Collection Date',
    'Year',
    'Month',
    'Day',
    ];

    $renderPage = 'renderAll';

    if ($_GET['Database'] !== 'all') {
        $fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

        $layouts = $fm->listLayouts();

        if (FileMaker::isError($layouts)) {
            $_SESSION['error'] = $layouts->getMessage();
            header('Location: error.php');
            exit;
        }

        $layout = $layouts[0];

        foreach ($layouts as $l) {
            //get current database name
            $page = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '=') + 1);
            if ($page == 'mi' and str_contains($l, 'search')) {
                $layout = $l;
                break;
            } else if (str_contains($l, 'search')) {
                $layout = $l;
            }
        }

        $fmLayout = $fm->getLayout($layout);
        $layoutFields = $fmLayout->listFields();
        $renderPage = 'render';
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="https://herbweb.botany.ubc.ca/arcgis_js_api/library/4.10/esri/css/main.css">
        <?php
          require_once ('partials/cssDecision.php');
          require_once ('partials/header.php');
          require_once('partials/widgets.php');
        ?>
        <link rel="stylesheet" href="css/search.css">
    </head>

    <body class="container-fluid no-padding">
        <?php require_once ('partials/navbar.php');?>

        <!-- Page title below navbar -->
        <?php TitleBanner(databaseName: $_GET['Database']); ?>

        <div class="container-fluid">
            <form action="render.php" method="get" id="submit-form">
                <div class ="row">

                    <!-- form elements -->
                    <div id="form" class = "col-sm-6">
                        <!-- hidden text field containing the database name -->
                        <input type="text" hidden name="Database"
                               value=<?php if (isset($_GET['Database'])) echo htmlspecialchars($_GET['Database']); ?>>

                        <!-- submit button -->
                        <div class="form-group">
                            <input id="form" class="btn btn-custom" type="button" value="Submit" onclick="Process(clearURL())">
                        </div>

                        <?php
                            $ignoreValues = ['SortNum', 'Accession Numerical', 'Imaged', 'IIFRNo', 'Photographs::photoFileName', 'Event::eventDate', 'card01', 'Has Image', 'imaged'];
                            $layoutFields = array_diff($layoutFields, $ignoreValues);
                            list($layoutFields1, $layoutFields2) = array_chunk($layoutFields, ceil(count($layoutFields) / 2));
                            $count = 0;
                            foreach ($layoutFields1 as $rf) {
                        ?>
                        <div class="row">
                            <!--- Section that is one label and one search box --->
                            <div class="col-sm-3">
                                <label for="field-<?php echo $rf?>">
                                    <?php echo htmlspecialchars(formatField($rf)) ?>
                                </label>
                            </div>

                            <div class="col-sm-3">
                                <input type="text" id="field-<?php echo $rf?>"
                                    <?php
                                    if (isset($_POST[str_replace(' ', '_', $rf)]))
                                      echo "value=".htmlspecialchars($_POST[str_replace(' ', '_', $rf)]);
                                    ?>
                                    name="<?php echo htmlspecialchars($rf) ?>"
                                    class="form-control"
                                >
                            </div>

                            <!--- End of a single label, input instance --->
                            <?php if($count < sizeof($layoutFields2)) { ?>

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
                            <?php } ?>

                        </div>
                        <?php $count++; } ?>
                    </div>

                    <!-- search ops, images, maps, etc -->
                    <div class="border col-sm-6 px-0">
                        <!-- special entomology title -->
                        <div>
                            <?php
                            if($_GET['Database'] === 'entomology'){
                                echo '
                                    <div id="entoSite" class="row no-gutters">
                                        <div class="col-sm-12" style="background: url(images/entomologyBannerImages/rotator.php) no-repeat center center; background-size: 100% auto; text-align: center; color: white;">
                                            <div style ="margin-top:30px;margin-bottom:30px;">
                                                <a href="https://www.zoology.ubc.ca/entomology/" style="text-sdecoration: none; color: white;">
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
                                <label class = "btn btn-custom active" id="andLabel" style="font-size:12px;">
                                    <input type="radio"  id = "and" autocomplete="off"  checked> AND
                                </label>

                                <!-- OR -->
                                <label class = "btn btn-custom" id="orLabel" style="font-size:12px;">
                                    <input type="radio" id = "or" autocomplete="off" > <span style="visibility: hidden">&nbsp;</span>OR<span style="visibility: hidden">&nbsp;</span>
                                </label>
                            </div>
                        </div>

                        <!-- all records button -->
                        <div class="form-group">
                            <a href="render.php?Database=<?php echo htmlspecialchars($_GET['Database'])?>"
                               role="button" class="btn btn-custom"
                               style="font-size:12px; text-align:left; padding-left:2px; padding-right:2px;">Show All Records</a>
                        </div>

                        <!-- only image select -->
                        <div class="form-group">
                            <?php if ($_GET['Database'] == 'fish' || $_GET['Database'] == 'avian' ||$_GET['Database'] == 'herpetology' || $_GET['Database'] == 'mammal'
                                || $_GET['Database'] == 'vwsp' || $_GET['Database'] == 'bryophytes' ||
                                $_GET['Database'] == 'fungi' || $_GET['Database'] == 'lichen' || $_GET['Database'] == 'algae') { ?>
                                  <div class="col">
                                        <input type="checkbox" id="imageCheck">
                                        <label for="imageCheck">
                                            Only show records that contain an image
                                        </label>
                                  </div>
                                  <input type="hidden" name = "hasImage" id = "hasImage">
                            <?php } ?>
                            <input type="hidden" name = "type" id = "type">
                        </div>

                        <!-- example, shows a different example every time -->
                        <div>
                            <?php
                            if ($_GET['Database'] == 'vwsp' || $_GET['Database'] == 'bryophytes' || $_GET['Database'] == 'fungi'
                                || $_GET['Database'] == 'lichen' || $_GET['Database'] == 'algae' || $_GET['Database'] == 'avian'
                                || $_GET['Database'] == 'mammal'
                                || $_GET['Database'] == 'fish'
                                || $_GET['Database'] == 'entomology') {

                                $getSampleScript = $fm->newPerformScriptCommand('examples', 'Search Page Sample Selection');
                                $result = $getSampleScript->execute();
                                $record = $result->getRecords()[0];
                                $id = 'accessionNumber';
                                $lat = 'Geo_LatDecimal';
                                $lng = 'Geo_LongDecimal';
                                $genus = 'Genus';
                                $species = 'Species';
                                $url = '';

                                if ($_GET['Database'] == 'avian' || $_GET['Database'] == 'mammal') {
                                    //$url = getPhotoUrl($record->getRecordID());
                                    $url = "https://collections.zoology.ubc.ca".$record->getRelatedSet('Photographs')[0]->getField('Photographs::photoContainer');
                                    $id = 'catalogNumber';
                                    $lat = 'Geolocation::decimalLatitude';
                                    $lng = 'Geolocation::decimalLongitude';
                                    $genus = 'Taxon::genus';
                                    $species = 'Taxon::specificEpithet';
                                }
                                else if ($_GET['Database'] == 'vwsp' || $_GET['Database'] == 'bryophytes' || $_GET['Database'] == 'fungi'
                                    || $_GET['Database'] == 'lichen' || $_GET['Database'] == 'algae') {
                                    $url = getPhotoUrl($record->getField('Accession Number'));
                                    $id = 'Accession Number';
                                    $lat = 'Geo_LatDecimal';
                                    $lng = 'Geo_LongDecimal';
                                    $genus = 'Genus';
                                    $species = 'Species';
                                }
                                else if ($_GET['Database'] == 'entomology') {
                                    $id = 'SEM #';
                                    $lat = 'Latitude';
                                    $lng = 'Longitude';
                                    $genus = 'Genus';
                                    $species = 'Species';

                                }
                                else if ($_GET['Database'] == 'fish') {
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
                                                <a id = "catalogNumber" href  =  "details.php?Database=<?php echo htmlspecialchars($_GET['Database']).
                                                    '&AccessionNo='.htmlspecialchars($record->getField($id)) ?>">
                                                    <h4><b><?php echo $record->getField($id)?></b></h4></a>
                                            </div>
                                        </div>
                                        <div class = "row">
                                            <div class = "col d-flex justify-content-center" id = "taxon">
                                                <a id = "taxonInfo" href = "render.php?Database=<?php echo htmlspecialchars($_GET['Database']).
                                                    '&'.$genus.'='.htmlspecialchars($record->getField($genus)).
                                                    '&'.$species.'='.htmlspecialchars($record->getField($species)) ?>">
                                                    <h5><?php echo $record->getField($genus).' '.$record->getField($species);?></h5>
                                                </a>
                                            </div>
                                        </div>
                                        <div class = "row">
                                            <div id = "sample-img" class = "col-xl-6 d-flex justify-content-center">
                                                <?php
                                                if ($_GET['Database'] == 'entomology') {
                                                    $genusPage = getGenusPage($record);
                                                    $genusSpecies = getGenusSpecies($record);
                                                    $html = file_get_html($genusPage);
                                                    $species = $html->find('.speciesentry');
                                                    $semnumber = $record->getField('SEM #');
                                                    $foundImage = false;
                                                    foreach($species as $spec) {
                                                        $speciesName = $spec->innertext;
                                                        if (strpos($speciesName, $genusSpecies) !== false  && strpos($speciesName, $semnumber) !== false) {
                                                            $foundImage = true;
                                                            $images = $spec->find('a');
                                                            $link = $images[0]->href;
                                                            $url = str_replace('http:','https:',$genusPage);
                                                            $final = "".$url.$link;
                                                            echo '<a href ='. htmlspecialchars($url).' target="_blank" rel="noopener noreferrer">'.'<img id = "sample" class="minHeight" src="'.htmlspecialchars($final) .'"></a>';
                                                            break;
                                                        }
                                                    }
                                                }
                                                else if ($_GET['Database'] == 'fish') {
                                                    $url = 'https://open.library.ubc.ca/media/download/jpg/fisheries/'.$record->getField("card01").'/0';
                                                    $linkToWebsite = 'https://open.library.ubc.ca/collections/fisheries/items/'.$record->getField("card01");
                                                    echo '<a href ='. htmlspecialchars($linkToWebsite).' target="_blank" rel="noopener noreferrer">'.'<img id = "fish-sample" class="minHeight" src="'.htmlspecialchars($url) .'"></a>';
                                                }
                                                else {
                                                    echo '<a href ='. $url.' target="_blank" rel="noopener noreferrer">'.'<img id = "sample" class="minHeight" src="'.$url .'"></a>';
                                                }
                                                echo '<div hidden = true id = "Latitude">'. $record->getField($lat).'</div>';
                                                echo '<div hidden = true id = "Longitude">'. $record->getField($lng).'</div>';
                                                ?>
                                            </div>
                                            <div id = "sample-map" class = "col-xl-6 d-flex justify-content-center">
                                                <div id="viewDiv"></div>
                                                <script src="https://herbweb.botany.ubc.ca/arcgis_js_api/library/4.10/dojo/dojo.js"></script>
                                                <script src="js/map.js"></script>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                    </div>
                </div>
            </form>
        </div>

        <!-- footer -->
        <?php Footer(imgSrc: 'images/beatyLogo.png'); ?>

        <!-- scripts -->
        <script src="js/process.js"> </script>

        <script >
         // js for accordion icon
          $('.collapse').on('shown.bs.collapse', function(){
            $(this).parent().find(".oi-plus").removeClass("oi-plus").addClass("oi-minus");
          }).on('hidden.bs.collapse', function(){
            $(this).parent().find(".oi-minus").removeClass("oi-minus").addClass("oi-plus");
          });
        </script>
    </body>
</html>
