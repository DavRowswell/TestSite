<?php
require_once ('FileMaker.php');
require_once ('partials/header.php');
require_once ('functions.php');
require_once ('db.php');
?>
<head>
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
    </style>
</head>

<body> 
    <h1> <b>Home Page </b> </h1>
    <h2 id = "herbarium"> <b>Herbarium Databases</b> </h2>
    <hr align = "left">
    <a role="button" class="btn btn-danger d-flex" href="search.php?Database=algae">Algae</a>
    <a role="button" class="btn btn-danger" href="search.php?Database=bryophytes">Bryophytes</a>
    <a role="button" class="btn btn-danger" href="search.php?Database=fungi">Fungi</a>
    <a role="button" class="btn btn-danger" href="search.php?Database=lichen">Lichen</a>
    <a role="button" class="btn btn-danger" href="search.php?Database=vwsp">Vascular</a>

    <h2 id= "vertebrate"> <b>Vertebrate Databases</b> </h2>
    <hr align = "left">
    <a role="button" class="btn btn-danger" href="search.php?Database=avian">Avian</a>
    <a role="button" class="btn btn-danger" href="search.php?Database=herpetology">Herpetology</a>
    <a role="button" class="btn btn-danger" href="search.php?Database=mammal">Mammals</a>

    <h2 id = "invertebrate"> <b>Invertebrate Databases</b> </h2>
    <hr align = "left">
    <a role="button" class="btn btn-danger" href="search.php?Database=miw">Wet Marine Invertebrates</a>
    <a role="button" class="btn btn-danger" href="search.php?Database=mi">Dry Marine Invertebrates</a>

    <h2 id = "bone"> <b>Bone Databases</b> </h2>
    <hr align = "left">
    <a role="button" class="btn btn-danger" href="search.php?Database=fish">Fish</a>
    <a role="button" class="btn btn-danger" href="search.php?Database=fossil">Fossils</a>
</body>