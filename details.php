<?php

use airmoi\FileMaker\FileMakerException;

require_once('utilities.php');
require_once ('credentials_controller.php');
require_once ('constants.php');
require_once ('DatabaseSearch.php');

session_set_cookie_params(0,'/','.ubc.ca',isset($_SERVER["HTTPS"]), true);
session_start();

define('DATABASE', $_GET['Database'] ?? null);

checkDatabaseField(DATABASE);

try {
    $databaseSearch = DatabaseSearch::fromDatabaseName(DATABASE);
} catch (FileMakerException $e) {
    $_SESSION['error'] = 'Unsupported database given';
    header('Location: error.php');
    exit;
}

$findCommand = $databaseSearch->getFileMaker()->newFindCommand($databaseSearch->getDetailLayout()->getName());

# add a search param to the query to exactly '==' equal the accession number
if (isset($_GET['AccessionNo']) && $_GET['AccessionNo'] !== '') {
    $findCommand->addFindCriterion($databaseSearch->getIDFieldName(), '=='.$_GET['AccessionNo']);
}

try {
    $result = $findCommand->execute();
} catch (FileMakerException $e) {
    $_SESSION['error'] = 'Search fields returned an error!';
    header('Location: error.php');
    exit;
}

$allRecordsFound = $result->getRecords();

# we should only get one record back!
if (sizeof($allRecordsFound) != 1) {
    $_SESSION['error'] = 'No records or more than one records found. This is an internal error. Please contact the admin!';
    header('Location: error.php');
    exit;
}

