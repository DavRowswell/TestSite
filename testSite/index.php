<!DOCTYTPE html>
<html>
<head>
    <?php
    require_once ('FileMaker.php');
    require_once ('partials/header.php');
    ?>
    <link rel="stylesheet" href="css/indexcss.css">
</head>
<body class="d-flex flex-column">
    <?php require_once ('partials/navbar.php'); ?>
    <div id="main">
        <div id="main-title" class="row">
            <div class="col">
                <h1>Database List</h1>
            </div>
        </div>
        <div class="row no-gutters">
            <div id="column-1" class = "col-sm-3">
                <div class="row no-gutters">
                    <div class="col-sm-12">
                        <div id="title-divider"></div>
                        <div id="collection-titles" class="row no-gutters">
                            <div class="col-sm-12 text-center">
                                <h2 id = "herbarium"><b>Herbarium</b></h2>
                            </div>
                        </div>
                        <div id="column-divider"></div>
                    </div>
                </div>
                <div class="row no-gutters column-body">
                    <div class="col-sm-12">
                        <div class="row herbarium-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=algae"><figure><img class="img-fluid" id= "someid" src ="images/algae.png"><figcaption style="text-align:center;"><h4>Algae</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <div class="row herbarium-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=bryophytes"><figure><img class="img-fluid" id= "someid" src ="images/bryophytes.png"><figcaption style="text-align:center;"><h4>Bryophytes</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <div class="row herbarium-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=fungi"><figure><img class="img-fluid" id= "someid" src ="images/fungi.png"><figcaption style="text-align:center;"><h4>Fungi</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <div class="row herbarium-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=lichen"><figure><img class="img-fluid" id= "someid" src ="images/lichen.png"><figcaption style="text-align:center;"><h4>Lichen</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <div class="row herbarium-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=vwsp"><figure><img class="img-fluid" id= "someid" src ="images/herbarium.png"><figcaption style="text-align:center;"><h4>Vascular</h4></figcaption></figure></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="column-2" class = "col-sm-3">
                <div class="row no-gutters">
                    <div class="col-sm-12">
                        <div id="title-divider"></div>
                        <div id="collection-titles" class="row no-gutters">
                            <div class="col-sm-12 text-center">
                                <h2 id= "vertebrate"> <b>Vertebrate</b> </h2>
                            </div>
                        </div>
                        <div id="column-divider"></div>
                    </div>
                </div>
                <div class="row no-gutters column-body">
                    <div class="col-sm-12">
                        <div class="row tetrapod-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="search.php?Database=avian"><figure><img class="img-fluid" id= "someid" src ="images/tetrapods.png"><figcaption style="text-align:center;"><h4>Avian</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <div class="row tetrapod-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="search.php?Database=herpetology"><figure><img class="img-fluid" id= "someid" src ="images/herptology.png"><figcaption style="text-align:center;"><h4>Herpetology</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <div class="row tetrapod-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="search.php?Database=mammal"><figure><img class="img-fluid" id= "someid" src ="images/mammal.png"><figcaption style="text-align:center;"><h4>Mammals</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <div class="row fish-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="search.php?Database=fish"><figure><img class="img-fluid" id= "someid" src ="images/fish.png"><figcaption style="text-align:center;"><h4>Fish</h4></figcaption></figure></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="column-3" class = "col-sm-3">
                <div class="row no-gutters">
                    <div class="col-sm-12">
                        <div id="title-divider"></div>
                        <div id="collection-titles" class="row no-gutters">
                            <div class="col-sm-12 text-center">
                                <h2 id = "invertebrate"> <b>Invertebrate</b> </h2>
                            </div>
                        </div>
                        <div id="column-divider"></div>
                    </div>
                </div>
                <div class="row no-gutters column-body">
                    <div class="col-sm-12">
                        <div class="row entomology-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="search.php?Database=entomology"><figure><img class="img-fluid" id= "someid" src ="images/entomology.png"><figcaption style="text-align:center;"><h4>Entomology</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <div class="row marine-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="search.php?Database=mi"><figure><img class="img-fluid" id= "someid" src ="images/marine-invertebrates-dry.png"><figcaption style="text-align:center;"><h4>Dry Marine Invertebrates</h4></figcaption></figure></a>
                            </div>
                        </div>
                        <div class="row marine-databases">
                            <div class="col d-flex justify-content-center">
                                <a href="search.php?Database=miw"><figure><img class="img-fluid" id= "someid" src ="images/marine-invertebrates-wet.png"><figcaption style="text-align:center;"><h4>Wet Marine Invertebrates</h4></figcaption></figure></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="column-4" class = "col-sm-3">
                <div class="row no-gutters">
                    <div class="col-sm-12">
                        <div id="title-divider"></div>
                        <div id="collection-titles" class="row no-gutters">
                            <div class="col-sm-12 text-center">
                                <h2 id = "bone"> <b>Fossil</b> </h2>
                            </div>
                        </div>
                        <div id="column-divider"></div>
                    </div>
                </div>
                <div class="row no-gutters column-body">
                    <div class="col-sm-12">
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
    <?php require_once("partials/footer.php");?>
</body>
</html>