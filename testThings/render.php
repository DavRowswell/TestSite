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
    echo $result->message;
    exit;
  } else {
    $recFields = $result->getFields();
    require_once ('partials/pageController.php');
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
          if($j == 'Accession No.' || $j == 'Accession Number'){
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