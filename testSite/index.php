<!DOCTYTPE html>
<html>
<head>
    <?php
    require_once ('FileMaker.php');
    require_once ('partials/header.php');
    ?>
    <style>
        hr {
            border: 1px solid grey;
            width: 100%;
        }

        h2 {
            font-size: 1.7em;
        }

        a[role = "button"] {
            width: 200px;
            height: 50px;
            margin-right: 10px; 
            line-height: 2.5;
            vertical-align: middle;
            font-size: 14;
        }

        .btn-beaty {
            color: #fff;
            background-color: #CC232A;
            border-color: #CC232A;
        }
        #someid {
            height: 20%;
        }
    </style>
</head>
<body class="container-fluid">
    <?php require_once ('partials/navbar.php'); ?>
    <div id="main">
        <div id="main-title" class="row">
            <div class="col">
                <h1><b>Database List</b></h1>
            </div>
        </div>
        <div class="row">
            <div id="column-1" class = "col-sm-3"> 
                <div id="herbarium-title" class="row">
                    <div class="col">
                        <h2 id = "herbarium"><b>Herbarium Databases</b></h2>
                        <hr align = "left">
                    </div>
                </div>
                <div class="row herbarium-databases">
                    <div class="col d-flex justify-content-center">
                        <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=algae"><figure><img class="img-fluid" id= "someid" src ="images/algae.png"><figcaption style="text-align:center;">Algae</figcaption></figure></a>
                    </div>
                </div>
                <div class="row herbarium-databases">
                    <div class="col d-flex justify-content-center">
                        <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=bryophytes"><figure><img class="img-fluid" id= "someid" src ="images/bryophytes.png"><figcaption style="text-align:center;">Bryophytes</figcaption></figure></a>
                    </div>
                </div>
                <div class="row herbarium-databases">
                    <div class="col d-flex justify-content-center">
                        <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=fungi"><figure><img class="img-fluid" id= "someid" src ="images/fungi.png"><figcaption style="text-align:center;">Fungi</figcaption></figure></a>
                    </div>
                </div>
                <div class="row herbarium-databases">
                    <div class="col d-flex justify-content-center">
                        <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=lichen"><figure><img class="img-fluid" id= "someid" src ="images/lichen.png"><figcaption style="text-align:center;">Lichen</figcaption></figure></a>
                    </div>
                </div>
                <div class="row herbarium-databases">
                    <div class="col d-flex justify-content-center">
                        <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=vwsp"><figure><img class="img-fluid" id= "someid" src ="images/herbarium.png"><figcaption style="text-align:center;">Vascular</figcaption></figure></a>
                    </div>
                </div>
            </div>
            <div id="column-2" class = "col-sm-3">
                <div id="vertabrate-title" class="row">
                    <div class="col">
                        <h2 id= "vertebrate"> <b>Vertebrate Databases</b> </h2>
                        <hr align = "left">
                    </div>
                </div>
                <div class="row vertebrate-databases">
                    <div class="col d-flex justify-content-center">
                        <a href="search.php?Database=avian"><figure><img class="img-fluid" id= "someid" src ="images/tetrapods.png"><figcaption style="text-align:center;">Avian</figcaption></figure></a>
                    </div>
                </div>
                <div class="row vertebrate-databases">
                    <div class="col d-flex justify-content-center">
                        <a href="search.php?Database=herpetology"><figure><img class="img-fluid" id= "someid" src ="images/herptology.png"><figcaption style="text-align:center;">Herpetology</figcaption></figure></a>
                    </div>
                </div>
                <div class="row vertebrate-databases">
                    <div class="col d-flex justify-content-center">
                        <a href="search.php?Database=mammal"><figure><img class="img-fluid" id= "someid" src ="images/mammal.png"><figcaption style="text-align:center;">Mammals</figcaption></figure></a>
                    </div>
                </div>
                <div class="row vertebrate-databases">
                    <div class="col d-flex justify-content-center">
                        <a href="search.php?Database=fish"><figure><img class="img-fluid" id= "someid" src ="images/fish.png"><figcaption style="text-align:center;">Fish</figcaption></figure></a>
                    </div>
                </div>
            </div>
            <div id="column-3" class = "col-sm-3">
                <div id="invertebrate-title" class="row">
                    <div class="col">
                        <h2 id = "invertebrate"> <b>Invertebrate Databases</b> </h2>
                        <hr align = "left">
                    </div>
                </div>
                <div class="row invertebrate-databases">
                    <div class="col d-flex justify-content-center">
                        <a href="search.php?Database=entomology"><figure><img class="img-fluid" id= "someid" src ="images/entomology.png"><figcaption style="text-align:center;">Entomology</figcaption></figure></a>
                    </div>
                </div>
                <div class="row invertebrate-databases">
                    <div class="col d-flex justify-content-center">
                        <a href="search.php?Database=mi"><figure><img class="img-fluid" id= "someid" src ="images/marine-invertebrates-dry.png"><figcaption style="text-align:center;">Dry Marine Invertebrates</figcaption></figure></a>
                    </div>
                </div>
                <div class="row invertebrate-databases">
                    <div class="col d-flex justify-content-center">
                        <a href="search.php?Database=miw"><figure><img class="img-fluid" id= "someid" src ="images/marine-invertebrates-wet.png"><figcaption style="text-align:center;">Wet Marine Invertebrates</figcaption></figure></a>
                    </div>
                </div>
            </div>
            <div id="column-4" class = "col-sm-3">
                <div id="bone-title" class="row">
                    <div class="col">
                        <h2 id = "bone"> <b>Fossil Database</b> </h2>
                        <hr align = "left">
                    </div>
                </div>
                <div id="bone-databases" class="row">
                    <div class="col d-flex justify-content-center">
                        <a href="search.php?Database=fossil"><figure><img class="img-fluid" id= "someid" src ="images/fossils.png"><figcaption style="text-align:center;">Fossils</figcaption></figure></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once("partials/footer.php");?>
</body>
</html>