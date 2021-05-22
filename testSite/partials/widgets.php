<?php

# A title banner is a row with a title and a background color
function TitleBanner(string $databaseName) {
    if($databaseName === "mi" || $databaseName === "miw" || $databaseName === "vwsp") {
        if ($databaseName === "mi") { $title = "Dry Marine Invertebrate"; }
        else if ($databaseName === "vwsp") { $title = "Vascular"; }
        else { $title = "Wet Marine Invertebrate"; }
    } else {
        $title = ucfirst($databaseName);
    }

    echo '
            <div class="container-fluid no-padding">
                  <h1>
                      <b>
                        '. $title .'Search
                      </b>
                  </h1>
            </div>
        
        ';
}

# A card widget is a actionable card to represent a database in the main catalog
function DatabaseCard(string $title, string $img_source, string $href, string $background_color, string $alt='') {
    echo '
        <!--- '. $title .' image and link--->
        <div class="col d-flex justify-content-center">
            <a href='. $href .'>
                <figure class="text-center" style="background: '. $background_color .'">
                    <img class="img-fluid img-sized" src='. $img_source .' alt='. $alt .'>
                    <figcaption>
                        <h4>'. $title .'</h4>
                    </figcaption>
                </figure>
            </a>
        </div>
        ';
}

# a page controller has widgets to move around all the possible pages for a table
function PageController($maxResponses, $result) {
    $uri = $_SERVER['REQUEST_URI'];

    $parts = explode('&', $uri);

    $amountOfRecords = $result->getFoundSetCount();


    $pages = ceil($amountOfRecords / $maxResponses);
    $page = 1;
    if (isset($_GET['Page']) && $_GET['Page'] != '') {
        $page = $_GET['Page'];
    }

    $pageInfo = "$amountOfRecords records found in page ".htmlspecialchars($page)." / ".htmlspecialchars($pages);
    $maxPages = htmlspecialchars($pages);

    echo '
        <style>
            a {
              text-decoration: none;
              display: inline-block;
              padding: 8px 16px;
            }
            
            a:hover {
              background-color: #ddd;
              color: black;
            }
        </style>
            
        <form action="render.php" method="get" id="pageForm">
            <div class="form-row">
                <!-- text with num of results and pages -->
                <div class="form-group">
                    <p>'. $pageInfo .'</p>
                </div>
        
                <!-- buttons to travel to next or previous page -->
                <div class="form-group">
                    ';
    NextBackButtons($pages, $parts, $amountOfRecords, $maxResponses);
    echo '
                </div>
            </div>
        
            <div class="form-row">
                <label for="numberInput">Go to page: </label>
                <!-- page number input -->
                <div class="form-group mx-sm-3">
                    <!-- TODO add value to input as current page -->
                    <input type="number" name="Page" class="form-control" id="numberInput" min="1" max='. $maxPages .'>
                </div>
                <button type="submit" form="pageForm" value="Submit" class="btn btn-custom">Go</button>
            </div>
        </form>
    ';
}

# echos one or two buttons to travel between pages
function NextBackButtons($pages, $parts, $amountOfRecords, $numRes) {
    if (isset($_GET['Page']) && $_GET['Page'] != '') {
        $pageNum = $_GET['Page'];
        if ($pageNum > 1) {
            $parts[sizeof($parts)-1] = 'Page='.($pageNum - 1);
            $lasturi = implode('&', $parts);
            echo '<a href=' . htmlspecialchars($lasturi) . ' class="previous round">&#8249</a>';
        }
        if ($pageNum < $pages && $pageNum != '') {
            $parts[sizeof($parts)-1] = 'Page='.($pageNum + 1);
            $nexturi = implode('&', $parts);
            echo '<a href=' . htmlspecialchars($nexturi) . ' class="next round">&#8250</a>';
        }
    } else {
        if ($amountOfRecords > $numRes){
            array_push($parts, 'Page=2');
            $nexturi = implode('&', $parts);
            echo '<a href=' . htmlspecialchars($nexturi) . ' class="next round">&#8250</a>';
        }
    }
}

# echos the footer html
function Footer(string $imgSrc) {
    echo '
        <!-- The footer for all pages -->
        <div id="footer" class="row no-gutters" style="margin-top: 15px;">
            <div class="col-sm-12 text-center">
                <div id="footer-section" style="padding: 15px 0px;">
                    <a href="https://beatymuseum.ubc.ca/">
                    <img src='.$imgSrc.' width = "300px" length = "150px" alt="Image for the Beaty Biodiversity Museum">
                    </a>
                </div>
            </div>
        </div>
    ';
}