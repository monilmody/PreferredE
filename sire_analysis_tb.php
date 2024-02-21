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
</head>
<?php

include_once("config.php");


$sire_param =$_GET["sire"];
$year_param =$_GET["year"];
$elig_param =$_GET["elig"];
$gait_param =$_GET["gait"];
$sort1_param =$_GET["sort1"];
$sort2_param =$_GET['sort2'];
$sort3_param =$_GET['sort3'];
$sort4_param =$_GET['sort4'];
$sort5_param =$_GET['sort5'];

$resultFound = fetchSireAnalysis_tb($sire_param,$year_param,$elig_param, $gait_param);
$resultList = fetchSireList_tb($year_param);
$yearList = getYearsList_tb();
$eligList = getEligList_tb();
//$gaitList = getGaitList_tb();

$sortList = array("Rank","FRank","CRank","SaleDate","Day","SaleCode", "Dam","Sireofdam", 
                  "Sex","Color","Type", "Elig", "Hip", "Price Desc", "ConsNo","Purlname","Purfname");

echo "<br>";

echo '<div style= "margin:5px 30px 30px 30px;">';
echo '<h1 style="text-align:center;">THOROUGHBRED SIRE ANALYSIS   
<label style="color:5D6D7E";>'.$year_param.'</label> <label style="color:#D98880";>'.$sire_param.'</label></h1>';
?>
<select class="custom-select1" id="year"> <!--onchange="location = this.value;" -->
	<option value="">Sale Year</option>
	<option  value="">All Years</option>
	<?php foreach($yearList as $row) {
	    echo '<option>'.$row['Year'].'</option>';
    } ?>
</select>
 <select class="custom-select1" id="sire"> <!--onchange="location = this.value;" -->
	<option value="">Sire Filter</option>
	<option value="">All Sire</option>
	<?php foreach($resultList as $row) {
  	    echo '<option>'.$row['Sire'].'</option>';
    } ?>
</select>
<select class="custom-select1" id="elig"> 
	<option value="">Elig Filter</option>
	<option  value="">All Elig</option>
	<?php foreach($eligList as $row) {
  	    echo '<option>'.$row['Elig'].'</option>';
    } ?>
</select>
  <select style="background-color:#229954;" class="custom-select1" id="sort1"> 
	<option  value="">Sort By 1st</option>
	<?php foreach($sortList as $row) {
  	    echo '<option>'.$row.'</option>';
    } ?>
</select>
 <select style="background-color:#229954;" class="custom-select1" id="sort2">
	<option value="">Sort By 2nd</option>
	<?php foreach($sortList as $row) {
  	    echo '<option>'.$row.'</option>';
    } ?>
</select>
 <select style="background-color:#229954;" class="custom-select1" id="sort3">
	<option value="">Sort By 3rd</option>
	<?php foreach($sortList as $row) {
  	    echo '<option>'.$row.'</option>';
    } ?>
</select>
 <select style="background-color:#229954;" class="custom-select1" id="sort4">
	<option value="">Sort By 4th</option>
	<?php foreach($sortList as $row) {
  	    echo '<option>'.$row.'</option>';
    } ?>
</select>
 <select style="background-color:#229954;" class="custom-select1" id="sort5">
	<option value="">Sort By 5th</option>
	<?php foreach($sortList as $row) {
  	    echo '<option>'.$row.'</option>';
    } ?>
