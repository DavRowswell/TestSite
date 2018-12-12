<?php 
require_once ('FileMaker.php');
require_once ('partials/header.php');
require_once ('databases/db.php');

$fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

$layouts = $fm->listLayouts();
$layout = $layouts[0];

foreach ($layouts as $l) {
  if (strpos($l, 'search') !== false) {
    $layout = $l;
  }
}

$findCommand = $fm->newFindCommand($layout);
$result = $findCommand->execute();
$findAllRec = $result->getRecords();
$recFields = $result->getFields();

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
      <input class="btn btn-primary" type="submit">
    </div>
  </form>
</body>
