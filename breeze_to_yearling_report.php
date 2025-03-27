<?php
include("./header.php");
?>

<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="assets/css/table.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
  <script src="assets/js/script.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>

<?php
include_once("config.php");
$salecode_param = $_GET['salecode'];
$year_param = $_GET['year'];
$type_param = $_GET['type'];
$sex_param = $_GET['sex'];
$sire_param = $_GET['sire'];

$resultFound = fetchOffsprings_breeze_tb1($year_param, $salecode_param, $type_param, $sex_param, $sire_param);

$yearList = getYearsList_tb_breeze();
$resultList = fetchSalecodeList_tb1($year_param);
$typeList = fetchTypeList_tb();
$sexList = getSexList_tb();
$sireList = fetchSireListAll_tb($year_param);
?>

<br>
<div style="margin: 5px 30px 30px 30px;">
  <h1 style="text-align: center; color: #D98880;">YEARLING TO BREEZE REPORT THOROUGHBRED</h1>
  <br><br>
  <!-- Filter Section -->
  <select class="custom-select1" id="year" onchange="updateSalecode_tb(this.value)">
    <option value="">Sale Year</option>
    <option value="">All Years</option>
    <?php foreach($yearList as $row) {
      echo '<option>'.$row['Year'].'</option>';
    } ?>
  </select>
  <select class="custom-select1" id="salecode">
    <option value="">Sale Code Filter</option>
    <option value="">All Salecode</option>
    <?php foreach($resultList as $row) {
      echo '<option>'.$row['Salecode'].'</option>';
    } ?>
  </select>
  <select class="custom-select1" id="type">
    <option value="">Type Filter</option>
    <option value="">All Types</option>
    <option value="Y">Y : Yearling</option>
    <option value="R">R : Racing Horse</option>
    <script>
      $(document).ready(function() {
        $('#type').val('R');
      });
    </script>
  </select>
  <select class="custom-select1" id="sex">
    <option value="">Sex Filter</option>
    <option value="">All Sex</option>
    <?php foreach($sexList as $row) {
      echo '<option>'.$row['Sex'].'</option>';
    } ?>
  </select>
  <select class="custom-select1" id="sire">
    <option value="">Sire Filter</option>
    <option value="">All Sire</option>
    <?php foreach($sireList as $row) {
      echo '<option>'.$row['Sire'].'</option>';
    } ?>
  </select>
  <input class="custom-select1" type="submit" onclick="getValues()" name="SUBMITBUTTON" value="Submit" style="font-size:20px; "/>
  <hr>

  <!-- Table Section -->
  <div style="max-height: calc(96.2vh - 96.2px); overflow: auto;">
    <div class="table" style="width: device-width">
      <!-- Table Header: Only Displayed Once -->
      <div class="row header blue" style="line-height: 25px; font-size: 12px; position: sticky; top: 0;">
        <div class="cell" style="background-color:#D98880">Offspring Horse</div>
        <div class="cell" style="background-color:#D98880">Hip</div>
        <div class="cell" style="background-color:#D98880">Sex</div>
        <div class="cell" style="background-color:#D98880">Datefoal</div>
        <div class="cell" style="background-color:#D98880">Salecode</div>
        <div class="cell" style="background-color:#D98880">Price</div>
        <div class="cell" style="background-color:#D98880">Rating</div>
        <div class="cell" style="background-color:#D98880">Sale Type</div>
        <div class="cell" style="background-color:#D98880">Dam-R</div>
        <div class="cell">No.</div>
        <div class="cell">Purchaser Name</div>
        <div class="cell">HIP</div>
        <div class="cell">Horse</div>
        <div class="cell">Sire</div>
        <div class="cell">Datefoal</div>
        <div class="cell">Dam</div>
        <div class="cell">Sex</div>
        <div class="cell">Type</div>
        <div class="cell">Price</div>
        <div class="cell">SaleCode</div>
        <div class="cell">Day</div>
        <div class="cell">Consno</div>
        <div class="cell">Sale Type</div>
        <div class="cell">Age</div>
        <div class="cell">Rating</div>
        <div class="cell" style="background-color:#D98880">Total</div>
      </div>

      <?php
      setlocale(LC_MONETARY, "en_US");
      $number = 0;

      // Loop to display the main fields
      foreach ($resultFound as $row) {
          // Retrieve offspring data
          $offspringRows = fetchBreezeReport1($row['Salecode'], $row['TDAM']);
          
          // Main fields to display from the first function
          echo "<div class='row'>";
          echo "<div class='cell'>" . $row['Horse'] . "</div>";
          echo "<div class='cell'>" . $row['Hip'] . "</div>";
          echo "<div class='cell'>" . $row['Sex'] . "</div>";
          echo "<div class='cell'>" . date("m/d/y", strtotime($row['Datefoal'])) . "</div>";
          echo "<div class='cell'>" . $row['Salecode'] . "</div>";
          echo "<div class='cell'>" . "$" . number_format($row['Price'], 0) . "</div>";
          echo "<div class='cell'>" . $row['Rating'] . "</div>";
          echo "<div class='cell'>" . $row['b_type'] . "</div>";
          echo "<div class='cell'>" . $row['TDAM'] . "</div>";
          echo "</div>"; // End of first function row

          // Loop through the offspring details and display their fields
          foreach ($offspringRows as $offspringRow) {
            $number = $number+1;
            $elementCount = 0;
            $totalPrice = floatval($row['Price']); // Assuming the price column name is 'Price'
            $offspringTotalPrice = 0;

            // Generate a unique ID for the collapse panel
            $collapseID = "collapse" . $number;
            
            echo "<div class='row'>";
            echo "<div class='cell'>".$number."</div>";

            echo "<div class='cell'>";
            echo "<button class='btn btn-link' type='button' data-toggle='collapse' data-target='#$collapseID' aria-expanded='false' aria-controls='$collapseID'>";
            echo "Purchaser" . "</button>"; // HIP button for collapsing
            echo "</div>";

            foreach($row as $key => $elements) {
              if ($key === 'Purlname' || $key === 'Purfname') {
                continue; // Skip Purlname and Purfname in the main row display
              }
                $elementCount =$elementCount+1;
                if($elementCount == 18){
                    $elements = "$".number_format(floatval($elements), 0);
                }
                if ($elements == "1900-01-01") {
                    $elements="";
                }
                if ($elementCount == 14) {
                    // if ($elements != "") {
                    //     $date=date_create($elements);
                    //     $elements = date_format($date,"m/d/y");
                    // }
                }
                if ($elementCount == 10) {
                    // $elements= substr($elements, 0,4);
                }
                echo "<div class='cell'>".$elements."</div>";


              // Price difference cell with color coding
              $priceDifference = $offspringRow['Price'] - $row['Price'];
              $cellColor = ($priceDifference < 0) ? '#FF6347' : '#32CD32';
              echo "<div class='cell' style='background-color:$cellColor'>" . "$" . number_format($priceDifference, 0) . "</div>";

              // Purchaser collapsible functionality for offspring
              $collapseID = "collapse" . $number;
              echo "<div class='cell'><button class='btn btn-link' type='button' data-toggle='collapse' data-target='#$collapseID' aria-expanded='false' aria-controls='$collapseID'>Purchaser</button></div>";

              // Collapsible Panel for Purchaser Name for offspring
              echo "<div id='$collapseID' class='collapse' style='padding: 0; margin: 0; background-color: #d3d3d3;'>";
              echo "<div class='cell' style='padding-left: 20px;'><i>BUYER:</i> <i>" . $offspringRow['Purlname'] . ' ' . $offspringRow['Purfname'] . "</i></div>";
              echo "</div>";

              echo "</div>"; // End of offspring row
          }
      }
      ?>
    </div>
  </div>
</div>

<script>
  document.getElementById('year').value = "<?php echo $year_param; ?>";
  document.getElementById('salecode').value = "<?php echo $salecode_param; ?>";
  document.getElementById('type').value = "<?php echo $type_param; ?>";
  document.getElementById('sex').value = "<?php echo $sex_param; ?>";
  document.getElementById('sire').value = "<?php echo $sire_param; ?>";
</script>

<script>
  function getValues() {
    var year = document.getElementById('year').value;
    var salecode = document.getElementById('salecode').value;
    var type = document.getElementById('type').value;
    var sex = document.getElementById('sex').value;
    var sire = document.getElementById('sire').value;

    var link = "breeze_to_yearling_report.php?year=" + year
      + "&salecode=" + salecode
      + "&type=" + type
      + "&sex=" + sex
      + "&sire=" + sire;

    window.open(link, "_self");
    if (year == "" && salecode == "" && type == "" && sex == "" && sire == "") {
      alert("Please Select Atleast One Category Filter");
    }
  }
</script>
