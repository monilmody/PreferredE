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
$salecode_param =$_GET['salecode'];
$year_param =$_GET['year'];
$type_param =$_GET['type'];
$sex_param =$_GET['sex'];
$sire_param =$_GET['sire'];
// Capture sorting parameters
$sortField = isset($_GET['sortFields']) ? $_GET['sortFields'] : 'Hip';  // Default to 'Hip'
$sortOrder = isset($_GET['sortOrder']) ? $_GET['sortOrder'] : 'ASC';  // Default to 'ASC'

$resultFound = breezeFromYearlingReport_tb($year_param, $salecode_param, $type_param, $sex_param, $sire_param, $sortField, $sortOrder);

$yearList = getYearsList_tb_breeze();
$resultList = fetchSalecodeList_tb1($year_param);
$typeList = fetchTypeList_tb();
$sexList = getSexList_tb();
$sireList = fetchSireListAll_tb($year_param);

?>
<br>


<div style= "margin:5px 30px 30px 30px;">
<h1 style="text-align:center;color:#D98880;">BREEZE FROM YEARLING REPORT THOROUGHBRED</h1>
<!-- <b> -->
<!-- <button class="custom-select1" style="background-color:#35CC03;" onclick="downloadData();">Export Data to CSV</button> -->
<!-- </b> -->
<br>
<br>
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
	<?php 
    //foreach($typeList as $row) {
// 	    echo '<option>'.$row[Type].'</option>';
//     } 
    ?>
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

  <!-- Sorting Fields -->
  <select name="sortFields" class="custom-select1">
        <option value="Hip" <?php echo ($sortField == 'Hip') ? 'selected' : ''; ?>>Sort by Hip</option>
        <option value="Sex" <?php echo ($sortField == 'Sex') ? 'selected' : ''; ?>>Sort by Sex</option>
        <option value="Price" <?php echo ($sortField == 'Price') ? 'selected' : ''; ?>>Sort by Price</option>
        <option value="utt" <?php echo ($sortField == 'utt') ? 'selected' : ''; ?>>Sort by UTT</option>
    </select>

    <select name="sortOrder" class="custom-select1">
        <option value="ASC" <?php echo ($sortOrder == 'ASC') ? 'selected' : ''; ?>>Ascending</option>
        <option value="DESC" <?php echo ($sortOrder == 'DESC') ? 'selected' : ''; ?>>Descending</option>
    </select>

<input class="custom-select1" type="submit" onclick="getValues()" name="SUBMITBUTTON" value="Submit" style="font-size:20px; "/>


<hr>
<div style="max-height: calc(96.2vh - 96.2px);overflow:auto;">
	<div class="table" style="width: device-width">
          <div class="row header blue" style="line-height: 25px;font-size: 12px;position: sticky;top: 0;"> 

          <div class="cell" style="width: device-width;background-color:#D98880">
                Offspring Horse
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880">
                Hip
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880">
                Sex
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880">
                Datefoal
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880">
                Salecode
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880">
                Price
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880 ">
                Rating 
              </div> 
              <div class="cell" style="width: device-width;background-color:#D98880 ">
                Sale Type
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880 ">
                Dam-Racing
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880 ">
                UTT
              </div>
              <div class="cell" style="width: device-width;background-color:#D98880 ">
                Sire
              </div>


              <div class="cell"  style="width: device-width;">
                No.
              </div>
              <div class="cell"  style="width: device-width;">
                Purchaser Name
              </div> 
              <div class="cell"  style="width: device-width;">
                HIP
              </div>
              <div class="cell"  style="width: device-width;">
                Datefoal
              </div>
              <div class="cell"  style="width: device-width;">
                Dam- Y or W
              </div>
              <div class="cell"  style="width: device-width;">
                Sex
              </div>
              <div class="cell"  style="width: device-width;">
                Type
              </div>
              <div class="cell"  style="width: device-width;">
                Price
              </div>
              <div class="cell"  style="width: device-width;">
                SaleCode
              </div>
              <div class="cell"  style="width: device-width;">
                Day
              </div>
              <div class="cell"  style="width: device-width;">
                Consno
              </div>
              <div class="cell"  style="width: device-width;">
                Sale Type
              </div>
              <div class="cell"  style="width: device-width;">
                Age
              </div>
              <div class="cell"  style="width: device-width;">
                Rating
              </div>

              <div class="cell" style="width: device-width;background-color:#D98880 ">
                Total
              </div>
              
             

             </div>
