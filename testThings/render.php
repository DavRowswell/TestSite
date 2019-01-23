<div class="container-fluid">

<?php
require_once ('FileMaker.php');
require_once ('partials/header.php');
require_once ('functions.php');
?>

<html>
  <body>

  <?php
  // echo __LINE__;
  // Check if layout exists, and get fields of layout
  If(FileMaker::isError($result)){
    // echo $result->message;
    echo 'No Records Found';
    // echo __LINE__;
    exit;
  } else {
    // echo __LINE__;
    $recFields = $result->getFields();
    require_once ('partials/pageController.php');
  ?>
  

  <!-- construct table for given layout and fields -->
  <table class="table table-hover table-striped 
          table-condensed tasks-table table-responsive">
    <thead>
      <tr>
        <?php foreach($recFields as $i){?>
          <th scope="col"><a href=<?php echo replaceURIElement(replaceURIElement($_SERVER['REQUEST_URI'], 'Sort', replaceSpace($i)), 'Page', '1'); ?>>
          <?php echo formatField($i) ?></a></th>
        <?php }?>
      </tr>
    </thead>
    <tbody>
      <?php foreach($findAllRec as $i){
      ?>
      <tr>
        <?php foreach($recFields as $j){
          if(formatField($j) == 'Accession No.' || formatField($j) == 'Accession Number' || $j == 'ID'){
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