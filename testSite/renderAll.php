    <?php
    session_start();
    require_once ('FileMaker.php');
    require_once ('partials/header.php');
    require_once ('functions.php');
    require_once ('DatabaseSearch.php');
    
    // list databases
    // $databases = ['algae', 'avian', 'bryophytes', 'entomology', 'fish', 
    // 'fossil', 'fungi', 'herpetology', 'lichen', 'mammal', 'mi', 
    // 'miw', 'vwsp'];

    $databases = ['avian', 'entomology', 'fish', 
    'fossil', 'herpetology', 'mammal', 'mi', 
    'miw'];

    $searchDatabases = [];

    foreach ($databases as $db) {
        require_once ('databases/'.$db.'db.php');
        // echo "$FM_FILE <br>";
        $fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);
        if (FileMaker::isError($fm->listLayouts())) {
          // echo $FM_FILE;

          continue;
        }
        $databaseSearch = new DatabaseSearch($fm, $db);
        array_push($searchDatabases, $databaseSearch);
    }
    // exit;
    ?>
<link rel="stylesheet" href="css/rendercss.css">
<!DOCTYPE html>
<html>
<head>
    </head>
    <body class="container-fluid">
    <?php

    require_once ('partials/navbar.php');
    // generate results from FileMaker query
    foreach ($searchDatabases as $sd) {
        // determine search and results layouts for given database
        setLayouts($sd);
        generateTable($sd);
    }
  ?>
      
<?php include_once ('partials/footer.php');?>    
<script src="js/process.js"> </script>
</body>
</html>