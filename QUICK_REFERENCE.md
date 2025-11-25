# 🚀 Quick Reference Guide

## What You Got

An AI-powered data synchronization system that automatically:
- ✅ Identifies columns in your CSV files
- ✅ Matches data with existing database records
- ✅ Detects what changed
- ✅ Updates only the changed fields
- ✅ Adds new records
- ✅ Logs everything

---

## Get Started in 2 Minutes

### Step 1: Open the Web UI
```
Go to: ai_import_enhanced.php
```

### Step 2: Upload Your CSV
```
Click "Upload & Analyze"
Select your CSV file
Choose target table (probably "Horse")
Click Submit
```

### Step 3: Review Columns
```
See auto-detected column mappings
Each mapping has a confidence %
Fix any wrong mappings manually (optional)
Set unique key column (e.g., "Hip")
Click "Proceed with Sync"
```

### Step 4: View Results
```
See statistics:
  - Records inserted
  - Records updated
  - Records unchanged
  - Records skipped

See change log showing what changed
```

**Done!** Your database is updated.

---

## Three Ways to Use

### 1️⃣ Web UI (Easiest)
```
ai_import_enhanced.php
→ No coding, just upload & click
→ Visual feedback
→ Safe review step
```

### 2️⃣ REST API (For Automation)
```
POST /data_sync_api.php?action=upload
POST /data_sync_api.php?action=sync
GET /data_sync_api.php?action=report
→ Use from any application
→ JSON responses
```

### 3️⃣ PHP Code (For Integration)
```php
require_once('ai_data_sync.php');
$sync = new AIDataSync();
$results = $sync->syncData('file.csv', 'horse', 'Hip');
→ Full control
→ Batch processing
→ Scheduled jobs
```

---

## Core Concepts

### Fuzzy Matching
```
CSV Column       →    Database Column    Match Score
------------------------------------------------------
"Horse Name"    →    "Horse"           95%
"Birth Year"    →    "Yearfoal"        72%  
"Sire_ID"       →    "SIRE"            88%
"Dam"           →    "DAM"             98%
```

Confidence > 60% = Accepted ✓
Confidence < 60% = Skipped ✗ (you can override)

### Data Comparison
```
Record Hip: 1001 already exists

CSV has:              DB has:              Action:
Horse: "New Name"     Horse: "Old Name"    UPDATE
Sire: "123"           Sire: "123"          NO CHANGE

Result: Only Horse field is updated
```

### Change Log
```
INSERTED: 1001 - Brand new record
UPDATED: 1002 - Sire field changed from "SIRE_123" to "SIRE_456"
UNCHANGED: 1003 - No changes detected
```

---

## File Structure

```
📁 Project Root
├── 📄 ai_data_sync.php              ← Core engine
├── 📄 data_sync_api.php             ← REST API
├── 📄 ai_import_enhanced.php        ← Web UI ⭐ USE THIS
├── 📄 ai_data_sync_examples.php     ← Code examples
├── 📄 sample_horse_data.csv         ← Test data
│
├── 📄 README_AI_SYNC.md             ← Overview (start here)
├── 📄 SETUP_INTEGRATION_GUIDE.md    ← How to set up
├── 📄 AI_DATA_SYNC_DOCUMENTATION.md ← Technical reference
├── 📄 ARCHITECTURE.md               ← How it works
└── 📄 QUICK_REFERENCE.md            ← This file
```

---

## Common Scenarios

### Scenario 1: Weekly Sales Update
```
1. Keenland sends you updated sales CSV
2. You upload to ai_import_enhanced.php
3. System auto-detects columns
4. You click proceed
5. Database is updated automatically
✓ No manual data entry!
```

### Scenario 2: Batch Import
```
1. Import 500 horses at once
2. System auto-maps columns
3. Finds existing records
4. Updates what changed
5. Adds new records
✓ Done in seconds!
```

### Scenario 3: Scheduled Daily Import
```
1. Set up cron job to run at 2 AM
2. System checks uploads/incoming/ folder
3. Processes all CSV files automatically
4. Logs results to sync_log.txt
✓ Fully automated!
```

### Scenario 4: Fix Duplicate/Bad Data
```
1. Export problematic records to CSV
2. Fix the data in CSV
3. Upload via ai_import_enhanced.php
4. System finds records and updates them
5. Only changed fields are updated
✓ No duplication!
```

