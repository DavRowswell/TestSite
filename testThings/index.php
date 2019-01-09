<?php 
require_once ('FileMaker.php');
require_once ('partials/header.php');
require_once ('db.php');

$fm = new FileMaker($FM_FILE, $FM_HOST, $FM_USER, $FM_PASS);

echo "FM_FILE: $FM_FILE <br>
      FM_HOST: $FM_HOST <br>
      FM_USER: $FM_USER <br>
      FM_PASS: $FM_PASS <br>";

$layouts = $fm->listLayouts();

if (FileMaker::isError($layouts)) {
  echo $layouts;
}

$layout = $layouts[0];

foreach ($layouts as $l) {
  if (strpos($l, 'search') !== false) {
    $layout = $l;
  }
}
$fmLayout = $fm->getLayout($layout);
$layoutFields = $fmLayout->listFields();

?>

<body class="container">
  <form action="render.php" method="get">
    <div class="form-group">
      <input type="text" name="Database" style="display:none;" 
      value=<?php if (isset($_GET['Database'])) echo $_GET['Database']; ?>>
    </div>
    <?php foreach ($layoutFields as $rf) { ?>
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
