# AI Data Sync System - Setup & Integration Guide

## Installation Steps

### 1. Copy Files to Your Project

Place these files in your project root directory:
- `ai_data_sync.php` - Core sync engine
- `data_sync_api.php` - REST API
- `ai_import_enhanced.php` - Web UI
- `ai_data_sync_examples.php` - Usage examples

### 2. Create Upload Directory

```bash
mkdir -p uploads/sync
chmod 755 uploads/sync
```

### 3. Verify Database Credentials

The system uses `DataSource.php` which should already have your database configuration:

```php
const HOST = 'preferredequinesalesresultsdatabase.cdq66kiey6co.us-east-1.rds.amazonaws.com';
const USERNAME = 'preferredequine';
const PASSWORD = '914MoniMaker77$$';
const DATABASENAME = 'horse';
```

### 4. Test Installation

Navigate to: `http://yourdomain.com/ai_import_enhanced.php`

You should see the upload form.

---

## How It Works - The Process

### Flow 1: Web UI (User-Friendly)

```
User uploads CSV
       ↓
System detects columns automatically
       ↓
User reviews mappings on screen
       ↓
User confirms (with optional overrides)
       ↓
System compares data:
  - For each row, checks if record exists
  - If exists, compares field values
  - If data changed, marks for UPDATE
  - If data same, marks as UNCHANGED
  - If new, marks for INSERT
       ↓
Database is updated (inserts + updates)
       ↓
User sees results with statistics
```

### Flow 2: Programmatic API

```
External system calls data_sync_api.php?action=upload
       ↓
File stored with temporary ID
       ↓
External system calls ?action=detect-columns
       ↓
API returns column mappings with confidence scores
       ↓
External system calls ?action=sync
       ↓
System processes and returns results as JSON
```

### Flow 3: Command Line

```php
require_once('ai_data_sync.php');
$sync = new AIDataSync();
$results = $sync->syncData('file.csv', 'horse', 'Hip');
```

---

## Key Features Explained

### 1. Automatic Column Detection

The system uses "fuzzy matching" - it's like having an AI that understands what columns mean:

```
CSV Headers          Database Columns       Match Score
-----------          ----------------       -----------
"Horse Name"    →    "Horse"                95%
"Birth Year"    →    "Yearfoal"             72%
"Sire ID"       →    "SIRE"                 88%
"Dam"           →    "DAM"                  98%
```

If confidence is above 60%, it's accepted.

### 2. Smart Data Matching

The system identifies which records are the same using a "unique key" (like Hip number):

```
Database Record:        CSV Record:             Result:
Hip: 12345             Hip: 12345              Same record found
Horse: "Old Name"      Horse: "New Name"       → UPDATE Horse field
Sire: "SIRE ID"        Sire: "SIRE ID"         → Leave Sire unchanged
```

### 3. Change Detection

The system only updates fields that actually changed:

```
Old Value: "Smith Farms"
New Value: "Smith Farms"  (same)
Action: NO UPDATE

Old Value: "Smith Farms"
New Value: "Jones Ranch"  (different)
Action: UPDATE
```

---

## Integration with Your Existing System

### Option A: Replace Your Current Import

1. Backup your current `import_csv.php`
2. Update your menu to link to `ai_import_enhanced.php`
3. Keep `import_csv.php` as fallback

### Option B: Add as New Feature

1. Add menu item: "Data Sync (AI-Powered)"
2. Link to `ai_import_enhanced.php`
3. Keep existing import system active

### Option C: Automated Nightly Sync

Create file: `cron_sync.php`

```php
<?php
require_once('ai_data_sync.php');

$sync = new AIDataSync();
$inboxDir = 'uploads/incoming/';

if (is_dir($inboxDir)) {
    foreach (glob($inboxDir . '*.csv') as $file) {
        $results = $sync->syncData($file, 'horse', 'Hip');
        
        // Log results
        file_put_contents('sync_log.txt', 
            date('Y-m-d H:i:s') . ' - ' . 
            basename($file) . ': ' . 
            $results['stats']['inserted'] . ' inserted, ' .
            $results['stats']['updated'] . " updated\n",
            FILE_APPEND
        );
        
        unlink($file); // Delete after processing
    }
}
```

Then add to crontab:
```bash
0 2 * * * /usr/bin/php /path/to/cron_sync.php
```

---

## Configuration Options

### Available Database Tables

The system supports these tables (modify as needed):
- `horse` - Main horse data
- `horse_sales` - Sales information
- `damsire` - Dam/Sire relationships
- `documents` - Document tracking

To add more tables, edit `ai_import_enhanced.php`:

```php
<select name="table" id="table" class="form-control" required>
    <option value="horse">Horse</option>
    <option value="horse_sales">Horse Sales</option>
    <option value="your_table">Your Table Name</option>
</select>
```

### Unique Key Column

The "unique key" is how the system identifies which records are the same.

Common unique keys:
- `Hip` - For horse data (what you currently use)
- `id` - For most tables
- `email` - For contact data
- `salecode` - For sales data

