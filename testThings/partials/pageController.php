<?php
    $uri = $_SERVER['REQUEST_URI'];
    $parts = explode('&', $uri);
    $lastPart = end($parts);

    if (isset($_GET['Skip'])) {
      if ($_GET['Skip'] > 99) {
        $parts[sizeof($parts)-1] = 'Skip='.($_GET['Skip'] - 100);
        $lasturi = implode('&', $parts);
        echo "<a href=$lasturi>Last Page</a> ";
      }
      $parts[sizeof($parts)-1] = 'Skip='.($_GET['Skip'] + 100);
      $nexturi = implode('&', $parts);
      echo "<a href=$nexturi>Next Page</a>";
    } else {
      array_push($parts, 'Skip=100');
      $nexturi = implode('&', $parts);
      echo "<a href=$nexturi>Next Page</a>";
    }
?>