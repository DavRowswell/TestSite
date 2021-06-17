<?php

/**
 * UI or header widgets used in HTML files.
 * @package Widgets
 */

/**
 * A title banner is a row with a title and a background color
 * @param string $databaseName
 */
function TitleBanner(string $databaseName) {
    if($databaseName === "mi" || $databaseName === "miw" || $databaseName === "vwsp") {
        if ($databaseName === "mi") { $title = "Dry Marine Invertebrate"; }
        else if ($databaseName === "vwsp") { $title = "Vascular"; }
        else { $title = "Wet Marine Invertebrate"; }
    } else {
        $title = ucfirst($databaseName);
    }

    echo '
            <div class="container-fluid p-0">
                  <h1>
                      <b>
                        '. $title .' Search
                      </b>
                  </h1>
            </div>
        
        ';
}

/**
 * A card widget is a actionable card to represent a database in the main catalog
 *
 * @param string $title
 * @param string $img_source
 * @param string $href
 * @param string $background_color
 * @param string $alt
 */
function DatabaseCard(string $title, string $img_source, string $href, string $background_color, string $alt='') {
    echo '
        <!--- '. $title .' image and link--->
        <div class="justify-content-center">
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

/**
 * A page controller has widgets to move around all the possible pages for a table
 *
 * @param $maxResponses
 * @param $result
 */
function TableControllerWidget($maxResponses, $result) {
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

/**
 * Echos one or two buttons to travel between pages
 *
 * @param $pages
 * @param $parts
 * @param $amountOfRecords
 * @param $numRes
 */
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

/**
 * Common footer html
 *
 * @param string $imgSrc
 */
function FooterWidget(string $imgSrc) {
    echo '
        <div class="container-fluid text-center p-0 red-background">
            <a href="https://beatymuseum.ubc.ca/" role="button">
                <img src='.$imgSrc.' width="300px" alt="Image for the Beaty Biodiversity Museum" class="m-4">
            </a>
        </div>
    ';
}

/**
 * Echos the header html, title will come after 'BBM Database'
 *
 * @param string $title
 */
function HeaderWidget(string $title = '') {

        echo '
        <title>BBM Database '. $title .'</title>

        <!-- meta -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- css stylesheets -->
        <link rel="stylesheet" type="text/css" href="public/css/open-iconic-master/font/css/open-iconic-bootstrap.css">
        <link rel="stylesheet" type="text/css" href="public/css/normalize.css">
        <link rel="stylesheet" type="text/css" href="public/bootstrap-v5/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="public/css/common.css">
        
        <!-- js scripts -->
        <script src="public/bootstrap-v5/js/bootstrap.bundle.min.js"></script>
    ';
}

/**
 * The navigation bar widget.
 * Must be used in a file on the main folder.
 */
function Navbar() {
    echo '
        <!-- The navigation bar for all pages. Contains dropdowns with links to all possible databases -->
        <nav class="navbar navbar-expand-lg navbar-dark red-background">
            
            <!-- BMD title sends to main page -->
            <a class="navbar-brand" href="index.php"><h3>Beaty Museum Databases</h3></a>
        
            <!-- Used for collapse support -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        
            <!-- Databases navbar dropdown row -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav">
        
                    <!-- Herbarium -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button">Herbarium</a>
                        <ul class="dropdown-menu">
                            <a class="dropdown-item" href="search.php?Database=algae">Algae</a>
                            <a class="dropdown-item" href="search.php?Database=bryophytes">Bryophytes</a>
                            <a class="dropdown-item" href="search.php?Database=fungi">Fungi</a>
                            <a class="dropdown-item" href="search.php?Database=lichen">Lichen</a>
                            <a class="dropdown-item" href="search.php?Database=vwsp">Vascular Plants</a>
                        </ul>
                    </li>
        
                    <!-- Vertebrates -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button">Vertebrates</a>
                        <ul class="dropdown-menu">
                            <a class="dropdown-item" href="search.php?Database=avian">Avian</a>
                            <a class="dropdown-item" href="search.php?Database=herpetology">Herpetology</a>
                            <a class="dropdown-item" href="search.php?Database=mammal">Mammal</a>
                            <a class="dropdown-item" href="search.php?Database=fish">Fish</a>
                        </ul>
                    </li>
        
                    <!-- Invertebrates -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button">Invertebrates</a>
                        <ul class="dropdown-menu">
                            <a class="dropdown-item" href="search.php?Database=entomology">Entomology</a>
                            <a class="dropdown-item" href="search.php?Database=miw"> Wet Marine Invertebrates</a>
                            <a class="dropdown-item" href="search.php?Database=mi">Dry Marine Invertebrates</a>
                        </ul>
                    </li>
        
                    <!-- Fossils -->
                    <li class="nav-item">
                        <a class="nav-link" href="search.php?Database=fossil">Fossils</a>
                    </li>
                </ul>
            </div>
        </nav>
    ';
}