<?php
// Include the database connection file
include("./header.php");
include("./session_page.php");
include_once("config.php");

// Initialize column preferences if not set
if (!isset($_SESSION['column_prefs'])) {
    $_SESSION['column_prefs'] = [
        'Hip' => true,
        'Horse' => true,
        'Yearfoal' => true,
        'Sex' => true,
        'Sire' => true,
        'Dam' => true,
        'Datefoal' => false,
        'Type' => false,
        'Color' => false,
        'Gait' => false,
        'Farmname' => false,
        'Bredto' => false,
        'Consigner' => false,
        'Salecode' => false
    ];
}

// Handle column preference updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['column_prefs'])) {
    // Convert string "true"/"false" to boolean values
    $prefs = [];
    foreach ($_POST['column_prefs'] as $col => $value) {
        $prefs[$col] = ($value === 'true');
    }
    $_SESSION['column_prefs'] = $prefs;
    header("Location: horse_list.php");
    exit();
}

// Get sort parameters
$sort1_param = $_GET['sort1'] ?? '';
$sort2_param = $_GET['sort2'] ?? '';
$sort3_param = $_GET['sort3'] ?? '';
$sort4_param = $_GET['sort4'] ?? '';
$sort5_param = $_GET['sort5'] ?? '';

$sort1_param_order = isset($_GET['sort1_order']) ? $_GET['sort1_order'] : ''; // default to 'ASC' if not set
$sort2_param_order = isset($_GET['sort2_order']) ? $_GET['sort2_order'] : ''; // default to 'ASC' if not set
$sort3_param_order = isset($_GET['sort3_order']) ? $_GET['sort3_order'] : ''; // default to 'ASC' if not set
$sort4_param_order = isset($_GET['sort4_order']) ? $_GET['sort4_order'] : ''; // default to 'ASC' if not set
$sort5_param_order = isset($_GET['sort5_order']) ? $_GET['sort5_order'] : ''; // default to 'ASC' if not set

// Fetch horse data using your existing function
$horseSearch = $_GET['horse_search'] ?? '';
$damSearch = $_GET['dam_search'] ?? '';
$LocationSearch = $_GET['location_search'] ?? '';
$FoalSearch = $_GET['foal_search'] ?? '';
$ConsignerSearch = $_GET['consigner_search'] ?? '';
$SalecodeSearch = $_GET['salecode_search'] ?? '';

$result = fetchHorseList($sort1_param, $sort2_param, $sort3_param, $sort4_param, $sort5_param, $horseSearch, $damSearch, $LocationSearch, $FoalSearch, $ConsignerSearch, $SalecodeSearch);

// Define sortable columns for the dropdowns
$sortList = array("Horse", "Yearfoal", "Sex", "Sire", "Dam", "Farmname", "Datefoal");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="assets/css/table.css">
    <link rel="stylesheet" href="assets/css/horse-list.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="assets/js/script.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
</head>

<br>

<h1 style="text-align:center;color:#FF6B35;">HORSE LIST - STANDARDBRED</h1>

<div class="search-container">
    <form class="form-inline" action="horse_list.php" method="GET">
        <!-- Preserve sort parameters -->
        <?php
        $sortParams = ['sort1', 'sort2', 'sort3', 'sort4', 'sort5'];
        foreach ($sortParams as $param) {
            if (!empty($_GET[$param])) {
                echo '<input type="hidden" name="' . $param . '" value="' . htmlspecialchars($_GET[$param]) . '">';
            }
        }
        ?>

        <!-- Horse Search -->
        <input type="text" name="horse_search" class="search-box" placeholder="Search Horses..."
            value="<?php echo isset($_GET['horse_search']) ? htmlspecialchars($_GET['horse_search']) : '' ?>">

        <!-- Dam Search -->
        <input type="text" name="dam_search" class="search-box" placeholder="Search Dams..."
            value="<?php echo isset($_GET['dam_search']) ? htmlspecialchars($_GET['dam_search']) : '' ?>">

        <!-- Location Search -->
        <input type="text" name="location_search" class="search-box" placeholder="Search Locations..."
            value="<?php echo isset($_GET['location_search']) ? htmlspecialchars($_GET['location_search']) : '' ?>">

        <!-- Foal Search -->
        <input type="text" name="foal_search" class="search-box" placeholder="Search Year Foaled..."
            value="<?php echo isset($_GET['foal_search']) ? htmlspecialchars($_GET['foal_search']) : '' ?>">

        <!-- Consigner Search -->
        <input type="text" name="consigner_search" class="search-box" placeholder="Search Consigners..."
            value="<?php echo isset($_GET['consigner_search']) ? htmlspecialchars($_GET['consigner_search']) : '' ?>">

        <!-- Consigner Search -->
        <input type="text" name="salecode_search" class="search-box" placeholder="Search Salecodes..."
            value="<?php echo isset($_GET['salecode_search']) ? htmlspecialchars($_GET['salecode_search']) : '' ?>">

        <button type="submit" class="search-button">Search</button>

        <?php if (isset($_GET['horse_search']) || isset($_GET['dam_search']) || isset($_GET['location_search']) || isset($_GET['foal_search']) || isset($_GET['consigner_search']) || isset($_GET['salecode_search'])): ?>
            <a href="horse_list.php" class="clear-button">Clear All</a>
        <?php endif; ?>
    </form>
</div>
<br>
<script>
    // Function to toggle sorting order between ASC and DESC
    function updateSortOrder(sortField) {
        // Get the selected value from the sort dropdown
        let sortValue = document.getElementById(sortField).value;

        // Get the selected order (ASC or DESC) from the corresponding dropdown
        let orderValue = document.getElementById(sortField + '_order').value;

        // Update the hidden input fields with the selected values
        document.getElementById(sortField + '_order').value = orderValue;

        console.log(`Sorting by ${sortValue} in ${orderValue} order`);
    }
</script>

<!-- Sorting Filters (1st to 5th) -->
<select style="background-color:#229954;" class="custom-select1" id="sort1" name="sort1" onchange="updateSortOrder('sort1')">
    <option value="">Sort By 1st</option>
    <?php foreach ($sortList as $row) {
        echo '<option value="' . strtolower($row) . '">' . $row . '</option>';
    } ?>
</select>

<select style="background-color: #3498db;" class="custom-select1" id="sort1_order" onchange="updateSortOrder('sort1')">
    <option value="">Select Order</option> <!-- Default option -->
    <option value="ASC" <?php echo ($sort1_param_order == 'ASC') ? 'selected' : ''; ?>>ASC</option>
    <option value="DESC" <?php echo ($sort1_param_order == 'DESC') ? 'selected' : ''; ?>>DESC</option>
</select>

<select style="background-color:#229954;" class="custom-select1" id="sort2" name="sort2" onchange="updateSortOrder('sort2')">
    <option value="">Sort By 2nd</option>
    <?php foreach ($sortList as $row) {
        echo '<option value="' . strtolower($row) . '">' . $row . '</option>';
    } ?>
</select>

<select style="background-color: #3498db;" class="custom-select1" id="sort2_order" onchange="updateSortOrder('sort2')">
    <option value="">Select Order</option> <!-- Default option -->
    <option value="ASC" <?php echo ($sort2_param_order == 'ASC') ? 'selected' : ''; ?>>ASC</option>
    <option value="DESC" <?php echo ($sort2_param_order == 'DESC') ? 'selected' : ''; ?>>DESC</option>