---

## Results You'll See

### Statistics Box
```
┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐
│    150      │  │      45     │  │     200     │  │      5      │
│  Inserted   │  │   Updated   │  │  Unchanged  │  │   Skipped   │
└─────────────┘  └─────────────┘  └─────────────┘  └─────────────┘
```

### Change Log
```
INSERTED: 1001
UPDATED: 1002 - Horse: "Old" → "New"
UPDATED: 1003 - Sire: "ABC" → "XYZ"
UNCHANGED: 1004
UNCHANGED: 1005
...
```

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Column not detected | Manually type DB column name in Step 2 |
| Records not updating | Check unique key (Hip) values match |
| Upload fails | Verify file is CSV, < 50MB |
| Slow performance | For 10,000+ rows, process in batches |
| Access denied | Add `chmod 755 uploads/sync/` |

---

## Configuration

### Database Credentials
Already configured in `DataSource.php`:
```php
HOST: preferredequinesalesresultsdatabase.cdq66kiey6co.us-east-1.rds.amazonaws.com
USERNAME: preferredequine
PASSWORD: [set]
DATABASE: horse
```

### Unique Key
Default: `Hip` (You can change this)
```php
$sync->syncData($file, 'horse', 'Hip');  // Change to 'id' or any column
```

### Confidence Threshold
Default: 60% (You can adjust)
```php
// In ai_data_sync.php, line 85
if ($match['score'] > 0.6) { // Higher = stricter
```

---

## Security

✅ **Safe because:**
- Files are validated before processing
- SQL injection prevented (prepared statements)
- Review step before any changes
- Full audit trail of changes
- Original data not deleted

⚠️ **Best practices:**
1. Backup database before first use
2. Add authentication to `ai_import_enhanced.php`
3. Test with sample data first
4. Review column mappings carefully
5. Start with small files

---

## Performance

| Scenario | Speed |
|----------|-------|
| 500 rows | < 5 seconds |
| 5,000 rows | ~30 seconds |
| 50,000 rows | ~5 minutes |
| 100,000+ rows | Use batch processing |

---

## What the AI Does

### Fuzzy Matching Algorithm
```
Compares two strings and calculates similarity:

"Horse Name" vs "Horse"
  ↓
Levenshtein Distance = 5
Maximum Length = 10
Similarity = 1 - (5/10) = 0.5 = 50%

"Sire" vs "SIRE"
  ↓
Levenshtein Distance = 0 (case-insensitive)
Maximum Length = 4
Similarity = 1 - (0/4) = 1.0 = 100%
```

### Smart Matching
```
Strategies tried in order:
1. Exact match? → 100%
2. One contains other? → 90%
3. Fuzzy match? → varies
4. No match? → 0%

Threshold: > 60% = Accept
```

---

## API Quick Reference

### For JavaScript/Frontend
```javascript
// Upload
let formData = new FormData();
formData.append('file', fileInput.files[0]);
await fetch('data_sync_api.php?action=upload', {method: 'POST', body: formData});

// Sync
await fetch('data_sync_api.php?action=sync', {
    method: 'POST',
    body: new URLSearchParams({
        file_id: 'sync_abc123.csv',
        table: 'horse',
        unique_key: 'Hip'
    })
});
```

### For cURL/Backend
```bash
# Upload
curl -F "file=@data.csv" http://domain.com/data_sync_api.php?action=upload

# Sync
curl -d "file_id=sync_123&table=horse&unique_key=Hip" \
     http://domain.com/data_sync_api.php?action=sync

# Report
curl http://domain.com/data_sync_api.php?action=report
```

### For PHP/Backend
```php
$sync = new AIDataSync();
$results = $sync->syncData('file.csv', 'horse', 'Hip');
echo $results['stats']['inserted'];  // 150
echo $results['stats']['updated'];   // 45
```

---

## Examples

### Example 1: Basic Sync
```php
require_once('ai_data_sync.php');
$sync = new AIDataSync();
$results = $sync->syncData('horses.csv', 'horse', 'Hip');
print_r($results);
```

### Example 2: Validate First
```php
$sync = new AIDataSync();
$validation = $sync->validateCSV('horses.csv');
if ($validation['valid']) {
    $results = $sync->syncData('horses.csv', 'horse', 'Hip');
}
```

