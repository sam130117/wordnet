<?php
use models\Wordnet;
use models\StringManager;

require_once('../models/Wordnet.php');
require_once('../models/StringManager.php');


$originalString = isset($_POST['original-text']) ? $_POST['original-text'] : '';
$result = '';


if (!empty($originalString)) {
    $result = StringManager::process($originalString);
}

return print_r($result);

//echo json_encode([
//    'result' => $result,
//]);

//$initialString = isset($_POST['initial-text']) ? $_POST['initial-text'] : '';
//$expectedString = isset($_POST['expected-text']) ? $_POST['expected-text'] : '';
//$distance = -1;
//$info = -1;
//$insert = -1;
//$delete = -1;
//$replace = -1;

//if(!empty($initialString) && !empty($expectedString)){
//    $distance = levenshtein($initialString, $expectedString, 1, 100, 10000);
//
//    $delete = intval($distance / 10000);
//    $currentValue = $delete < 1 ? $distance : $distance % 10000;
//    $replace = intval($currentValue / 100);
//    $currentValue = $replace < 1 ? $currentValue : $currentValue % 100;
//    $insert = intval($currentValue);
//    $info = new Wordnet($expectedString);
//    $info = ['lemma' => '123', 'pos' => '1'];
//}

//echo json_encode([
//    'info' => $info,
//    'distance' => $distance,
//    'insert' => $insert,
//    'replace' => $replace,
//    'delete' => $delete,
//]);


