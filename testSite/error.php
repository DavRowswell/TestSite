<!DOCTYPE html>
<html>
    <head>
        <?php
            
            require_once ('Filemaker.php');
            require_once ('partials/header.php');
            require_once ('functions.php');

        ?>
    </head>

    <body>
        <?php 
        session_start();
        require_once ('partials/navbar.php');  

        echo $_SESSION['error'];
        ?>   
        <?php require_once ("partials/footer.php");?>
    </body>


</html>