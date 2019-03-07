<?php
    require_once ('functions.php');

    $qs = $_SERVER['QUERY_STRING'];
    $uri = $_SERVER['REQUEST_URI'];
    $parts = explode('&', $uri);
    $qsparts = explode('&', $qs);
    $lastPart = end($parts);
    $found = $result->getFoundSetCount();

    // echo "$found records found";

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

<main style="position:relative; top:4px">
<form action="render.php" method="get" id="pageForm">
  <div class="form-group">
  <label for="pageInput"><?php echo "$found records found" ?></label>
    <?php
        foreach ($qsparts as $part) {
            $keyVal =  explode('=', $part);
            $input = $keyVal[1];
            if (strpos($part, "Page") === 0 || $input == '') continue;
            ?>
            <input type="hidden" 
            name="<?php echo htmlspecialchars($keyVal[0])?>" 
            value="<?php echo htmlspecialchars(str_replace('%3A', ':', str_replace('%2B', '+', $input)))?>" />
            <?php
        }
        ?>
        <div style="position:relative; left:-16px" class="col-8">
        <input type="number" name="Page" class="form-control" id="pageInput" min="1" max=<?php echo htmlspecialchars($pages)?>>
        </div>
    </div>
</form>
<button type="submit" form="pageForm" value="Submit" class="btn btn-primary">Navigate to Page</button>
<small class="form-text text-muted">
<?php
    echo "Page ".htmlspecialchars($page)." / ".htmlspecialchars($pages); ?>
</small>
<div style="position:relative; top:6px">
<?php
    if (isset($_GET['Page']) && $_GET['Page'] != '') {
  
      $pageNum = $_GET['Page'];
      if ($pageNum > 1) {
        $parts[sizeof($parts)-1] = 'Page='.($pageNum - 1);
        $lasturi = implode('&', $parts);
        echo '<a href=' . htmlspecialchars($lasturi) . ' class="previous round">&#8249</a>';
      }
      if ($pageNum < $pages && $pageNum != '') {
        $parts[sizeof($parts)-1] = 'Page='.($pageNum + 1);
        $nexturi = implode('&', $parts);
        echo '<a href=' . htmlspecialchars($nexturi) . ' class="next round">&#8250</a>';
      }
    //   if($_GET['Page'] > 1 && $_GET['Page'] < $pages) echo "<br>";
    // echo "Page $page / $pages <br>";

    } else { 
        if ($found > $numRes){
            array_push($parts, 'Page=2');
            $nexturi = implode('&', $parts);
            echo '<a href=' . htmlspecialchars($nexturi) . ' class="next round">&#8250</a>';
        }
    }
?>
</div>
</main>