</select>

<select style="background-color:#229954;" class="custom-select1" id="sort3" name="sort3" onchange="updateSortOrder('sort3')">
    <option value="">Sort By 3rd</option>
    <?php foreach ($sortList as $row) {
        echo '<option value="' . strtolower($row) . '">' . $row . '</option>';
    } ?>
</select>

<select style="background-color: #3498db;" class="custom-select1" id="sort3_order" onchange="updateSortOrder('sort3')">
    <option value="">Select Order</option> <!-- Default option -->
    <option value="ASC" <?php echo ($sort3_param_order == 'ASC') ? 'selected' : ''; ?>>ASC</option>
    <option value="DESC" <?php echo ($sort3_param_order == 'DESC') ? 'selected' : ''; ?>>DESC</option>
</select>

<select style="background-color:#229954;" class="custom-select1" id="sort4" name="sort4" onchange="updateSortOrder('sort4')">
    <option value="">Sort By 4th</option>
    <?php foreach ($sortList as $row) {
        echo '<option value="' . strtolower($row) . '">' . $row . '</option>';
    } ?>
</select>

<select style="background-color: #3498db;" class="custom-select1" id="sort4_order" onchange="updateSortOrder('sort4')">
    <option value="">Select Order</option> <!-- Default option -->
    <option value="ASC" <?php echo ($sort4_param_order == 'ASC') ? 'selected' : ''; ?>>ASC</option>
    <option value="DESC" <?php echo ($sort4_param_order == 'DESC') ? 'selected' : ''; ?>>DESC</option>
</select>

<select style="background-color:#229954;" class="custom-select1" id="sort5" name="sort5" onchange="updateSortOrder('sort5')">
    <option value="">Sort By 5th</option>
    <?php foreach ($sortList as $row) {
        echo '<option value="' . strtolower($row) . '">' . $row . '</option>';
    } ?>
</select>

<select style="background-color: #3498db;" class="custom-select1" id="sort5_order" onchange="updateSortOrder('sort5')">
    <option value="">Select Order</option> <!-- Default option -->
    <option value="ASC" <?php echo ($sort5_param_order == 'ASC') ? 'selected' : ''; ?>>ASC</option>
    <option value="DESC" <?php echo ($sort5_param_order == 'DESC') ? 'selected' : ''; ?>>DESC</option>
</select>


<input class="custom-select1" type="submit" onclick="getValues()" name="SUBMITBUTTON" value="Submit" style="font-size:20px; " />

<br>

