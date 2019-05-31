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
    // echo "FM_FILE: $FM_FILE <br>
    //       FM_HOST: $FM_HOST <br>
    //       FM_USER: $FM_USER <br>
    //       FM_PASS: $FM_PASS <br>";

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
      if ($page == 'mi') {
        if (strpos($l, 'search') !== false) {
          $layout = $l;
          break;
        }
      }
      else if (strpos($l, 'search') !== false) {
        $layout = $l;
      }
    }
    $fmLayout = $fm->getLayout($layout);
    $layoutFields = $fmLayout->listFields(); 
    $renderPage = 'render';
  }

?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://herbweb.botany.ubc.ca/arcgis_js_api/library/4.10/esri/css/main.css">
<link rel="stylesheet" href="css/searchcss.css">
<?php
  require_once ('partials/cssDecision.php');
  require_once ('partials/header.php');
?>
</head>
<body class="d-flex flex-column">
  <?php require_once ('partials/navbar.php');?>
  <div id ="main">
    <div class="row">
      <div class="col">
        <?php if($_GET['Database'] === "mi" || $_GET['Database'] === "miw" || $_GET['Database'] === "vwsp") { ?>
          <h1><b><?php 
                    if($_GET['Database'] === "mi"){echo "Dry Marine Invertebrate";}
                    else if($_GET['Database'] === "vwsp"){echo "Vascular";}
                    else{echo "Wet Marine Invertebrate";} 
                  ?> Search</b>
          </h1>
        <?php } else { ?>
          <h1><b><?php echo ucfirst($_GET['Database']); ?> Search</b></h1>
        <?php }?>
        <div id="column-divider"></div>
      </div>
    </div>
  </div>
  <div class="container-fluid">    
    <form action="render.php" method="get" id = "submit-form">
      <div class ="row">
        <div id="form" class = "col-sm-6">
          <div class="form-group">
            <input type="text" name="Database" style="display:none;" 
            value=<?php if (isset($_GET['Database'])) echo htmlspecialchars($_GET['Database']); ?>>
          </div>
          <div class="row">
            <div id = 'submit'>
              <input id="form" class="btn btn-custom" type="button" value="Submit" onclick="Process(clearURL())">    
            </div>     
          </div>
          <br>   
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
              class="form-control">
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
              class="form-control">
            </div>
            <!--- End of a single label, input instance --->
            <?php } ?>
          </div>
          <?php $count++; } ?>
        </div>
        <div id="legend" class="border col-sm-6 px-0"> 
          <?php
          if($_GET['Database'] === 'entomology'){
            echo '<div id="entoSite" class="row no-gutters">';
              echo '<div class="col-sm-12" style="background: url(images/entomologyBannerImages/rotator.php) no-repeat center center; background-size: 100% auto; text-align: center; color: white;">';
                echo '<div style ="margin-top:30px;margin-bottom:30px;">';
                  echo '<a href="https://www.zoology.ubc.ca/entomology/" style="text-sdecoration: none; color: white;">';
                    echo '<p>Welcome to the</p><h3>SPENCER ENTOMOLOGICAL COLLECTION</h3>';
                  echo '</a>';
                echo '</div>';
              echo '</div>';
              //<img src="images/entomologyBannerImages/rotator.php"/>
            echo '</div>';
          }
          ?>

          <!--- start of accordion collipsible--->
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
         <!--- end of accordion collipsible--->
          <div class = "row">
            <div class = "col"> 
              <h4 style=padding-top:12px;>Search By</h4>
            </div>
          </div>
          <div class = "row">
            <div class="col">
              <div class = "btn-group btn-group-toggle" data-toggle="buttons" >
                <label class = "btn btn-custom active" id="andLabel" style="font-size:12px;">
                  <input type="radio"  id = "and" autocomplete="off"  checked> AND 
                </label>
                <label class = "btn btn-custom" id="orLabel" style="font-size:12px;">
                  <input type="radio" id = "or" autocomplete="off" > <span style="visibility: hidden">&nbsp;</span>OR<span style="visibility: hidden">&nbsp;</span>
                </label> 
              </div>
            </div>
          </div>
          <div class="row" style="padding-top:12px;">
            <div class="col">
              <a href="render.php?Database=<?php echo htmlspecialchars($_GET['Database'])?>" 
                  role="button" class="btn btn-custom" 
                  style="font-size:12px; text-align:left; padding-left:2px; padding-right:2px;">Show All Records</a>   
            </div>
          </div>
          <div class="row" style="padding-top:12px;">
            <?php if ($_GET['Database'] == 'fish' || $_GET['Database'] == 'avian' ||$_GET['Database'] == 'herpetology' || $_GET['Database'] == 'mammal'
            || $_GET['Database'] == 'vwsp' || $_GET['Database'] == 'bryophytes' || $_GET['Database'] == 'entomology' ||
            $_GET['Database'] == 'fungi' || $_GET['Database'] == 'lichen' || $_GET['Database'] == 'algae') { ?>
              <div class="col">
                <input type="checkbox" value="" id="imageCheck">
                <label for="imageCheck">
                  Only show records that contain an image
                </label>
              </div>
              <input type="hidden" name = "hasImage" id = "hasImage">
            <?php } ?>
            <input type="hidden" name = "type" id = "type">
          </div>
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
                $url = getPhotoUrl($record->getRecordID());
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
                      if ($_GET['Database'] == 'mammal' || $_GET['Database'] == 'avian') { // mammal, avian jumbotron
                        $tableNamesObj = $record->getRelatedSet('Photographs');
                        $possible_answer = $tableNamesObj[0]->getField('Photographs::photoContainer');
                        if (strpos($possible_answer, '.JPG') !== false){   // make sure actually an image
                          $possible_answer= "https://collections.zoology.ubc.ca".$possible_answer;
                           echo '<a href ='.$possible_answer.' target="_blank" rel="noopener noreferrer">'.
                          '<img id = "avian" class="img-fluid minHeight" src="'.$possible_answer .'"></a>';
                        }
                        // if it's not an image, then just doesn't show an image. still looks okay, but ideally use diff record
                      }
                      else {
                        // old code
                        echo '<a href ='. htmlspecialchars($url).' target="_blank" rel="noopener noreferrer">'.'<img id = "sample" class="minHeight" src="'.htmlspecialchars($url) .'"></a>';      
                      }
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
    </form>
  </div>
  <?php require_once("partials/footer.php");?>
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
