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

    foreach ($searchDatabases as $sd) {
        $fm_db = $sd->getFM();
        $layouts = $fm_db->getLayouts();
        $layout = "";
        $formatLayout = "";

        foreach ($layouts as $l) {
            if ($sd->getDatabase() === 'mi') {
                if ($l == 'search-MI') {
                    $layout = $l;
                } else if ($l == 'results-MI') {
                    $formatLayout = $l;
                }
            } else {
                if (strpos($l, 'search') !== false) {
                    $layout = $l;
                } else if (strpos($l, 'results') !== false) {
                    $formatLayout = $l;
                }
            }
        }
    }

    
    ?>
</head>
</html>