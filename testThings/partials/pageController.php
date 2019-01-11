<?php
    $qs = $_SERVER['QUERY_STRING'];
    $uri = $_SERVER['REQUEST_URI'];
    $parts = explode('&', $uri);
    $qsparts = explode('&', $qs);
    $lastPart = end($parts);
    $found = $result->getFoundSetCount();

    echo "$found records found <br>";

    $pages = ceil($found / 100);
    $page = 1;
    if (isset($_GET['Page'])) {
        $page = $_GET['Page'];
    }
    // echo explode('?', $qs)[0];
?>
<form action="render.php" method="get">
    <?php
        foreach ($qsparts as $part) {
            if (strpos($part, "Page")) continue;
            $keyVal = explode('=', $part);
            ?>
            <input type="hidden" name="<?php echo $keyVal[0]?>" value="<?php echo $keyVal[1]?>" />
            <?php
        }
        ?>
    <input type="number" name="Page"><br>
    <input type="submit" value="Submit">
</form>

<?php
    echo "Page $page / $pages <br>";

    if (isset($_GET['Page'])) {
      if ($_GET['Page'] > 1) {
        $parts[sizeof($parts)-1] = 'Page='.($_GET['Page'] - 1);
        $lasturi = implode('&', $parts);
        echo "<a href=$lasturi>Last Page</a>";
      }

      if ($_GET['Page'] < $pages) {
        $parts[sizeof($parts)-1] = 'Page='.($_GET['Page'] + 1);
        $nexturi = implode('&', $parts);
        echo "<a href=$nexturi>Next Page</a>";
      }
    } else { 
        if ($found > 100){
            array_push($parts, 'Page=2');
            $nexturi = implode('&', $parts);
            echo "<a href=$nexturi>Next Page</a>";
        }
    }
?>