<!-- Bootstrap CSS -->
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<?php
  function active($link) {
    $pos = strrpos($_SERVER['REQUEST_URI'], '/');
    $page = substr($_SERVER['REQUEST_URI'], $pos+1);
      if ($page == $link) {
        return "active";
      }
    return "false";
  }
?>

<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#">HerbariumDB</a>
    </div>
    <ul class="nav navbar-nav">
      <li class="nav-item dropdown">
         <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Herbarium <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li class="<?php echo(active("index.php?Database=algae"))?>"><a href="index.php?Database=algae">Algae</a></li>
              <li class="<?php echo(active("index.php?Database=bryophytes"))?>"><a href="index.php?Database=bryophytes">Bryophytes</a></li>
              <li class="<?php echo(active("index.php?Database=fungi"))?>"><a href="index.php?Database=fungi">Fungi</a></li>
              <li class="<?php echo(active("index.php?Database=lichen"))?>"><a href="index.php?Database=lichen">Lichen</a></li>
              <li class="<?php echo(active("index.php?Database=vwsp"))?>"><a href="index.php?Database=vwsp">Vascular Plants</a></li>
            </ul>
        </li>
        <li class="nav-item dropdown">
         <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Vertebrates <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li class="<?php echo(active("index.php?Database=avian"))?>"><a href="index.php?Database=avian">Avian</a></li>
              <li class="<?php echo(active("index.php?Database=fish"))?>"><a href="index.php?Database=fish">Fish</a></li>
              <li class="<?php echo(active("index.php?Database=herpetology"))?>"><a href="index.php?Database=herpetology">Herpetology</a></li>
              <li class="<?php echo(active("index.php?Database=mammal"))?>"><a href="index.php?Database=mammal">Mammal</a></li>
            </ul>
        </li>
        <li class="nav-item dropdown">
         <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="#">Invertebrates <span class="caret"></span></a>
            <ul class="dropdown-menu">
                   <li class="<?php echo(active("index.php?Database=miw"))?>"><a href="index.php?Database=miw">Marine Invertebrates</a></li>
            </ul>
        </li>
      </li>
    </ul>
  </div>
</nav>