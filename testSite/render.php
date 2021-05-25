<?php 
    session_set_cookie_params(0,'/','.ubc.ca',isset($_SERVER["HTTPS"]), true);
    session_start();

    $_SESSION['error'] = "";

    if (!isset($_GET['Database'])){
        $_SESSION['error'] = "No database given";
        header('Location: error.php');
        exit;
    }
    
    require_once ('FileMaker.php');
    require_once ('functions.php');
    require_once ('lib/simple_html_dom.php');
    require_once ('DatabaseSearch.php');
    
    if($_GET['Database'] == "all") {
        $databases = ['avian', 'entomology', 'fish',
        'fossil', 'herpetology', 'mammal', 'mi',
        'miw'];
    } else {
        $databases = [$_GET['Database']];
    }
    
    $searchDatabases = [];
    foreach ($databases as $db) {
        require_once ('databases/'.$db.'db.php');
        $fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);
        if (FileMaker::isError($fm->listLayouts())) {
          continue;
        }
        $databaseSearch = new DatabaseSearch($fm, $db);
        array_push($searchDatabases, $databaseSearch);
    }
    
    
    if(sizeOf($searchDatabases)==1) {
        $fm = $searchDatabases[0]->getFM();
        setLayouts($searchDatabases[0]);
        $layout = $searchDatabases[0]->getSearchLayout();
        $formatLayout = $searchDatabases[0]->getResultLayout();

        $fmLayout = $fm->getLayout($searchDatabases[0]->getSearchLayout());
        $layoutFields = $fmLayout->listFields();

        $maxResponses = 100;
        $result = generateOneTable($searchDatabases[0], $maxResponses);
    } else {
        $maxResponses = 50;
        foreach ($searchDatabases as $sd) {
            $fm = $sd->getFM();
            setLayouts($sd);
            $layout = $sd->getSearchLayout();
            $formatLayout = $sd->getResultLayout();

            $fmLayout = $fm->getLayout($sd->getSearchLayout());
            $layoutFields = $fmLayout->listFields();

            $result = generateTable($sd, $maxResponses);
        }
    }

    // Check if layout exists, and get fields of layout
    If(FileMaker::isError($result)){
        $_SESSION['error'] = $result->getMessage();
        header('Location: error.php');
        exit;
    }

    $findAllRec = $result->getRecords();
    $fmFormatLayout = $fm->getLayout($formatLayout);
    $recFields = $fmFormatLayout->listFields();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
            require_once ('partials/cssDecision.php');
            require_once('partials/widgets.php');
            HeaderWidget('Search Table');
        ?>
        <link rel="stylesheet" href="css/render.css">
    </head>

    <body class="container-fluid no-padding">

        <!-- navbar -->
        <?php Navbar(); ?>

        <!-- Page title below navbar -->
        <?php TitleBanner(databaseName: $_GET['Database']); ?>

        <!-- main body with table and its widgets -->
        <div class="container-fluid">
            <?php
                if(sizeOf($searchDatabases)==1) :
                    TableController($maxResponses, $result);
            ?>
                <!-- Modify Search Button -->
                <div class="form-group">
                    <form method=post action=<?php echo "search.php"."?Database=".htmlspecialchars($_GET['Database']);?>>
                        <?php
                        $db = $_GET['Database'];
                        foreach ($_GET as $key=>$value) {
                            if (in_array($key, $layoutFields) || (in_array(str_replace('_', ' ', $key), $layoutFields))) {
                                echo "<input  type=hidden value=".htmlspecialchars($value)." name=".htmlspecialchars($key).">";
                            }
                        }
                        ?>
                        <button type="submit" value = "Submit" class="btn btn-custom">Modify Search</button>
                    </form>
                </div>

            <?php
                # data table
                echoDataTable($searchDatabases[0]->getDatabase(),$findAllRec,$recFields);

                TableController($maxResponses, $result);

                endif;
            ?>
        </div>

        <!-- footer -->
        <?php FooterWidget(imgSrc: 'images/beatyLogo.png'); ?>
    </body>
</html>