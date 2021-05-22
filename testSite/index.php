<!DOCTYPE html>
<html lang="en">

    <head>
        <?php
        // TODO check if we really need this in index.php and maybe just wait for search.php
        //Necessary for database connection to Filemaker
        require_once ('FileMaker.php');

        //Contains all the information for the head of every page
        require_once ('partials/header.php');

        // get the widgets
        require_once ('partials/widgets.php');

        ?>

        <!---Link to the CSS for this page--->
        <link rel="stylesheet" href="css/index.css">
    </head>

    <body class="container-fluid no-padding">
        <!--- Contains the navbar on the top of every page--->
        <?php require_once ('partials/navbar.php'); ?>

        <!--- Div for the main content of the page--->
        <div id="main">
            <!--- The main title of the page under the navbar--->
            <div class="container-fluid no-padding">
                <h1>Database List</h1>
            </div>

            <!--- Row for all the content--->
            <div class="row no-gutters">

                <!--- First column of databases--->
                <div class="col-sm-3">

                    <!--- Herbarium title section--->
                    <div class="text-center title-box">
                        <h2><b>Herbarium</b></h2>
                    </div>

                    <!--- Herbarium links and content--->
                    <div class="column-body">
                        <?php
                             DatabaseCard(
                                title: 'Algae',
                                img_source: 'images/algae.png',
                                href: 'https://herbweb.botany.ubc.ca/herbarium/search.php?Database=algae',
                                background_color: '#3c8a2e',
                            );

                             DatabaseCard(
                                title: 'Bryophytes',
                                img_source: 'images/bryophytes.png',
                                href: 'https://herbweb.botany.ubc.ca/herbarium/search.php?Database=bryophytes',
                                background_color: '#3c8a2e',
                            );

                             DatabaseCard(
                                title: 'Fungi',
                                img_source: 'images/fungi.png',
                                href: 'https://herbweb.botany.ubc.ca/herbarium/search.php?Database=fungi',
                                background_color: '#3c8a2e',
                            );

                             DatabaseCard(
                                title: 'Lichen',
                                img_source: 'images/lichen.png',
                                href: 'https://herbweb.botany.ubc.ca/herbarium/search.php?Database=lichen',
                                background_color: '#3c8a2e',
                            );

                             DatabaseCard(
                                title: 'Vascular',
                                img_source: 'images/herbarium.png',
                                href: 'https://herbweb.botany.ubc.ca/herbarium/search.php?Database=vwsp',
                                background_color: '#3c8a2e',
                            )
                        ?>
                    </div>
                </div>

                <!--- Vertebrate column of content--->
                <div class="col-sm-3">

                    <!--- Vertebrate Title Section--->
                    <div class="text-center title-box">
                        <h2><b>Vertebrate</b></h2>
                    </div>

                    <!--- Vertebrate image and link content--->
                    <div class="column-body">
                        <?php
                             DatabaseCard(
                                title: 'Avian',
                                img_source: 'images/tetrapods.png',
                                href: 'search.php?Database=avian',
                                background_color: '#70382d',
                            );

                             DatabaseCard(
                                title: 'Herpetology',
                                img_source: 'images/herptology.png',
                                href: 'search.php?Database=herpetology',
                                background_color: '#70382d',
                            );

                             DatabaseCard(
                                title: 'Mammals',
                                img_source: 'images/mammal.png',
                                href: 'search.php?Database=mammal',
                                background_color: '#70382d',
                            );

                             DatabaseCard(
                                title: 'Fish',
                                img_source: 'images/fish.png',
                                href: 'search.php?Database=fish',
                                background_color: '#165788',
                            );
                        ?>
                    </div>
                </div>

                <!--- Invertebrate content and title--->
                <div class="col-sm-3">

                    <!--- Invertebrate title section--->
                    <div class="text-center title-box">
                        <h2><b>Invertebrate</b></h2>
                    </div>

                    <!--- Invertebrate column content--->
                    <div class="column-body">
                        <?php
                             DatabaseCard(
                                title: 'Entomology',
                                img_source: 'images/entomology.png',
                                href: 'search.php?Database=entomology',
                                background_color: '#824bb0',
                            );

                             DatabaseCard(
                                title: 'Dry Marine Invertebrates',
                                img_source: 'images/marine-invertebrates-dry.png',
                                href: 'search.php?Database=mi',
                                background_color: '#ffb652',
                            );

                             DatabaseCard(
                                title: 'Wet Marine Invertebrates',
                                img_source: 'images/marine-invertebrates-wet.png',
                                href: 'search.php?Database=miw',
                                background_color: '#ffb652',
                            );

                        ?>
                    </div>
                </div>

                <!--- Fossil Column--->
                <div class="col-sm-3">

                    <!--- Fossil title row--->
                    <div class="text-center title-box">
                        <h2><b>Fossil</b></h2>
                    </div>

                    <!--- Fossil Content--->
                    <div class="column-body">
                        <?php
                             DatabaseCard(
                                title: 'Fossils',
                                img_source: 'images/fossils.png',
                                href: 'search.php?Database=fossil',
                                background_color: '#bd3632',
                            );
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!--- Code for the footer on each page--->
        <?php Footer(imgSrc: 'images/beatyLogo.png'); ?>
    </body>
</html>