<?php
header('Content-Type: application/json');
require_once 'db-settings.php';  // Include your DB connection
require_once 'functions.php';    // Include your functions file with getHorseDetails()

// Function to sanitize the horseId
function sanitizeHorseId($horseId) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '', $horseId); // Removes any invalid characters
}

// Check if the horseId is set in the GET request
if (isset($_GET['horseId'])) {
    $horseId = $_GET['horseId'];

    // Sanitize the horseId before passing to the function
    $sanitizedHorseId = sanitizeHorseId($horseId);

    // Call getHorseDetails with the sanitized horseId
    $data = getHorseDetails($sanitizedHorseId);

    // Return the data as a JSON response
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'Missing horseId']);
}
