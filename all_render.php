<?php
    session_start();
    require_once ('FileMaker.php');
    require_once('utilities.php');
    require_once ('classes/DatabaseSearch.php');
    require_once ('partials/widgets.php');
    require_once ('credentials_controller.php');

    // list databases
    // $databases = ['algae', 'avian', 'bryophytes', 'entomology', 'fish',
    // 'fossil', 'fungi', 'herpetology', 'lichen', 'mammal', 'mi',
    // 'miw', 'vwsp'];

    $databases = ['avian', 'entomology', 'fish', 
    'fossil', 'herpetology', 'mammal', 'mi', 
    'miw'];

    $searchDatabases = [];

    # create a DatabaseSearch obj for each db used
    foreach ($databases as $db) {
        $databaseSearch = DatabaseSearch::fromDatabaseName($db);
        if (!$databaseSearch) {
            continue;
        } else {
            array_push($searchDatabases, $databaseSearch);
        }

    }
    // exit;
?>
<!DOCTYPE html>
<html>
    <head>
        <?php HeaderWidget(); ?>
    </head>
    <body class="container-fluid">
        <?php

        Navbar();

        // generate results from FileMaker query
        foreach ($searchDatabases as $sd) {
            // determine search and results layouts for given database
            generateTable($sd, 20);
        }
        ?>

        <?php FooterWidget('public/images/beatyLogo.png'); ?>
    </body>
</html>