<?php

/**
 * UI or header widgets used in HTML files.
 * @package Widgets
 */

require_once ('utilities.php');

/**
 * A title banner is a row with a title and a background color
 * @param string $databaseName
 * @param int $paddingIndex
 */
function TitleBannerSearch(string $databaseName, int $paddingIndex = 2) {
    echo "
            <div class='container-fluid p-$paddingIndex conditional-background text-center'>
                  <h1>
                      <b> Welcome to the $databaseName Collection </b>
                  </h1>
            </div>
        ";
}
function TitleBannerRender(string $databaseName, int $recordNumber) {
    echo "
        <div class='container-fluid p-2 conditional-background text-center'>
            <h2>
            <b>Your $databaseName Collection search found $recordNumber records!</b>
            </h2>
        </div>
    ";
}
function TitleBannerDetail(string $databaseName, string $accessionNumber, string $backHref) {
    echo "
        <div class='container-fluid p-2 conditional-background row g-0'>
            <div class='col-1'>
                <a href='$backHref' class='btn conditional-background-light-no-hover'>Back</a>
            </div>
            
            <div class='col text-center'>
                <h2>
                    <b>$databaseName Collection Information for Specimen $accessionNumber</b>
                </h2>
            </div>
            <div class='col-1'>
                
            </div>
        </div>
    ";
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
            <a href='. $href .' role="button" class="databaseCard">
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
    $noPageUri = removeUrlVar($_SERVER['REQUEST_URI'], 'Page');

    $amountOfRecords = $result->getFoundSetCount();

    # amount of pages available
    $maxPages = ceil($amountOfRecords / $maxResponses);

    # get current page number
    $page = 1;
    if (isset($_GET['Page']) && $_GET['Page'] != '') {
        $page = $_GET['Page'];
    }

    $pageInfo = "$amountOfRecords records found in page ".htmlspecialchars($page)." / ".htmlspecialchars($maxPages);

    echoPaginationButtons($page, $noPageUri, $maxPages);
    echo "
        <div class='form-text'>
            $pageInfo
        </div>
    ";
}
/**
 * Will clean out a url from a variable using regex.
 * Kudos to https://stackoverflow.com/questions/1251582/beautiful-way-to-remove-get-variables-with-php
 * @param string $url full url to remove var from
 * @param string $varname url var name to remove
 * @return string
 */
function removeUrlVar(string $url, string $varname): string
{
    return preg_replace('/([?&])'.$varname.'=[^&]+(&|$)/','',$url);
}

/**
 * @param int $page current page index
 * @param string $noPageUri uri without Page field
 * @param float $maxPages maximum pages possible
 */
function echoPaginationButtons(int $page, string $noPageUri, float $maxPages): void
{
    $paginationOptions = array(
        $page - 10, $page - 5, $page - 2, $page - 1, $page,
        $page + 1, $page + 2, $page + 5, $page + 10
    );

    $paginationUrls = array_map(function ($pageNum) use ($noPageUri) {
        return $noPageUri . '&Page=' . $pageNum;
    }, $paginationOptions);

    $paginationData = array_combine($paginationOptions, $paginationUrls);

    $pageUrlBack = $noPageUri . '&Page=' . $page - 1;
    $pageUrlForward = $noPageUri . '&Page=' . $page + 1;

    echo " <ul class='pagination'> ";

    # back button
    if ($page - 1 <= 0)
        echo "<li class='page-item disabled'>
                    <a class='page-link conditional-text-color' href='$pageUrlBack'><span>&laquo;</span></a>
                </li>";
    else
        echo "<li class='page-item'>
                    <a class='page-link conditional-text-color' href='$pageUrlBack'><span>&laquo;</span></a>
                </li>";

    # each numbered pagination button
    foreach ($paginationData as $pageNumber => $pageUrl) {
        if ($pageNumber == $page)
            echo "<li class='page-item active'><a class='page-link conditional-text-color' href='$pageUrl'>$pageNumber</a></li>";
        else if ($pageNumber <= 0 or $pageNumber > $maxPages)
            echo "<li class='page-item disabled'><a class='page-link conditional-text-color' href='$pageUrl'>$pageNumber</a></li>";
        else
            echo "<li class='page-item'><a class='page-link conditional-text-color' href='$pageUrl'>$pageNumber</a></li>";
    }

    # forward button
    if ($page + 1 > $maxPages)
        echo "<li class='page-item disabled'>
                    <a class='page-link conditional-text-color' href='$pageUrlForward'><span>&raquo;</span></a>
                </li>
            </ul>";
    else
        echo "<li class='page-item'>
                    <a class='page-link conditional-text-color' href='$pageUrlForward'><span>&raquo;</span></a>
                </li>
            </ul>";
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
        
        <!-- icon kudos to https://stackoverflow.com/questions/1344122/favicon-png-vs-favicon-ico-why-should-i-use-png-instead-of-ico -->
        <link rel="shortcut icon" href="public/images/favicon.ico">
        
        <!-- css stylesheets -->
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
            <a class="navbar-brand px-3" href="index.php"><h2>Beaty Museum Databases</h2></a>
        
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