<div class="container">

<?php
require_once ('FileMaker.php');
require_once ('partials/header.php');
require_once ('functions.php');
?>

<html>
  <body>

  <?php
  // Check if layout exists, and get fields of layout
  If(FileMaker::isError($result)){
    echo $result;
  } else {
    $recFields = $result->getFields();
    $uri = $_SERVER['REQUEST_URI'];
    $parts = explode('&', $uri);
    $lastPart = end($parts);
    // if (substr($lastPart, 0, strpos($lastPart, '=') == 'Size')) {
    //   $Skip = substr($lastPart, strpos($lastPart, '='), strlen($lastPart));
    // } else {
    //   $Skip = 0;
    // }

    if (isset($_GET['Skip']) && $_GET['Skip'] > 99) {
      $parts[sizeof($parts)-1] = 'Skip='.($_GET['Skip'] - 100);
      $lasturi = implode('&', $parts);
      echo "<a href=$lasturi>Last Page</a> ";
    }
    $parts[sizeof($parts)-1] = 'Skip='.($_GET['Skip'] + 100);
    $nexturi = implode('&', $parts);
    echo "<a href=$nexturi>Next Page</a>";
  ?>

  <!-- construct table for given layout and fields -->
  <table class="table">
    <thead>
      <tr>
        <?php foreach($recFields as $i){?>
          <th scope="col"><?php echo $i ?></th>
        <?php }?>
      </tr>
    </thead>
    <tbody>
      <?php foreach($findAllRec as $i){
      ?>
      <tr>
        <?php foreach($recFields as $j){
          if($j == 'Accession No.'){
            echo '<td><a href=\'details.php?Database=' . $_GET['Database'] . '&AccessionNo='.$i->getField($j).'\'>'.$i->getField($j).'</a></td>';
          } 
          else {
            echo '<td>'.$i->getField($j).'</td>';
          }
        }?>
      </tr>
      <?php }?>
    </tbody>
  </table>
    
  <?php
  }
  ?>

  </body>
</html>
</div>