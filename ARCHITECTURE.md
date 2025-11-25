# AI Data Sync - System Architecture

## System Components

```
┌─────────────────────────────────────────────────────────────────┐
│                    USER INTERACTION LAYER                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. Web UI               2. REST API           3. PHP Code     │
│  (ai_import_            (data_sync_          (require         │
│   enhanced.php)         api.php)             ai_data_sync)     │
│                                                                 │
│  Upload CSV          POST /api/upload         Direct call      │
│  Review Mappings     GET/POST /api/detect     Local script     │
│  Confirm & Sync      POST /api/sync           Cron job         │
│  View Results        GET /api/report          Batch process    │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                    CORE SYNC ENGINE LAYER                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│                    AIDataSync Class                            │
│              (ai_data_sync.php)                                │
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐     │
│  │ 1. VALIDATION MODULE                                │     │
│  │    • validateCSV()                                  │     │
│  │    • File format check                              │     │
│  │    • Size limits                                    │     │
│  │    • Header validation                              │     │
│  └──────────────────────────────────────────────────────┘     │
│                         ↓                                      │
│  ┌──────────────────────────────────────────────────────┐     │
│  │ 2. COLUMN DETECTION MODULE                          │     │
│  │    • detectAndMapColumns()                          │     │
│  │    • Fuzzy string matching                          │     │
│  │    • Levenshtein distance algorithm                 │     │
│  │    • Confidence scoring (0-100%)                    │     │
│  └──────────────────────────────────────────────────────┘     │
│                         ↓                                      │
│  ┌──────────────────────────────────────────────────────┐     │
│  │ 3. DATA COMPARISON MODULE                           │     │
│  │    • getRecord()                                    │     │
│  │    • compareData()                                  │     │
│  │    • Detect insert/update/unchanged                 │     │
│  └──────────────────────────────────────────────────────┘     │
│                         ↓                                      │
│  ┌──────────────────────────────────────────────────────┐     │
│  │ 4. DATABASE SYNC MODULE                             │     │
│  │    • insertRecord()                                 │     │
│  │    • updateRecord()                                 │     │
│  │    • processRow()                                   │     │
│  └──────────────────────────────────────────────────────┘     │
│                         ↓                                      │
│  ┌──────────────────────────────────────────────────────┐     │
│  │ 5. REPORTING MODULE                                 │     │
│  │    • getResults()                                   │     │
│  │    • generateReport()                               │     │
│  │    • Statistics & change log                        │     │
│  └──────────────────────────────────────────────────────┘     │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                    DATA LAYER                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  CSV File Input      →      Database (AWS RDS)                │
│  (uploads/sync/)             MySQL Tables:                     │
│  • horse_data.csv           • horse                            │
│  • sales_data.csv           • horse_sales                      │
│  • etc.                     • damsire                          │
│                             • documents                        │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## Data Flow Diagram

### User Web UI Flow

```
START
  ↓
┌─────────────────┐
│  User Uploads   │
│  CSV File       │
└────────┬────────┘
         ↓
┌─────────────────────────────┐
│ ai_import_enhanced.php      │
│ (Step 1: Upload & Validate) │
│                             │
│ • Check file exists         │
│ • Validate format           │
│ • Check file size           │
└────────┬────────────────────┘
         ↓
┌─────────────────────────────┐
│ AIDataSync::validateCSV()   │
│ • Size check                │
│ • Format check              │
│ • Header check              │
└────────┬────────────────────┘
         ↓
     ✓ Valid?
    /        \
   Y          N
   ↓          └→ Error Message → User
   ↓
┌─────────────────────────────┐
│ ai_import_enhanced.php      │
│ (Step 2: Review Mappings)   │
│                             │
│ • Read CSV headers          │
│ • Call detectAndMapColumns()│
│ • Show mapping table        │
│ • Allow overrides           │
└────────┬────────────────────┘
         ↓
   User Reviews & Confirms
         ↓
┌─────────────────────────────┐
│ ai_import_enhanced.php      │
│ (Step 3: Execute Sync)      │
│                             │
│ • Read POST data            │
│ • Extract column mappings   │
│ • Call syncData()           │
└────────┬────────────────────┘
         ↓
┌─────────────────────────────┐
│ AIDataSync::syncData()      │
│ • Read CSV file             │
│ • For each row:             │
│   - Get unique value        │
│   - Check if exists in DB   │
│   - Compare data            │
│   - UPDATE or INSERT        │
│ • Track changes             │
└────────┬────────────────────┘
         ↓
┌─────────────────────────────┐
│ Database Operations         │
│ • INSERT new records        │
│ • UPDATE changed fields     │
│ • SKIP unchanged            │
└────────┬────────────────────┘
         ↓
