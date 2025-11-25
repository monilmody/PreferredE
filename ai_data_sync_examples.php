<?php
/**
 * AI Data Sync - Quick Start Guide
 * 
 * This file demonstrates the main ways to use the AI Data Sync system
 */

// Example 1: Simple Web UI Usage
// =============================
// Just navigate to: /ai_import_enhanced.php
// The UI will guide you through:
// 1. Upload CSV
// 2. Review column mappings
// 3. Execute sync
// 4. View results


// Example 2: Programmatic Usage
// ==============================

require_once('ai_data_sync.php');

/**
 * CASE 1: Auto-detect columns and sync
 */
function syncWithAutoDetection() {
    $sync = new AIDataSync();
    
    // Validate file first
    $validation = $sync->validateCSV('uploads/horse_data.csv');
    if (!$validation['valid']) {
        echo "Validation failed: " . implode(", ", $validation['errors']);
        return;
    }
    
    // Sync data (auto-detects columns)
    $results = $sync->syncData(
        'uploads/horse_data.csv',
        'horse',          // target table
        'Hip',            // unique key column
        null              // auto-detect columns
    );
    
    // Check results
    if ($results['status'] === 'success') {
        echo "Sync completed!\n";
        echo "Inserted: " . $results['stats']['inserted'] . "\n";
        echo "Updated: " . $results['stats']['updated'] . "\n";
        echo "Unchanged: " . $results['stats']['unchanged'] . "\n";
    } else {
        echo "Errors occurred:\n";
        foreach ($results['errors'] as $error) {
            echo "- $error\n";
        }
    }
    
    // Generate report
    echo $sync->generateReport();
}


/**
 * CASE 2: Manual column mapping
 */
function syncWithManualMapping() {
    $sync = new AIDataSync();
    
    // Define your column mapping
    $columnMapping = [
        'hip' => 'Hip',
        'horse' => 'Horse',
        'yearfoal' => 'Yearfoal',
        'sex' => 'Sex',
        'sire' => 'SIRE',
        'dam' => 'DAM',
        'color' => 'Color',
        'gait' => 'Gait',
        'farmname' => 'Farmname'
    ];
    
    // Sync with manual mapping
    $results = $sync->syncData(
        'uploads/horse_data.csv',
        'horse',
        'Hip',
        $columnMapping
    );
    
    return $results;
}


/**
 * CASE 3: Detect mapping first, review, then sync
 */
function syncWithMappingReview() {
    $sync = new AIDataSync();
    
    // Step 1: Read CSV headers
    $handle = fopen('uploads/horse_data.csv', 'r');
    $headers = fgetcsv($handle, 10000, ',');
    fclose($handle);
    
    // Step 2: Auto-detect mappings
    $mappingResult = $sync->detectAndMapColumns($headers, 'horse');
    
    // Step 3: Review mappings (in production, show to user)
    echo "=== Column Mapping Analysis ===\n";
    foreach ($headers as $header) {
        $mapped = $mappingResult['mapping'][strtolower($header)] ?? 'Not mapped';
        $confidence = $mappingResult['confidence'][strtolower($header)] ?? 0;
        echo "$header -> $mapped (" . round($confidence * 100) . "%)\n";
    }
    
    // Step 4: Adjust mapping if needed
    $adjustedMapping = $mappingResult['mapping'];
    // Manually override any incorrect mappings:
    // $adjustedMapping['horse_id'] = 'Hip'; // example override
    
    // Step 5: Execute sync with adjusted mapping
    $results = $sync->syncData(
        'uploads/horse_data.csv',
        'horse',
        'Hip',
        $adjustedMapping
    );
    
    return $results;
}


/**
 * CASE 4: API Usage
 */
function apiUsageExample() {
    // This would be called from JavaScript or external application
    
    /*
    // Step 1: Upload file
    curl -X POST http://yoursite.com/data_sync_api.php?action=upload \
      -F "file=@horse_data.csv"
    
    Response:
    {
      "status": "success",
      "data": {
        "file_id": "sync_abc123_horse_data.csv",
        "file_name": "horse_data.csv",
        "size": 102400
      }
    }
    
    // Step 2: Validate file
    curl -X POST http://yoursite.com/data_sync_api.php?action=validate \
      -d "file_id=sync_abc123_horse_data.csv"
    
    // Step 3: Detect columns
    curl -X POST http://yoursite.com/data_sync_api.php?action=detect-columns \
      -d "file_id=sync_abc123_horse_data.csv&table=horse"
    
    // Step 4: Execute sync
    curl -X POST http://yoursite.com/data_sync_api.php?action=sync \
      -d "file_id=sync_abc123_horse_data.csv&table=horse&unique_key=Hip"
    
    Response:
    {
      "status": "success",
      "data": {
        "stats": {
          "inserted": 150,
          "updated": 45,
          "unchanged": 200,
          "skipped": 5
        },
        "changes": [...]
      }
    }
    */
}


