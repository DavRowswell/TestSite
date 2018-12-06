<?php 
require_once ('partials/header.php');
?>

<body class="container">
  <form action="render.php" method="get">
    <div class="form-group">
      <input type="text" name="Database" style="display:none;" 
      value=<?php if (isset($_GET['db'])) echo $_GET['db'] ?>>
    </div>
    <div class="row">
      <div class="col-sm-2">
      <label>Accession No.</label>
      </div>
      <div class="col-sm-2">
      <input type="text" name="AccessionNo">
      </div>
    </div>
    <div class="row">
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
    </div>
      <input class="btn btn-primary" type="submit">
    </div>
  </form>
</body>
