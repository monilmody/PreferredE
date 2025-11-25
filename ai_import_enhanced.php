<?php
/**
 * Enhanced Horse Data Import with AI Synchronization
 * 
 * Integrates with existing import_csv.php to provide:
 * - Automatic column detection
 * - Smart data matching
 * - Change tracking
 * - Duplicate prevention
 */

include("./header.php");
include("./session_page.php");
include_once("config.php");
require_once('ai_data_sync.php');

use Phppot\DataSource;

echo '<br><h1 style="text-align:center;color:#FF6B35;">AI-POWERED DATA IMPORT & SYNC</h1><br>';

// Check if form is submitted
$showResults = false;
$syncResults = null;
$mappingResults = null;
$fileId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['step']) && $_POST['step'] === 'upload') {
        // Step 1: Upload and validate file
        
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $error = "No file uploaded or upload error occurred";
        } else {
            $sync = new AIDataSync();
            
            // Validate file
            $validation = $sync->validateCSV($_FILES['file']['tmp_name']);
            
            if (!$validation['valid']) {
                $error = implode(", ", $validation['errors']);
            } else {
                // Store file temporarily
                $uploadDir = 'uploads/sync/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileId = uniqid('sync_') . '_' . basename($_FILES['file']['name']);
                $uploadPath = $uploadDir . $fileId;
                
                if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath)) {
                    // Detect columns
                    $handle = fopen($uploadPath, 'r');
                    $headers = fgetcsv($handle, 10000, ',');
                    fclose($handle);
                    
                    $table = $_POST['table'] ?? 'horse';
                    $mappingResults = $sync->detectAndMapColumns($headers, $table);
                    
                    // Display mapping for user review
                    $_SESSION['sync_file_id'] = $fileId;
                    $_SESSION['sync_table'] = $table;
                    $_SESSION['sync_headers'] = $headers;
                }
            }
        }
    }
    elseif (isset($_POST['step']) && $_POST['step'] === 'confirm') {
        // Step 2: Confirm mapping and execute sync
        
        $fileId = $_SESSION['sync_file_id'] ?? null;
        $table = $_SESSION['sync_table'] ?? 'horse';
        $uniqueKey = $_POST['unique_key'] ?? 'Hip';
        
        if (!$fileId) {
            $error = "Session expired. Please upload again.";
        } else {
            $sync = new AIDataSync();
            $uploadDir = 'uploads/sync/';
            $uploadPath = $uploadDir . $fileId;
            
            // Build column mapping from POST data
            $manualMapping = [];
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'map_') === 0 && $value) {
                    $csvCol = str_replace('map_', '', $key);
                    $manualMapping[$csvCol] = $value;
                }
            }
            
            // Execute sync
            $syncResults = $sync->syncData($uploadPath, $table, $uniqueKey, $manualMapping);
            
            // Clean up
            if (file_exists($uploadPath)) {
                unlink($uploadPath);
            }
            unset($_SESSION['sync_file_id']);
            unset($_SESSION['sync_table']);
            unset($_SESSION['sync_headers']);
            
            $showResults = true;
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="assets/css/table.css">
    <style>
        .sync-container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        
        .mapping-table {
            margin: 20px 0;
            width: 100%;
        }
        
        .mapping-table th {
            background-color: #FF6B35;
            color: white;
            padding: 10px;
        }
        
        .mapping-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .confidence-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .confidence-high {
            background-color: #5cb85c;
            color: white;
        }
        
        .confidence-medium {
            background-color: #f0ad4e;
            color: white;
        }
        
        .confidence-low {
            background-color: #d9534f;
            color: white;
        }
        
        .stats-box {
            display: inline-block;
            margin: 10px 15px;
            padding: 15px;
            background-color: white;
            border-left: 4px solid #FF6B35;
            border-radius: 3px;
        }
        
        .stats-box h3 {
            margin: 0;
            font-size: 32px;
            color: #FF6B35;
        }
        
        .stats-box p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        
        .change-log {
            background-color: white;
            padding: 15px;
            margin: 15px 0;
            border-radius: 3px;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .change-item {
            padding: 8px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }
        
        .change-item.inserted {
            background-color: #d4edda;
            color: #155724;
        }
        
        .change-item.updated {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .alert-custom {
            padding: 15px;
            margin: 15px 0;
            border-radius: 3px;
        }
        
        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .form-section {
            margin: 20px 0;
        }
        
        .btn-action {
            padding: 10px 20px;
            margin: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="sync-container">
    
    <!-- Step 1: File Upload -->
    <?php if (!$showResults && !$mappingResults): ?>
        
        <h2>Step 1: Upload Data File</h2>
        <p class="text-muted">Upload a CSV or Excel file with your data. The system will automatically detect columns and match them to your database.</p>
        
        <?php if (isset($error)): ?>
            <div class="alert-custom alert-error">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="step" value="upload">
            
            <div class="form-group">
                <label for="file">Select File (CSV, XLSX, XLS):</label>
                <input type="file" name="file" id="file" class="form-control" accept=".csv,.xlsx,.xls" required>
            </div>
            
            <div class="form-group">
                <label for="table">Target Database Table:</label>
                <select name="table" id="table" class="form-control" required>
                    <option value="horse">Horse</option>
                    <option value="horse_sales">Horse Sales</option>
                    <option value="damsire">Dam/Sire</option>
                    <option value="documents">Documents</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary btn-action">Upload & Analyze</button>
        </form>
    
    <?php endif; ?>
    
    
    <!-- Step 2: Column Mapping Review -->
    <?php if ($mappingResults && !$showResults): ?>
        
        <h2>Step 2: Review Column Mapping</h2>
        <p class="text-muted">The system has detected the following column mappings. Please review and adjust as needed.</p>
        
        <table class="mapping-table">
            <thead>
                <tr>
                    <th>CSV Column</th>
                    <th>Database Column</th>
                    <th>Confidence</th>
                    <th>Override</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['sync_headers'] as $index => $header): ?>
                    <?php
                        $header = trim($header);
                        $mapped = $mappingResults['mapping'][strtolower($header)] ?? null;
                        $confidence = $mappingResults['confidence'][strtolower($header)] ?? 0;
                        
                        if ($confidence >= 0.8) {
                            $confidenceClass = 'confidence-high';
                            $confidenceText = 'High (' . round($confidence * 100) . '%)';
                        } elseif ($confidence >= 0.6) {
                            $confidenceClass = 'confidence-medium';
                            $confidenceText = 'Medium (' . round($confidence * 100) . '%)';
                        } else {
                            $confidenceClass = 'confidence-low';
                            $confidenceText = 'Low (' . round($confidence * 100) . '%)';
                        }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($header); ?></td>
                        <td>
                            <?php if ($mapped): ?>
                                <strong><?php echo htmlspecialchars($mapped); ?></strong>
                            <?php else: ?>
                                <span style="color: #999;">Not mapped</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($confidence > 0): ?>
                                <span class="confidence-badge <?php echo $confidenceClass; ?>">
                                    <?php echo $confidenceText; ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <input type="text" name="map_<?php echo htmlspecialchars(strtolower($header)); ?>" 
                                   placeholder="Override..." class="form-control" style="font-size: 12px;">
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div style="background-color: #f0f0f0; padding: 15px; margin-top: 15px; border-radius: 3px;">
            <form method="POST">
                <input type="hidden" name="step" value="confirm">
                <input type="hidden" name="table" value="<?php echo htmlspecialchars($_SESSION['sync_table']); ?>">
                
                <div style="margin-bottom: 15px;">
                    <label><strong>Unique Key Column (for matching):</strong></label>
                    <input type="text" name="unique_key" class="form-control" placeholder="e.g., Hip, ID" 
                           value="Hip" style="margin-top: 5px;">
                </div>
                
                <button type="submit" class="btn btn-success btn-action">Proceed with Sync</button>
                <a href="?reset" class="btn btn-default btn-action">Start Over</a>
            </form>
        </div>
    
    <?php endif; ?>
    
    
    <!-- Step 3: Sync Results -->
    <?php if ($showResults && $syncResults): ?>
        
        <h2>Synchronization Complete</h2>
        
        <?php if ($syncResults['status'] === 'success'): ?>
            <div class="alert-custom alert-success">
                <strong>Success!</strong> Data has been synchronized with the database.
            </div>
        <?php else: ?>
            <div class="alert-custom alert-error">
                <strong>Errors occurred:</strong>
                <ul>
                    <?php foreach ($syncResults['errors'] as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <!-- Statistics -->
        <div style="margin: 20px 0;">
            <h3>Statistics</h3>
            <div style="display: flex; flex-wrap: wrap;">
                <div class="stats-box">
                    <h3><?php echo $syncResults['stats']['inserted']; ?></h3>
                    <p>Records Inserted</p>
                </div>
                <div class="stats-box">
                    <h3><?php echo $syncResults['stats']['updated']; ?></h3>
                    <p>Records Updated</p>
                </div>
                <div class="stats-box">
                    <h3><?php echo $syncResults['stats']['unchanged']; ?></h3>
                    <p>Records Unchanged</p>
                </div>
                <div class="stats-box">
                    <h3><?php echo $syncResults['stats']['skipped']; ?></h3>
                    <p>Records Skipped</p>
                </div>
            </div>
        </div>
        
        <!-- Change Log -->
        <?php if (!empty($syncResults['changes'])): ?>
            <div>
                <h3>Change Log</h3>
                <div class="change-log">
                    <?php foreach ($syncResults['changes'] as $log): ?>
                        <div class="change-item <?php echo htmlspecialchars($log['action']); ?>">
                            <strong><?php echo strtoupper($log['action']); ?>:</strong> 
                            <?php echo htmlspecialchars($log['record']); ?>
                            
                            <?php if (isset($log['changes'])): ?>
                                <ul style="margin: 5px 0 0 20px; padding: 0;">
                                    <?php foreach ($log['changes'] as $column => $change): ?>
                                        <li style="font-size: 11px;">
                                            <strong><?php echo htmlspecialchars($column); ?>:</strong>
                                            "<?php echo htmlspecialchars($change['old']); ?>" → 
                                            "<?php echo htmlspecialchars($change['new']); ?>"
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 20px;">
            <a href="ai_import_enhanced.php" class="btn btn-primary btn-action">Import Another File</a>
            <a href="horse_list.php" class="btn btn-default btn-action">View Horse List</a>
        </div>
    
    <?php endif; ?>

</div>

<?php if (isset($_GET['reset'])): ?>
    <script>
        window.location.href = 'ai_import_enhanced.php';
    </script>
<?php endif; ?>

</body>
</html>
