<div class="container-fluid">

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
    echo $result->message;
    exit;
  } else {
    $recFields = $result->getFields();
    require_once ('partials/pageController.php');
    function mapField($field) {
      return $field;
    }
    
    function formatField($field) {
      $colonPosition = strrpos($field, ":");
      if ($colonPosition) {
        $field = substr($field, $colonPosition + 1);
      }
      return mapField($field);
    }
  ?>

  <!-- construct table for given layout and fields -->
  <table class="table table-hover table-striped 
          table-condensed tasks-table table-responsive">
    <thead>
      <tr>
        <?php foreach($recFields as $i){?>
          <th scope="col"><?php echo formatField($i) ?></th>
        <?php }?>
      </tr>
    </thead>
    <tbody>
      <?php foreach($findAllRec as $i){
      ?>
      <tr>
        <?php foreach($recFields as $j){
          if($j == 'Accession No.' || $j == 'Accession Number' || $j == 'ID'){
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
  require ('partials/pageController.php');
  ?>

  </body>
</html>
</div>