</select>
<input class="custom-select1" type="submit" onclick="getValues()" name="SUBMITBUTTON" value="Submit" style="font-size:20px; "/>
<div style="height:810px;overflow:auto;">
<hr>
<?php
        setlocale(LC_MONETARY,"en_US");
		foreach($resultFound as $row) {
    		    echo '<div class="sire-header">';
    		    echo '<h4 style="color:#E74C3C";><B><u>'.$row['Sire'].'</u></B></h4>';
    		    echo '<h5><B>Total Sold - <label style="color:#E74C3C";>'.$row['Count'].'</label> | 
                        Total- <label style="color:#E74C3C";>$'.number_format($row['Total']).'</label> | 
                        Average - <label style="color:#E74C3C";>$'.number_format($row['Avg']).'</label> | 
                        Top Seller - <label style="color:#E74C3C";>$'.number_format($row['Top']).'</label></B></h5>';
    		    echo '<h5><B>Colts Sold - <label style="color:#E74C3C";>'.$row['CCount'].'</label> | 
                        Colts Total- <label style="color:#E74C3C";>$'.number_format($row['CTotal']).'</label> | 
                        Colts Average - <label style="color:#E74C3C";>$'.number_format($row['CAvg']).'</label> | 
                        Colts Top Seller - <label style="color:#E74C3C";>$'.number_format($row['CTop']).'</label>
                        </B></h5>
                        <h5><B>Fillies Sold - <label style="color:#E74C3C";>'.$row['FCount'].'</label> | 
                        Fillies Total- <label style="color:#E74C3C";>$'.number_format($row['FTotal']).'</label> | 
                        Fillies Average - <label style="color:#E74C3C";>$'.number_format($row['FAvg']).'</label> | 
                        Fillies Top Seller - <label style="color:#E74C3C";>$'.number_format($row['FTop']).'</label></B></h5>';
    		    echo '</div>';
    		    
if ($year_param != "" or $sire_param != "" or $elig_param != "") {
?>
       <div class="table" style="max-height: calc(96.2vh - 96.2px);overflow: auto;">
          <div class="row header blue" style="line-height: 25px;font-size: 12px;position: sticky;top: 0;">
        	  <div class="cell" style="width: device-width;">
                No.
              </div>
              <div class="cell" style="width: device-width;">
                R
              </div>
              <div class="cell" style="width: device-width;">
                FR
              </div>
              <div class="cell" style="width: device-width;">
                CR
              </div>
              <div class="cell" style="width: device-width;">
                HIP
              </div>
              <div class="cell" style="width: device-width;">
                Horse
              </div>
              <div class="cell" style="width: device-width;">
                S
              </div>
              <div class="cell" style="width: device-width;">
                C
              </div>
              <div class="cell" style="width: device-width;">
                T
              </div>
              <div class="cell" style="width: device-width;">
                DOB
              </div>
              <div class="cell" style="width: device-width;">
                Elig
              </div>
              <div class="cell" style="width: device-width;">
                Dam
              </div>
              <div class="cell" style="width: device-width;">
                SireOfDam
              </div>
              <div class="cell" style="width: device-width;">
                SaleCode
              </div>
              <div class="cell" style="width: device-width;">
                Consno
              </div>
              <div class="cell" style="width: device-width;">
                Saledate
              </div>
              <div class="cell" style="width: device-width;">
                Day
              </div>
              <div class="cell" style="width: device-width;">
                Price
              </div>
              <div class="cell" style="width: device-width;">
                Curr
              </div>
              <div class="cell" style="width: device-width;">
                PLastName
              </div>
              <div class="cell" style="width: device-width;">
                PFirstName
              </div>
              <div class="cell" style="width: device-width;">
                Rate
              </div>
          </div>
          
          <?php
		    
		    #$lastname1 =$row[Sire];
            $number =0;
            $sireData = fetchSireData_tb($row['Sire'],$year_param,$elig_param,$gait_param,$sort1_param,$sort2_param,$sort3_param,$sort4_param,$sort5_param);         

            foreach($sireData as $row1) {
                  $elementCount = 0;
                  $number = $number+1;
                  
                  echo "<div class='row'>";
                  echo "<div class='cell'>".$number."</div>";
                  //echo "<div class='cell'>".$number."</div>";
                  #echo "</a>";
                  #echo $row[Price];
                  foreach($row1 as $elements) {
                      $elementCount =$elementCount+1;
                      // Check if $elements is numeric before formatting
                      if (($elementCount == 17) && is_numeric($elements)) {
                        $elements = "$" . number_format((float)$elements);
                      }
                      // Check and format date fields
                      if ($elementCount == 9 || $elementCount == 15) {
                        if ($elements !== "" && $elements !== "1900-01-01") {
                            $date = date_create($elements);
                            if ($date !== false) {
                                $elements = date_format($date, 'Y-m-d');
                            } else {
                                // Handle invalid date format here, maybe log it
                                error_log("Invalid date format: $elements");
                            }
                        } else {
                            $elements = "1900-01-01"; // Set to empty string if date is empty or "0000-00-00"
                        }
                      }
                      if ($elementCount == 14) {
                          $elements= substr($elements, 0,4);
                      }
                      
                      echo "<div class='cell'>".$elements."</div>";
                      
                  }echo "</div>";
            }
                  echo "</div>";
              }
		}
          ?>
</div>
</div>
<br>
<script>
	document.getElementById('year').value="<?php echo $year_param;?>";
	document.getElementById('sire').value="<?php echo $sire_param;?>";
	document.getElementById('elig').value="<?php echo $elig_param;?>";
	document.getElementById('sort1').value="<?php echo $sort1_param;?>";
	document.getElementById('sort2').value="<?php echo $sort2_param;?>";
	document.getElementById('sort3').value="<?php echo $sort3_param;?>";
	document.getElementById('sort4').value="<?php echo $sort4_param;?>";
	document.getElementById('sort5').value="<?php echo $sort5_param;?>";

</script>
<script>
function getValues() {
    var year = document.getElementById('year').value;
	var sire = document.getElementById('sire').value;
	var elig = document.getElementById('elig').value;
	var sort1 = document.getElementById('sort1').value;
	var sort2 = document.getElementById('sort2').value;
	var sort3 = document.getElementById('sort3').value;
	var sort4 = document.getElementById('sort4').value;
	var sort5 = document.getElementById('sort5').value;

      // Store selected values in localStorage
    localStorage.setItem('selectedYear', year);
    localStorage.setItem('selectedSire', sire);
    localStorage.setItem('selectedElig', elig);
    localStorage.setItem('selectedSort1', sort1);
    localStorage.setItem('selectedSort2', sort2);
    localStorage.setItem('selectedSort3', sort3);
    localStorage.setItem('selectedSort4', sort4);
    localStorage.setItem('selectedSort5', sort5);

    var link ="sire_analysis_tb.php?year="+year
    							+"&sire="+sire
    							+"&elig="+elig
    							+"&sort1="+sort1
    							+"&sort2="+sort2
    							+"&sort3="+sort3
    							+"&sort4="+sort4
    							+"&sort5="+sort5;
    //alert(link);
    							
  	window.open(link,"_self");
  	
}

// Function to set default selected values from localStorage
function setDefaultValues() {
    document.getElementById('year').value = localStorage.getItem('selectedYear') || '';
    document.getElementById('sire').value = localStorage.getItem('selectedSire') || '';
    document.getElementById('elig').value = localStorage.getItem('selectedElig') || '';
    document.getElementById('sort1').value = localStorage.getItem('selectedSort1') || '';
    document.getElementById('sort2').value = localStorage.getItem('selectedSort2') || '';
    document.getElementById('sort3').value = localStorage.getItem('selectedSort3') || '';
    document.getElementById('sort4').value = localStorage.getItem('selectedSort4') || '';
    document.getElementById('sort5').value = localStorage.getItem('selectedSort5') || '';
}

// Call the function to set default values when the page loads
setDefaultValues();
</script>


