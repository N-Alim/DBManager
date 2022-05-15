<?php
session_start();
date_default_timezone_set('Europe/Paris');
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

require_once __DIR__.'/vendor/autoload.php';
require './functions/autoLoadFunction.php';

spl_autoload_register(function ($className) {
    include './classes/' . $className . '.php';
});

require './includes/head.php';
require './includes/main.php';
require './includes/footer.php';