Change in UI or code:
```php
$results = $sync->syncData($file, 'horse', 'Hip');  // Change 'Hip' to your column
```

### Confidence Threshold

To be stricter or more lenient with column matching, edit `ai_data_sync.php`:

```php
// Line ~85: Change this value
if ($match['score'] > 0.6) { // 0.6 = 60% confidence
    // Lower = more lenient, higher = more strict
    $mapping[$csvHeader] = $match['column'];
}
```

---

## Monitoring & Troubleshooting

### Check Sync Log

Results are shown in the UI. For API usage, check response JSON.

### Common Issues & Solutions

**Problem: "Column not mapped"**
- Solution: Manually enter database column name in Step 2
- Check if column name has spaces or special characters

**Problem: "Records not updating"**
- Solution: Verify unique key column exists and has data
- Check that values match exactly (case-sensitive)

**Problem: "Upload fails"**
- Solution: Check `uploads/sync/` directory permissions
- Ensure file is valid CSV format

**Problem: "Memory error"**
- Solution: For large files (>10,000 rows), process in batches
- Edit `php.ini`: `memory_limit = 512M`

---

## API Examples

### Using with JavaScript

```javascript
// Upload and sync
async function syncData(file) {
    // Step 1: Upload
    let formData = new FormData();
    formData.append('file', file);
    
    let uploadRes = await fetch('data_sync_api.php?action=upload', {
        method: 'POST',
        body: formData
    });
    let uploadData = await uploadRes.json();
    let fileId = uploadData.data.file_id;
    
    // Step 2: Sync
    let syncRes = await fetch('data_sync_api.php?action=sync', {
        method: 'POST',
        body: new URLSearchParams({
            file_id: fileId,
            table: 'horse',
            unique_key: 'Hip'
        })
    });
    let syncData = await syncRes.json();
    
    console.log('Inserted:', syncData.data.stats.inserted);
    console.log('Updated:', syncData.data.stats.updated);
}
```

### Using with cURL

```bash
# Upload file
curl -X POST http://yoursite.com/data_sync_api.php?action=upload \
  -F "file=@horses.csv" \
  -o upload_response.json

# Get file ID from response
FILE_ID=$(cat upload_response.json | grep -o '"file_id":"[^"]*' | cut -d'"' -f4)

# Execute sync
curl -X POST http://yoursite.com/data_sync_api.php?action=sync \
  -d "file_id=$FILE_ID&table=horse&unique_key=Hip" \
  -o sync_response.json

cat sync_response.json
```

### Using with PHP

```php
// In your PHP code
require_once('ai_data_sync.php');

$sync = new AIDataSync();

// Validate first
$validation = $sync->validateCSV('path/to/file.csv');
if (!$validation['valid']) {
    die('File validation failed');
}

// Then sync
$results = $sync->syncData(
    'path/to/file.csv',
    'horse',
    'Hip'
);

// Check results
if ($results['status'] === 'success') {
    echo 'Success! ';
    echo $results['stats']['inserted'] . ' inserted, ';
    echo $results['stats']['updated'] . ' updated';
} else {
    echo 'Error: ' . implode(', ', $results['errors']);
}
```

---

## Performance Tips

### For Large Files (10,000+ rows):

1. **Increase PHP limits**:
   ```
   memory_limit = 512M
   max_execution_time = 300
   ```

2. **Process in batches** (modify `ai_data_sync.php`):
   ```php
   // Process 1000 rows at a time
   for ($i = 0; $i < count($csvData); $i += 1000) {
       $batch = array_slice($csvData, $i, 1000);
       foreach ($batch as $row) {
           $this->processRow($row, $table, $uniqueKey);
       }
   }
   ```

3. **Temporarily disable database triggers**:
   ```sql
   SET @disable_trigger = 1;
   -- Your INSERT/UPDATE statements here
   SET @disable_trigger = 0;
   ```

4. **Add indexes to unique key column**:
   ```sql
   ALTER TABLE horse ADD INDEX idx_hip (Hip);
   ```

---

## Security Notes

### Important:

1. **Never upload unvalidated files** - The system validates, but add your own checks
2. **Use unique keys carefully** - Wrong key can cause data duplication
3. **Backup before first use** - Always backup your database
4. **Restrict upload access** - Only logged-in users should access `ai_import_enhanced.php`

Add to `ai_import_enhanced.php` at the top:

```php
// Require authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Optional: Only admins
if ($_SESSION['user_role'] !== 'admin') {
    die('Unauthorized');
}
```

---

## Next Steps

1. Test with a small sample CSV file first
2. Review the detected column mappings carefully
3. Test sync on a non-production table
4. Once confident, use on your main data
5. Set up automated syncs with cron jobs (optional)

---

## Support Files

- `AI_DATA_SYNC_DOCUMENTATION.md` - Complete technical documentation
- `ai_data_sync_examples.php` - 8 usage examples
- This file - Setup and integration guide

---

## Questions?

Common questions answered in the main documentation file.

For troubleshooting, check:
1. File validation report
2. Column mapping confidence scores
3. Sync error messages
4. Database logs

