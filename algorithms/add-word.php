<?php
use models\WordnetManager;

require_once('../models/WordnetManager.php');


$lemma = isset($_POST['lemma']) ? $_POST['lemma'] : '';
$frequency = isset($_POST['frequency']) ? $_POST['frequency'] : 0;
$sample = isset($_POST['sample']) ? $_POST['sample'] : '';
$result = '';

if (!empty($lemma)) {
    $result = WordnetManager::addWord($lemma, $frequency, $sample);
}

echo json_encode([
    'result' => $result,
]);