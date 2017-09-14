<?php
$initialString = isset($_POST['initial-text']) ? $_POST['initial-text'] : '';
$expectedString = isset($_POST['expected-text']) ? $_POST['expected-text'] : '';
$distance = -1;

if(!empty($initialString) && !empty($expectedString)){
    $distance = levenshtein($initialString, $expectedString);
//    $distance = levenshtein($initialString, $expectedString, 5, 7, 9);
}
echo $distance;