┌─────────────────────────────┐
│ Generate Results            │
│ • Statistics               │
│ • Change log               │
│ • Error report             │
└────────┬────────────────────┘
         ↓
┌─────────────────────────────┐
│ Display Results Page        │
│ • Show stats (boxes)        │
│ • Show change log           │
│ • Show errors (if any)      │
└────────┬────────────────────┘
         ↓
       END
```

---

## Algorithm: Column Detection

```
INPUT: CSV Headers, Database Table
OUTPUT: Mapping {csv_column → db_column}, Confidence Scores

PROCEDURE detectAndMapColumns(headers, table):
    dbColumns = getTableColumns(table)
    
    FOR EACH csvHeader IN headers:
        csvHeaderLower = lowercase(trim(csvHeader))
        bestMatch = {column: null, score: 0}
        
        FOR EACH dbColumn IN dbColumns:
            dbColumnLower = lowercase(dbColumn)
            
            // Try different matching strategies
            IF csvHeaderLower === dbColumnLower:
                score = 1.0  // Exact match
            ELSE IF csvHeaderLower CONTAINS dbColumnLower OR 
                    dbColumnLower CONTAINS csvHeaderLower:
                score = 0.9  // Substring match
            ELSE:
                score = calculateLevenshtein(csvHeaderLower, dbColumnLower)
            
            IF score > bestMatch.score:
                bestMatch = {column: dbColumnLower, score: score}
        
        // Only accept matches above threshold
        IF bestMatch.score > 0.6:  // 60% confidence
            mapping[csvHeaderLower] = bestMatch.column
            confidence[csvHeaderLower] = bestMatch.score
    
    RETURN {mapping, confidence, unmapped}
```

---

## Algorithm: Data Synchronization

```
INPUT: CSV File, Table Name, Unique Key Column, Column Mapping
OUTPUT: Statistics {inserted, updated, unchanged, skipped}, Change Log

PROCEDURE syncData(filePath, table, uniqueKey, columnMapping):
    csvData = readCSV(filePath)
    
    FOR EACH row IN csvData:
        // Map CSV columns to DB columns
        mappedData = mapRowData(row, columnMapping)
        
        IF mappedData is empty:
            stats.skipped++
            CONTINUE
        
        // Get unique identifier
        uniqueValue = mappedData[uniqueKey]
        IF uniqueValue is empty:
            stats.skipped++
            CONTINUE
        
        // Check if record exists
        existingRecord = getRecord(table, uniqueKey, uniqueValue)
        
        IF existingRecord exists:
            // Compare data
            differences = compareData(existingRecord, mappedData)
            
            IF differences is not empty:
                // Update record
                updateRecord(table, uniqueKey, uniqueValue, mappedData)
                stats.updated++
                changeLog.add({action: 'updated', record: uniqueValue, changes: differences})
            ELSE:
                // No changes
                stats.unchanged++
        ELSE:
            // Insert new record
            insertRecord(table, mappedData)
            stats.inserted++
            changeLog.add({action: 'inserted', record: uniqueValue})
    
    RETURN {stats, changeLog}
```

---

## Algorithm: String Similarity (Levenshtein Distance)

```
The system uses Levenshtein distance to calculate similarity:

Example: "Horse Name" vs "Horse"
- Distance = 5 (number of edits needed)
- Max length = 10
- Similarity = 1 - (5/10) = 0.5 = 50%

Example: "Birth Date" vs "Yearfoal"
- Distance = 8
- Max length = 10
- Similarity = 1 - (8/10) = 0.2 = 20%

Example: "Sire" vs "SIRE"
- Distance = 0 (case-insensitive)
- Max length = 4
- Similarity = 1 - (0/4) = 1.0 = 100%
```

---

## Data Comparison Logic

```
FOR EACH column IN mappedData:
    newValue = trim(mappedData[column])
    existingValue = trim(existingRecord[column]) OR ""
    
    IF newValue ≠ existingValue:
        differences[column] = {old: existingValue, new: newValue}

IF differences is empty:
    Action = "UNCHANGED"
ELSE:
    Action = "UPDATE"
    Log each difference