<body>
    <!-- Button Container -->
    <div class="button-container">
        <!-- Column Selector Button -->
        <button id="columnSelectorBtn">Select Columns</button>
    </div>

    <!-- Column Checkboxes (will be moved to modal by JavaScript) -->
    <div id="columnCheckboxes" style="display:none;">
        <?php
        $allColumns = [
            'Hip',
            'Horse',
            'Yearfoal',
            'Sex',
            'Sire',
            'Dam',
            'Datefoal',
            'Type',
            'Color',
            'Gait',
            'Farmname',
            'Bredto',
            'Consigner',
            'Salecode'
        ];
        foreach ($allColumns as $col): ?>
            <div class="column-checkbox">
                <label>
                    <input type="checkbox" name="column_prefs[<?php echo $col; ?>]"
                        <?php echo ($_SESSION['column_prefs'][$col] ?? false) ? 'checked' : ''; ?>>
                    <?php echo $col; ?>
                </label>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Table Section -->
    <div class="responsive-table-container">
        <table>
            <thead>
                <tr>
                    <?php foreach ($_SESSION['column_prefs'] as $col => $visible): ?>
                        <?php if ($visible): ?>
                            <th class="col-<?php echo strtolower($col); ?>"><?php echo $col; ?></th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row): ?>
                    <tr>
                        <?php foreach ($_SESSION['column_prefs'] as $col => $visible): ?>
                            <?php if ($visible): ?>
                                <td class="col-<?php echo strtolower($col); ?>">
                                    <?php if ($col === 'Horse'): ?>
                                        <a href="#" class="horse-link" data-horse-id="<?php echo htmlspecialchars($row['horse']); ?>">
                                            <?php echo htmlspecialchars($row['horse']); ?>
                                        </a>
                                    <?php else: ?>
                                        <?php
                                        $dbField = '';
                                        switch ($col) {
                                            case 'Hip':
                                                $dbField = 'HIP';
                                                break;
                                            case 'Yearfoal':
                                                $dbField = 'YEARFOAL';
                                                break;
                                            case 'Sex':
                                                $dbField = 'sex';
                                                break;
                                            case 'Sire':
                                                $dbField = 'sire';
                                                break;
                                            case 'Dam':
                                                $dbField = 'dam';
                                                break;
                                            case 'Farmname':
                                                $dbField = 'farmname';
                                                break;
                                            case 'Datefoal':
                                                $dbField = 'Datefoal';
                                                break;
                                            case 'Bredto':
                                                $dbField = 'bredto';
                                                break;
                                            case 'Consigner':
                                                $dbField = 'CONSLNAME';
                                                break;
                                            case 'Salecode':
                                                $dbField = 'SALECODE';
                                                break;
                                            default:
                                                $dbField = strtolower($col);
                                        }
                                        // Safely display dates
                                        $fieldValue = $row[$dbField] ?? '';
                                        if (in_array($dbField, ['DATEFOAL'])) {
                                            echo $fieldValue && strtotime($fieldValue)
                                                ? date('Y-m-d', strtotime($fieldValue))
                                                : '';
                                        } else {
                                            echo htmlspecialchars($fieldValue);
                                        }                                        ?>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Right-side Sidebar for Horse Details -->
    <div id="horseDetailsSidebar" class="sidebar">

        <button class="closebtn" onclick="closeSidebar()">CLOSE</button>

        <!-- Horse details in the title of side bar -->
        <div class="horse-title-section">
            <div class="horse-details-title">
                <h2 id="horseName" class="horse-info"></h2>
                <div class="parent-info">
                    <span id="sireTitle" class="horse-info"></span>
                    <span id="damTitle" class="horse-info"></span>
                    <span id="datefoalTitle" class="horse-info"></span>
                </div>
            </div>
        </div>

        <!-- Tab buttons -->
        <div class="tab-buttons">
            <button class="tab-button active" data-tab="detailsTab">Details</button>
            <button class="tab-button" data-tab="photosTab">Photos</button>
            <button class="tab-button" data-tab="inspectionTab">Inspection</button>
        </div>

        <!-- Tab content -->
        <div class="tab-content">
            <div id="detailsTab" class="tab-pane active">
                <div id="horseDetailsContent">
                    <!-- Basic Info -->
                    <div class="detail-section">
                        <h4>Basic Information</h4>
                        <p><strong>Year Foaled:</strong> <span id="yearFoalDisplay"></span> </p>
                        <p><strong>Sex:</strong> <span id="sexDisplay"></span> <input type="text" id="sexInput" class="edit-field"></p>
                        <p><strong>Type:</strong> <span id="typeDisplay"></span> <input type="text" id="typeInput" class="edit-field"></p>
                        <p><strong>Color:</strong> <span id="colorDisplay"></span> <input type="text" id="colorInput" class="edit-field"></p>
                        <p><strong>Gait:</strong> <span id="gaitDisplay"></span> <input type="text" id="gaitInput" class="edit-field"></p>
                    </div>

                    <!-- Pedigree -->
                    <div class="detail-section">
                        <h4>Pedigree</h4>
                        <p><strong>Sire:</strong> <span id="sireDisplay"></span> <input type="text" id="sireInput" class="edit-field"></p>
                        <p><strong>Dam:</strong> <span id="damDisplay"></span> <input type="text" id="damInput" class="edit-field"></p>
                        <p><strong>Sire of Dam:</strong> <span id="sireofdamDisplay"></span></p>
                    </div>

                    <!-- Sale Info -->
                    <div class="detail-section">
                        <h4>Sale Information</h4>
                        <p><strong>Sale Price:</strong> <span id="priceDisplay"></span></p>
                        <p><strong>Sale Date:</strong> <span id="saledateDisplay"></span></p>
                        <p><strong>Sale Code:</strong> <span id="salecodeDisplay"></span></p>
                        <p><strong>Consignor:</strong> <span id="conslnameDisplay"></span></p>
                    </div>

                    <!-- Breeding Info -->
                    <div class="detail-section">
                        <h4>Breeding Information</h4>
                        <p><strong>Date Foaled:</strong> <span id="datefoalDisplay"></span> <input type="date" id="datefoalInput" class="edit-field"></p>
                        <p><strong>Bred To:</strong> <span id="bredtoDisplay"></span> <input type="text" id="bredtoInput" class="edit-field"></p>
                        <p><strong>Last Bred:</strong> <span id="lastbredDisplay"></span> <input type="date" id="lastbredInput" class="edit-field"> </p>
                    </div>

                    <!-- Farm Info -->
                    <div class="detail-section">
                        <h4>Farm Information</h4>
                        <p><strong>Farm Name:</strong> <span id="farmnameDisplay"></span> <input type="text" id="farmnameInput" class="edit-field"></p>
                        <p><strong>Location:</strong> <span id="locationDisplay"></span></p>
                    </div>

                    <!-- Purchaser Info -->
                    <div class="detail-section">
                        <h4>Purchaser Information</h4>
                        <p><strong>Purchaser:</strong> <span id="purchaserDisplay"></span></p>
                    </div>
                </div>

                <div class="sticky-action-bar">
                    <button id="editBtn" class="btn btn-primary">Edit</button>
                    <button id="saveBtn" class="btn btn-success" style="display:none;">Save</button>
                    <button id="cancelBtn" class="btn btn-danger" style="display:none;">Cancel</button>
                </div>

            </div>

            <div id="inspectionTab" class="tab-pane">
                <div id="inspectionForm">

                    <div class="field-group">
                        <div class="form-row">
                            <label for="salecode">Salecode:</label>
                            <input type="text" id="salecode" name="salecode" readonly>
                        </div>
                    </div>

                    <div class="section-header">HORSE INFORMATION</div>

                    <div class="field-group">
                        <div class="form-row">
                            <label for="sex">Sex:</label>
                            <input type="text" id="sex" name="sex" readonly>
                        </div>

                        <div class="form-row">
                            <label><strong>Sex Change:</strong></label>
                            <div class="button-group" data-field="sex_change">
                                <button type="button" class="btn-option">Ridgling</button>
                                <button type="button" class="btn-option">Gelding</button>
                            </div>
                        </div>
                    </div>

                    <div class="section-header">DAVE REID RATING</div>

                    <div class="field-group-vertical">
                        <div class="form-row-vertical">
                            <label><strong>Day Rating Indicator:</strong></label>
                            <div class="button-group button-group-vertical" data-field="day_rating_indicator">
                                <button type="button" class="btn-option">Ok</button>
                                <button type="button" class="btn-option">Move Up</button>
                                <button type="button" class="btn-option">Move Later</button>
                            </div>
                        </div>

                        <div class="form-row-vertical">
                            <label><strong>DAVE REID (Rat'g 4):</strong></label>
                            <div class="button-group button-group-vertical" data-field="dave_reid_rating">
                                <button type="button" class="btn-option">1</button>
                                <button type="button" class="btn-option">2</button>
                                <button type="button" class="btn-option">2.5</button>
                                <button type="button" class="btn-option">2.75</button>
                                <button type="button" class="btn-option">3</button>
                                <button type="button" class="btn-option">3.15</button>
                                <button type="button" class="btn-option">3.25</button>
                                <button type="button" class="btn-option">3.33</button>
                                <button type="button" class="btn-option">3.5</button>
                                <button type="button" class="btn-option">3.75</button>
                                <button type="button" class="btn-option">4</button>
                                <button type="button" class="btn-option">4.25</button>
                                <button type="button" class="btn-option">4.5</button>
                                <button type="button" class="btn-option">5</button>
                            </div>
                        </div>

                        <div class="form-row-vertical">
                            <label><strong>UP / DN / EV:</strong></label>
                            <div class="button-group button-group-vertical" data-field="up_dn_ev">
                                <button type="button" class="btn-option">EVEN</button>
                                <button type="button" class="btn-option">UP</button>
                                <button type="button" class="btn-option">DOWN</button>
                            </div>
                        </div>
                    </div>

                    <div class="field-group">
                        <div class="form-row">
                            <div class="checkbox-group" data-field="neck_upright">
                                <!-- Hidden input to send 0 if checkbox is unchecked -->
                                <input type="hidden" name="neck_ok" value="0">
                                <input type="checkbox" id="neck_ok" name="neck_ok" value="1">
                                <label for="neck_ok">Ok</label>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="checkbox-group" data-field="neck_very_nice">
                                <!-- Hidden input to send 0 if checkbox is unchecked -->
                                <input type="hidden" name="neck_very_nice" value="0">
                                <input type="checkbox" id="neck_very_nice" name="neck_very_nice" value="1">
                                <label for="neck_very_nice">Very Nice</label>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="checkbox-group" data-field="neck_dr">
                                <!-- Hidden input to send 0 if checkbox is unchecked -->
                                <input type="hidden" name="neck_dr" value="0">
                                <input type="checkbox" id="neck_dr" name="neck_dr" value="1">
                                <label for="neck_dr">DR</label>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="checkbox-group" data-field="neck_top_horse">
                                <!-- Hidden input to send 0 if checkbox is unchecked -->
                                <input type="hidden" name="neck_top_horse" value="0">
                                <input type="checkbox" id="neck_top_horse" name="neck_top_horse" value="1">
                                <label for="neck_top_horse">Top Horse</label>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="checkbox-group" data-field="neck_athletic">
                                <!-- Hidden input to send 0 if checkbox is unchecked -->
                                <input type="hidden" name="neck_athletic" value="0">
                                <input type="checkbox" id="neck_athletic" name="neck_athletic" value="1">
                                <label for="neck_athletic">Athletic</label>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="checkbox-group" data-field="neck_racey">
                                <!-- Hidden input to send 0 if checkbox is unchecked -->
                                <input type="hidden" name="neck_racey" value="0">
                                <input type="checkbox" id="neck_racey" name="neck_racey" value="1">
                                <label for="neck_racey">Racey</label>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="checkbox-group" data-field="neck_clean_correct">
                                <!-- Hidden input to send 0 if checkbox is unchecked -->
                                <input type="hidden" name="neck_clean_correct" value="0">
                                <input type="checkbox" id="neck_clean_correct" name="neck_clean_correct" value="1">
                                <label for="neck_clean_correct">Clean N Correct</label>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="checkbox-group" data-field="neck_needs_grow">
                                <!-- Hidden input to send 0 if checkbox is unchecked -->
                                <input type="hidden" name="neck_needs_grow" value="0">
                                <input type="checkbox" id="neck_needs_grow" name="neck_needs_grow" value="1">
                                <label for="neck_needs_grow">Needs to GROW</label>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="checkbox-group" data-field="neck_needs_mature">
                                <!-- Hidden input to send 0 if checkbox is unchecked -->
                                <input type="hidden" name="neck_needs_mature" value="0">
                                <input type="checkbox" id="neck_needs_mature" name="neck_needs_mature" value="1">
                                <label for="neck_needs_mature">Needs to Mature</label>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="checkbox-group" data-field="neck_needs_improve">
                                <!-- Hidden input to send 0 if checkbox is unchecked -->
                                <input type="hidden" name="neck_needs_improve" value="0">
                                <input type="checkbox" id="neck_needs_improve" name="neck_needs_improve" value="1">
                                <label for="neck_needs_improve">Needs to Improve</label>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="checkbox-group" data-field="neck_nm_ty">
                                <!-- Hidden input to send 0 if checkbox is unchecked -->
                                <input type="hidden" name="neck_nm_ty" value="0">
                                <input type="checkbox" id="neck_nm_ty" name="neck_nm_ty" value="1">
                                <label for="neck_nm_ty">N M Ty</label>
                            </div>
                        </div>
                    </div>

                    <div class="section-header">SIDE VIEW / SIZE . BALANCE . GIRTH . WITHERS. SHOULDERS</div>

                    <div class="field-group">
                        <div class="form-row">
                            <label><strong>Size:</strong></label>
                            <div class="button-group" data-field="size">
                                <button type="button" class="btn-option">Very Big</button>
                                <button type="button" class="btn-option">Big</button>
                                <button type="button" class="btn-option">Good</button>
                                <button type="button" class="btn-option">Average</button>
                                <button type="button" class="btn-option">Medium</button>
                                <button type="button" class="btn-option">Small</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <label><strong>Size to Foal Date:</strong></label>
                            <div class="button-group" data-field="size_to_foal_date">
                                <button type="button" class="btn-option">Ok</button>
                                <button type="button" class="btn-option">Small</button>
                                <button type="button" class="btn-option">Big</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <label><strong>Short Legged:</strong></label>
                            <div class="button-group" data-field="short_legged">
                                <button type="button" class="btn-option">Yes</button>
                                <button type="button" class="btn-option">No</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <label><strong>Balance:</strong></label>
                            <div class="button-group" data-field="balance">
                                <button type="button" class="btn-option">Very Good</button>
                                <button type="button" class="btn-option">Good</button>
                                <button type="button" class="btn-option">Avg</button>
                                <button type="button" class="btn-option">Poor</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <label><strong>Girth:</strong></label>
                            <div class="button-group" data-field="girth">
                                <button type="button" class="btn-option">Deep</button>
                                <button type="button" class="btn-option">Average</button>
                                <button type="button" class="btn-option">Thin</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <label><strong>Withers:</strong></label>
                            <div class="button-group" data-field="withers">
                                <button type="button" class="btn-option">High</button>
                                <button type="button" class="btn-option">Average</button>
                                <button type="button" class="btn-option">Low</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <label><strong>Shoulder Angle:</strong></label>
                            <div class="button-group" data-field="shoulder_angle">
                                <button type="button" class="btn-option">Avg</button>
                                <button type="button" class="btn-option">Straight</button>
                                <button type="button" class="btn-option">Sloped</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <label><strong>Body:</strong></label>
                            <div class="button-group" data-field="body">
                                <button type="button" class="btn-option">Average</button>
                                <button type="button" class="btn-option">Strong</button>
                                <button type="button" class="btn-option">Heavy</button>
                                <button type="button" class="btn-option">Weak</button>
                            </div>
                        </div>
                    </div>

                    <div class="section-header">HEAD PLACEMENT - NECK</div>

                    <div class="field-group">
                        <div class="form-row">
                            <label><strong>HEAD PLACEMENT</strong></label>
                            <div class="button-group" data-field="head_placement">
                                <button type="button" class="btn-option">Good</button>
                                <button type="button" class="btn-option">Avg</button>
                                <button type="button" class="btn-option">Poor</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <label><strong>Neck Upright:</strong></label>
                            <div class="checkbox-group" data-field="neck_upright">
                                <!-- Hidden input to send 0 if checkbox is unchecked -->
                                <input type="hidden" name="neck_upright" value="0">
                                <input type="checkbox" id="neck_upright" name="neck_upright" value="1">
                                <label for="neck_upright">Upright Neck</label>
                            </div>
                        </div>

                        <div class="form-row">
                            <label><strong>NECK LENGTH</strong></label>
                            <div class="button-group" data-field="neck_length">
                                <button type="button" class="btn-option">Average</button>
                                <button type="button" class="btn-option">Short</button>
                                <button type="button" class="btn-option">Long</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <label><strong>NECK FEATURE</strong></label>
                            <div class="button-group" data-field="neck_feature">
                                <button type="button" class="btn-option">Cresty</button>
                                <button type="button" class="btn-option">Thin</button>
                            </div>
                        </div>
                    </div>

                    <div class="section-header">LENGTH - BACK - HIP - CROUP</div>

                    <div class="field-group">
                        <div class="form-row">
                            <label><strong>LENGTH</strong></label>
                            <div class="button-group" data-field="body_length">
                                <button type="button" class="btn-option">Average</button>
                                <button type="button" class="btn-option">Long</button>
                                <button type="button" class="btn-option">Short</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <label><strong>BACK</strong></label>
                            <div class="button-group" data-field="back">
                                <button type="button" class="btn-option">Average</button>
                                <button type="button" class="btn-option">Long</button>
                                <button type="button" class="btn-option">Short</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <label><strong>BACK SWAY</strong></label>
                            <div class="button-group" data-field="back_sway">
                                <button type="button" class="btn-option">Slightly</button>
                                <button type="button" class="btn-option">Bad</button>
                                <button type="button" class="btn-option">none</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <label><strong>BEHIND HIGH</strong></label>
                            <div class="button-group" data-field="behind_high">
                                <button type="button" class="btn-option">No</button>
                                <button type="button" class="btn-option">Yes</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <label><strong>BEHIND (SIDE)</strong></label>
                            <div class="button-group" data-field="behind_side">
                                <button type="button" class="btn-option">Very Strong</button>
                                <button type="button" class="btn-option">Strong</button>
                                <button type="button" class="btn-option">Avg</button>
                                <button type="button" class="btn-option">Weak</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <label><strong>HIPS (SIDE)</strong></label>
                            <div class="button-group" data-field="hips_side">
                                <button type="button" class="btn-option">Neutral</button>
                                <button type="button" class="btn-option">Long</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <label><strong>HIP SHORT</strong></label>
                            <div class="button-group" data-field="hip_short">
                                <button type="button" class="btn-option">No</button>
                                <button type="button" class="btn-option">Yes</button>
                            </div>
                        </div>

                        <div class="form-row">
                            <label><strong>HIP DROPS</strong></label>
                            <div class="button-group" data-field="hip_drops">
                                <button type="button" class="btn-option">No</button>
                                <button type="button" class="btn-option">Yes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="photosTab" class="tab-pane">

                <!-- 📸 Photo Upload Section -->
                <div id="photoSection" style="margin-top: 20px;">
                    <form id="fileUploadForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="horseId" id="hiddenHorseId">
                        <input type="hidden" id="hiddenHorseIdSanitized">

                        <input type="file" name="file" id="fileInput" accept="image/*">
                        <button type="submit" class="btn btn-success" style="display:none;">
                            <i class="fas fa-upload"></i> Upload File
                        </button>
                    </form>
                </div>

                <h3 class="sticky-header">Photos</h3>

                <div id="photoPreview"></div>

            </div>
        </div>
    </div>
    <br>


    <script>
        document.getElementById('sort1').value = "<?php echo $sort1_param; ?>";
        document.getElementById('sort2').value = "<?php echo $sort2_param; ?>";
        document.getElementById('sort3').value = "<?php echo $sort3_param; ?>";
        document.getElementById('sort4').value = "<?php echo $sort4_param; ?>";
        document.getElementById('sort5').value = "<?php echo $sort5_param; ?>";
        document.getElementById('sort1_order').value = "<?php echo $sort1_param_order; ?>";
        document.getElementById('sort2_order').value = "<?php echo $sort2_param_order; ?>";
        document.getElementById('sort3_order').value = "<?php echo $sort3_param_order; ?>";
        document.getElementById('sort4_order').value = "<?php echo $sort4_param_order; ?>";
        document.getElementById('sort5_order').value = "<?php echo $sort5_param_order; ?>";

        // Function to collect selected sort values and pass them as parameters
        function getValues() {
            var sort1 = document.getElementById('sort1').value;
            var sort2 = document.getElementById('sort2').value;
            var sort3 = document.getElementById('sort3').value;
            var sort4 = document.getElementById('sort4').value;
            var sort5 = document.getElementById('sort5').value;

            // Sorting orders (ASC or DESC)
            var sort1_order = document.getElementById('sort1_order').value;
            var sort2_order = document.getElementById('sort2_order').value;
            var sort3_order = document.getElementById('sort3_order').value;
            var sort4_order = document.getElementById('sort4_order').value;
            var sort5_order = document.getElementById('sort5_order').value;

            // Get search terms
            var horseSearch = "<?php echo isset($_GET['horse_search']) ? $_GET['horse_search'] : '' ?>";
            var damSearch = "<?php echo isset($_GET['dam_search']) ? $_GET['dam_search'] : '' ?>";
            var locationSearch = "<?php echo isset($_GET['location_search']) ? $_GET['location_search'] : '' ?>";
            var FoalSearch = "<?php echo isset($_GET['foal_search']) ? $_GET['foal_search'] : '' ?>";
            var ConsignerSearch = "<?php echo isset($_GET['consigner_search']) ? $_GET['consigner_search'] : '' ?>";
            var SalecodeSearch = "<?php echo isset($_GET['salecode_search']) ? $_GET['salecode_search'] : '' ?>";

            var link = "horse_list.php?&sort1=" + sort1 +
                "&sort1_order=" + sort1_order // Added sorting order
                +
                "&sort2=" + sort2 +
                "&sort2_order=" + sort2_order // Added sorting order
                +
                "&sort3=" + sort3 +
                "&sort3_order=" + sort3_order // Added sorting order
                +
                "&sort4=" + sort4 +
                "&sort4_order=" + sort4_order // Added sorting order
                +
                "&sort5=" + sort5 +
                "&sort5_order=" + sort5_order; // Added sorting order

            // Add search parameters if they exist
            if (horseSearch) {
                link += "&horse_search=" + encodeURIComponent(horseSearch);
            }
            if (damSearch) {
                link += "&dam_search=" + encodeURIComponent(damSearch);
            }
            if (locationSearch) {
                link += "&location_search=" + encodeURIComponent(locationSearch);
            }
            if (FoalSearch) {
                link += "&foal_search=" + encodeURIComponent(FoalSearch);
            }
            if (ConsignerSearch) {
                link += "&consigner_search=" + encodeURIComponent(ConsignerSearch);
            }
            if (SalecodeSearch) {
                link += "&salecode_search=" + encodeURIComponent(SalecodeSearch);
            }

            window.location.href = link;
        }

        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // This is the value of the checkbox (1 for checked, 0 for unchecked)
                const isChecked = this.checked ? '1' : '0';

                // Extract horse name from the DOM
                const horse_name = document.getElementById('horseName').textContent.trim(); // Corrected to get text content

                const data = {
                    horse_name: horse_name, // Replace with actual variable holding the horse name
                    field: this.name, // Get the field name (e.g., 'neck_upright')
                    value: isChecked
                };

                // Send the data to the server
                fetch('update_inspection.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams(data)
                    })
                    .then(response => response.text())
                    .then(result => {
                        console.log(result); // Handle success
                    })
                    .catch(error => {
                        console.error('Error updating checkbox:', error); // Handle error
                    });
            });
        });

        document.querySelectorAll('.button-group').forEach(group => {
            const field = group.dataset.field;

            group.querySelectorAll('.btn-option').forEach(button => {
                button.addEventListener('click', function() {
                    const horse_name = document.getElementById('hiddenHorseId').value;
                    if (!horse_name) {
                        console.error('Horse name is missing.');
                        return;
                    }

                    const isSelected = this.classList.contains('selected');
                    let value = null;

                    if (isSelected) {
                        // Unselect the button
                        this.classList.remove('selected');
                        value = ''; // This will be interpreted as NULL in PHP
                    } else {
                        // Deselect others and select this one
                        group.querySelectorAll('.btn-option').forEach(btn => btn.classList.remove('selected'));
                        this.classList.add('selected');
                        value = this.textContent.trim();
                    }

                    fetch('update_inspection.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `horse_name=${encodeURIComponent(horse_name)}&field=${encodeURIComponent(field)}&value=${encodeURIComponent(value)}`
                        })
                        .then(response => response.text())
                        .then(data => {
                            console.log("Updated:", data);
                        })
                        .catch(error => {
                            console.error("Error:", error);
                        });
                });
            });
        });

        function loadHorseInspection(horseName) {
            console.log("Loading inspection for:", horseName);

            // Get the horse details from the sidebar (already loaded)
            const horseDetails = {
                sex: $('#sexDisplay').text().trim(),
                salecode: $('#salecodeDisplay').text().trim()
            };

            // Populate the inspection form fields
            $('#sex').val(horseDetails.sex);
            $('#salecode').val(horseDetails.salecode);

            fetch(`get_horse_values.php?horseId=${encodeURIComponent(horseName)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Inspection data received:", data);

                    if (!data || Object.keys(data).length === 0) {
                        console.log("No inspection data found for this horse");
                        return;
                    }

                    // Clear all selections first
                    document.querySelectorAll('.btn-option').forEach(btn => {
                        btn.classList.remove('selected');
                    });

                    // Set selections from data
                    document.querySelectorAll('.button-group, .button-group-vertical').forEach(group => {
                        const field = group.dataset.field;
                        const value = data[field];

                        if (!value) {
                            console.log(`No value for field: ${field}`);
                            return;
                        }

                        console.log(`Looking for match: ${field} = ${value}`);

                        let found = false;
                        group.querySelectorAll('.btn-option').forEach(button => {
                            const buttonText = button.textContent.trim().toLowerCase();
                            if (buttonText === value.toLowerCase()) {
                                button.classList.add('selected');
                                found = true;
                                console.log(`Matched button: ${buttonText}`);
                            }
                        });

                        if (!found) {
                            console.warn(`No button matched for ${field} = ${value}`);
                        }
                    });

                    // Update known checkbox fields
                    const checkboxFields = ['neck_upright',
                        'neck_ok',
                        'neck_very_nice',
                        'neck_dr',
                        'neck_top_horse',
                        'neck_athletic',
                        'neck_racey',
                        'neck_clean_correct',
                        'neck_needs_grow',
                        'neck_needs_mature',
                        'neck_needs_improve',
                        'neck_nm_ty'
                    ]; // add more keys here as needed

                    checkboxFields.forEach(field => {
                        const checkbox = document.querySelector(`input[type="checkbox"][name="${field}"]`);
                        if (checkbox && data.hasOwnProperty(field)) {
                            const val = data[field];
                            checkbox.checked = val === 1 || val === '1' || val === true;
                            console.log(`Checkbox ${field} set to: ${checkbox.checked}`);
                        }
                    });
                })
                .catch(error => {
                    console.error("Error loading inspection data:", error);
                });
        }

        // File Upload Handler
        $('#fileUploadForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const fileInput = $('#fileInput')[0];

            if (fileInput.files.length === 0) {
                alert('Please select a file to upload');
                return;
            }

            uploadFileToServer(formData);
        });

        function uploadFileToServer(formData) {
            $.ajax({
                url: 'upload_photo.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('Raw response:', response); // Debugging line
                    if (response && response.success) {
                        addFileToGallery(response);
                        alert('File uploaded successfully!');
                    } else {
                        alert('Upload failed: ' + (response?.error || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Upload error:', xhr.responseText);
                    try {
                        const errResponse = JSON.parse(xhr.responseText);
                        alert('Upload failed: ' + (errResponse.error || 'Unknown error'));
                    } catch (e) {
                        alert('Upload failed. Server response: ' + xhr.responseText);
                    }
                }
            });
        }

        $(document).ready(function() {
            // Handle file selection
            $('#fileInput').on('change', function(event) {
                const file = event.target.files[0];

                if (!file) {
                    return;
                }

                // Show preview of the image
                const reader = new FileReader();
                reader.onload = function(e) {

                    // Automatically upload the file
                    uploadFile(file);
                };
                reader.readAsDataURL(file); // Preview the image
            });

            // Function to upload the file automatically when selected
            function uploadFile(file) {
                const formData = new FormData();
                const horseId = $('#hiddenHorseId').val(); // Get horseId

                formData.append('file', file);
                formData.append('horseId', horseId); // Add horseId to the FormData

                $.ajax({
                    url: 'upload_photo.php', // The server-side script to handle file upload
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        console.log('Upload response:', response);
                        if (response && response.success) {
                            addFileToGallery(response);
                            alert('File uploaded successfully!');
                        } else {
                            alert('Upload failed: ' + (response?.error || 'Unknown error'));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Upload error:', xhr.responseText);
                        try {
                            const errResponse = JSON.parse(xhr.responseText);
                            alert('Upload failed: ' + (errResponse.error || 'Unknown error'));
                        } catch (e) {
                            alert('Upload failed. Server response: ' + xhr.responseText);
                        }
                    }
                });
            }

            // Adds the uploaded file to the gallery after successful upload
            // Adds the uploaded file to the gallery after successful upload
            function addFileToGallery(fileInfo) {
                const gallery = $('#photoPreview');

                // Remove "no files" message if present
                gallery.find('.no-files-message').remove();

                const isImage = fileInfo.name.match(/\.(jpg|jpeg|png|gif|webp)$/i);

                // Constructing the file element HTML
                const fileElement = `
        <div class="photo-card" data-id="${fileInfo.id}">
            ${isImage ? 
                `<img src="${fileInfo.url}" class="photo-thumbnail" data-full-url="${fileInfo.url}" />` : 
                `<div class="file-icon">
                    <i class="fas fa-file"></i>
                    <span>${fileInfo.name.split('.').pop()}</span>
                </div>`
            }
            <button class="delete-photo" data-id="${fileInfo.id}" data-url="${fileInfo.url}">×</button>
        </div>`;

                // Append the new file element to the gallery
                gallery.append(fileElement);
            }
        });

        // Check if page was just reloaded after upload
        $(document).ready(function() {
            // Open the sidebar and show the photo section after reload
            if (window.location.hash === "#photosTab") {
                $('#horseDetailsSidebar').addClass('open');
                $('#photoSection').show(); // Show the photo section of the sidebar
            }
        });

        // Function to refresh the page and open the sidebar photo section
        function openSidebarWithPhotos() {
            // Set the hash to trigger the sidebar to open
            window.location.hash = "photosTab";
            $('#horseDetailsSidebar').addClass('open');
            $('#photoSection').show(); // Make sure the photo section is visible
        }

        // Optional: Stop camera when sidebar closes
        function closeSidebar() {
            $('#horseDetailsSidebar').removeClass('open');
            location.reload(); // Basic page refresh
        }

        function sanitizeHorseId(name) {
            return name.replace(/[^a-zA-Z0-9_-]/g, '');
        }

        // Open Sidebar and fetch horse details
        function openSidebar(horseName) {
            console.log("Horse ID (sanitized): " + horseName); // Debugging log

            $.ajax({
                url: 'get_horse_details.php',
                type: 'GET',
                data: {
                    horseId: horseName
                },
                dataType: 'json',
                success: function(response) {
                    console.log("Received response:", response); // Debug log

                    if (!response) {
                        console.error("Empty response received");
                        alert("Error: Empty response from server");
                        return;
                    }

                    if (response.error) {
                        console.error("Server error:", response.error);
                        alert("Error: " + response.error);
                        return;
                    }

                    try {
                        // Basic Info
                        $('#horseName').text(response.HORSE || 'N/A');
                        $('#yearFoalDisplay').text(response.YEARFOAL || 'N/A');
                        $('#sexDisplay').text(response.SEX || 'N/A');
                        $('#typeDisplay').text(response.TYPE || 'N/A');
                        $('#colorDisplay').text(response.COLOR || 'N/A');
                        $('#gaitDisplay').text(response.GAIT || 'N/A');

                        // Pedigree
                        $('#sireTitle').text(response.Sire || 'N/A');
                        $('#sireDisplay').text(response.Sire || 'N/A');
                        $('#damTitle').text(response.DAM || 'N/A');
                        $('#damDisplay').text(response.DAM || 'N/A');
                        $('#sireofdamDisplay').text(response.Sireofdam || 'N/A');

                        // Sale Info
                        $('#priceDisplay').text(response.PRICE ? '$' + parseFloat(response.PRICE).toLocaleString() : 'N/A');
                        $('#saledateDisplay').text(response.SALEDATE ? new Date(response.SALEDATE).toLocaleDateString() : 'N/A');
                        $('#salecodeDisplay').text(response.SALECODE || 'N/A');
                        $('#conslnameDisplay').text(response.CONSLNAME || 'N/A');

                        // Breeding Info
                        const datefoalText = response.DATEFOAL || '';
                        $('#datefoalTitle').text(datefoalText);
                        $('#datefoalDisplay').text(datefoalText ? new Date(datefoalText).toLocaleDateString() : 'N/A');
                        $('#bredtoDisplay').text(response.BREDTO || 'N/A');
                        $('#lastbredDisplay').text(response.LASTBRED ? new Date(response.LASTBRED).toLocaleDateString() : 'N/A');

                        // Farm Info
                        $('#farmnameDisplay').text(response.FARMNAME || 'N/A');
                        $('#locationDisplay').text(
                            [response.SBCITY, response.SBSTATE, response.SBCOUNTRY]
                            .filter(Boolean).join(', ') || 'N/A'
                        );

                        // Purchaser Info
                        const purchaser = [response.PURFNAME, response.PURLNAME].filter(Boolean).join(' ');
                        $('#purchaserDisplay').text(purchaser || 'N/A');

                        // Set hidden horse ID
                        $('#hiddenHorseId').val(response.HORSE || '');

                        // Load inspection data
                        loadHorseInspection(response.HORSE);

                        // Show the sidebar
                        $('#horseDetailsSidebar').addClass('open');
                        $('#horseDetailsOverlay').show();

                        console.log("Sidebar opened successfully for:", response.HORSE);
                    } catch (error) {
                        console.error("Error processing response:", error);
                        alert("Error processing horse details");
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response text:', xhr.responseText);
                    alert("Failed to load horse details. Please try again.");
                }
            });

            loadHorseInspection(response.HORSE);

            $('#hiddenHorseId').val(response.HORSE);

            $('#horseDetailsContent').html(`
    <p><strong>Year of Foal:</strong> 
        <span id="yearFoalDisplay">${response.YEARFOAL}</span>
        <input type="text" id="yearFoalInput" value="${response.YEARFOAL}" style="display:none;">
    </p>
    <p><strong>Sex:</strong> 
        <span id="sexDisplay">${response.SEX}</span>
        <input type="text" id="sexInput" value="${response.SEX}" style="display:none;">
    </p>
    <p><strong>Sire:</strong> 
        <span id="sireDisplay">${response.Sire}</span>
        <input type="text" id="sireInput" value="${response.Sire}" style="display:none;">
    </p>
    <p><strong>Dam:</strong> 
        <span id="damDisplay">${response.DAM}</span>
        <input type="text" id="damInput" value="${response.DAM}" style="display:none;">
    </p>
        <p><strong>DATEFOAL:</strong> 
        <span id="datefoalDisplay">${response.DATEFOAL}</span>
        <input type="text" id="DatefoalInput" value="${response.DATEFOAL}" style="display:none;">
    </p>
`);

            // Handle images (display the uploaded images)
            let imagesHtml = '';
            if (response.images && response.images.length > 0) {
                response.images.forEach(imgUrl => {
                    imagesHtml += `
<div class="photo-card">
    <img src="${imgUrl}" class="photo-thumbnail" data-full-url="${imgUrl}" />
    <button class="delete-photo" data-url="${imgUrl}">×</button>
</div>`;
                });
            } else {
                imagesHtml = '<p>No photos uploaded yet for this horse.</p>';
            }

            // Display images in the sidebar
            $('#photoPreview').html(imagesHtml);

            // Show the sidebar
            const horseIdForImages = sanitizeHorseId(response.HORSE);
            $('#hiddenHorseIdSanitized').val(horseIdForImages); // Assuming you have a hidden input for horseId
            $('#horseDetailsSidebar').addClass('open');
            $('#photoSection').show();

            // Show Edit, Save and Cancel buttons
            $('#editBtn').show();
            $('#saveBtn').hide();
            $('#cancelBtn').hide();
        }

        $(document).on('click', '.tab-button', function() {
            $('.tab-button').removeClass('active');
            $(this).addClass('active');

            const tabToShow = $(this).data('tab');
            $('.tab-pane').removeClass('active');
            $('#' + tabToShow).addClass('active');
        });

        $(document).on('click', '.photo-thumbnail', function() {
            const fullImageUrl = $(this).data('full-url');

            // Remove any existing modal if it exists
            $('#imageModal').remove();

            // Create modal HTML structure dynamically
            const modalHtml = `
        <div id="imageModal" class="modal" style="display: none;">
            <span class="modal-close">&times;</span>
            <img class="modal-content" id="fullImage" src="${fullImageUrl}">
        </div>
    `;

            // Append modal to body
            $('body').append(modalHtml);

            // Show modal
            $('#imageModal').fadeIn();
            $('body').addClass('modal-open');
        });

        $(document).on('click', '.delete-photo', function() {
            const imageUrl = $(this).data('url');
            const parentElement = $(this).closest('.photo-card');

            if (confirm("Are you sure you want to delete this image?")) {
                $.ajax({
                    url: 'delete_photo.php',
                    type: 'POST',
                    data: {
                        imageUrl: imageUrl
                    },
                    success: function(response) {
                        try {
                            const res = typeof response === 'string' ? JSON.parse(response) : response;
                            if (res.success) {
                                parentElement.remove(); // Remove image from UI
                                alert('Image deleted successfully.');
                            } else {
                                alert('Failed to delete image: ' + (res.error || 'Unknown error'));
                            }
                        } catch (e) {
                            console.error('Invalid response:', response);
                            alert('Unexpected server response.');
                        }
                    },
                    error: function(xhr) {
                        console.error('Delete error:', xhr.responseText);
                        alert('Failed to delete image.');
                    }
                });
            }
        });

        // Close when clicking close button
        $(document).on('click', '.modal-close', function() {
            $('#imageModal').fadeOut(function() {
                $(this).remove();
            });
            $('body').removeClass('modal-open');
        });

        // Close when clicking outside the image
        $(document).on('click', '#imageModal', function(e) {
            if (e.target === this) {
                $(this).fadeOut(function() {
                    $(this).remove();
                });
                $('body').removeClass('modal-open');
            }
        });

        // Horse link click
        $(document).on('click', '.horse-link', function(event) {
            event.preventDefault();
            const horseId = $(this).data('horse-id');
            openSidebar(horseId);
        });

        // Close camera functionality
        $('#closeCameraBtn').on('click', function() {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop()); // Stop all camera tracks
            }
            $('#cameraContainer').hide(); // Hide the camera container
        });

        // Edit button: Switch to edit mode
        $(document).on('click', '#editBtn', function() {
            // Hide all display spans
            $('[id$="Display"]').hide();

            // Show all input fields and populate them
            $('[id$="Input"]').show().each(function() {
                const fieldId = this.id.replace('Input', 'Display');
                $(this).val($('#' + fieldId).text().trim() || '');
            });

            // Toggle buttons
            $('#editBtn').hide();
            $('#saveBtn, #cancelBtn').show();
        });

        // Cancel button: Revert back to view mode
        $(document).on('click', '#cancelBtn', function() {
            // Show all display spans
            $('[id$="Display"]').show();

            // Hide all input fields
            $('[id$="Input"]').hide();

            // Toggle buttons
            $('#editBtn').show();
            $('#saveBtn, #cancelBtn').hide();
        });

        $(document).on('click', '#saveBtn', function() {
            const updatedData = {};
            const fields = [{
                    id: 'yearFoalInput',
                    db: 'YEARFOAL',
                    type: 'number'
                },
                {
                    id: 'sexInput',
                    db: 'SEX',
                    type: 'string'
                },
                {
                    id: 'sireInput',
                    db: 'Sire',
                    type: 'string'
                },
                {
                    id: 'damInput',
                    db: 'DAM',
                    type: 'string'
                },
                {
                    id: 'datefoalInput',
                    db: 'DATEFOAL',
                    type: 'date'
                },
                {
                    id: 'typeInput',
                    db: 'TYPE',
                    type: 'string'
                },
                {
                    id: 'colorInput',
                    db: 'COLOR',
                    type: 'string'
                },
                {
                    id: 'gaitInput',
                    db: 'GAIT',
                    type: 'string'
                },
                {
                    id: 'bredtoInput',
                    db: 'BREDTO',
                    type: 'string'
                },
                {
                    id: 'farmnameInput',
                    db: 'FARMNAME',
                    type: 'string'
                }
            ];

            // Process each field
            fields.forEach(field => {
                const inputElement = $(`#${field.id}`);
                const displayElement = $(`#${field.db.toLowerCase()}Display`);

                let inputValue = inputElement.val() ? inputElement.val().trim() : '';
                let displayValue = displayElement.text() ? displayElement.text().trim() : '';

                // Skip if no change
                if (inputValue === displayValue && inputValue !== '') {
                    return;
                }

                // Type-specific processing
                switch (field.type) {
                    case 'number':
                        inputValue = inputValue !== '' ? parseInt(inputValue, 10) : null;
                        // Validate year is reasonable (1900-current year + 1)
                        if (inputValue !== null && (inputValue < 1900 || inputValue > new Date().getFullYear() + 1)) {
                            alert(`Please enter a valid year between 1900 and ${new Date().getFullYear() + 1}`);
                            inputElement.focus();
                            throw new Error('Invalid year');
                        }
                        break;

                    case 'date':
                        // Convert display format (MM/DD/YYYY) to input format (YYYY-MM-DD) if needed
                        if (displayValue.includes('/')) {
                            const [month, day, year] = displayValue.split('/');
                            displayValue = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
                        }

                        // Validate date format
                        if (inputValue !== '' && !isValidDate(inputValue)) {
                            alert('Please enter a valid date in YYYY-MM-DD format');
                            inputElement.focus();
                            throw new Error('Invalid date');
                        }

                        inputValue = inputValue !== '' ? inputValue : null;
                        break;

                    default: // string
                        inputValue = inputValue !== '' ? inputValue : null;
                }

                // Only add to update if changed
                if (inputValue !== null) {
                    updatedData[field.db] = inputValue;
                }
            });

            if (Object.keys(updatedData).length === 0) {
                alert('No changes detected.');
                return;
            }

            const horseId = $('#hiddenHorseId').val();

            $.ajax({
                url: 'update_horse_details.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    horseId: horseId,
                    t: Date.now(),
                    ...updatedData
                },
                success: function(response) {
                    if (response.success) {
                        // Update display fields with formatted values
                        fields.forEach(field => {
                            const displayId = field.db.toLowerCase() + 'Display';
                            const value = response.updatedData ? response.updatedData[field.db] : updatedData[field.db];

                            if (value !== undefined) {
                                // Special formatting for each type
                                switch (field.type) {
                                    case 'date':
                                        if (value) {
                                            const [year, month, day] = value.split('-');
                                            $(`#${displayId}`).text(`${month}/${day}/${year}`);
                                        } else {
                                            $(`#${displayId}`).text('N/A');
                                        }
                                        break;
                                    case 'number':
                                        $(`#${displayId}`).text(value || 'N/A');
                                        break;
                                    default:
                                        $(`#${displayId}`).text(value || 'N/A');
                                }
                            }
                        });

                        // Visual feedback with color highlight
                        $('[id$="Display"]').css({
                            'background-color': '#e6ffe6',
                            'transition': 'background-color 0.5s'
                        }).hide().fadeIn(200);

                        setTimeout(() => {
                            $('[id$="Display"]').css('background-color', '');
                        }, 1000);

                        // Switch to view mode
                        $('.edit-field').hide();
                        $('[id$="Display"]').show();
                        $('#editBtn').show();
                        $('#saveBtn, #cancelBtn').hide();

                        // Success notification
                        showNotification('Horse details updated successfully!', 'success');
                    } else {
                        showNotification('Update failed: ' + (response.error || 'Unknown error'), 'error');
                    }
                },
                error: function(xhr) {
                    showNotification('Error updating horse details. Please try again.', 'error');
                    console.error("AJAX error:", xhr.responseText);
                }
            });
        });

        // Helper function to validate dates
        function isValidDate(dateString) {
            const regEx = /^\d{4}-\d{2}-\d{2}$/;
            if (!dateString.match(regEx)) return false;
            const d = new Date(dateString);
            return !isNaN(d.getTime());
        }

        // Better notification function (replace alerts)
        function showNotification(message, type) {
            // Implement your preferred notification system here
            // Could be Toastr, SweetAlert, or a custom div
            alert(type.toUpperCase() + ': ' + message);
        }

        $(document).ready(function() {
            // Create modal dialog for column selector
            const modalHTML = `
    <div id="columnSelectorModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3>Select Columns to Display</h3>
            <form id="columnSelectorForm">
                ${$('#columnCheckboxes').html()}
                <button type="submit" class="btn btn-primary" style="margin-top:10px">Save Preferences</button>
            </form>
        </div>
    </div>`;

            $('body').append(modalHTML);

            // Open modal when button is clicked
            $('#columnSelectorBtn').click(function() {
                $('#columnSelectorModal').show();
            });

            // Close modal when X is clicked
            $('.close-modal').click(function() {
                $('#columnSelectorModal').hide();
            });

            // Close modal when clicking outside
            $(window).click(function(event) {
                if (event.target.id === 'columnSelectorModal') {
                    $('#columnSelectorModal').hide();
                }
            });

            // Handle form submission
            // Handle form submission
            $('#columnSelectorForm').on('submit', function(e) {
                e.preventDefault();

                // Collect all checkbox values as booleans
                var columnPrefs = {};
                $('input[name^="column_prefs["]').each(function() {
                    var colName = $(this).attr('name').match(/\[(.*?)\]/)[1];
                    columnPrefs[colName] = $(this).is(':checked');
                });

                // Submit via AJAX
                $.ajax({
                    url: window.location.href,
                    type: 'POST',
                    data: {
                        column_prefs: columnPrefs
                    },
                    success: function() {
                        // Force reload to ensure changes are applied
                        window.location.reload(true);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error saving preferences:', error);
                        alert('Error saving column preferences');
                    }
                });
            });
        });

        document.querySelectorAll('.inspection-input').forEach(input => {
            input.addEventListener('change', function() {
                const field = this.name;
                const value = this.value;
                const horse_name = $('#hiddenHorseId').val();

                console.log('horse_name:', horse_name); // Debugging line to see the value

                fetch('update_inspection.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `horse_name=${encodeURIComponent(horse_name)}&field=${encodeURIComponent(field)}&value=${encodeURIComponent(value)}`
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log("Updated:", data);
                    })
                    .catch(error => {
                        console.error("Error:", error);
                    });
            });
        });
    </script>

</body>

</html>