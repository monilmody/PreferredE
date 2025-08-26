<?php
ob_start();

require 'vendor/autoload.php';
require 'db-settings.php';

use Aws\S3\S3Client;
use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;
use Aws\Sts\StsClient;

header('Content-Type: application/json');

try {
    $region = 'us-east-1';

    $roleArn = 'arn:aws:iam::211125609145:role/python-website-logs'; // Role to assume
    $sessionName = 'GetHorseDetailsSession';

    // Step 1: Assume Role to get temporary credentials
    $stsClient = new StsClient([
        'region' => $region,
        'version' => 'latest',
        'DurationSeconds' => 3600,  // Set the session duration (1 hour in this case)
    ]);

    error_log("Assuming role: $roleArn");
    $assumeRoleResult = $stsClient->assumeRole([
        'RoleArn' => $roleArn,
        'RoleSessionName' => $sessionName,
    ]);

    error_log(message: "AssumeRole result: " . json_encode($assumeRoleResult));
    error_log("Temporary credentials received: " . json_encode($assumeRoleResult['Credentials']));

    $creds = $assumeRoleResult['Credentials'];

    // Secrets Manager client
    $secretsClient = new SecretsManagerClient([
        'version' => 'latest',
        'region' => $region,
        'credentials' => [
            'key'    => $creds['AccessKeyId'],
            'secret' => $creds['SecretAccessKey'],
            'token'  => $creds['SessionToken'],
        ],
    ]);

    // Load S3 credentials
    $s3SecretResult = $secretsClient->getSecretValue([
        'SecretId' => 'MyApp/S3Credentials',
    ]);
    $s3Secrets = json_decode($s3SecretResult['SecretString'], true);

    $bucket = $s3Secrets['AWS_BUCKET'];

    $s3 = new S3Client([
        'region' => $region,
        'version' => 'latest',
        'credentials' => [
            'key'    => $creds['AccessKeyId'],
            'secret' => $creds['SecretAccessKey'],
            'token'  => $creds['SessionToken'],
        ],
        'suppress_php_deprecation_warning' => true // ✅ THIS LINE
    ]);

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK || !isset($_POST['horseId'])) {
        throw new Exception('Missing file or horse ID.');
    }

    $fileTmpPath = $_FILES['file']['tmp_name'];
    $originalName = $_FILES['file']['name'];
    $fileMimeType = mime_content_type($fileTmpPath);
    $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    // Extension & MIME type check
    $allowedExtensions = ['pdf', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff'];
    $allowedMimeTypes = [
        'application/pdf',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/bmp',
        'image/tiff'
    ];

    if (!in_array($fileExtension, $allowedExtensions)) {
        throw new Exception("Invalid file extension: .$fileExtension");
    }

    if (!in_array($fileMimeType, $allowedMimeTypes)) {
        throw new Exception("Unsupported MIME type: $fileMimeType");
    }

    // 🔍 Scan file for viruses using ClamAV
    $clamScanResult = shell_exec("clamscan " . escapeshellarg($fileTmpPath));

    if (strpos($clamScanResult, 'Infected files: 1') !== false) {
        throw new Exception("Virus detected in uploaded file. Upload blocked.");
    }

    $horseId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_POST['horseId']);
    $uniqueFilename = "uploads/horse_" . $horseId . "_" . time() . "." . $fileExtension;

    $s3->putObject([
        'Bucket' => $bucket,
        'Key'    => $uniqueFilename,
        'Body'   => fopen($fileTmpPath, 'rb'),
        'ContentType' => $fileMimeType
    ]);

    // Presigned URL valid for 5 minutes
    $cmd = $s3->getCommand('GetObject', [
        'Bucket' => $bucket,
        'Key'    => $uniqueFilename
    ]);
    $request = $s3->createPresignedRequest($cmd, '+5 minutes');

    // DB insert (uses global $mysqli from db-settings.php)
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO horse_images (horse_id, image_url, uploaded_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $horseId, $uniqueFilename);

    if (!$stmt->execute()) {
        throw new Exception("Database error: " . $stmt->error);
    }

    $fileId = $stmt->insert_id;
    $stmt->close();

    ob_end_clean();
    echo json_encode([
        'success' => true,
        'url' => (string) $request->getUri(),
        'id' => $fileId,
        'name' => $originalName
    ]);
    exit;
} catch (AwsException $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'error' => 'AWS Error: ' . ($e->getAwsErrorMessage() ?: $e->getMessage())
    ]);
    exit;
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}
