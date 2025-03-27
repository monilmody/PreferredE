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

$resultFound = fetchOffsprings_breeze_tb1($year_param, $salecode_param, $type_param, $sex_param, $sire_param);

$yearList = getYearsList_tb_breeze();
$resultList = fetchSalecodeList_tb1($year_param);
$typeList = fetchTypeList_tb();
$sexList = getSexList_tb();
$sireList = fetchSireListAll_tb($year_param);

?>
<br>


<div style= "margin:5px 30px 30px 30px;">
<h1 style="text-align:center;color:#D98880;">YEARLING TO BREEZE REPORT THOROUGHBRED</h1>
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
                Dam-R
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
                Horse
              </div>
              <div class="cell"  style="width: device-width;">
                Sire
              </div>
              <div class="cell"  style="width: device-width;">
                Datefoal
              </div>
              <div class="cell"  style="width: device-width;">
                Dam
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

// First loop to display the main fields
foreach ($resultFound as $row) {
    // Retrieve offspring data
    $offspringRows = fetchBreezeReport1($row['Salecode'], $row['TDAM']);
    
    // Start a row for each entry
    echo "<div class='row'>";

    // Main fields to display from the first function
    echo "<div class='cell'>" . $row['Horse'] . "</div>";  // b.Horse
    echo "<div class='cell'>" . $row['Hip'] . "</div>";    // b.HIP
    echo "<div class='cell'>" . $row['Sex'] . "</div>";    // b.Sex
    echo "<div class='cell'>" . date("m/d/y", strtotime($row['Datefoal'])) . "</div>"; // b.Datefoal
    echo "<div class='cell'>" . $row['Salecode'] . "</div>"; // b.Salecode
    echo "<div class='cell'>" . "$" . number_format($row['Price'], 0) . "</div>"; // b.Price
    echo "<div class='cell'>" . $row['Rating'] . "</div>"; // b.Rating
    echo "<div class='cell'>" . $row['b_type'] . "</div>";  // b.SaleType
    echo "<div class='cell'>" . $row['TDAM'] . "</div>";  // b.Dam-R

    // Close the row for the first function
    echo "</div>";

    // Now loop through the offspring details (second function) and display their fields
    foreach ($offspringRows as $offspringRow) {
        // Start a new row for each offspring
        echo "<div class='row'>";

        // Display the requested fields from the second function
        echo "<div class='cell'>" . $number++ . "</div>";  // No.
        echo "<div class='cell'>" . $offspringRow['Purlname'] . ' ' . $offspringRow['Purfname'] . "</div>"; // Purchaser Name
        echo "<div class='cell'>" . $offspringRow['HIP'] . "</div>"; // HIP
        echo "<div class='cell'>" . $offspringRow['Horse'] . "</div>"; // Horse
        echo "<div class='cell'>" . $offspringRow['tSire'] . "</div>"; // Sire
        echo "<div class='cell'>" . date("m/d/y", strtotime($offspringRow['Datefoal'])) . "</div>"; // Datefoal
        echo "<div class='cell'>" . $offspringRow['Dam'] . "</div>"; // Dam
        echo "<div class='cell'>" . $offspringRow['Sex'] . "</div>"; // Sex
        echo "<div class='cell'>" . $offspringRow['type'] . "</div>"; // Type
        echo "<div class='cell'>" . "$" . number_format($offspringRow['Price'], 0) . "</div>"; // Price
        echo "<div class='cell'>" . $offspringRow['Salecode'] . "</div>"; // Salecode
        echo "<div class='cell'>" . $offspringRow['Day'] . "</div>"; // Day
        echo "<div class='cell'>" . $offspringRow['Consno'] . "</div>"; // Consno
        echo "<div class='cell'>" . $offspringRow['saletype'] . "</div>"; // Sale Type
        echo "<div class='cell'>" . $offspringRow['Age'] . "</div>"; // Age
        echo "<div class='cell'>" . $offspringRow['Rating'] . "</div>"; // Rating

        // Calculate and display price difference for each offspring (if needed)
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

        // Close the row for the offspring details
        echo "</div>";
    }
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
	
</script>
<script>
function getValues() {
    var year = document.getElementById('year').value;
	var salecode = document.getElementById('salecode').value;
	var type = document.getElementById('type').value;
	var sex = document.getElementById('sex').value;
	var sire = document.getElementById('sire').value;

    var link ="breeze_to_yearling_report.php?year="+year
    							+"&salecode="+salecode
    							+"&type="+type
    							+"&sex="+sex
    							+"&sire="+sire;
    							
    window.open(link,"_self");
  	if(year== "" && salecode== "" && type == "" && sex== "" && sire == "")
  	{
  		alert("Please Select Atleast One Category Filter");
  	}
}

</script>