/**
 * CASE 5: Batch Processing Multiple Files
 */
function batchSync($directory) {
    $sync = new AIDataSync();
    $files = glob($directory . '/*.csv');
    $totalResults = [
        'inserted' => 0,
        'updated' => 0,
        'unchanged' => 0,
        'skipped' => 0,
        'errors' => 0
    ];
    
    foreach ($files as $filePath) {
        echo "Processing: " . basename($filePath) . "\n";
        
        $results = $sync->syncData($filePath, 'horse', 'Hip', null);
        
        if ($results['status'] === 'success') {
            $totalResults['inserted'] += $results['stats']['inserted'];
            $totalResults['updated'] += $results['stats']['updated'];
            $totalResults['unchanged'] += $results['stats']['unchanged'];
            $totalResults['skipped'] += $results['stats']['skipped'];
            echo "  ✓ Completed\n";
        } else {
            $totalResults['errors']++;
            echo "  ✗ Error: " . implode(", ", $results['errors']) . "\n";
        }
    }
    
    echo "\n=== Batch Summary ===\n";
    echo "Total Inserted: " . $totalResults['inserted'] . "\n";
    echo "Total Updated: " . $totalResults['updated'] . "\n";
    echo "Total Unchanged: " . $totalResults['unchanged'] . "\n";
    echo "Total Skipped: " . $totalResults['skipped'] . "\n";
    echo "Errors: " . $totalResults['errors'] . "\n";
    
    return $totalResults;
}


/**
 * CASE 6: Schedule Regular Syncs (Cron Job)
 */
function scheduledSync() {
    // Add to your cron job script
    // Run this daily/weekly to sync incoming files
    
    $sync = new AIDataSync();
    $inboxDir = 'uploads/inbox/';
    $processedDir = 'uploads/processed/';
    
    // Get all unprocessed files
    $files = glob($inboxDir . '*.csv');
    
    $logFile = fopen('sync_log.txt', 'a');
    fwrite($logFile, "Sync run at " . date('Y-m-d H:i:s') . "\n");
    
    foreach ($files as $file) {
        $result = $sync->syncData($file, 'horse', 'Hip', null);
        
        // Log result
        $log = basename($file) . " - " . $result['status'];
        $log .= " (I:" . $result['stats']['inserted'] . 
                " U:" . $result['stats']['updated'] . 
                " U:" . $result['stats']['unchanged'] . ")\n";
        fwrite($logFile, $log);
        
        // Move processed file
        if ($result['status'] === 'success') {
            rename($file, $processedDir . basename($file));
        }
    }
    
    fclose($logFile);
}


/**
 * CASE 7: Data Validation Before Sync
 */
function validateBeforeSync($filePath) {
    $sync = new AIDataSync();
    
    $validation = $sync->validateCSV($filePath);
    
    echo "=== File Validation Report ===\n";
    echo "Valid: " . ($validation['valid'] ? 'YES' : 'NO') . "\n";
    
    if (!empty($validation['errors'])) {
        echo "\nErrors:\n";
        foreach ($validation['errors'] as $error) {
            echo "- $error\n";
        }
        return false;
    }
    
    if (!empty($validation['warnings'])) {
        echo "\nWarnings:\n";
        foreach ($validation['warnings'] as $warning) {
            echo "- $warning\n";
        }
    }
    
    return true;
}


/**
 * CASE 8: Monitor Sync Progress
 */
function syncWithProgress($filePath) {
    $sync = new AIDataSync();
    
    // Read CSV to get row count
    $lines = count(file($filePath));
    echo "File has $lines lines\n";
    
    // For large files, could add progress tracking
    // This would require modifications to the core sync class
    
    $results = $sync->syncData($filePath, 'horse', 'Hip', null);
    
    echo "Progress: 100%\n";
    echo "Results: " . json_encode($results['stats']) . "\n";
    
    return $results;
}


// ======================
// QUICK START - CHOOSE CASE
// ======================

if (php_sapi_name() === 'cli') {
    // Command line usage
    if ($argc > 1) {
        $case = $argv[1];
        $file = $argv[2] ?? null;
        
        switch ($case) {
            case '1':
                syncWithAutoDetection();
                break;
            case '2':
                syncWithManualMapping();
                break;
            case '3':
                syncWithMappingReview();
                break;
            case '5':
                batchSync($file ?? 'uploads/');
                break;
            case '7':
                validateBeforeSync($file ?? 'uploads/horse_data.csv');
                break;
            default:
                echo "Usage: php ai_data_sync_examples.php <case> [file]\n";
                echo "Cases: 1, 2, 3, 5, 7\n";
        }
    }
}

?>
