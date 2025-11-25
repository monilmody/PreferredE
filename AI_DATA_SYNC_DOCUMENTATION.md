# AI Data Synchronization System - Documentation

## Overview

The AI Data Synchronization System is an intelligent solution designed to automate the process of importing, matching, and syncing CSV/Excel data with your database. It features:

- **Automatic Column Detection**: Uses AI/fuzzy matching to detect and map CSV columns to database columns
- **Smart Data Matching**: Identifies unique records and detects changes
- **Change Tracking**: Logs all inserts, updates, and unchanged records
- **Data Validation**: Validates CSV files before processing
- **Conflict Resolution**: Intelligently handles duplicate and conflicting data

---

## Components

### 1. **ai_data_sync.php** - Core Synchronization Engine

The main class `AIDataSync` handles all data synchronization logic.

#### Key Methods:

**`detectAndMapColumns($headers, $table)`**
- Detects CSV headers and maps them to database columns
- Uses fuzzy string matching with Levenshtein distance
- Returns confidence scores (0-1) for each match
- Threshold: 60% confidence minimum

```php
$sync = new AIDataSync();
$mapping = $sync->detectAndMapColumns($csvHeaders, 'horse');
// Returns: ['horse_name' => 'Horse', 'birth_date' => 'Yearfoal', ...]
```

**`syncData($filePath, $table, $uniqueKey, $columnMapping = null)`**
- Executes full data synchronization
- Detects new records and updates existing ones
- Returns comprehensive statistics and change log

```php
$results = $sync->syncData(
    'uploads/sync/file.csv',
    'horse',
    'Hip',  // Unique identifier column
    null    // Auto-detect columns or provide manual mapping
);
```

**`validateCSV($filePath)`**
- Validates file format, size, and headers
- Returns detailed validation report with errors and warnings

```php
$validation = $sync->validateCSV('path/to/file.csv');
if (!$validation['valid']) {
    echo "Errors: " . implode(", ", $validation['errors']);
}
```

**`generateReport()`**
- Creates text report of sync results
- Includes statistics, change log, and errors

---

### 2. **data_sync_api.php** - REST API Interface

Provides HTTP endpoints for programmatic access to sync functionality.

#### Endpoints:

**POST `/data_sync_api.php?action=upload`**
Upload a CSV file
```
Parameters:
  file: File input (multipart/form-data)

Response:
{
  "status": "success",
  "data": {
    "file_id": "sync_abc123_data.csv",
    "file_name": "data.csv",
    "size": 102400
  }
}
```

**POST `/data_sync_api.php?action=validate`**
Validate uploaded file
```
Parameters:
  file_id: ID from upload endpoint

Response:
{
  "status": "success",
  "data": {
    "validation": {
      "valid": true,
      "errors": [],
      "warnings": []
    }
  }
}
```

**POST `/data_sync_api.php?action=detect-columns`**
Detect column mappings
```
Parameters:
  file_id: ID from upload
  table: Target database table (e.g., 'horse')

Response:
{
  "status": "success",
  "data": {
    "csv_headers": ["Hip", "Horse", "Sire", ...],
    "column_mapping": {
      "mapping": {"hip": "Hip", "horse": "Horse", ...},
      "confidence": {"hip": 0.95, "horse": 0.90, ...},
      "unmapped": [...]
    }
  }
}
```

**POST `/data_sync_api.php?action=sync`**
Execute data synchronization
```
Parameters:
  file_id: ID from upload
  table: Target database table
  unique_key: Column for unique record identification (default: 'id')
  column_mapping: JSON column mapping (optional)

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
    "changes": [
      {
        "action": "inserted",
        "record": "12345"
      },
      {
        "action": "updated",
        "record": "67890",
        "changes": {
          "Horse": {"old": "Old Name", "new": "New Name"}
        }
      }
    ]
  }
}
```

**GET `/data_sync_api.php?action=report`**
Download text report (returns plain text)

**GET `/data_sync_api.php?action=help`**
Get API documentation

---

### 3. **ai_import_enhanced.php** - Web UI

User-friendly web interface for data synchronization with visual feedback.

#### Features:

- **Step 1: Upload**
  - File upload form
  - Format validation
  - File type restriction (CSV, XLSX, XLS)

- **Step 2: Review Mapping**
  - Display detected column mappings
  - Show confidence scores (color-coded)
  - Allow manual overrides
  - Specify unique key column

- **Step 3: Results**
  - Display synchronization statistics
  - Show change log with details
  - Color-coded changes (green=inserted, yellow=updated)

#### Usage:

1. Navigate to `ai_import_enhanced.php`
2. Upload your CSV file and select target table
3. Review the detected column mappings
4. Override any incorrect mappings if needed
5. Click "Proceed with Sync"
6. View results and change log

---

## Column Mapping Algorithm

The system uses a multi-layered approach to match CSV columns to database columns:

1. **Exact Match**: If CSV header exactly matches database column name
2. **Substring Match**: If one string contains the other
3. **Fuzzy Match**: Uses Levenshtein distance to calculate similarity