<?php
  setlocale(LC_MONETARY,"en_US");
  $number =0;  

  // Loop through the first set of data (main horse details)
  foreach ($resultFound as $row) {
    // Retrieve offspring data (second set of data)
    $offspringRows = fetchBreezeSoldAsYearling($row['Salecode'], $row['TDAM']);
    
    // Start a row for the main horse details
    echo "<div class='row'>";

    // Display the main horse data (from the first query)
    echo "<div class='cell'>" . $row['Horse'] . "</div>";  // 1. Offspring Horse
    echo "<div class='cell'>" . $row['Hip'] . "</div>";    // 2. Hip
    echo "<div class='cell'>" . $row['Sex'] . "</div>";    // 3. Sex
    echo "<div class='cell'>" . date("m/d/y", strtotime($row['Datefoal'])) . "</div>"; // 4. Datefoal
    echo "<div class='cell'>" . $row['Salecode'] . "</div>"; // 5. Salecode
    echo "<div class='cell'>" . "$" . number_format($row['Price'], 0) . "</div>"; // 6. Price
    echo "<div class='cell'>" . $row['Rating'] . "</div>"; // 7. Rating
    echo "<div class='cell'>" . $row['b_type'] . "</div>";  // 8. Sale Type
    echo "<div class='cell'>" . $row['TDAM'] . "</div>";  // 9. Dam-R
    echo "<div class='cell'>" . $row['utt'] . "</div>"; // 10. utt
    echo "<div class='cell'>" . $row['tSire'] . "</div>";  // 11. Sire


    // Now, loop through the offspring details (second query)
    // and append their data in the same row
    foreach ($offspringRows as $offspringRow) {
        // Display offspring data in the same row as the main horse
        echo "<div class='cell'>" . $number++ . "</div>";  // 12. No.
        echo "<div class='cell'>" . $offspringRow['Purlname'] . ' ' . $offspringRow['Purfname'] . "</div>"; // 13. Purchaser Name
        echo "<div class='cell'>" . $offspringRow['HIP'] . "</div>"; // 14. HIP
        echo "<div class='cell'>" . date("m/d/y", strtotime($offspringRow['Datefoal'])) . "</div>"; // 15. Datefoal
        echo "<div class='cell'>" . $offspringRow['Dam'] . "</div>"; // 16. Dam
        echo "<div class='cell'>" . $offspringRow['Sex'] . "</div>"; // 17. Sex
        echo "<div class='cell'>" . $offspringRow['type'] . "</div>"; // 18. Type
        echo "<div class='cell'>" . "$" . number_format($offspringRow['Price'], 0) . "</div>"; // 19. Price
        echo "<div class='cell'>" . $offspringRow['Salecode'] . "</div>"; // 20. SaleCode
        echo "<div class='cell'>" . $offspringRow['Day'] . "</div>"; // 21. Day
        echo "<div class='cell'>" . $offspringRow['Consno'] . "</div>"; // 22. Consno
        echo "<div class='cell'>" . $offspringRow['saletype'] . "</div>"; // 23. Sale Type
        echo "<div class='cell'>" . $offspringRow['Age'] . "</div>"; // 24. Age
        echo "<div class='cell'>" . $offspringRow['Rating'] . "</div>"; // 25. Rating

        // Calculate and display price difference for each offspring
        $priceDifference = $row['Price'] - $offspringRow['Price'];
        $cellColor = ($priceDifference < 0) ? '#FF6347' : '#32CD32';
        echo "<div class='cell' style='background-color:$cellColor'>" . "$" . number_format($priceDifference, 0) . "</div>"; // 26. Total
    }
    
    // Close the row for this horse entry (main data + offspring data)
    echo "</div>";
  }
?>

    </div>


 </div>
</div>
<script>
	document.getElementById('year').value="<?php echo $year_param;?>";
	document.getElementById('salecode').value="<?php echo $salecode_param;?>";
	document.getElementById('type').value="<?php echo $type_param;?>";
	document.getElementById('sex').value="<?php echo $sex_param;?>";
	document.getElementById('sire').value="<?php echo $sire_param;?>";
  document.getElementById('sortFields').value="<?php echo $sex_param;?>";
	document.getElementById('sortOrder').value="<?php echo $sire_param;?>";
	
</script>
<script>
function getValues() {
  var year = document.getElementById('year').value;
	var salecode = document.getElementById('salecode').value;
	var type = document.getElementById('type').value;
	var sex = document.getElementById('sex').value;
	var sire = document.getElementById('sire').value;
  // Get sort values
  var sortField = document.getElementById('sortFields').value;
  var sortOrder = document.getElementById('sortOrder').value;

    var link ="breeze_to_yearling_report.php?year="+year
    							+"&salecode="+salecode
    							+"&type="+type
    							+"&sex="+sex
    							+"&sire="+sire
                  + "&sortFields=" + sortField
                  + "&sortOrder=" + sortOrder;  // Include sortField and sortOrder in the URL
    							
    window.open(link,"_self");
  	if(year== "" && salecode== "" && type == "" && sex== "" && sire == "")
  	{
  		alert("Please Select Atleast One Category Filter");
  	}
}

</script>

