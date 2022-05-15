<?php

function csvReader($fileName, $linesOffset = 0) : array
{
    $csvLines = array();

    $handle = fopen($fileName, "rb");

    for ($cnt=0; $cnt < $linesOffset; $cnt++) 
    { 
        fgets($handle);
    }

    while (!feof($handle))
        array_push($csvLines, explode(";", trim(fgets($handle))));
    
        fclose($handle);

    return $csvLines;
}