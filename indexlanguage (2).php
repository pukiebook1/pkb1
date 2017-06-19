<?php
session_start();
require 'requirelanguage.php';
?>
<!DOCTYPE html>
<html lang="es">
    <head>
<title>Sitio multilinugüe</title>   
    <meta charset="UTF-8">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/css/bootstrap.min.css" integrity="sha384-y3tfxAZXuh4HwSYylfB+J125MxIs6mR5FOHamPBG064zB+AFeWH94NdvaCBm8qnd" crossorigin="anonymous">
    <meta name="keywords" content="php, multilingüe, multiidioma,website">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.2/js/bootstrap.min.js" integrity="sha384-vZ2WRJMwsjRMW/8U7i6PWi6AlO1L79snBrmgiDpgIWJ82z8eA5lenwvxbMV1PAh7" crossorigin="anonymous"></script>

    </head>
        <body>
            <nav class="navbar navbar-light bg-faded">
  <div class="nav navbar-nav">
    <a class="nav-item nav-link active" href="#"><?php echo $inicio; ?> <span class="sr-only">(current)</span></a>
  
  </div>
</nav>
            <div><?php echo $holamundo; ?></div>
<div class="dropdown closed">
  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?php echo $cambiarIdioma; ?>
  </button>
  <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
      <a href="changelanguage.php?language=es">
    <button class="dropdown-item" type="button"><?php echo $spanish; ?></button>      
      </a>
      <a href="changelanguage.php?language=en">
    <button class="dropdown-item" type="button"><?php echo $english; ?></button>
      </a>
      <a href="changelanguage.php?language=fr">
    <button class="dropdown-item" type="button"><?php echo $french; ?></button>
      </a>
  </div>
</div>
        </body>   
</html>