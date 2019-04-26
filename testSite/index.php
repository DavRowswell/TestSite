<!DOCTYTPE html>
<html>
<head>
    <?php
    //Necessary for database connection to Filemaker
    require_once ('FileMaker.php');
    //Contains all the information for the head of every page
    require_once ('partials/header.php');
    ?>
    <!---Link to the CSS for this page--->
    <link rel="stylesheet" href="css/indexcss.css">
</head>
<body class="d-flex flex-column">
    <!--- Contains the navbar on the top of every page--->
    <?php require_once ('partials/navbar.php'); ?>
    <!--- Div for the main content of the page--->
    <div id="main">
        <!--- The main title of the page under the navbar--->
        <div id="main-title" class="row">
            <div class="col">
                <h1>Database List</h1>
            </div>
        </div>
        <!--- Row for all the content--->
        <div class="row no-gutters">
            <!--- First column of databases--->
            <div id="column-1" class = "col-sm-3">
                <!--- Herbarium title section--->
                <div class="row no-gutters">
                    <div class="col-sm-12">
                        <!---Black line to separate sub title from main title--->
                        <div id="title-divider"></div>
                        <!--- Div for the subtitle itself--->
                        <div id="collection-titles" class="row no-gutters">
                            <div class="col-sm-12 text-center">
                                <h2 id = "herbarium"><b>Herbarium</b></h2>
                            </div>
                        </div>
                        <!--- Black line to separate sub title from content--->
                        <div id="column-divider"></div>
                    </div>
                </div>
                <!--- Herbarium links and content--->
                <div class="row no-gutters column-body">
                    <div class="col-sm-12">
                        <!--- Algae image and link--->
                        <div class="row herbarium-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=algae"><figure><img class="img-fluid" id= "someid" src ="images/algae.png"><figcaption style="text-align:center;"><h4>Algae</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <!--- Bryophytes image and link--->
                        <div class="row herbarium-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=bryophytes"><figure><img class="img-fluid" id= "someid" src ="images/bryophytes.png"><figcaption style="text-align:center;"><h4>Bryophytes</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <!--- Fungi image and link--->
                        <div class="row herbarium-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=fungi"><figure><img class="img-fluid" id= "someid" src ="images/fungi.png"><figcaption style="text-align:center;"><h4>Fungi</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <!--- Lichen image and link--->
                        <div class="row herbarium-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=lichen"><figure><img class="img-fluid" id= "someid" src ="images/lichen.png"><figcaption style="text-align:center;"><h4>Lichen</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <!--- Vascular image and link--->
                        <div class="row herbarium-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=vwsp"><figure><img class="img-fluid" id= "someid" src ="images/herbarium.png"><figcaption style="text-align:center;"><h4>Vascular</h4></figcaption></figure></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--- Vertebrate column of content--->
            <div id="column-2" class = "col-sm-3">
                <!--- Vertebrate Title Section--->
                <div class="row no-gutters">
                    <div class="col-sm-12">
                        <!--- Black line between title and subtitle--->
                        <div id="title-divider"></div>
                        <!--- Vertebrate title section--->
                        <div id="collection-titles" class="row no-gutters">
                            <div class="col-sm-12 text-center">
                                <h2 id= "vertebrate"> <b>Vertebrate</b> </h2>
                            </div>
                        </div>
                        <!--- Black line to separate subtitle from content--->
                        <div id="column-divider"></div>
                    </div>
                </div>
                <!--- Vertebrate image and link content--->
                <div class="row no-gutters column-body">
                    <div class="col-sm-12">
                        <!--- Avian Image and Link--->
                        <div class="row tetrapod-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="search.php?Database=avian"><figure><img class="img-fluid" id= "someid" src ="images/tetrapods.png"><figcaption style="text-align:center;"><h4>Avian</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <!--- Herptology Image and Link--->
                        <div class="row tetrapod-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="search.php?Database=herpetology"><figure><img class="img-fluid" id= "someid" src ="images/herptology.png"><figcaption style="text-align:center;"><h4>Herpetology</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <!--- Mammal Image and Link--->
                        <div class="row tetrapod-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="search.php?Database=mammal"><figure><img class="img-fluid" id= "someid" src ="images/mammal.png"><figcaption style="text-align:center;"><h4>Mammals</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <!--- Fish Image and Link--->
                        <div class="row fish-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="search.php?Database=fish"><figure><img class="img-fluid" id= "someid" src ="images/fish.png"><figcaption style="text-align:center;"><h4>Fish</h4></figcaption></figure></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--- Invertebrate content and title--->
            <div id="column-3" class = "col-sm-3">
                <!--- Invertebrate title section--->
                <div class="row no-gutters">
                    <div class="col-sm-12">
                        <!--- Black line to separate sub title form main title--->
                        <div id="title-divider"></div>
                        <!--- Invertebrate title content--->
                        <div id="collection-titles" class="row no-gutters">
                            <div class="col-sm-12 text-center">
                                <h2 id = "invertebrate"> <b>Invertebrate</b> </h2>
                            </div>
                        </div>
                        <!--- Black line to separate sub title from content--->
                        <div id="column-divider"></div>
                    </div>
                </div>
                <!--- Invertebrate column content--->
                <div class="row no-gutters column-body">
                    <div class="col-sm-12">
                        <!--- Entomology Image and Link--->
                        <div class="row entomology-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="search.php?Database=entomology"><figure><img class="img-fluid" id= "someid" src ="images/entomology.png"><figcaption style="text-align:center;"><h4>Entomology</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <!--- Dry Marine Invertebrate Image and Link--->
                        <div class="row marine-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="search.php?Database=mi"><figure><img class="img-fluid" id= "someid" src ="images/marine-invertebrates-dry.png"><figcaption style="text-align:center;"><h4>Dry Marine Invertebrates</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <!--- Wet Marine Invertebrate Image and Link--->
                        <div class="row marine-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="search.php?Database=miw"><figure><img class="img-fluid" id= "someid" src ="images/marine-invertebrates-wet.png"><figcaption style="text-align:center;"><h4>Wet Marine Invertebrates</h4></figcaption></figure></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--- Fossil Column--->
            <div id="column-4" class = "col-sm-3">
                <!--- Fossil title row--->
                <div class="row no-gutters">
                    <div class="col-sm-12">
                        <!--- Black line to separte subtitle from main title--->
                        <div id="title-divider"></div>
                        <!--- Fossil subtitle content--->
                        <div id="collection-titles" class="row no-gutters">
                            <div class="col-sm-12 text-center">
                                <h2 id = "bone"> <b>Fossil</b> </h2>
                            </div>
                        </div>
                        <!--- Black line to separate subtitle from content--->
                        <div id="column-divider"></div>
                    </div>
                </div>
                <!--- Fossil Content--->
                <div class="row no-gutters column-body">
                    <div class="col-sm-12">
                        <!--- Fossil Image and Link--->
                        <div class="row bone-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="search.php?Database=fossil"><figure><img class="img-fluid" id= "someid" src ="images/fossils.png"><figcaption style="text-align:center;"><h4>Fossils</h4></figcaption></figure></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--- Code for the footer on each page--->
    <?php require_once("partials/footer.php");?>
</body>
</html>