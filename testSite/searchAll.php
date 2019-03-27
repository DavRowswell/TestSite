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
        array_push($fm_databases);
    }

    

    
    ?>
</head>
</html>