<?php

function inclusionInc(string $value) : void
{
    $files = glob('./includes/*.inc.php');
    
    $page = $_GET['page'] ?? 'home';

    $pageTest = './includes/' . $page . '.inc.php';

    if (in_array($pageTest, $files)) {
        require $pageTest;
    }
    
    else {
        require './includes/' . $value . '.inc.php';
    }
}
