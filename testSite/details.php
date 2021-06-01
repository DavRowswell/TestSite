<?php

require_once('utilities.php');
require_once ('lib/simple_html_dom.php');
require_once ('credentials_controller.php');
require_once ('constants.php');
require_once ('DatabaseSearch.php');

session_set_cookie_params(0,'/','.ubc.ca',isset($_SERVER["HTTPS"]), true);
session_start();

define('DATABASE', $_GET['Database'] ?? null);

checkDatabaseField(DATABASE);

$databaseSearch = DatabaseSearch::fromDatabaseName(DATABASE);

$findCommand = $databaseSearch->getFileMaker()->newFindCommand($databaseSearch->getDetailLayout()->getName());

# add a search param to the query to exactly '==' equal the accession number
if (isset($_GET['AccessionNo']) && $_GET['AccessionNo'] !== '') {
    $findCommand->addFindCriterion($databaseSearch->getIDFieldName(), '=='.$_GET['AccessionNo']);
}

$result = $findCommand->execute();

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
        <link rel="stylesheet" href="css/details.css">
        <?php
          require_once('partials/conditionalCSS.php');
          require_once ('partials/widgets.php');

          HeaderWidget('Specie Details');
        ?>
    </head>

    <body class="d-flex flex-column">
        <?php Navbar(); ?>

        <?php TitleBanner(DATABASE); ?>

    <div class="container-fluid">
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
                                        if (formatField($field) === "Latitude") {echo "id='Latitude'"; $lat = $allRecordsFound[0]->getField($field);}
                                        if (formatField($field) === "Longitude") {echo "id='Longitude'"; $long = $allRecordsFound[0]->getField($field);}
                                    ?>
                                    >
                                <?php endif; ?>
                                <?php echo $allRecordsFound[0]->getField($field) ?>
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
                            <script src="js/map.js"></script>
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
                            if (DATABASE === 'fish') {

                                $numOfCards = $allRecordsFound[0]->getField("iffrCardNb");

                                for ($num = 1; $num <= $numOfCards; $num++) {
                                    $num_padded = sprintf("%02d", $num);
                                    $cardName = "card".$num_padded;

                                    $url =  'https://open.library.ubc.ca/media/download/jpg/fisheries/'.$allRecordsFound[0]->getField($cardName).'/0';
                                    $linkToWebsite =  'https://open.library.ubc.ca/collections/fisheries/items/'.$allRecordsFound[0]->getField($cardName);

                                    if (@getimagesize($url)[0] >0 && @getimagesize($url)[1] > 0) {

                                        echo '<div class="mySlides">';

                                        echo '<a href ='. htmlspecialchars($linkToWebsite).' target="_blank" rel="noopener noreferrer">'.
                                        '<img id = "fish" class="img-fluid minHeight" src="'.htmlspecialchars($url) .'"></a>';

                                    } else {
                                        echo '<div style="height: 300px; text-align:center; line-height:300px;">';
                                        echo '<span style="">No picture found for this record</span>';
                                    }
                                    echo '</div>';
                                }
                              echo '<a class="prevbutton" onclick="plusSlides(-1)">&#10094;</a>';
                              echo '<a class="nextbutton" onclick="plusSlides(1)">&#10095;</a>';

                            }
                            else if (DATABASE === 'entomology') {

                                $genusPage = getGenusPage($allRecordsFound[0]);
                                $genusSpecies = getGenusSpecies($allRecordsFound[0]);
                                $html = file_get_html($genusPage);
                                $species = $html->find('.speciesentry');
                                $semnumber = $allRecordsFound[0]->getField('SEM #');
                                $foundImage = false;

                                foreach($species as $spec) {
                                    $speciesName = $spec->innertext;
                                    if (str_contains($speciesName, $genusSpecies) && str_contains($speciesName, $semnumber)) {
                                        $foundImage = true;
                                        $images = $spec->find('a');
                                        for ($num=0; $num<sizeof($images); $num++){
                                            $link = $images[$num]->href;
                                            $url = str_replace('http:','https:',$genusPage);

                                            echo '<div class="mySlides">';
                                            echo '<a href="'.$url.'" target="_blank" rel="noopener noreferrer">
                                            <img class="img-fluid minHeight" src ="'.$url.$link.'"> </a>';
                                            echo '</div>';
                                        }
                                        echo '<a class="prevbutton" onclick="plusSlides(-1)">&#10094;</a>';
                                        echo '<a class="nextbutton" onclick="plusSlides(1)">&#10095;</a>';
                                    }
                                }

                                if($foundImage==false) {
                                    echo '<div style="height: 300px; text-align:center; line-height:300px;">';
                                        $order = $allRecordsFound[0]->getField('Order');
                                        $fam=$allRecordsFound[0]->getField("Family");
                                        $subfam=$allRecordsFound[0]->getField("Subfamily");
                                        if ($subfam !== ""){
                                            echo '<a href="https://www.zoology.ubc.ca/entomology/main/'.$order.'/'.$fam.'/'.$subfam.'/" style="text-align:center;"> 
                                            <button class="btn btn-custom" id="showAll" > See more of '.$subfam.' here!</button> </a>';
                                        }
                                        else {
                                            echo ' <a href="https://www.zoology.ubc.ca/entomology/main/'.$order.'/'.$fam.'/" style="text-align:center;"> 
                                            <role="button" class="btn btn-custom" id="showAll" > See more of '.$fam.' here!</button> </a> ';
                                        }
                                    echo '</div>';
                                }
                            }
                            else {
                                $validDb = false;
                                if (DATABASE == 'avian' ||DATABASE == 'herpetology' || DATABASE == 'mammal') {
                                    $tableNamesObj = $allRecordsFound[0]->getRelatedSet('Photographs');

                                    // if images, type = 'array'; else 'object'
                                    if (gettype($tableNamesObj)=='array') {
                                        foreach ($tableNamesObj as $relatedRow) {
                                            $possible_answer = $relatedRow->getField('Photographs::photoContainer');
                                            if (str_contains(strtolower($possible_answer), "jpg")){ // delete this if later
                                                $possible_answer= "https://collections.zoology.ubc.ca".$possible_answer;
                                                echo '<div class="mySlides">';
                                                echo '<a href ='.$possible_answer.' target="_blank" rel="noopener noreferrer">'.
                                                '<img id = "avian" class="img-fluid minHeight" src="'.$possible_answer .'"></a>';
                                                echo '</div>';
                                            }
                                        }

                                        echo '<a class="prevbutton" onclick="plusSlides(-1)">&#10094;</a>';
                                        echo '<a class="nextbutton" onclick="plusSlides(1)">&#10095;</a>';
                                        $validDb = false;
                                    } else {
                                        echo '<div style="height: 300px; text-align:center; line-height:300px;">';
                                        echo '<span style="">No picture found for this record</span>';
                                        echo '</div>';
                                    }

                                }
                                else if (DATABASE == 'vwsp' || DATABASE == 'bryophytes' || DATABASE == 'fungi'
                                || DATABASE == 'lichen' || DATABASE == 'algae') {
                                    $url = getPhotoUrl($_GET['AccessionNo'], DATABASE);
                                    $validDb = true;
                                }
                                if ($validDb) {
                                    if (@getimagesize($url)[0] >0 && @getimagesize($url)[1] > 0) {
                                        echo '<a href ='. htmlspecialchars($url).' target="_blank" rel="noopener noreferrer">'.'<img class="img-fluid minHeight" src="'.htmlspecialchars($url) .'"></a>';
                                    } else {
                                        echo '<div style="height: 300px; text-align:center; line-height:300px;">';
                                        echo '<span style="">No picture found for this record</span>';
                                        echo '</div>';
                                    }
                                }
                            }
                            ?>
                        </div>

                        <!-- Slideshow UI controller -->
                        <div style="text-align:center">
                            <?php // adds the dots to the slideshow
                              if (DATABASE === 'fish') {
                                for ($num=1; $num<=$numOfCards; $num++){
                                  echo '<span class="dot" onclick="currentSlide('.$num.')"></span>';
                                }
                            }
                              if (DATABASE === 'avian' || DATABASE === 'mammal' || DATABASE === 'herpetology') {
                                $num =1;
                                foreach ($tableNamesObj as $relatedRow){
                                  if (gettype($tableNamesObj)=='array') {
                                    $possible_answer = $relatedRow->getField('Photographs::photoContainer');
                                    if ((str_contains(strtolower($possible_answer), "jpg"))){  // delete if later
                                      echo '<span class="dot" onclick="currentSlide('.$num.')"></span>';
                                    }
                                  }
                                  $num++;
                                }
                            }
                              if (DATABASE === 'entomology'){
                                if ($foundImage===true){
                                    for ($num=1; $num<=sizeof($images); $num++){
                                      echo '<span class="dot" onclick="currentSlide('.$num.')"></span>';
                                    }

                                }
                                 }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- entomology special link to more images -->
                <div class="row">
                    <?php
                    if (DATABASE === 'entomology'){
                        if ($foundImage===true){
                            $fam=$allRecordsFound[0]->getField("Family");
                            $subfam=$allRecordsFound[0]->getField("Subfamily");
                            if ($subfam!==""){
                                echo '<a href="'.$url.'" style="text-align:center;"> 
                                    <button class="btn btn-custom" id="showAll" > See more '.$subfam.' here!</button> </a>';
                            } else {
                                echo ' <a href="'.$url.'" style="text-align:center;"> 
                                    <role="button" class="btn btn-custom" id="showAll" > See more '.$fam.' here!</button> </a> ';
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php FooterWidget('images/beatyLogo.png') ;?>

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