Confidence Score Thresholds:
- **Exact Match**: 1.0 (100%)
- **Substring Match**: 0.9 (90%)
- **Fuzzy Match**: 0.6-0.9 (varies by similarity)
- **Accept Threshold**: 0.6+ (60%)

Examples:
```
"Horse Name" → "Horse" (0.89)
"Birth Date" → "Yearfoal" (0.75)
"Sire ID" → "SIRE" (0.85)
"Dam/Sire" → "DAMSIRE" (0.70)
```

---

## Data Comparison Logic

When updating records, the system:

1. **Identifies Unique Records**: Uses specified unique key (e.g., Hip, ID)
2. **Compares Fields**: Checks if new data differs from existing
3. **Handles Nulls**: Treats empty strings and nulls as equivalent
4. **Trims Whitespace**: Removes leading/trailing spaces
5. **Records Changes**: Logs old vs new values

Example:
```
Database: Hip='12345', Horse='Old Name', Sire='SIRE ID'
CSV:      Hip='12345', Horse='New Name', Sire='SIRE ID'
Result:   Updated, Horse changed from 'Old Name' to 'New Name'
```

---

## Error Handling

The system provides comprehensive error handling:

### Validation Errors:
- File not found
- Empty file
- Invalid file format
- Corrupt CSV structure
- Duplicate headers

### Sync Errors:
- Missing unique key in mapping
- Database connection issues
- Invalid column names
- Data type mismatches

### Recovery:
- All errors logged in results
- Partial sync continues on non-critical errors
- Detailed error messages for debugging
- Transaction rollback support (future enhancement)

---

## Configuration

### Database Configuration
Update `DataSource.php` with your database credentials:

```php
const HOST = 'your-host.rds.amazonaws.com';
const USERNAME = 'your-username';
const PASSWORD = 'your-password';
const DATABASENAME = 'horse';
```

### Upload Directory
Default upload directory: `uploads/sync/`
Ensure this directory is writable:

```bash
mkdir -p uploads/sync
chmod 755 uploads/sync
```

---

## Usage Examples

### Example 1: Basic Sync with Auto-Detection

```php
require_once('ai_data_sync.php');

$sync = new AIDataSync();
$results = $sync->syncData(
    'horse_data.csv',
    'horse',
    'Hip'
);

echo "Inserted: " . $results['stats']['inserted'] . "\n";
echo "Updated: " . $results['stats']['updated'] . "\n";
```

### Example 2: Manual Column Mapping

```php
$mapping = [
    'horse_name' => 'Horse',
    'birth_date' => 'Yearfoal',
    'sire_name' => 'SIRE',
    'dam_name' => 'DAM'
];

$results = $sync->syncData(
    'horse_data.csv',
    'horse',
    'Hip',
    $mapping
);
```

### Example 3: API Usage with JavaScript

```javascript
// Upload file
const formData = new FormData();
formData.append('file', fileInput.files[0]);

const uploadResponse = await fetch('data_sync_api.php?action=upload', {
    method: 'POST',
    body: formData
});
const uploadData = await uploadResponse.json();
const fileId = uploadData.data.file_id;

// Detect columns
const detectResponse = await fetch('data_sync_api.php?action=detect-columns', {
    method: 'POST',
    body: new URLSearchParams({
        file_id: fileId,
        table: 'horse'
    })
});
const detectData = await detectResponse.json();

// Execute sync
const syncResponse = await fetch('data_sync_api.php?action=sync', {
    method: 'POST',
    body: new URLSearchParams({
        file_id: fileId,
        table: 'horse',
        unique_key: 'Hip'
    })
});
const syncData = await syncResponse.json();
console.log(syncData.data.stats);
```

---

## Performance Considerations

### File Size Limits
- Recommended: < 10,000 rows
- Maximum: 50MB (configurable)
- Very large files may require batch processing

### Optimization Tips
1. **Index Unique Key**: Ensure the unique key column is indexed
2. **Batch Processing**: Process large files in chunks
3. **Memory**: Monitor memory usage for large datasets
4. **Database**: Consider temporarily disabling triggers during sync

---

## Troubleshooting

### Column Not Detected
- Check CSV headers match database column names (case-insensitive)
- Verify columns exist in database table
- Manually override in Step 2 of web UI

### Records Not Updating
- Verify unique key column is correctly specified
- Check data types match (strings vs numbers)
- Verify database connection credentials

### File Upload Fails
- Check `uploads/sync/` directory permissions
- Verify file size < 50MB
- Ensure valid CSV format

### Out of Memory
- Process file in smaller batches
- Increase PHP memory limit in `php.ini`:
  ```
  memory_limit = 512M
  ```

---

## Future Enhancements

- [ ] Batch processing for large files
- [ ] Transaction support with rollback
- [ ] Custom validation rules
- [ ] Scheduled synchronization
- [ ] Data transformation pipeline
- [ ] Advanced conflict resolution
- [ ] Change notifications/webhooks
- [ ] Audit logging

---

## Support

For issues or questions:
1. Check error messages in sync results
2. Review validation report
3. Check database connection
4. Review column mapping confidence scores

