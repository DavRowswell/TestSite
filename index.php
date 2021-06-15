<!DOCTYPE html>
<html lang="en">

    <head>
        <?php

        # require the composer autoloader
        require_once ('vendor/autoload.php');

        // get the widgets
        require_once ('partials/widgets.php');

        HeaderWidget();

        ?>

        <!---Link to the CSS for this page--->
        <link rel="stylesheet" href="public/css/index.css">
    </head>

    <body class="container-fluid no-padding">
        <!--- Contains the navbar on the top of every page--->
        <?php Navbar(); ?>

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
                                img_source: 'public/images/algae.png',
                                href: 'search.php?Database=algae',
                                background_color: '#3c8a2e',
                            );

                             DatabaseCard(
                                title: 'Bryophytes',
                                img_source: 'public/images/bryophytes.png',
                                href: 'search.php?Database=bryophytes',
                                background_color: '#3c8a2e',
                            );

                             DatabaseCard(
                                title: 'Fungi',
                                img_source: 'public/images/fungi.png',
                                href: 'search.php?Database=fungi',
                                background_color: '#3c8a2e',
                            );

                             DatabaseCard(
                                title: 'Lichen',
                                img_source: 'public/images/lichen.png',
                                href: 'search.php?Database=lichen',
                                background_color: '#3c8a2e',
                            );

                             DatabaseCard(
                                title: 'Vascular',
                                img_source: 'public/images/herbarium.png',
                                href: 'search.php?Database=vwsp',
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
                                img_source: 'public/images/tetrapods.png',
                                href: 'search.php?Database=avian',
                                background_color: '#70382d',
                            );

                             DatabaseCard(
                                title: 'Herpetology',
                                img_source: 'public/images/herptology.png',
                                href: 'search.php?Database=herpetology',
                                background_color: '#70382d',
                            );

                             DatabaseCard(
                                title: 'Mammals',
                                img_source: 'public/images/mammal.png',
                                href: 'search.php?Database=mammal',
                                background_color: '#70382d',
                            );

                             DatabaseCard(
                                title: 'Fish',
                                img_source: 'public/images/fish.png',
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
                                img_source: 'public/images/entomology.png',
                                href: 'search.php?Database=entomology',
                                background_color: '#824bb0',
                            );

                             DatabaseCard(
                                title: 'Dry Marine Invertebrates',
                                img_source: 'public/images/marine-invertebrates-dry.png',
                                href: 'search.php?Database=mi',
                                background_color: '#ffb652',
                            );

                             DatabaseCard(
                                title: 'Wet Marine Invertebrates',
                                img_source: 'public/images/marine-invertebrates-wet.png',
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
                                img_source: 'public/images/fossils.png',
                                href: 'search.php?Database=fossil',
                                background_color: '#bd3632',
                            );
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!--- Code for the footer on each page--->
        <?php FooterWidget(imgSrc: 'public/images/beatyLogo.png'); ?>
    </body>
</html>