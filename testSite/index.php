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
            width: 80%;
        }

        h2 {
            margin-top: 50px;

        }

        body {
            padding-bottom: 100px;
        }

        a[role = "button"] {
            width: 200px;
            height: 50px;
            margin-right: 10px; 
            line-height: 2.5;
            vertical-align: middle;
            font-size: 14;
        }

        #main {
           /* margin-left: 2%;*/
        }

        .btn-beaty {
            color: #fff;
            background-color: #CC232A;
            border-color: #CC232A;
        }
        #someid {
           /* height: 20%;*/
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
        <div id="herbarium-title" class="row">
            <div class="col">
                <h2 id = "herbarium"><b>Herbarium Databases</b></h2>
                <hr align = "left">
            </div>
        </div>
        <div id="herbarium-databases" class="row">
            <div class="col-sm-1">
                <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=algae"><figure><img class="img-fluid" id= "someid" src ="images/herbarium.png"><figcaption style="text-align:center;">Algae</figcaption></figure></a>
            </div>
            <div class="col-sm-1">
                <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=bryophytes"><figure><img class="img-fluid" id= "someid" src ="images/herbarium.png"><figcaption style="text-align:center;">Bryophytes</figcaption></figure></a>
            </div>
            <div class="col-sm-1">
                <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=fungi"><figure><img class="img-fluid" id= "someid" src ="images/herbarium.png"><figcaption style="text-align:center;">Fungi</figcaption></figure></a>
            </div>
            <div class="col-sm-1">
                <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=lichen"><figure><img class="img-fluid" id= "someid" src ="images/herbarium.png"><figcaption style="text-align:center;">Lichen</figcaption></figure></a>
            </div>
            <div class="col-sm-1">
                <a href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=vwsp"><figure><img class="img-fluid" id= "someid" src ="images/herbarium.png"><figcaption style="text-align:center;">Vascular</figcaption></figure></a>
            </div>
        </div>
        <div id="vertabrae-title" class="row">
            <div class="col">
                <h2 id= "vertebrate"> <b>Vertebrate Databases</b> </h2>
                <hr align = "left">
            </div>
        </div>
        <div id="vertebrate-databases" class="row">
            <div class="col-sm-1">
                <a href="search.php?Database=avian"><figure><img class="img-fluid" id= "someid" src ="images/tetrapods.png"><figcaption style="text-align:center;">Avian</figcaption></figure></a>
            </div>
            <div class="col-sm-1">
                <a href="search.php?Database=herpetology"><figure><img class="img-fluid" id= "someid" src ="images/tetrapods.png"><figcaption style="text-align:center;">Herpetology</figcaption></figure></a>
            </div>
            <div class="col-sm-1">
                <a href="search.php?Database=mammal"><figure><img class="img-fluid" id= "someid" src ="images/tetrapods.png"><figcaption style="text-align:center;">Mammals</figcaption></figure></a>
            </div>
            <div class="col-sm-1">
                <a href="search.php?Database=fish"><figure><img class="img-fluid" id= "someid" src ="images/fish.png"><figcaption style="text-align:center;">Fish</figcaption></figure></a>
            </div>
        </div>
        <div id="invertebrate-title" class="row">
            <div class="col">
                <h2 id = "invertebrate"> <b>Invertebrate Databases</b> </h2>
                <hr align = "left">
            </div>
        </div>
        <div id="invertebrate-databases" class="row">
            <div class="col-sm-1">
                <a href="search.php?Database=entomology"><figure><img class="img-fluid" id= "someid" src ="images/entomology.png"><figcaption style="text-align:center;">Entomology</figcaption></figure></a>
            </div>
            <div class="col-sm-1">
                <a href="search.php?Database=miw"><figure><img class="img-fluid" id= "someid" src ="images/marine-invertebrates.png"><figcaption style="text-align:center;">Wet Marine Invertebrates</figcaption></figure></a>
            </div>
            <div class="col-sm-1">
                <a href="search.php?Database=mi"><figure><img class="img-fluid" id= "someid" src ="images/marine-invertebrates.png"><figcaption style="text-align:center;">Dry Marine Invertebrates</figcaption></figure></a>
            </div>
        </div>
        <div id="bone-title" class="row">
            <div class="col">
                <h2 id = "bone"> <b>Fossil Database</b> </h2>
                <hr align = "left">
            </div>
        </div>
        <div id="bone-databases">
            <div class="col-sm-1">
                <a href="search.php?Database=fossil"><figure><img class="img-fluid" id= "someid" src ="images/fossils.png"><figcaption style="text-align:center;">Fossils</figcaption></figure></a>
            </div>
        </div>
    </div>
    <?php require_once("partials/footer.php");?>
</body>
</html>