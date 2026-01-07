<?php
ini_set('log_errors', 1);
ini_set('error_log', 'php://stderr');
require_once 'functions.php';
require 'db-settings.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $horseName = $_POST['horseId'] ?? null;

    if (empty($horseName)) {
        echo json_encode(['success' => false, 'error' => 'Horse name is required']);
        exit;
    }

    // Build the data array from known expected fields
    $expectedFields = ['YEARFOAL', 'SEX', 'Sire', 'DAM', 'DATEFOAL', 'TYPE', 'COLOR', 'GAIT', 'BREDTO', 'FARMNAME'];
    $data = [];

    foreach ($expectedFields as $field) {
        if (isset($_POST[$field])) {
            $data[$field] = $_POST[$field];
        }
    }

    // Call the update function
    $result = updateHorseDetailsTb($horseName, $data);

    // Return the result as JSON
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
