<?php
  session_set_cookie_params(0,'/','.ubc.ca',isset($_SERVER["HTTPS"]), true);
  session_start();
  $_SESSION['error'] = "";
  if (isset($_GET['Database'])){}
  else {
    $_SESSION['error'] = "No database given";
    header('Location: error.php');
    exit;
  }

  require_once ('FileMaker.php');
  require_once ('functions.php');
  require_once ('lib/simple_html_dom.php');
  require_once ('db.php');

  $fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

  $layouts = $fm->listLayouts();
  $layout = $layouts[0];
  foreach ($layouts as $l) {
    if ($_GET['Database'] === 'mi') {
      if (str_contains($l, 'details')) {
        $layout = $l;
        break;
      }
    }
    else if (str_contains($l, 'details')) {
      $layout = $l;
    }
  }

  $findCommand = $fm->newFindCommand($layout);
  if (isset($_GET['AccessionNo']) && $_GET['AccessionNo'] !== '') {
      if ($_GET['Database'] == 'vwsp' or $_GET['Database'] == 'bryophytes' or 
          $_GET['Database'] == 'fungi' or $_GET['Database'] == 'lichen' or $_GET['Database'] == 'algae'){
        $findCommand->addFindCriterion('Accession Number', '=='.$_GET['AccessionNo']);
      }
      else if ($_GET['Database'] == 'fossil' || $_GET['Database'] == 'avian' || $_GET['Database'] == 'herpetology' || $_GET['Database'] == 'mammal') {
        $findCommand->addFindCriterion('catalogNumber', '=='.$_GET['AccessionNo']);
      }
      else if ($_GET['Database'] == 'fish'){   
        $findCommand->addFindCriterion('accessionNo', '=='.$_GET['AccessionNo']);  
      }
      else if ($_GET['Database'] == 'entomology'){
        $findCommand->addFindCriterion('SEM #', '=='.$_GET['AccessionNo']);
      }
      else {
        $findCommand->addFindCriterion('Accession No', '=='.$_GET['AccessionNo']);
      }
  }

  $result = $findCommand->execute();

  if(FileMaker::isError($result)) {
    $_SESSION['error'] = $result->getMessage();
    header('Location: error.php');
    exit;
  } else {
    $findAllRec = $result->getRecords();
  }
  $lat="";
  $long="";
  ?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="https://herbweb.botany.ubc.ca/arcgis_js_api/library/4.10/esri/css/main.css">
        <link rel="stylesheet" href="css/details.css">
        <style>
            html, body, {
                height: 100%;
                width: 100%;
                margin: 0;
                padding: 0;
            }
        </style>
        <?php
          require_once ('partials/cssDecision.php');
          require_once ('partials/widgets.php');
          HeaderWidget('Specie Details');
        ?>
    </head>

    <body class="d-flex flex-column">
      <?php Navbar(); ?>

      <?php
      # check for errors, if so return error.php
      if(FileMaker::isError($result)){
        $_SESSION['error'] = $result->getMessage();
        header('Location: error.php');
        exit;
      }
      $recFields = $result->getFields();
      ?>

    <?php TitleBanner($_GET['Database']); ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Specie Information -->
            <div class="col-sm-9">
                <!-- construct table for given layout and fields -->
                <table class="table">
                    <tbody>
                        <?php
                        $ignoreValues = ['SortNum', 'Accession Numerical', 'Photographs::photoContainer', 'Imaged', 'IIFRNo', 'Photographs::photoFileName', 'Event::eventDate', 'iffrCardNb', 'card01', 'Has Image', 'imaged','card02','card03','card04','card05','card06','card07','card08','card09','card10','card11','card12','card13'];
                        foreach($recFields as $i) :
                            if (in_array($i, $ignoreValues)) continue;?>
                            <tr>
                                <th><b><?php echo formatField($i) ?></b></th>
                                <?php if(formatField($i) == "Genus" || formatField($i) == "Species") : ?>
                                    <td style="font-style:italic;">
                                <?php else: ?>
                                    <td
                                    <?php
                                        if (formatField($i) === "Latitude") {echo "id='Latitude'"; $lat = $findAllRec[0]->getField($i);}
                                        if (formatField($i) === "Longitude") {echo "id='Longitude'"; $long = $findAllRec[0]->getField($i);}
                                    ?>
                                    >
                                <?php endif; ?>
                                <?php echo $findAllRec[0]->getField($i) ?>
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
                        <?php if($lat != "" && $long != "") : ?>
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
                            if ($_GET['Database'] === 'fish') {

                                $numOfCards = $findAllRec[0]->getField("iffrCardNb");

                                for ($num = 1; $num <= $numOfCards; $num++) {
                                    $num_padded = sprintf("%02d", $num);
                                    $cardName = "card".$num_padded;

                                    $url =  'https://open.library.ubc.ca/media/download/jpg/fisheries/'.$findAllRec[0]->getField($cardName).'/0';
                                    $linkToWebsite =  'https://open.library.ubc.ca/collections/fisheries/items/'.$findAllRec[0]->getField($cardName);

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
                            else if ($_GET['Database'] === 'entomology') {

                                $genusPage = getGenusPage($findAllRec[0]);
                                $genusSpecies = getGenusSpecies($findAllRec[0]);
                                $html = file_get_html($genusPage);
                                $species = $html->find('.speciesentry');
                                $semnumber = $findAllRec[0]->getField('SEM #');
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
                                        $order = $findAllRec[0]->getField('Order');
                                        $fam=$findAllRec[0]->getField("Family");
                                        $subfam=$findAllRec[0]->getField("Subfamily");
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
                                if ($_GET['Database'] == 'avian' ||$_GET['Database'] == 'herpetology' || $_GET['Database'] == 'mammal') {
                                    $tableNamesObj = $findAllRec[0]->getRelatedSet('Photographs');

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
                                else if ($_GET['Database'] == 'vwsp' || $_GET['Database'] == 'bryophytes' || $_GET['Database'] == 'fungi'
                                || $_GET['Database'] == 'lichen' || $_GET['Database'] == 'algae') {
                                    $url = getPhotoUrl($_GET['AccessionNo']);
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
                              if ($_GET['Database'] === 'fish') {
                                for ($num=1; $num<=$numOfCards; $num++){
                                  echo '<span class="dot" onclick="currentSlide('.$num.')"></span>';
                                }
                            }
                              if ($_GET['Database'] === 'avian' || $_GET['Database'] === 'mammal' || $_GET['Database'] === 'herpetology') {
                                $num =1;
                                foreach ($tableNamesObj as $relatedRow){
                                  if (gettype($tableNamesObj)=='array') {
                                    $possible_answer = $relatedRow->getField('Photographs::photoContainer');
                                    if ((strpos(strtolower($possible_answer), "jpg") !== false)){  // delete if later
                                      echo '<span class="dot" onclick="currentSlide('.$num.')"></span>';
                                    }
                                  }
                                  $num++;
                                }
                            }
                              if ($_GET['Database'] === 'entomology'){
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
                    if ($_GET['Database'] === 'entomology'){
                        if ($foundImage===true){
                            $fam=$findAllRec[0]->getField("Family");
                            $subfam=$findAllRec[0]->getField("Subfamily");
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

    <script >
     // js slideshow
      var slideIndex = 1;
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
        var i;
        var slides = document.getElementsByClassName("mySlides");
        var dots = document.getElementsByClassName("dot");
        if (n > slides.length) {slideIndex = 1}
        if (n < 1) {slideIndex = slides.length}
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