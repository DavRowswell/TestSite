<!DOCTYPE html>
<html>
<head>
  <?php
    require_once ('FileMaker.php');
    require_once ('partials/header.php');
    require_once ('functions.php');

    // echo "FM_FILE: $FM_FILE <br>
    //       FM_HOST: $FM_HOST <br>
    //       FM_USER: $FM_USER <br>
    //       FM_PASS: $FM_PASS <br>";

    $layouts = $fm->listLayouts();

    if (FileMaker::isError($layouts)) {
      echo $layouts->getMessage();
      exit();
    }

    $layout = $layouts[0];

    foreach ($layouts as $l) {
      //get current database name
      $page = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '=') + 1);
      if ($page == 'mi') {
        if (strpos($l, 'search') !== false) {
          $layout = $l;
          break;
        }
      }
      else if (strpos($l, 'search') !== false) {
        $layout = $l;
      }
    }
    $fmLayout = $fm->getLayout($layout);
    $layoutFields = $fmLayout->listFields();
  ?>
</head>
<body class="container-fluid">
 <?php require_once ('partials/navbar.php'); ?>
  <form action="render.php" method="get" id = "submit-form">
    <div class="form-group">
      <input type="text" name="Database" style="display:none;" 
      value=<?php if (isset($_GET['Database'])) echo htmlspecialchars($_GET['Database']); ?>>
    </div>
    <?php foreach ($layoutFields as $rf) {
      if ($rf === 'SortNum') continue; ?>
    <div class="row">
      <div class="col-sm-2">
      <label><?php echo formatField($rf) ?></label>
      </div>
      <div class="col-sm-2">
      <input type="text" name=<?php echo $rf ?>>
      </div>
    </div> 
    <?php } ?>
    <input id = "form" class="btn btn-primary" type = "button" value = "Submit" onclick="Process(clearURL())">    
    </div>
  </form>
  <?php require_once("partials/footer.php");?>
  <script src="js/process.js"> </script>
</body>
</html>