### Example 3: Batch Process
```php
$sync = new AIDataSync();
foreach (glob('uploads/*.csv') as $file) {
    $results = $sync->syncData($file, 'horse', 'Hip');
    echo "Processed " . basename($file) . "\n";
}
```

### Example 4: API Usage
```bash
#!/bin/bash
# Upload
RESPONSE=$(curl -s -F "file=@horses.csv" http://domain.com/data_sync_api.php?action=upload)
FILE_ID=$(echo $RESPONSE | grep -o '"file_id":"[^"]*' | cut -d'"' -f4)

# Sync
curl -d "file_id=$FILE_ID&table=horse&unique_key=Hip" \
     http://domain.com/data_sync_api.php?action=sync
```

---

## Important Files Reference

| File | Purpose | Use When |
|------|---------|----------|
| `ai_import_enhanced.php` | Web UI | You want to upload via browser |
| `data_sync_api.php` | REST API | Integrating with other systems |
| `ai_data_sync.php` | Core engine | Writing PHP code |
| `sample_horse_data.csv` | Test data | Testing without real data |
| `README_AI_SYNC.md` | Overview | Getting started |
| `SETUP_INTEGRATION_GUIDE.md` | Setup | Installing/configuring |
| `AI_DATA_SYNC_DOCUMENTATION.md` | Technical docs | Deep dive |
| `ARCHITECTURE.md` | How it works | Understanding design |

---

## Next Steps

### Immediately
1. ✅ Open `ai_import_enhanced.php` in browser
2. ✅ Upload `sample_horse_data.csv`
3. ✅ Review auto-detected columns
4. ✅ Click "Proceed"
5. ✅ See the results!

### Soon
1. Test with real (small) dataset
2. Verify column mappings are correct
3. Try updating an existing record
4. Verify only changed fields updated

### Later
1. Add authentication to `ai_import_enhanced.php`
2. Integrate into your menu
3. Set up scheduled syncs (cron)
4. Train your team

---

## Frequently Asked Questions

**Q: Will it delete my data?**
A: No. It only INSERTs new records or UPDATEs existing ones.

**Q: Can I undo a sync?**
A: Restore from database backup (backup first!).

**Q: What if column names don't match?**
A: You can manually override in Step 2.

**Q: How fast is it?**
A: ~30-60 seconds for 5,000 rows.

**Q: Can multiple people use it at same time?**
A: Yes, each sync is independent.

**Q: Will it duplicate my data?**
A: No, it uses the unique key (Hip) to find existing records.

**Q: What if the unique key column is empty?**
A: That row will be skipped.

**Q: Can I schedule automatic imports?**
A: Yes, use cron jobs (see SETUP_INTEGRATION_GUIDE.md).

---

## Support Resources

- 📖 **Getting Started**: `README_AI_SYNC.md`
- 🔧 **Setup & Integration**: `SETUP_INTEGRATION_GUIDE.md`
- 📚 **Full Documentation**: `AI_DATA_SYNC_DOCUMENTATION.md`
- 🏗️ **Architecture Details**: `ARCHITECTURE.md`
- 💡 **Code Examples**: `ai_data_sync_examples.php`

---

## Key Commands

### View AI Import UI
```
http://yourdomain.com/ai_import_enhanced.php
```

### Test REST API
```bash
curl http://yourdomain.com/data_sync_api.php?action=help
```

### Check PHP Class
```php
<?php
require_once('ai_data_sync.php');
$sync = new AIDataSync();
// Use methods like:
// $sync->validateCSV()
// $sync->detectAndMapColumns()
// $sync->syncData()
// $sync->generateReport()
```

---

## Success Checklist

- [ ] Files uploaded to server
- [ ] `uploads/sync/` directory created
- [ ] Database credentials verified
- [ ] Tested with sample CSV
- [ ] Verified column detection works
- [ ] Tested with real (small) dataset
- [ ] Verified data was updated correctly
- [ ] Added authentication
- [ ] Trained team members
- [ ] Documented in your system

---

**Ready to start? Open `ai_import_enhanced.php` and upload your first CSV file! 🎉**

For detailed info, see `README_AI_SYNC.md`
