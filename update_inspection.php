<?php
require_once("db-settings.php");

$horse_name = $_POST['horse_name'] ?? '';  // Horse name from the client
$field = $_POST['field'] ?? '';            // Field to be updated (e.g., size, balance)
$horse = [];

$checkboxFields = [
    'neck_upright',
    'neck_ok',
    'neck_very_nice',
    'neck_dr',
    'neck_top_horse',
    'neck_athletic',
    'neck_racey',
    'neck_clean_correct',
    'neck_needs_grow',
    'neck_needs_mature',
    'neck_needs_improve',
    'neck_nm_ty',
    'sickle_hock',
    'sickle_hock_slightly',
    'post_legged',
    'camped_out',
    'tied_in_right_knee',
    'tied_in_left_knee',
    'rt_knee_face_out',
    'rt_knee_face_in',
    'splint_right_front',
    'left_knee_face_out',
    'left_knee_face_in',
    'splint_left_front'
];

// Normalize value, especially for checkbox fields like 'neck_upright'
// Modify your value normalization section:
if (in_array($field, ['pasterns_notes', 'dave_rating_comments'])) {
    $value = isset($_POST['value']) ? trim($_POST['value']) : null;
} elseif (in_array($field, $checkboxFields)) {
    $value = isset($_POST['value']) && filter_var($_POST['value'], FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
} else {
    $value = $_POST['value'] ?? '';
}

// Allowed fields to update
$allowedFields = [
    // HORSE INFORMATION
    'sex_change',

    // DAVE REID RATING
    'day_rating_indicator',
    'dave_reid_rating',
    'up_dn_ev',
    'neck_ok',
    'neck_very_nice',
    'neck_dr',
    'neck_top_horse',
    'neck_athletic',
    'neck_racey',
    'neck_clean_correct',
    'neck_needs_grow',
    'neck_needs_mature',
    'neck_needs_improve',
    'neck_nm_ty',
    'dave_rating_comments',

    // SIDE VIEW / SIZE . BALANCE . GIRTH . WITHERS. SHOULDERS
    'size',
    'size_to_foal_date',
    'short_legged',
    'balance',
    'girth',
    'withers',
    'shoulder_angle',
    'body',

    // HEAD PLACEMENT - NECK
    'head_placement',
    'neck_upright',
    'neck_length',
    'neck_feature',

    // LENGTH - BACK - HIP - CROUP
    'body_length',
    'back',
    'back_sway',
    'behind_high',
    'behind_side',
    'hips_side',
    'hip_short',
    'hip_drops',

    // SIDE HIPS - STIFLES - GASKIN - HOCKS - SICKLE - POST
    'sickle_hock',
    'sickle_hock_slightly',
    'post_legged',
    'camped_out',
    'stifle_quality',
    'gaskin_quality',

    // SIDE KNEES (Back, Over, Tied) - PASTERNS
    'back_right_knee',
    'tied_in_right_knee',
    'over_right_knee',
    'back_left_knee',
    'tied_in_left_knee',
    'over_left_knee',
    'pasterns_length',
    'pasterns_angle',
    'pasterns_strength',
    'pasterns_notes',

    // FRONT VIEW / HEAD - EYES - EARS - NOSE
    'head_detail',
    'head_size',
    'eyes_width',
    'eyes',
    'ears',

    // FRONT VIEW / BONE - CHEST - KNEES - SPLINTS (New Fields)
    'bone',
    'chest_width',
    'base_width_front',
    'right_front',
    'right_knee',
    'left_front',
    'left_knee',
    'rt_knee_face_out',
    'rt_knee_face_in',
    'splint_right_front',
    'left_knee_face_out',
    'left_knee_face_in',
    'splint_left_front'
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

// Step 3: Fetch horse inspection data after ensuring record exists
$query = "SELECT * FROM horse_inspection WHERE horse = ? LIMIT 1";
$stmt = $mysqli->prepare($query);
$stmt->bind_param('s', $found_horse_name);
$stmt->execute();
$result = $stmt->get_result();
$horse = $result->fetch_assoc() ?? [];
$stmt->close();

// Step 4: Update the specific field in horse_inspection
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
