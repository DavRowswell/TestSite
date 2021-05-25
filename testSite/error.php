<!DOCTYPE html>
<html  lang="en">
    <head>
        <?php
            require_once ('partials/widgets.php');
            HeaderWidget('Error');
        ?>
    </head>

    <body class="d-flex flex-column">
        <?php 
            session_start();
            Navbar();
        ?>
        <div class="h-100">
            <div class="col-sm-12 my-auto">
                <?php
                if (isset($_SESSION['error']))
                    echo "<p align = center>" . htmlspecialchars($_SESSION['error']) . ".</p>";
                ?>
                <p align = "center"><a role="button" class="btn btn-danger" href="index.php">Main Page</a></p>
            </div>
        </div>
        <?php FooterWidget('images/beatyLogo.png'); ?>
    </body>


</html>