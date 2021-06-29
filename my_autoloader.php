<?php

# kudos to https://www.youtube.com/watch?v=z3pZdmJ64jo
spl_autoload_register(function ($className) {
    $path = "classes/";
    $ext = ".php";
    $fullPath = $path . $className . $ext;

    if (!file_exists($fullPath)) {
        return false;
    }

    include $fullPath;
});




