<?php
/**
 * AI-Powered Data Synchronization System
 * 
 * This module provides intelligent CSV/Excel data synchronization with database.
 * Features:
 * - Automatic column detection and mapping
 * - Smart data matching and comparison
 * - Change detection and updates
 * - New record insertion
 * - Data validation
 */

require_once('DataSource.php');
require_once('config.php');

class AIDataSync {
    
    private $db;
    private $conn;
    private $columnMapping = [];
    private $changeLog = [];
    private $errors = [];
    private $stats = [
        'inserted' => 0,
        'updated' => 0,
        'unchanged' => 0,
        'skipped' => 0
    ];

    public function __construct() {
        $this->db = new \Phppot\DataSource();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Detect and map CSV columns using AI logic
     * 
     * @param array $headers - CSV headers from first row
     * @param string $table - Target database table
     * @return array - Mapped columns [csv_col => db_col]
     */
    public function detectAndMapColumns($headers, $table) {
        // Get database table structure
        $dbColumns = $this->getTableColumns($table);
        
        // AI-based fuzzy matching
        $mapping = [];
        $confidence = [];
        
        foreach ($headers as $index => $csvHeader) {
            $csvHeader = trim(strtolower($csvHeader));
            $match = $this->findBestMatch($csvHeader, $dbColumns);
            
            if ($match['score'] > 0.6) { // 60% confidence threshold
                $mapping[$csvHeader] = $match['column'];
                $confidence[$csvHeader] = $match['score'];
            }
        }
        
        $this->columnMapping = $mapping;
        
        return [
            'mapping' => $mapping,
            'confidence' => $confidence,
            'unmapped' => array_diff_key(array_flip($headers), $mapping)
        ];
    }

    /**
     * Fuzzy string matching algorithm
     */
    private function findBestMatch($csvHeader, $dbColumns) {
        $bestMatch = ['column' => null, 'score' => 0];
        
        foreach ($dbColumns as $dbCol) {
            $dbCol = strtolower($dbCol);
            $score = $this->calculateSimilarity($csvHeader, $dbCol);
            
            if ($score > $bestMatch['score']) {
                $bestMatch = ['column' => $dbCol, 'score' => $score];
            }
        }
        
        return $bestMatch;
    }

    /**
     * Calculate string similarity using Levenshtein distance
     */
    private function calculateSimilarity($str1, $str2) {
        $strlen1 = strlen($str1);
        $strlen2 = strlen($str2);
        
        // Check for exact match
        if ($str1 === $str2) {
            return 1.0;
        }
        
        // Check if one contains the other
        if (strpos($str1, $str2) !== false || strpos($str2, $str1) !== false) {
            return 0.9;
        }
        
        // Levenshtein distance
        $distance = levenshtein($str1, $str2);
        $maxLen = max($strlen1, $strlen2);
        $similarity = 1.0 - ($distance / $maxLen);
        
        return max(0, $similarity);
    }

    /**
     * Get columns from database table
     */
    private function getTableColumns($table) {
        $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND TABLE_SCHEMA = DATABASE()";
        $result = $this->db->select($query, "s", [$table]);
        
        $columns = [];
        if ($result) {
            foreach ($result as $row) {
                $columns[] = $row['COLUMN_NAME'];
            }
        }
        
        return $columns;
    }

    /**
     * Sync CSV data with database
     * 
     * @param string $filePath - Path to CSV file
     * @param string $table - Target database table
     * @param string $uniqueKey - Column to identify unique records
     * @param array $columnMapping - Manual column mapping (optional)
     * @return array - Sync results
     */
    public function syncData($filePath, $table, $uniqueKey, $columnMapping = null) {
        if (!file_exists($filePath)) {
            $this->errors[] = "File not found: $filePath";
            return $this->getResults();
        }

        // Read CSV file
        $csvData = $this->readCSV($filePath);
        
        if (empty($csvData)) {
            $this->errors[] = "CSV file is empty";
            return $this->getResults();
        }

        // Detect or use provided column mapping
        if ($columnMapping === null) {
            $headers = array_shift($csvData);
            $mapResult = $this->detectAndMapColumns($headers, $table);
            $this->columnMapping = $mapResult['mapping'];
        } else {
            $this->columnMapping = $columnMapping;
            array_shift($csvData); // Remove header row
        }

        // Validate that unique key is mapped
        if (!in_array($uniqueKey, array_values($this->columnMapping))) {
            $this->errors[] = "Unique key '$uniqueKey' not found in column mapping";
            return $this->getResults();
        }

        // Process each row
        foreach ($csvData as $rowIndex => $row) {
            $this->processRow($row, $table, $uniqueKey);
        }

        return $this->getResults();
    }

    /**
     * Process individual row - compare and update/insert
     */
    private function processRow($row, $table, $uniqueKey) {
        // Map row data to database columns
        $mappedData = [];
        foreach ($this->columnMapping as $csvIndex => $dbColumn) {
            // Find the CSV header index
            $colIndex = array_search($csvIndex, array_keys($this->columnMapping));
            if (isset($row[$colIndex])) {
                $mappedData[$dbColumn] = $row[$colIndex];
            }
        }

        if (empty($mappedData)) {
            $this->stats['skipped']++;
            return;
        }

        // Get unique identifier value
        $uniqueValue = $mappedData[$uniqueKey] ?? null;
        if (!$uniqueValue) {
            $this->stats['skipped']++;
            return;
        }

        // Check if record exists
        $existingRecord = $this->getRecord($table, $uniqueKey, $uniqueValue);

        if ($existingRecord) {
            // Compare data
            $differences = $this->compareData($existingRecord, $mappedData);
            
            if (!empty($differences)) {
                // Update record
                $this->updateRecord($table, $uniqueKey, $uniqueValue, $mappedData);
                $this->stats['updated']++;
                $this->changeLog[] = [
                    'action' => 'updated',
                    'record' => $uniqueValue,
                    'changes' => $differences
                ];
            } else {
                $this->stats['unchanged']++;
            }
        } else {
            // Insert new record
            $this->insertRecord($table, $mappedData);
            $this->stats['inserted']++;
            $this->changeLog[] = [
                'action' => 'inserted',
                'record' => $uniqueValue
            ];
        }
    }

    /**
     * Read CSV file and return data array
     */
    private function readCSV($filePath) {
        $data = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($row = fgetcsv($handle, 10000, ',')) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }
        return $data;
    }

