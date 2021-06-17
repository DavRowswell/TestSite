<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
            require_once ('partials/widgets.php');
            HeaderWidget('Error');
        ?>
    </head>

    <body>
        <?php 
            session_start();
            Navbar();
        ?>

        <div class="container-fluid d-flex flex-grow-1 justify-content-center align-items-center">
            <div class="text-center">
                <?php
                if (isset($_SESSION['error'])) {
                    $error_text = htmlspecialchars($_SESSION['error']);
                    echo "<p>$error_text</p>";
                }
                ?>
                <p><a role="button" class="btn btn-danger" href="index.php">Main Page</a></p>
            </div>
        </div>

        <?php FooterWidget('public/images/beatyLogo.png'); ?>
    </body>
</html>