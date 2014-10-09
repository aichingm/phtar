<?php

spl_autoload_register(function($classname) {
    $path = str_replace("\\", DIRECTORY_SEPARATOR, $classname);
    if (strpos($path, "phtar" . DIRECTORY_SEPARATOR) == 0 && is_file(__DIR__ . DIRECTORY_SEPARATOR . $path . ".php")) {
        require_once __DIR__ . DIRECTORY_SEPARATOR . $path . ".php";
    }
});
