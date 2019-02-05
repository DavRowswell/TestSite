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
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="index.php">HerbariumDB</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav"> 
        <li class="dropdown"><a class="nav-link dropdown-toggle" data-toggle="dropdown" id="navbarDropdownMenuLink" href="#">Herbarium <span class="caret"></span></a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="search.php?Database=algae">Algae</a>
              <a class="dropdown-item" href="search.php?Database=bryophytes">Bryophytes</a>
              <a class="dropdown-item" href="search.php?Database=fungi">Fungi</a>
              <a class="dropdown-item" href="search.php?Database=lichen">Lichen</a>
              <a class="dropdown-item" href="search.php?Database=vwsp">Vascular Plants</a>
            </ul>
        </li>
        <li class="nav-item dropdown">
         <li class="dropdown"><a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">Vertebrates <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <a class="dropdown-item" href="search.php?Database=avian">Avian</a>
              <a class="dropdown-item" href="search.php?Database=herpetology">Herpetology</a>
              <a class="dropdown-item" href="search.php?Database=mammal">Mammal</a>
            </ul>
        </li>
        <li class="nav-item dropdown">
         <li class="dropdown"><a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">Invertebrates <span class="caret"></span></a>
            <ul class="dropdown-menu">
                   <a class="dropdown-item" href="search.php?Database=miw"> Wet Marine Invertebrates</a>
                   <a class="dropdown-item" href="search.php?Database=mi">Dry Marine Invertebrates</a>
            </ul>
        </li>
        <li class="nav-item dropdown">
         <li class="dropdown"><a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">Bones <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <a class="dropdown-item" href="search.php?Database=fish">Fish</a>
              <a class="dropdown-item" href="search.php?Database=fossil">Fossils</a>            
            </ul>
        </li>
      </li>
    </ul>
  </div>
</nav>