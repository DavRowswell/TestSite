<!DOCTYPE html>
<html>
    <head>
        <?php
            
            require_once ('Filemaker.php');
            require_once ('partials/header.php');
            require_once ('functions.php');
        ?>

        <style>
        hr {
            border: 1px solid gray;
            width: 100%;
        }
        </style>

    </head>

    <body>
        <?php 
        session_start();
        require_once ('partials/navbar.php');  
        if (isset($_SESSION['error']))
            echo "<br>"; 
            echo "<p align = center>" . htmlspecialchars($_SESSION['error']) . ".</p>";
        ?>
        <br>
        <p align = "center">
        <a role="button" class="btn btn-danger" href="index.php">Main Page</a>
        <?php require_once ("partials/footer.php");?>
    </body>


</html>