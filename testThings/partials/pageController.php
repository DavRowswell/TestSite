<?php
    require_once ('functions.php');

    $qs = $_SERVER['QUERY_STRING'];
    $uri = $_SERVER['REQUEST_URI'];
    $parts = explode('&', $uri);
    $qsparts = explode('&', $qs);
    $lastPart = end($parts);
    $found = $result->getFoundSetCount();

    echo "$found records found <br>";

    $pages = ceil($found / $numRes);
    $page = 1;
    if (isset($_GET['Page']) && $_GET['Page'] != '') {
        $page = $_GET['Page'];
    }
    // echo explode('?', $qs)[0];
?>
<form action="render.php" method="get">
    <?php
        foreach ($qsparts as $part) {
            if (strpos($part, "Page") === 0) continue;
            $keyVal = explode('=', $part);
            ?>
            <input type="hidden" name="<?php echo $keyVal[0]?>" value="<?php echo $keyVal[1]?>" />
            <?php
        }
        ?>
    <input type="number" name="Page" min="1" max=<?php echo $pages?>><br>
    <input type="submit" value="Navigate to Page">
</form>

<?php
    echo "Page $page / $pages <br>";

    if (isset($_GET['Page'])) {
      if ($_GET['Page'] > 1) {
        $parts[sizeof($parts)-1] = 'Page='.($_GET['Page'] - 1);
        $lasturi = implode('&', $parts);
        echo "<a href=$lasturi>Last Page</a>";
      }

      if ($_GET['Page'] < $pages && $_GET['Page'] != '') {
        $parts[sizeof($parts)-1] = 'Page='.($_GET['Page'] + 1);
        $nexturi = implode('&', $parts);
        echo "<a href=$nexturi>Next Page</a>";
      }
    } else { 
        if ($found > $numRes){
            array_push($parts, 'Page=2');
            $nexturi = implode('&', $parts);
            echo "<a href=$nexturi>Next Page</a>";
        }
    }
?>