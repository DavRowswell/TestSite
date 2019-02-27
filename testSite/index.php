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
            margin-left: 2%;
        }

        .btn-beaty {
            color: #fff;
            background-color: #CC232A;
            border-color: #CC232A;
        }
    </style>
</head>
<body class="container-fluid">
    <?php require_once ('partials/navbar.php'); ?>
    <div id="main">
        <h1 style="position:relative; top:16px"> <b>Home Page </b> </h1>
        <div id="herbarium-databases">
        <h2 id = "herbarium"> <b>Herbarium Databases</b> </h2>
        <hr align = "left">
        <div id="herbarium-links">
        <a role="button" class="btn btn-danger" href="http://herbweb.botany.ubc.ca/testSite/search.php?Database=algae">Algae</a>
        <a role="button" class="btn btn-danger" href="http://herbweb.botany.ubc.ca/testSite/search.php?Database=bryophytes">Bryophytes</a>
        <a role="button" class="btn btn-danger" href="http://herbweb.botany.ubc.ca/testSite/search.php?Database=fungi">Fungi</a>
        <a role="button" class="btn btn-danger" href="http://herbweb.botany.ubc.ca/testSite/search.php?Database=lichen">Lichen</a>
        <a role="button" class="btn btn-danger" href="http://herbweb.botany.ubc.ca/testSite/search.php?Database=vwsp">Vascular</a>
        </div>
        </div>
        <div id="vertebrate-databases">
        <h2 id= "vertebrate"> <b>Vertebrate Databases</b> </h2>
        <hr align = "left">
        <div id="vertebrate-links">
        <a role="button" class="btn btn-danger" href="search.php?Database=avian">Avian</a>
        <a role="button" class="btn btn-danger" href="search.php?Database=herpetology">Herpetology</a>
        <a role="button" class="btn btn-danger" href="search.php?Database=mammal">Mammals</a>
        <a role="button" class="btn btn-danger" href="search.php?Database=fish">Fish</a>
        </div>
        </div>
        <div id="invertebrate-databases">
        <h2 id = "invertebrate"> <b>Invertebrate Databases</b> </h2>
        <hr align = "left">
        <div id="invertebrate-links">
        <a role="button" class="btn btn-danger" href="search.php?Database=miw">Wet Marine Invertebrates</a>
        <a role="button" class="btn btn-danger" href="search.php?Database=mi">Dry Marine Invertebrates</a>
        </div>
        </div>
        <div id="bone-databases">
        <h2 id = "bone"> <b>Fossil Database</b> </h2>
        <hr align = "left">
        <div id="bone-links">
        <a role="button" class="btn btn-danger" href="search.php?Database=fossil">Fossils</a>
        </div>
        </div>
    </div>
    <?php require_once("partials/footer.php");?>
</body>
</html>