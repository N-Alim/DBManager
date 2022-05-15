<?php

$functionFiles = glob('./functions/*.php');

for ($i = 0 ; $i < count($functionFiles) ; $i++) {
    if ($functionFiles[$i] !== './functions/autoLoadFunction.php') {
        require $functionFiles[$i];
    }
}