$record = $allRecordsFound[0];

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="https://herbweb.botany.ubc.ca/arcgis_js_api/library/4.10/esri/css/main.css">
        <link rel="stylesheet" href="public/css/details.css">
        <?php
          require_once('partials/conditionalCSS.php');
          require_once ('partials/widgets.php');

          HeaderWidget('Specie Details');
        ?>
    </head>

    <body>
        <?php Navbar(); ?>

        <?php TitleBannerSearch(DATABASE); ?>

        <div class="container-fluid flex-grow-1">
            <div class="row">
                <!-- Specie Information -->
                <div class="col-sm-9">
                    <!-- construct table for given layout and fields -->
                    <table class="table">
                        <tbody>
                            <?php
                            $fields = $record->getFields();
                            $ignoreLayoutFieldNames = ['SortNum', 'Accession Numerical', 'Photographs::photoContainer', 'Imaged', 'IIFRNo', 'Photographs::photoFileName', 'Event::eventDate', 'iffrCardNb', 'card01', 'Has Image', 'imaged','card02','card03','card04','card05','card06','card07','card08','card09','card10','card11','card12','card13'];

                            foreach($fields as $field) :
                                # ignore values in field keys contained in the ignored list
                                if (in_array($field, $ignoreLayoutFieldNames)) continue;?>
                                <tr>
                                    <th><b><?php echo formatField($field) ?></b></th>
                                    <?php if(formatField($field) == "Genus" || formatField($field) == "Species") : ?>
                                        <td style="font-style:italic;">
                                    <?php else: ?>
                                        <td
                                        <?php
                                            if (formatField($field) === "Latitude") {echo "id='Latitude'"; $lat = $record->getField($field);}
                                            if (formatField($field) === "Longitude") {echo "id='Longitude'"; $long = $record->getField($field);}
                                        ?>
                                        >
                                    <?php endif; ?>
                                    <?php echo $record->getField($field) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Images and Maps -->
                <div class="col-sm-3">

                    <!-- ArcGIS Map -->
                    <div class="row">
                        <div class="col">
                            <?php if(isset($lat) && isset($long)) : ?>
                                <div id="viewDiv" style="height: 300px;"></div>
                                <script src="https://herbweb.botany.ubc.ca/arcgis_js_api/library/4.10/dojo/dojo.js"></script>
                                <script src="public/js/map.js"></script>
                            <?php else: ?>
                                <div style="height: 300px; text-align:center; line-height:300px;">
                                    <span style="">No coordinates for this record</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- image code starts here -->
                    <div class="row">
                        <div class = "col">
                            <div class = "slideshow-container">
                                <?php
                                # start without any image found, should be true if at least one found
                                $foundImage = false;

                                if (DATABASE === 'fish') {

                                    # get the image urls from the cards TODO ask what are these cards?
                                    $numOfCards = $record->getField("iffrCardNb");
                                    $specie = $record->getField("Species");

                                    $imageUrls = [];

                                    for ($num = 1; $num <= $numOfCards; $num++) {
                                        $num_padded = sprintf("%02d", $num);
                                        $cardName = "card".$num_padded;

                                        try {
                                            $cardFieldValue = $record->getField($cardName);
                                        } catch (FileMakerException $e) {
                                            continue;
                                        }

                                        $url =  'https://open.library.ubc.ca/media/download/jpg/fisheries/'.$cardFieldValue.'/0';
                                        $linkToWebsite =  'https://open.library.ubc.ca/collections/fisheries/items/'.$cardFieldValue;

                                        $imageUrls[$linkToWebsite] = $url;
                                    }

                                    # for each image, add it to the slider
                                    foreach ($imageUrls as $webUrl => $imageUrl) {
                                        if (@getimagesize($imageUrl)[0] > 0 && @getimagesize($imageUrl)[1] > 0) {
                                            $websiteLink = htmlspecialchars($webUrl);
                                            $imgLink = htmlspecialchars($imageUrl);
                                            echo "
                                                <div class='mySlides'>
                                                    <a href='$websiteLink' target='_blank'><img class='img-fluid' src='$imgLink' alt='Image for $specie'></a>
                                                </div>
                                            ";
                                            $foundImage = true;
                                        }
                                    }
                                }
                                else if (DATABASE === 'entomology') {

                                    try {
                                        $familyUrl = getGenusPage($record);
                                        $genus = $record->getField('Genus');
                                        $specie = $record->getField('Species');
                                        $fam= $record->getField("Family");
                                    } catch (FileMakerException $e) {
                                        $_SESSION['error'] = 'There was an error with File Maker Pro fields. Please contact the admin.';
                                        header('Location: error.php');
                                        exit;
                                    }

                                    # scrap the entomology website for images
                                    # source https://www.ostraining.com/blog/coding/extract-image-php/
                                    $html = file_get_contents($familyUrl);
                                    preg_match_all('|<img.*?src=[\'"](.*?)[\'"].*?>|i',$html, $matches);
                                    $rawImageNameList = $matches[1];

                                    # only use those images with the genus and specie name in it
                                    $imageNames = array_filter(
                                        $rawImageNameList,
                                        function ($imgUrl) use($genus, $specie) {
                                            return str_contains($imgUrl, $genus) and str_contains($imgUrl,  $specie);
                                        }
                                    );

                                    if (sizeof($imageNames) > 0) {
                                        # print each image for the specie in a div with the slides class
                                        foreach ($imageNames as $imageName) {
                                            $imageUrl = $familyUrl . $imageName;
                                            echo "
                                            <div class='mySlides'>
                                                <a href='$imageUrl' target='_blank'>
                                                    <img class='img-fluid minHeigh' src='$imageUrl' alt='Image for $genus - $specie'>
                                                </a>
                                            </div>
                                        ";
                                        }
                                        $foundImage = true;
                                    }

                                    # echo special button to move to entomology website
                                    echo "
                                            <div class='p-2'>
                                                <a href=$familyUrl class='text-center' target='_blank'>
                                                    <button class='btn btn-custm' id='showAll'> See more of $fam here! </button>
                                                </a>
                                            </div>
                                        ";
                                }
                                elseif (DATABASE == 'avian' or DATABASE == 'herpetology' or DATABASE == 'mammal') {
                                    $tableNamesObj = $record->getRelatedSet('Photographs');

                                    $imageUrls = [];

                                    // if images, type = 'array'; else 'object'
                                    if (gettype($tableNamesObj) == 'array') {
                                        foreach ($tableNamesObj as $relatedRow) {
                                            $possible_answer = $relatedRow->getField('Photographs::photoContainer');
                                            if (str_contains(strtolower($possible_answer), "jpg")) { // delete this if later
                                                $possible_answer = "https://collections.zoology.ubc.ca" . $possible_answer;
                                                $imageUrls[$possible_answer] = $possible_answer;
                                            }
                                        }
                                    }

                                    foreach ($imageUrls as $imageUrl) {
                                        $foundImage = true;
                                        echo "
                                                <div class='mySlides'>
                                                    <a href='$imageUrl' target='_blank'>
                                                        <img src='$imageUrl' class='img-fluid minHeight' alt='Species image.'>
                                                    </a>
                                                </div>
                                                ";
                                    }
                                }
                                else if (DATABASE == 'vwsp' or DATABASE == 'bryophytes' or DATABASE == 'fungi' or
                                    DATABASE == 'lichen' or DATABASE == 'algae') {

                                    $url = getPhotoUrl($_GET['AccessionNo'], DATABASE);
                                    if (@getimagesize($url)[0] > 0 && @getimagesize($url)[1] > 0) {
                                        echo '<a href =' . htmlspecialchars($url) . ' target="_blank"> <img class="img-fluid minHeight" src="' . htmlspecialchars($url) . '" alt="Species image."></a>';
                                        $foundImage = true;
                                    }
                                }

                                # if no image found echo text
                                if (!$foundImage) {
                                    echo "
                                            <div class='text-center'>
                                                <span> No picture found for this record</span>
                                            </div>
                                            ";
                                } else {
                                    # slider controllers to go left or right
                                    echo '<a class="prevbutton" onclick="plusSlides(-1)">&#10094;</a>';
                                    echo '<a class="nextbutton" onclick="plusSlides(1)">&#10095;</a>';
                                }
                                ?>
                            </div>

                            <!-- Slideshow UI controller -->
                            <div class="text-center">
                                <?php # adds the dots to the slideshow
                                if ($foundImage) {
                                    if (isset($imageUrls) and sizeof($imageUrls) > 0) {
                                        for ($num = 1; $num <= sizeof($imageUrls); $num++) {
                                            echo "<span class='dot' onclick='currentSlide($num)'></span>";
                                        }
                                    } else if (DATABASE === 'entomology' and isset($imageNames)) {
                                        for ($num = 1; $num <= sizeof($imageNames); $num++) {
                                            echo "<span class='dot' onclick='currentSlide($num)'></span>";
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php FooterWidget('public/images/beatyLogo.png') ;?>

        <!-- Scripts to handle slides -->
        <script>
            // js slideshow
            let slideIndex = 1;
            showSlides(slideIndex);

            // Next/previous controls
            function plusSlides(n) {
                showSlides(slideIndex += n);
            }

            // Thumbnail image controls
            function currentSlide(n) {
                showSlides(slideIndex = n);
            }

            function showSlides(n) {
                let i;
                const slides = document.getElementsByClassName("mySlides");
                const dots = document.getElementsByClassName("dot");

                if (n > slides.length) { slideIndex = 1 }
                if (n < 1) { slideIndex = slides.length }

                for (i = 0; i < slides.length; i++) {
                    slides[i].style.display = "none";
                }
                for (i = 0; i < dots.length; i++) {
                    dots[i].className = dots[i].className.replace(" active", "");
                }

                slides[slideIndex-1].style.display = "block";
                dots[slideIndex-1].className += " active";
            }
        </script>

    </body>
</html>