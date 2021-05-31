<?php
    require_once ('utilities.php');

    $qs = $_SERVER['QUERY_STRING'];
    $uri = $_SERVER['REQUEST_URI'];
    $parts = explode('&', $uri);
    $qsparts = explode('&', $qs);
    $lastPart = end($parts);
    $found = $result->getFoundSetCount();

    // echo "$found records found";

    $pages = ceil($found / $numRes);
    $page = 1;
    $allPage = $database . 'Page';
    if (isset($_GET[$allPage]) && $_GET[$allPage] != '') {
        $page = $_GET[$allPage];
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

<div class="row">
  <div class="col-sm-12">
    <form action="renderAll.php" method="get" id=<?php echo $allPage."Form" ?>>
      <div class="row">
        <div class="col-sm-3">
          <label for=<?php echo $allPage."Input" ?>><?php echo "$found records found" ?></label>
          <small class="form-text text-muted">
            <?php echo "Page ".htmlspecialchars($page)." / ".htmlspecialchars($pages); ?>
          </small>
        </div>
        <?php
          foreach ($qsparts as $part) {
            $keyVal = explode('=', $part);
            if (sizeOf($keyVal) > 1) {
              $input = $keyVal[1];
            } else {
              $input = '';
            }
            if (strpos($part, $allPage) === 0 || $input == '') continue;
        ?>
        <input type="hidden" 
          name="<?php echo htmlspecialchars(str_replace('%3A', ':', str_replace('%2B', '+', $keyVal[0])))?>" 
          value="<?php echo htmlspecialchars(str_replace('%3A', ':', str_replace('%2B', '+', $input)))?>" />
        <?php } ?>
      </div>
      <div class="row">
        <div class = "col-sm-2" style="padding-right:0px; padding-bottom:5px;">
          <input type="number" name=<?php echo $allPage?> class="form-control" id=<?php echo $allPage."Input"?> min="1" max=<?php echo htmlspecialchars($pages)?>>
        </div>
        <div class="col-sm-3" style="padding-bottom:5px;">
          <button type="submit" form=<?php echo $allPage."Form"?> value="Submit" class="btn btn-primary">Navigate to Page</button>
        </div>
      </div>
    </form>
    <div class="row">
      <div class="col-sm-12" style="padding-bottom:5px;">
        <?php
          if (isset($_GET[$allPage]) && $_GET[$allPage] != '') {
            $pageNum = $_GET[$allPage];

            $partIndex = 0;
            foreach($parts as $part) {
              if (strpos($part, $database."Page") !== false) {
                break;
              }
              $partIndex++;
            }

            if ($pageNum > 1) {
              $parts[$partIndex] = $allPage.'='.($pageNum - 1);
              $lasturi = implode('&', $parts);
              echo '<a href=' . htmlspecialchars($lasturi) . ' class="previous round">&#8249</a>';
            }
            if ($pageNum < $pages && $pageNum != '') {
              $parts[$partIndex] = $allPage.'='.($pageNum + 1);
              $nexturi = implode('&', $parts);
              echo '<a href=' . htmlspecialchars($nexturi) . ' class="next round">&#8250</a>';
            }
          } else { 
            if ($found > $numRes){
              array_push($parts, $allPage.'=2');
              $nexturi = implode('&', $parts);
              echo '<a href=' . htmlspecialchars($nexturi) . ' class="next round">&#8250</a>';
            }
          }
        ?>
      </div>
    </div>
  </div>
</div>