    /**
     * Get existing record from database
     */
    private function getRecord($table, $uniqueKey, $uniqueValue) {
        $query = "SELECT * FROM $table WHERE $uniqueKey = ?";
        $result = $this->db->select($query, "s", [$uniqueValue]);
        
        return $result ? $result[0] : null;
    }

    /**
     * Compare existing vs new data
     */
    private function compareData($existing, $new) {
        $differences = [];
        
        foreach ($new as $column => $newValue) {
            $newValue = trim($newValue);
            $existingValue = isset($existing[$column]) ? trim($existing[$column]) : '';
            
            if ($existingValue !== $newValue) {
                $differences[$column] = [
                    'old' => $existingValue,
                    'new' => $newValue
                ];
            }
        }
        
        return $differences;
    }

    /**
     * Insert new record
     */
    private function insertRecord($table, $data) {
        $columns = array_keys($data);
        $values = array_values($data);
        
        $columnList = implode(',', $columns);
        $placeholders = implode(',', array_fill(0, count($columns), '?'));
        
        $query = "INSERT INTO $table ($columnList) VALUES ($placeholders)";
        
        // Build paramType string
        $paramType = str_repeat('s', count($values));
        
        return $this->db->insert($query, $paramType, $values);
    }

    /**
     * Update existing record
     */
    private function updateRecord($table, $uniqueKey, $uniqueValue, $data) {
        $updates = [];
        $values = [];
        
        foreach ($data as $column => $value) {
            if ($column !== $uniqueKey) {
                $updates[] = "$column = ?";
                $values[] = $value;
            }
        }
        
        if (empty($updates)) {
            return;
        }
        
        $values[] = $uniqueValue;
        $updateList = implode(',', $updates);
        $query = "UPDATE $table SET $updateList WHERE $uniqueKey = ?";
        
        $paramType = str_repeat('s', count($values));
        
        return $this->db->update($query, $paramType, $values);
    }

    /**
     * Get synchronization results
     */
    public function getResults() {
        return [
            'status' => empty($this->errors) ? 'success' : 'error',
            'stats' => $this->stats,
            'changes' => $this->changeLog,
            'errors' => $this->errors
        ];
    }

    /**
     * Validate CSV before sync
     */
    public function validateCSV($filePath) {
        if (!file_exists($filePath)) {
            return ['valid' => false, 'errors' => ['File not found']];
        }

        $errors = [];
        $warnings = [];
        
        // Check file size
        $fileSize = filesize($filePath);
        if ($fileSize === 0) {
            $errors[] = "CSV file is empty";
        }
        if ($fileSize > 52428800) { // 50MB
            $warnings[] = "File is large (greater than 50MB), processing may be slow";
        }

        // Check file format
        if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'csv') {
            $warnings[] = "File extension is not .csv";
        }

        // Read and validate headers
        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = fgetcsv($handle, 10000, ',');
            
            if ($headers === false) {
                $errors[] = "Could not read CSV headers";
            } else {
                // Check for duplicate headers
                $headerCounts = array_count_values($headers);
                foreach ($headerCounts as $header => $count) {
                    if ($count > 1) {
                        $warnings[] = "Duplicate header: '$header'";
                    }
                }
            }
            
            fclose($handle);
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }

    /**
     * Generate sync report
     */
    public function generateReport() {
        $report = "=== DATA SYNCHRONIZATION REPORT ===\n\n";
        $report .= "Statistics:\n";
        $report .= "- Records Inserted: " . $this->stats['inserted'] . "\n";
        $report .= "- Records Updated: " . $this->stats['updated'] . "\n";
        $report .= "- Records Unchanged: " . $this->stats['unchanged'] . "\n";
        $report .= "- Records Skipped: " . $this->stats['skipped'] . "\n\n";
        
        if (!empty($this->changeLog)) {
            $report .= "Change Log:\n";
            foreach ($this->changeLog as $log) {
                $report .= "- " . strtoupper($log['action']) . ": " . $log['record'] . "\n";
                if (isset($log['changes'])) {
                    foreach ($log['changes'] as $column => $change) {
                        $report .= "  {$column}: '{$change['old']}' → '{$change['new']}'\n";
                    }
                }
            }
        }

        if (!empty($this->errors)) {
            $report .= "\nErrors:\n";
            foreach ($this->errors as $error) {
                $report .= "- ERROR: $error\n";
            }
        }

        return $report;
    }
}

?>
