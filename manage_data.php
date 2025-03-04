<?php
include("./header.php");
?>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="assets/css/table.css">
  
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8"
    src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
  <script src="assets/js/script.js"></script>
</head>
<?php
include_once("config.php");
$breed_param = $_GET['breed'];

$resultFound = getsaledata($breed_param);

if (!empty($_POST)) {
  $salecode = trim($_POST["salecode"]);
  $breed_param = trim($_POST["breed"]);
  //$password = trim($_POST["password"]);
  //echo("alert('aaaa')");
  echo $salecode;
}
?>

<br>
<style>
  #printButton {
  position: fixed;
  left: 20px;         /* Position on the left */
  padding: 12px 20px; /* Increased padding for a larger, more clickable button */
  background-color: #007BFF; /* Blue background color */
  color: white;       /* White text color */
  border: none;       /* Remove border */
  border-radius: 8px; /* Rounded corners */
  font-size: 14px;     /* Slightly larger font */
  font-weight: bold;   /* Bold text for emphasis */
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Soft shadow effect */
  cursor: pointer;    /* Pointer cursor on hover */
  transition: all 0.3s ease; /* Smooth transition for hover effects */
}

#printButton:hover {
  background-color: #0056b3; /* Darker shade of blue on hover */
  box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15); /* Increase shadow on hov */
  transform: translateY(-2px); /* Slight lift effect on hover */
}

#printButton:active {
  background-color: #004085; /* Even darker shade when pressed */
  transform: translateY(1px); /* Pressed effect */
}
</style>
<button id="printButton" onclick="window.print()">Print Page</button>

<div style="margin:5px 30px 30px 30px;">
  <h1 style="text-align:center;color:#D98880;">Manage File Upload Data</h1>



  <select style="background-color:#229954;" class="custom-select1" id="breed">
    <option value="">Breed Filter</option>
    <option value="S">S : Standardbred</option>
    <option value="T">T : Thoroughbred</option>

  </select>

  <input class="custom-select1" type="submit" onclick="getValues()" name="SUBMITBUTTON" value="Submit"
    style="font-size:20px; " />

  <hr>
  <div>
    <div class="table" style="width: device-width;">
      <div class="row header blue" style="line-height: 25px;font-size: 12px;position: sticky;top: 0;">
        <table id="salesTable">
          <div class="cell" style="width: device-width;">
            No.
          </div>
          <div class="cell" style="width: device-width;">
            Salecode
            <button onclick="sortTable('Salecode')">
              <img src="assets\images\sort.png" alt="Sort Salecode">
            </button>
          </div>
          <div class="cell" style="width: device-width;">
            Session
          </div>
          <div class="cell" style="width: device-width;">
            Saletype
          </div>
          <div class="cell" style="width: device-width;">
            Saledate
            <button onclick="sortTable('Saledate')">
              <img src="assets\images\sort.png" alt="Sort Saledate">
            </button>
          </div>
          <div class="cell" style="width:device-width;">
            Upload-date
            <button onclick="sortTable('upload_date')">
              <img src="assets\images\sort.png" alt="Sort Uploadtime">
            </button>
          </div>
          <div class="cell" style="width: device-width;">
            Salecount
          </div>
          <div class="cell" style="width: device-width;">
            Download
          </div>
          <div class="cell" style="width: device-width;">
            Delete
          </div>
          
        </table>
      </div>

      <?php
      if(!empty($resultFound)) {
      setlocale(LC_MONETARY, "en_US");
      $number = 0;
      foreach ($resultFound as $row) {
        $number = $number + 1;
        $elementCount = 0;
        echo "<div class='row'>";
        echo "<div class='cell'>" . $number . "</div>";
        foreach ($row as $elements) {
          $elementCount = $elementCount + 1;
          if ($elements == "0000-00-00") {
            $elements = "";
          }
          if ($elementCount == 4) {
            if ($elements != "") {
              $date = date_create($elements);
              $elements = date_format($date, "m/d/y");
            }
          }
          if ($elementCount == 5) { // For upload_date column
            if ($elements != "") {
              // Assuming $elements is already a valid date string
              $date = DateTime::createFromFormat('Y-m-d H:i:s', $elements); // Adjust the format if needed
              if ($date !== false) {
                $elements = $date->format('m/d/y H:i:s'); // Format the upload_date column
              } else {
                // Handle invalid date format if necessary
                $elements = "Invalid date format";
              }
            }
          }
          echo "<div class='cell'>" . $elements . "</div>";
          
        }

        // Download link column
        echo "<div class='cell' style='width:device-width;'>";
        echo "<a href='download_file.php?salecode=" . $row['Salecode'] . "'>";
        echo "<img src='assets/images/download-image.png' alt='Download' style='width:20px; height:20px;'/>";
        echo "</a>";
        echo "</div>";
    
        // Delete form column
        echo "<div class='cell' style='width:device-width;'>";
        echo "<form name='myform' action='" . $_SERVER['PHP_SELF'] . "' method='POST'>";
        echo "<input type='hidden' name='salecode' id='salecode' value='" . $row['Salecode'] . "' />";
        echo "<input type='hidden' name='breed' id='breed' value='" . $breed_param . "' />";
        echo "<button type='submit'>Delete</button>";
        echo "</form>";
        echo "</div>";

        echo "</div>"; // Close row div

      }}
        
        //echo "<div class='cell'><a href='javascript:deleteSaleData(`".$row[Salecode]."`);'>Delete</a></div>";
        //echo "</div>";
      

    //     ?>
  </div>
</div>
</div>
<br>
<script>
  document.getElementById('breed').value = "<?php echo $breed_param; ?>";
</script>

<script>
  function sortTable(column) {
    // Get the current sort order (ascending or descending) from the URL
    var urlParams = new URLSearchParams(window.location.search);
    var currentSortOrder = urlParams.get('sortOrder') === 'ASC' ? 'DESC' : 'ASC'; // Toggle the sort order

    // Redirect to the page with the updated query parameters for sorting
    window.location.href = '?orderby=' + column + '&sortOrder=' + currentSortOrder + '&breed=<?php echo $breed_param; ?>';
}
</script>

<script>
  function getValues() {
    var breed = document.getElementById('breed').value;

    var link = "manage_data.php?breed=" + breed;
    //alert(link);

    window.open(link, "_self");
    if (breed == "") {
      alert("Please Select Breed Category ");
    }
  }
</script>

<script>
  <?php if (!empty($_POST)) { ?>
    var breed = document.getElementById('breed').value;
    //alert(bred);
    //alert('<?php echo $salecode; ?>');
    var result = "";
    if (confirm("Are you sure, you want to delete -" + '<?php echo $salecode; ?>' + "?")) {
      txt = "You pressed OK!";
      result = "<?php echo deleteSalecode($breed_param, $salecode); ?>";
    } else {
      result = "You pressed Cancel!";
    } alert(result);
    getValues();
    //alert("Are You Sure?");
    <?php
  }
  ?>
</script>
