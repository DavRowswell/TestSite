<?php 
require_once ('partials/header.php');
?>

<body class="container">
  <form action="render.php" method="get">
    <div class="form-group">
      <label>Accession No.</label>
      <input type="text" name="AccessionNo"><br>
      <label>Genus</label>
      <input type="text" name="Genus"><br>
      <label>Species</label>
      <input type="text" name="Species"><br>
      <label>Location</label>
      <input type="text" name="Location"><br>
      <input class="btn btn-primary" type="submit">
    </div>
  </form>
</body>
