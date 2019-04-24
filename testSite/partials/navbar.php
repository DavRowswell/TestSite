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
<nav id="header" class="navbar navbar-expand-lg navbar-dark" style="background: #CC2229;">
  <a style="background-color:inherit;" class="navbar-brand" href="index.php">Beaty Museum Databases</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavDropdown">
    <ul class="navbar-nav"> 
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-toggle="dropdown" id="navbardrop" href="#">Herbarium <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <a class="dropdown-item" href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=algae">Algae</a>
            <a class="dropdown-item" href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=bryophytes">Bryophytes</a>
            <a class="dropdown-item" href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=fungi">Fungi</a>
            <a class="dropdown-item" href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=lichen">Lichen</a>
            <a class="dropdown-item" href="https://herbweb.botany.ubc.ca/testSite/search.php?Database=vwsp">Vascular Plants</a>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-toggle="dropdown" id="navbardrop" href="#">Vertebrates <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <a class="dropdown-item" href="search.php?Database=avian">Avian</a>
            <a class="dropdown-item" href="search.php?Database=herpetology">Herpetology</a>
            <a class="dropdown-item" href="search.php?Database=mammal">Mammal</a>
            <a class="dropdown-item" href="search.php?Database=fish">Fish</a>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-toggle="dropdown" id="navbardrop" href="#">Invertebrates <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <a class="dropdown-item" href="search.php?Database=entomology">Entomology</a>
            <a class="dropdown-item" href="search.php?Database=miw"> Wet Marine Invertebrates</a>
            <a class="dropdown-item" href="search.php?Database=mi">Dry Marine Invertebrates</a>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="search.php?Database=fossil">Fossils</a>
        </li>
      </li>
      <!---
      <li>
        <a class="nav-link" href="search.php?Database=all">All</a>
      </li>
      -->
    </ul>
  </div>
</nav>
<div id="page-content">