```

---

## Module Responsibilities

### 1. Validation Module
**Purpose**: Ensure input data quality
**Functions**:
- File exists and readable
- Correct format (CSV)
- Valid encoding
- File size within limits
- Headers present and valid

### 2. Column Detection Module
**Purpose**: Map CSV columns to database columns
**Functions**:
- Read CSV headers
- Get database columns
- Calculate similarity scores
- Filter by confidence threshold
- Return mappings and confidence scores

### 3. Data Comparison Module
**Purpose**: Identify which records need updating
**Functions**:
- Find existing record by unique key
- Compare each field
- Detect changes
- Track which action to take

### 4. Database Sync Module
**Purpose**: Update database with changes
**Functions**:
- Execute INSERT statements for new records
- Execute UPDATE statements for changed records
- Handle SQL errors
- Track operations

### 5. Reporting Module
**Purpose**: Report results to user
**Functions**:
- Calculate statistics
- Format change log
- Generate text report
- Return JSON response

---

## Configuration & Extension Points

### Confidence Threshold
```php
// Line 85 in ai_data_sync.php
if ($match['score'] > 0.6) {  // Change 0.6 to adjust
    $mapping[$csvHeader] = $match['column'];
}
```
- Lower = more lenient (0.4 = 40%)
- Higher = more strict (0.8 = 80%)

### File Size Limit
```php
// Line 353 in ai_data_sync.php
if ($fileSize > 52428800) { // 50MB, change as needed
    $warnings[] = "File is large";
}
```

### Database Tables
```php
// Edit ai_import_enhanced.php
// Add more options to the table selector
<option value="your_table">Your Table Name</option>
```

### Unique Key Column
```php
// When calling syncData()
$results = $sync->syncData($file, 'horse', 'Hip');
//                                        ^^^^
//                               Change this to use different column
```

---

## Performance Characteristics

### Time Complexity
- CSV Reading: O(n) where n = number of rows
- Column Detection: O(h × d) where h = CSV headers, d = DB columns
- Data Comparison: O(n × m) where n = rows, m = columns
- Overall: O(n × m)

### Space Complexity
- CSV Data: O(n × m)
- Change Log: O(c) where c = number of changes
- Mappings: O(h × d)

### Optimization Tips
1. **Index the unique key column** - Speeds up lookups
2. **Process in batches** - For files with 50,000+ rows
3. **Disable triggers** - Temporarily during bulk updates
4. **Increase PHP memory** - For large files

---

## Error Handling & Recovery

### Error Types
```
1. Validation Errors (recoverable)
   - File not found
   - Invalid format
   - Bad encoding
   
2. Mapping Errors (recoverable)
   - Column not found
   - Duplicate headers
   
3. Sync Errors (partially recoverable)
   - Database connection
   - Invalid SQL
   - Data type mismatch
   
4. Data Errors (non-recoverable)
   - Empty unique key
   - Corrupted data
```

### Recovery Strategy
- Validate early
- Log errors for user review
- Continue processing on non-critical errors
- Rollback on critical errors (future)

---

## Security Architecture

```
┌─────────────────────────────┐
│ Input Validation            │
│ • File type check           │
│ • File size check           │
│ • Format validation         │
└────────┬────────────────────┘
         ↓
┌─────────────────────────────┐
│ Data Sanitization           │
│ • Trim whitespace           │
│ • Escape SQL                │
│ • Type casting              │
└────────┬────────────────────┘
         ↓
┌─────────────────────────────┐
│ Access Control              │
│ • Session check             │
│ • Role validation           │
│ • Rate limiting             │
└────────┬────────────────────┘
         ↓
┌─────────────────────────────┐
│ Prepared Statements         │
│ • Parameterized queries     │
│ • No SQL injection          │
└────────┬────────────────────┘
         ↓
┌─────────────────────────────┐
│ Audit Logging               │
│ • All changes logged        │
│ • Timestamps                │
│ • User tracking             │
└────────┬────────────────────┘
```

---

## Integration Points

### With Your Existing System
```
Your Application
       ↓
    [Menu]
       ↓
┌──────────────────┐
│ AI Import        │ → ai_import_enhanced.php
│ Horse List       │ → horse_list.php
│ Import CSV       │ → import_csv.php (original)
│ Manage Data      │ → manage_data.php
└──────────────────┘
```

### With External Systems
```
Third-Party System
       ↓
    [API Call]
       ↓
data_sync_api.php
       ↓
AIDataSync Engine
       ↓
Database Updates
       ↓
    [JSON Response]
       ↓
Third-Party System
```

---

## Future Enhancements

### Planned Features
1. **Transaction Support**
   - Atomic operations
   - Rollback capability

2. **Batch Processing**
   - Memory optimization
   - Progress tracking

3. **Custom Validation**
   - Business rule engine
   - Data quality checks

4. **Scheduled Syncs**
   - Cron integration
   - Queue system

5. **Webhooks**
   - Event notifications
   - External triggers

6. **Advanced Matching**
   - Multiple unique keys
   - Fuzzy record matching
   - Duplicate detection

7. **Transformation Pipeline**
   - Data normalization
   - Value mapping
   - Calculations

---

## Deployment Checklist

- [ ] Copy all PHP files to production server
- [ ] Create `uploads/sync/` directory with write permissions
- [ ] Verify database credentials in `DataSource.php`
- [ ] Test with sample CSV file
- [ ] Add authentication/authorization
- [ ] Update menu links
- [ ] Backup production database
- [ ] Train users
- [ ] Monitor first few syncs
- [ ] Document in your system

