<?php
/**
 * Data Sync API - REST endpoints for AI data synchronization
 * 
 * Provides endpoints for:
 * - File upload and validation
 * - Column mapping detection
 * - Data synchronization
 * - Report generation
 */

header('Content-Type: application/json');

require_once('ai_data_sync.php');
require_once('config.php');

class DataSyncAPI {
    
    private $sync;
    private $uploadDir = 'uploads/sync/';

    public function __construct() {
        $this->sync = new AIDataSync();
        
        // Create upload directory if it doesn't exist
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Handle API requests
     */
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $_GET['action'] ?? 'help';

        try {
            switch ($action) {
                case 'upload':
                    return $this->handleUpload();
                
                case 'validate':
                    return $this->handleValidate();
                
                case 'detect-columns':
                    return $this->handleDetectColumns();
                
                case 'sync':
                    return $this->handleSync();
                
                case 'report':
                    return $this->handleReport();
                
                default:
                    return $this->getHelp();
            }
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * Handle file upload
     */
    private function handleUpload() {
        if (!isset($_FILES['file'])) {
            return $this->error('No file provided');
        }

        $file = $_FILES['file'];
        $fileSize = $file['size'];
        $fileTmpName = $file['tmp_name'];
        $fileName = basename($file['name']);
        
        // Validate file
        if ($fileSize === 0) {
            return $this->error('File is empty');
        }

        if ($fileSize > 52428800) { // 50MB
            return $this->error('File is too large (max 50MB)');
        }

        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        if (!in_array(strtolower($ext), ['csv', 'xlsx', 'xls'])) {
            return $this->error('Invalid file type. Allowed: CSV, XLSX, XLS');
        }

        // Generate unique filename
        $uniqueFileName = uniqid('sync_') . '_' . $fileName;
        $uploadPath = $this->uploadDir . $uniqueFileName;

        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            return $this->success([
                'message' => 'File uploaded successfully',
                'file_id' => basename($uploadPath),
                'file_name' => $fileName,
                'size' => $fileSize
            ]);
        } else {
            return $this->error('Failed to upload file');
        }
    }

    /**
     * Handle file validation
     */
    private function handleValidate() {
        $fileId = $_POST['file_id'] ?? null;
        
        if (!$fileId) {
            return $this->error('file_id is required');
        }

        $filePath = $this->uploadDir . $fileId;
        
        if (!file_exists($filePath)) {
            return $this->error('File not found');
        }

        $validation = $this->sync->validateCSV($filePath);
        
        return $this->success([
            'file_id' => $fileId,
            'validation' => $validation
        ]);
    }

    /**
     * Detect and map columns
     */
    private function handleDetectColumns() {
        $fileId = $_POST['file_id'] ?? null;
        $table = $_POST['table'] ?? null;

        if (!$fileId || !$table) {
            return $this->error('file_id and table are required');
        }

        $filePath = $this->uploadDir . $fileId;
        
        if (!file_exists($filePath)) {
            return $this->error('File not found');
        }

        // Read CSV headers
        $handle = fopen($filePath, 'r');
        $headers = fgetcsv($handle, 10000, ',');
        fclose($handle);

        if (!$headers) {
            return $this->error('Could not read CSV headers');
        }

        $mapping = $this->sync->detectAndMapColumns($headers, $table);

        return $this->success([
            'file_id' => $fileId,
            'table' => $table,
            'csv_headers' => $headers,
            'column_mapping' => $mapping
        ]);
    }

    /**
     * Execute data synchronization
     */
    private function handleSync() {
        $fileId = $_POST['file_id'] ?? null;
        $table = $_POST['table'] ?? null;
        $uniqueKey = $_POST['unique_key'] ?? 'id';
        $manualMapping = isset($_POST['column_mapping']) ? json_decode($_POST['column_mapping'], true) : null;

        if (!$fileId || !$table) {
            return $this->error('file_id and table are required');
        }

        $filePath = $this->uploadDir . $fileId;
        
        if (!file_exists($filePath)) {
            return $this->error('File not found');
        }

        // Execute sync
        $result = $this->sync->syncData($filePath, $table, $uniqueKey, $manualMapping);

        // Clean up uploaded file after sync
        unlink($filePath);

        return $this->success($result);
    }

    /**
     * Generate detailed report
     */
    private function handleReport() {
        $report = $this->sync->generateReport();
        
        header('Content-Type: text/plain');
        echo $report;
        exit;
    }

    /**
     * Get API help/documentation
     */
    private function getHelp() {
        return $this->success([
            'api' => 'Data Sync API v1.0',
            'description' => 'AI-powered data synchronization with database',
            'endpoints' => [
                [
                    'action' => 'upload',
                    'method' => 'POST',
                    'description' => 'Upload CSV/Excel file',
                    'params' => [
                        'file' => 'File input (multipart/form-data)'
                    ]
                ],
                [
                    'action' => 'validate',
                    'method' => 'POST',
                    'description' => 'Validate uploaded file',
                    'params' => [
                        'file_id' => 'ID returned from upload'
                    ]
                ],
                [
                    'action' => 'detect-columns',
                    'method' => 'POST',
                    'description' => 'Detect and map CSV columns to database',
                    'params' => [
                        'file_id' => 'ID returned from upload',
                        'table' => 'Target database table name'
                    ]
                ],
                [
                    'action' => 'sync',
                    'method' => 'POST',
                    'description' => 'Synchronize data with database',
                    'params' => [
                        'file_id' => 'ID returned from upload',
                        'table' => 'Target database table name',
                        'unique_key' => 'Column to identify unique records (default: id)',
                        'column_mapping' => 'Optional JSON mapping override'
                    ]
                ],
                [
                    'action' => 'report',
                    'method' => 'GET',
                    'description' => 'Generate text report'
                ]
            ]
        ]);
    }

    /**
     * Return success response
     */
    private function success($data) {
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
        exit;
    }

    /**
     * Return error response
     */
    private function error($message) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $message
        ]);
        exit;
    }
}

// Initialize and handle request
$api = new DataSyncAPI();
$api->handleRequest();

?>
