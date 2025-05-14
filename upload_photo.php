<?php
ob_start();

require 'vendor/autoload.php';
require 'db-settings.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Dotenv\Dotenv;

header('Content-Type: application/json');

try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    // Initialize S3 Client with updated configuration
    $s3 = new S3Client([
        'region' => $_ENV['AWS_REGION'],
        'version' => 'latest',
        'credentials' => [
            'key'    => $_ENV['AWS_ACCESS_KEY_ID'],
            'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'],
        ],
        'suppress_php_deprecation_warning' => true
    ]);

    $bucket = $_ENV['AWS_BUCKET'];

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK || !isset($_POST['horseId'])) {
        throw new Exception('Missing file or horse ID.');
    }

    $fileTmpPath = $_FILES['file']['tmp_name'];
    $horseId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_POST['horseId']);
    $originalName = $_FILES['file']['name'];
    $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
    
    $uniqueFilename = "uploads/horse_" . $horseId . "_" . time() . "." . $fileExtension;

    // Modified S3 upload parameters
    $result = $s3->putObject([
        'Bucket' => $bucket,
        'Key'    => $uniqueFilename,
        'Body'   => fopen($fileTmpPath, 'rb'),
        'ContentType' => mime_content_type($fileTmpPath)
    ]);

    // Get the public URL (works if bucket policy allows public access)
    $cmd = $s3->getCommand('GetObject', [
        'Bucket' => $bucket,
        'Key'    => $uniqueFilename
    ]);
    
    $request = $s3->createPresignedRequest($cmd, '+1 hour');

    // Database operations
    $conn = new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASS'], $_ENV['DB_NAME']);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    $objectKey = $uniqueFilename; // e.g., uploads/horse_12345_1715550000.jpg

    $stmt = $conn->prepare("INSERT INTO horse_images (horse_id, image_url, uploaded_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $horseId, $objectKey);
    if (!$stmt->execute()) {
        throw new Exception("Database error: " . $stmt->error);
    }

    $fileId = $stmt->insert_id;
    $stmt->close();
    $conn->close();

    ob_end_clean();
    die(json_encode([
        'success' => true,
        'url' => (string) $request->getUri(), // for immediate display
        'id' => $fileId,
        'name' => $originalName
    ]));

} catch (AwsException $e) {
    ob_end_clean();
    die(json_encode([
        'success' => false,
        'error' => 'S3 Error: ' . $e->getAwsErrorMessage() ?: $e->getMessage()
    ]));
} catch (Exception $e) {
    ob_end_clean();
    die(json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]));
}