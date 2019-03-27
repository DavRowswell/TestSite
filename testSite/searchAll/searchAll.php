<!DOCTYPE html>
<html>
<head>
    <?php
    session_start();
    require_once ('../FileMaker.php');
    require_once ('../partials/header.php');
    require_once ('../partials/navbar.php');
    require_once ('DatabaseSearch.php');
    
    // list databases
    $databases = ['algae', 'avian', 'bryophytes', 'entomology', 'fish', 
    'fossil', 'fungi', 'herpetology', 'lichen', 'mammal', 'mi', 
    'miw', 'vwsp'];

    $searchDatabases = [];

    // initialize FileMaker objects
    foreach ($databases as $db) {
        require_once ('../databases/'.$db.'db.php');
        $fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);
        $databaseSearch = new DatabaseSearch($fm, $db);
        array_push($searchDatabases, $databaseSearch);
    }

    // echo sizeof($fm_databases);

    // generate results from FileMaker query
    foreach ($searchDatabases as $sd) {
        // determine search and results layouts for given database
        $fm_db = $sd->getFM();
        $layouts = $fm_db->listLayouts();
        $searchLayout = "";
        $resultLayout = "";

        foreach ($layouts as $l) {
            if ($sd->getDatabase() === 'mi') {  // mi and miw layouts get mixed up so this check is necessary to get the mi layouts coorrectly
                if ($l == 'search-MI') {
                    $searchLayout = $l;
                } else if ($l == 'results-MI') {
                    $resultLayout = $l;
                }
            } else { // go through layouts and find the search and results layouts
                if (strpos($l, 'search') !== false) {
                    $searchLayout = $l;
                } else if (strpos($l, 'results') !== false) {
                    $resultLayout = $l;
                }
            }
        }

        // sets the search and results layouts of current DatabaseSearch object
        $sd->setSearchLayout($searchLayout);
        $sd->setResultLayout($resultLayout);



    }

    
    ?>
</head>
</html>