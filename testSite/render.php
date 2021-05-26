<?php 
    session_set_cookie_params(0,'/','.ubc.ca',isset($_SERVER["HTTPS"]), true);
    session_start();

    # Check to make sure the database file is loaded or send to error.php
    if (!isset($_GET['Database']) or $_GET['Database'] == ''){
        $_SESSION['error'] = "No database given";
        header('Location: error.php');
        exit;
    }
    # also check to make sure we dont have the 'all' database tag, this is not the page for this tag
    else if ($_GET['Database'] == 'all') {
        $_SESSION['error'] = "Wrong page for database given";
        header('Location: error.php');
        exit;
    }

    define("DATABASE", $_GET['Database']);
    
    require_once ('FileMaker.php');
    require_once ('functions.php');
    require_once ('lib/simple_html_dom.php');
    require_once ('DatabaseSearch.php');
    require_once ('credentials_controller.php');

    # get the FMP instance and create a DatabaseSearch instance
    list($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS) = getDBCredentials(DATABASE);
    if (!$FM_PASS or !$FM_FILE or !$FM_HOST or !$FM_USER) {
        $_SESSION['error'] = 'Unsupported database given';
        header('Location: error.php');
        exit;
    }

    $fileMaker = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

    $databaseSearch = new DatabaseSearch($fileMaker, DATABASE);

    # get the fields for the search and result layout
    $layoutFields = $databaseSearch->getSearchLayout()->listFields();
    $recFields = $databaseSearch->getResultLayout()->listFields();

    $maxResponses = 100;

    # remove any empty get fields
    $usefulFields = array_filter($_GET);

    # since we are diffing by keys, we need to set dummy values
    $unUsedFields = ['type' => '', 'sort' => '', 'Page' => '', 'SortOrder' => '', 'Database' => ''];
    $usefulFields = array_diff_key($usefulFields, $unUsedFields);

    $result = $databaseSearch->queryForResults($maxResponses, $usefulFields, $_GET['type'] ?? 'and',
        $_GET['Sort'] ?? null, $_GET['Page'] ?? 1, $_GET['SortOrder'] ?? null);

    if (FileMaker::isError($result)) {
        $_SESSION['error'] = $result->getMessage();
        header('Location: error.php');
        exit;
    }

    $findAllRec = $result->getRecords();

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
            require_once('partials/conditionalCSS.php');
            require_once('partials/widgets.php');
            HeaderWidget('Search Table');
        ?>
        <link rel="stylesheet" href="css/render.css">
    </head>

    <body class="container-fluid no-padding">

        <!-- navbar -->
        <?php Navbar(); ?>

        <!-- Page title below navbar -->
        <?php TitleBanner(databaseName: DATABASE); ?>

        <!-- main body with table and its widgets -->
        <div class="container-fluid">
            <?php
                TableControllerWidget($maxResponses, $result);
            ?>
                <!-- Modify Search Button -->
                <div class="form-group">
                    <form method=post action=<?php echo "search.php"."?Database=".htmlspecialchars(DATABASE);?>>
                        <?php
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
                echoDataTable($databaseSearch->getName(),$findAllRec,$recFields);

                TableControllerWidget($maxResponses, $result);
            ?>
        </div>

        <!-- footer -->
        <?php FooterWidget(imgSrc: 'images/beatyLogo.png'); ?>
    </body>
</html>