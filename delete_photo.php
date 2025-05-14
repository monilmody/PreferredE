<?php
require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Content-Type: application/json');

// Validate request
if (!isset($_POST['imageUrl'])) {
    echo json_encode(['success' => false, 'error' => 'Missing image URL']);
    exit;
}

$imageUrl = $_POST['imageUrl'];

// Parse S3 key from URL
$parsedUrl = parse_url($imageUrl);
$path = ltrim($parsedUrl['path'], '/'); // This is your S3 key

// Delete from S3
try {
    $s3 = new S3Client([
        'region' => $_ENV['AWS_REGION'],
        'version' => 'latest',
        'credentials' => [
            'key' => $_ENV['AWS_ACCESS_KEY_ID'],
            'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
        ], 
        'suppress_php_deprecation_warning' => true
    ]);

    $bucket = $_ENV['AWS_BUCKET'];

    $s3->deleteObject([
        'Bucket' => $bucket,
        'Key' => $path
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'S3 delete failed: ' . $e->getMessage()]);
    exit;
}

// Delete from database
require 'db-settings.php'; // $mysqli should be defined here

$stmt = $mysqli->prepare("DELETE FROM horse_images WHERE image_url = ?");
$stmt->bind_param("s", $path);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Database entry not found']);
}
$stmt->close();
$mysqli->close();
