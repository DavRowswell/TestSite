<!DOCTYTPE html>
<html>
<head>
    <?php
    require_once ('FileMaker.php');
    require_once ('partials/header.php');
    ?>
    <style>
        body {
            background: #ffffff;
        }

        h2 {
            font-size: 1.7em;
        }
        .btn-beaty {
            color: #fff;
            background-color: #CC232A;
            border-color: #CC232A;
        }
        #someid {
            height: 105px;
            width: 100px;
        }

        figure {
            text-align: center;
        }

        #herbarium-title, #vertebrate-title, #invertebrate-title, #bone-title {
            text-align: center;
        }

        figcaption {
            background: #3e3e3f;
            color: #ffffff;
        }

        a:link, a:visited, a:hover, a:active {
            text-decoration: none;
        }
        
        #main a {
            width: 100%;
        }

        a:hover figcaption {
            background: #000000;
        }

        .herbarium-databases figure {
            background: #3c8a2e;
            margin-bottom: 15px;
        }

        .tetrapod-databases figure {
            background: #70382d;
            margin-bottom: 15px;
        }

        .fish-databases figure {
            background: #165788;
            margin-bottom: 15px;
        }

        .entomology-databases figure {
            background: #824bb0;
            margin-bottom: 15px;
        }

        .marine-databases figure {
            background: #ffb652;
            margin-bottom: 15px;
        }

        .bone-databases figure {
            background: #bd3632;
            margin-bottom: 15px;
        }

        figcaption {
            padding: 2px;
        }

        figure img {
            margin: 8px;
        }

        h1{
            background: #3e3e3f;
            color:  #ffffff;
            padding: 15px;
            margin-bottom:0px;
        }

        h2{
            background: #3e3e3f;
            color:  #ffffff;
            margin: 0px;
        }

        #column1, #column2, #column3{
            margin-right: 0px
        }

    </style>
</head>
<body class="d-flex flex-column">
    <?php require_once ('partials/navbar.php'); ?>
    <div id="main" class="container-fluid">
        <div id="main-title" class="row">
            <div class="col">
                <h1>Database List</h1>
            </div>
        </div>
        <div id="title-divider"></div>
        <div id="collection-titles" class="row no-gutters">
            <div class="col-sm-3 text-center">
                <h2 id = "herbarium"><b>Herbarium</b></h2>
            </div>
            <div class="col-sm-3 text-center">
                <h2 id= "vertebrate"> <b>Vertebrate</b> </h2>
            </div>
            <div class="col-sm-3 text-center">
                <h2 id = "invertebrate"> <b>Invertebrate</b> </h2>
            </div>
            <div class="col-sm-3 text-center">
                <h2 id = "bone"> <b>Fossil</b> </h2>
            </div>
        </div>
        <div id="column-divider"></div>
        <div class="row">
            <div id="column-1" class = "col-sm-3"> 
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
            <div id="column-2" class = "col-sm-3">
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
            <div id="column-3" class = "col-sm-3">
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
            <div id="column-4" class = "col-sm-3">
                <div class="row bone-databases">
                    <div class="col d-flex justify-content-center">
                        <a href="search.php?Database=fossil"><figure><img class="img-fluid" id= "someid" src ="images/fossils.png"><figcaption style="text-align:center;"><h4>Fossils</h4></figcaption></figure></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require_once("partials/footer.php");?>
</body>
</html>