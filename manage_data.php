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
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
  <script src="assets/js/script.js"></script>
</head>
<?php
include_once("config.php");
$breed_param = $_GET['breed'];

$resultFound = getsaledata($breed_param);

$sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';
$orderBy = isset($_GET['orderby']) ? $_GET['orderby'] : 'Saledate';

// Construct your SQL query based on the sorting parameters
$sql = "SELECT Salecode, Saledate, count(*) FROM sales GROUP BY salecode ORDER BY $orderBy $sortOrder";
if ($breed_param == "T") {
  $sql = "SELECT Salecode, Saledate, count(*) FROM tsales GROUP BY salecode ORDER BY $orderBy $sortOrder";
}

if (!empty($_POST)) {
  $saledate = trim($_POST["saledate"]);
  // Construct the delete query based on the sale date
  $sql_delete = "DELETE FROM sales WHERE Saledate = '$saledate'";
  if ($breed_param == "T") {
    $sql_delete = "DELETE FROM tsales WHERE Saledate = '$saledate'";
  }

  // Execute the delete query
  $result = mysqli_query($link, $sql_delete);
  if ($result) {
    echo "Record deleted successfully";
  } else {
    echo "Error deleting record: " . mysqli_error($link);
  }
}
?>

<br>

<div style="margin:5px 30px 30px 30px;">
  <h1 style="text-align:center;color:#D98880;">Manage File Upload Data</h1>
  <select style="background-color:#229954;" class="custom-select1" id="breed">
    <option value="">Breed Filter</option>
    <option value="S">S : Standardbred</option>
    <option value="T">T : Thoroughbred</option>
  </select>
  <input class="custom-select1" type="submit" onclick="getValues()" name="SUBMITBUTTON" value="Submit" style="font-size:20px; " />
  <hr>
  <div style="max-height: calc(96.2vh - 96.2px);overflow:auto;">
    <div class="table" style="width: device-width;">
      <div class="row header blue" style="line-height: 25px;font-size: 12px;position: sticky;top: 0;">
        <table id="salesTable">
          <div class="cell" style="width: device-width;">No.</div>
          <div class="cell" style="width: device-width;">Salecode<button onclick="sortTable('Salecode')"><img src="assets\images\sort.png" alt="Sort Salecode"></button></div>
          <div class="cell" style="width: device-width;">Saledate<button onclick="sortTable('Saledate')"><img src="assets\images\sort.png" alt="Sort Saledate"></button></div>
          <div class="cell" style="width:device-width;">Upload-date<button onclick="sortTable('upload_date')"><img src="assets\images\sort.png" alt="Sort Uploadtime"></button></div>
          <div class="cell" style="width: device-width;">Salecount</div>
          <div class="cell" style="width: device-width;">delete</div>
        </table>
      </div>
      <?php
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
          if ($elementCount == 2) {
            if ($elements != "") {
              $date = date_create($elements);
              $elements = date_format($date, "m/d/y");
            }
          }
          echo "<div class='cell'>" . $elements . "</div>";
        }
        ?>
        <form name="myform" action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="POST">
          <input type="hidden" name="saledate" id="saledate" value="<?php echo $row['Saledate']; ?>" />
          <button type="submit" href="javascript:deleteSaleData();">Delete</button>
        </form>
        </div>
      <?php } ?>
    </div>
  </div>
</div>
<br>
<script>
  document.getElementById('breed').value = "<?php echo $breed_param; ?>";
</script>
<script>
  function sortTable(column) {
    var sortOrder = '<?php echo $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>';
    window.location.href = '?orderby=' + column + '&sortOrder=' + sortOrder + '&breed=<?php echo $breed_param; ?>';
  }
</script>

<script>
  function getValues() {
    var breed = document.getElementById('breed').value;
    var link = "manage_data.php?breed=" + breed;
    window.open(link, "_self");
    if (breed == "") {
      alert("Please Select Breed Category ");
    }
  }
</script>

<script>
  <?php if (!empty($_POST)) { ?>
    var breed = document.getElementById('breed').value;
    var saledate = document.getElementById('saledate').value;
    var result = "";
    if (confirm("Are you sure, you want to delete records with sale date -" + saledate + "?")) {
      txt = "You pressed OK!";
      result = "<?php echo deleteSalecode($breed_param, $salecode); ?>";
    } else {
      result = "You pressed Cancel!";
    } alert(result);
    getValues();
    <?php
  }
  ?>
</script>
