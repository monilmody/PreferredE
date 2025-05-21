<?php
require_once("db-settings.php");

$horse_name = $_POST['horse_name'] ?? '';  // Horse name from the client
$field = $_POST['field'] ?? '';            // Field to be updated (e.g., size, balance)
$value = $_POST['value'] ?? '';            // New value for the field

// Allowed fields to update
$allowedFields = [
    'size',
    'size_to_foal_date',
    'short_legged',
    'balance',
    'girth',
    'withers',
    'shoulder_angle',
    'body'
];

// Validate input
if (!in_array($field, $allowedFields) || empty($horse_name)) {
    http_response_code(400);
    echo "Invalid request: Field not allowed or horse name missing.";
    exit;
}

// Step 1: Verify that the horse exists in the sales table
$query = "SELECT HORSE FROM sales WHERE HORSE = ? LIMIT 1";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('s', $horse_name);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    http_response_code(404);
    echo "Horse name not found in sales table.";
    exit;
}
$stmt->bind_result($found_horse_name);
$stmt->fetch();
$stmt->close();

// Step 2: Ensure the horse exists in horse_inspection
$insertQuery = "INSERT IGNORE INTO horse_inspection (horse) VALUES (?)";
$stmt = $mysqli->prepare($insertQuery);
if (!$stmt) {
    http_response_code(500);
    echo "Prepare failed during horse_inspection sync: " . $mysqli->error;
    exit;
}
$stmt->bind_param('s', $found_horse_name);
$stmt->execute();
$stmt->close();

// Step 3: Update the specific field in horse_inspection
$updateQuery = "UPDATE horse_inspection SET `$field` = ? WHERE horse = ?";
$stmt = $mysqli->prepare($updateQuery);
if (!$stmt) {
    http_response_code(500);
    echo "Prepare failed during update: " . $mysqli->error;
    exit;
}
$stmt->bind_param('ss', $value, $found_horse_name);

if ($stmt->execute()) {
    echo "Update successful.";
} else {
    http_response_code(500);
    echo "Database error during update: " . $stmt->error;
}

$stmt->close();
$mysqli->close();
