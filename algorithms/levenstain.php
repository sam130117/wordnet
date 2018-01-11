<?php
use models\StringManager;

require_once('../models/WordnetManager.php');
require_once('../models/StringManager.php');


$originalString = isset($_POST['original-text']) ? $_POST['original-text'] : '';
$result = '';

if (!empty($originalString)) {
    $result = StringManager::process($originalString);
}

echo json_encode([
    'result' => $result,
]);
