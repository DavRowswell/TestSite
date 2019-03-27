<!DOCTYPE html>
<html>
<head>
    <?php
    session_start();
    require_once ('FileMaker.php');
    require_once ('partials/header.php');
    require_once ('partials/navbar.php');
    
    // list databases
    $databases = ['algae', 'avian', 'bryophytes', 'entomology', 'fish', 
    'fossil', 'fungi', 'herpetology', 'lichen', 'mammal', 'mi', 
    'miw', 'vwsp'];

    $fm_databases = [];

    // initialize FileMaker objects
    foreach ($databases as $db) {
        require_once ('databases/'.$db.'db.php');
        $fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);
        array_push($fm_databases, $fm);
    }

    // echo sizeof($fm_databases);

    $fm_results = [];
    
    foreach ($fm_databases as $fm_db) {
        $layouts = $fm_db->listLayouts();
        $layout = "";
        $formatLayout = "";
        
        echo $fm_db->getProperty('database');
        echo 'hello';

        foreach ($layouts as $l) {
            if ($fm_db->getProperty('database') === 'mi') {
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