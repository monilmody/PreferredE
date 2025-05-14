<?php
// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', 'php://stderr');
require_once 'functions.php';    // Include your functions file with getHorseDetails()

require 'db-settings.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get POST data
    $horseId = $_POST['horseId'];
    $yearFoal = $_POST['YEARFOAL'];
    $sex = $_POST['SEX'];
    $sire = $_POST['Sire'];
    $dam = $_POST['DAM'];

    // Call the updateHorseDetails function
    $result = updateHorseDetails($horseId, $yearFoal, $sex, $sire, $dam);

    // Return the result as JSON
    echo json_encode($result);
    exit;
}
