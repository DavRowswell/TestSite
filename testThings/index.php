<?php 
require_once ('FileMaker.php');
require_once ('partials/header.php');
require_once ('databases/db.php');

$fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

$layouts = $fm->listLayouts();
$findCommand = $fm->newFindCommand($layouts[2]);
$result = $findCommand->execute();
$findAllRec = $result->getRecords();
$recFields = $result->getFields();

// foreach ($recFields as $rf) {
//   echo $rf . " ";
// }
?>

<body class="container">
  <form action="render.php" method="get">
    <div class="form-group">
      <input type="text" name="Database" style="display:none;" 
      value=<?php if (isset($_GET['db'])) echo $_GET['db']; ?>>
    </div>
    <?php foreach ($recFields as $rf) { ?>
    <div class="row">
      <div class="col-sm-2">
      <label><?php echo $rf ?></label>
      </div>
      <div class="col-sm-2">
      <input type="text" name=<?php echo $rf ?>>
      </div>
    </div> 
    <?php } ?>
    <!-- <div class="row">
      <div class="col-sm-2">
      <label>Genus</label>
      </div>
      <div class="col-sm-2">
      <input type="text" name="Genus">
      </div>
    </div>
    <div class="row">
      <div class="col-sm-2">
      <label>Species</label>
      </div>
      <div class="col-sm-2">
      <input type="text" name="Species">
      </div>
    </div>
    <div class="row">
      <div class="col-sm-2">
      <label>Location</label>
      </div>
      <div class="col-sm-2">
      <input type="text" name="Location">
      </div>
    </div> -->
      <input class="btn btn-primary" type="submit">
    </div>
  </form>
</body>
