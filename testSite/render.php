<?php

require_once ('FileMaker.php');
require_once ('functions.php');
require_once ('lib/simple_html_dom.php');
require_once ('DatabaseSearch.php');
require_once ('credentials_controller.php');

session_set_cookie_params(0,'/','.ubc.ca',isset($_SERVER["HTTPS"]), true);
session_start();

define("DATABASE", $_GET['Database'] ?? null);

checkDatabaseField(DATABASE);

$databaseSearch = DatabaseSearch::fromDatabaseName(DATABASE);
# check to make sure the database is not false or null
if (!$databaseSearch) {
    $_SESSION['error'] = 'Unsupported database given';
    header('Location: error.php');
    exit;
}

# get the fields for the search and result layout
$layoutFields = $databaseSearch->getSearchLayout()->listFields();
$recFields = $databaseSearch->getResultLayout()->listFields();

$maxResponses = 100;

# remove any empty get fields
$usefulGETFields = array_filter($_GET);

# since we are diffing by keys, we need to set dummy values
$unUsedGETFields = ['type' => '', 'sort' => '', 'Page' => '', 'SortOrder' => '', 'Database' => ''];
$usefulGETFields = array_diff_key($usefulGETFields, $unUsedGETFields);

$result = $databaseSearch->queryForResults($maxResponses, $usefulGETFields, $_GET['type'] ?? 'and',
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