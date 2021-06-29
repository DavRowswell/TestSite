<!DOCTYPE html>
<html lang="en">

    <head>
        <?php

        # require the composer autoloader
        require_once ('vendor/autoload.php');
        require_once ('my_autoloader.php');

        try {
            new DatabaseSearch('', '');
        } catch (TypeError $exception) {
        }

        // get the widgets
        require_once ('partials/widgets.php');

        HeaderWidget();

        ?>
    </head>

    <body>
        <!--- Contains the navbar on the top of every page--->
        <?php Navbar(); ?>

        <!--- Div for the main content of the page--->
        <div class="container-fluid flex-grow-1 p-0">
            <!--- The main title of the page under the navbar--->
            <div class="container-fluid p-0">
                <h2 class="collection-title"><b>Our Collections</b></h2>
            </div>

            <!--- Row for all the content--->
            <div class="row g-0">

                <!--- First column of databases--->
                <div class="col-sm-3">

                    <!--- Herbarium title section--->
                    <div class="text-center title-box">
                        <h3 class="collection-header"><b>Herbarium</b></h3>
                    </div>

                    <!--- Herbarium links and content--->
                    <div class="mx-3">
                    <?php
                             DatabaseCard(
                                title: 'Algae',
                                img_source: 'public/images/collection-logos/algae.png',
                                href: 'search.php?Database=algae',
                                background_color: '#3c8a2e',
                            );

                             DatabaseCard(
                                title: 'Bryophytes',
                                img_source: 'public/images/collection-logos/bryophytes.png',
                                href: 'search.php?Database=bryophytes',
                                background_color: '#3c8a2e',
                            );

                             DatabaseCard(
                                title: 'Fungi',
                                img_source: 'public/images/collection-logos/fungi.png',
                                href: 'search.php?Database=fungi',
                                background_color: '#3c8a2e',
                            );

                             DatabaseCard(
                                title: 'Lichen',
                                img_source: 'public/images/collection-logos/lichen.png',
                                href: 'search.php?Database=lichen',
                                background_color: '#3c8a2e',
                            );

                             DatabaseCard(
                                title: 'Vascular',
                                img_source: 'public/images/collection-logos/herbarium.png',
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
                        <h3 class="collection-header"><b>Vertebrate</b></h3>
                    </div>

                    <!--- Vertebrate image and link content--->
                    <div class="mx-3">
                        <?php
                             DatabaseCard(
                                title: 'Avian',
                                img_source: 'public/images/collection-logos/tetrapods.png',
                                href: 'search.php?Database=avian',
                                background_color: '#70382d',
                            );

                             DatabaseCard(
                                title: 'Herpetology',
                                img_source: 'public/images/collection-logos/herptology.png',
                                href: 'search.php?Database=herpetology',
                                background_color: '#70382d',
                            );

                             DatabaseCard(
                                title: 'Mammals',
                                img_source: 'public/images/collection-logos/mammal.png',
                                href: 'search.php?Database=mammal',
                                background_color: '#70382d',
                            );

                             DatabaseCard(
                                title: 'Fish',
                                img_source: 'public/images/collection-logos/fish.png',
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
                        <h3 class="collection-header"><b>Invertebrate</b></h3>
                    </div>

                    <!--- Invertebrate column content--->
                    <div class="mx-3">
                        <?php
                             DatabaseCard(
                                title: 'Entomology',
                                img_source: 'public/images/collection-logos/entomology.png',
                                href: 'search.php?Database=entomology',
                                background_color: '#824bb0',
                            );

                             DatabaseCard(
                                title: 'Dry Marine Invertebrates',
                                img_source: 'public/images/collection-logos/marine-invertebrates-dry.png',
                                href: 'search.php?Database=mi',
                                background_color: '#ffb652',
                            );

                             DatabaseCard(
                                title: 'Wet Marine Invertebrates',
                                img_source: 'public/images/collection-logos/marine-invertebrates-wet.png',
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
                        <h3 class="collection-header"><b>Fossil</b></h3>
                    </div>

                    <!--- Fossil Content--->
                    <div class="mx-3">
                        <?php
                             DatabaseCard(
                                title: 'Fossils',
                                img_source: 'public/images/collection-logos/fossils.png',
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