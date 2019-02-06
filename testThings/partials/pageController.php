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

<style>
a {
  text-decoration: none;
  display: inline-block;
  padding: 8px 16px;
}

a:hover {
  background-color: #ddd;
  color: black;
}

.previous {
  background-color: #f1f1f1;
  color: black;
}

.next {
  background-color: #4CAF50;
  color: white;
}

.round {
  border-radius: 50%;
}
</style>

<form action="render.php" method="get">
    <?php
        foreach ($qsparts as $part) {
            $keyVal = explode('=', $part);
            if (strpos($part, "Page") === 0 || $keyVal[1] == '') continue;
            ?>
            <input type="hidden" name="<?php echo $keyVal[0]?>" value="<?php echo str_replace('%3A', ':', str_replace('%2B', '+', $keyVal[1]))?>" />
            <?php
        }
        ?> 
    <input type="number" name="Page" min="1" max=<?php echo $pages?>><br>
    <input type="submit" value="Navigate to Page">
</form>

<?php
    echo "Page $page / $pages <br>";

    if (isset($_GET['Page']) && $_GET['Page'] != '') {
      if ($_GET['Page'] > 1) {
        // echo "<br>";
        $parts[sizeof($parts)-1] = 'Page='.($_GET['Page'] - 1);
        $lasturi = implode('&', $parts);
        echo '<a href=' . $lasturi . ' class="previous round">&#8249</a>';
      }
      if ($_GET['Page'] < $pages && $_GET['Page'] != '') {
        $parts[sizeof($parts)-1] = 'Page='.($_GET['Page'] + 1);
        $nexturi = implode('&', $parts);
        echo '<a href=' . $nexturi . ' class="next round">&#8250</a>';
      }
    //   if($_GET['Page'] > 1 && $_GET['Page'] < $pages) echo "<br>";
    // echo "Page $page / $pages <br>";

    } else { 
        if ($found > $numRes){
            array_push($parts, 'Page=2');
            $nexturi = implode('&', $parts);
            echo '<a href=' . $nexturi . ' class="next round">&#8250</a>';
        }
    }
?>