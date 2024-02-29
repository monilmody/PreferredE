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
require_once("phpFastCaching.php");
use Phpfastcache\Helper\Psr16Adapter;
$time_start = microtime(true);
$year_param =$_GET['year'];
$salecode_param =$_GET['salecode'];
$type_param =$_GET['type'];
$elig_param =$_GET['elig'];
$gait_param =$_GET['gait'];
$sort1_param =$_GET['sort1'];
$sort2_param =$_GET['sort2'];
$sort3_param =$_GET['sort3'];
$sort4_param =$_GET['sort4'];
$sort5_param =$_GET['sort5'];

// Initialize the Phpfastcache
$cache = new \Phpfastcache\CacheManager('files');

// Build a unique cache key based on the parameters
$cacheKey = 'fetchIndividualSaleData_tb_' . md5(serialize($_GET));


//if ($year_param != "" && $salecode_param !="" && $type_param !="" && $elig_param !="" && $gait_param !="") {
  if ($cache->has($cacheKey)) {
    $resultFound = $cache->get($cacheKey);
  } else {
    $resultFound = fetchIndividualSaleData_tb($year_param,$salecode_param,$type_param,$elig_param,$gait_param,
        $sort1_param,$sort2_param,$sort3_param,$sort4_param,$sort5_param);

        if ($resultFound !== false) {
          $cache->set($cacheKey, $resultFound, 300);
        } else {
          echo "Error fetching data from database";
        }
  }
//}

if (!$cache->has('salecode_list')) {
  // If not cached, fetch and cache the salecode list
  $salecodeList = fetchSalecodeList_tb($year_param);
  $cache->set('salecode_list', $salecodeList, 300);
} else {
  // Retrieve salecode list from cache
  $salecodeList = $cache->get('salecode_list');
}

// Check if the years list is cached
if (!$cache->has('years_list')) {
  // If not cached, fetch and cache the years list
  $yearsList = getYearsList_tb();
  $cache->set('years_list', $yearsList, 300);
} else {
  // Retrieve years list from cache
  $yearsList = $cache->get('years_list');
}

// Check if the eligibility list is cached
if (!$cache->has('eligibility_list')) {
  // If not cached, fetch and cache the eligibility list
  $eligList = getEligList_tb();
  $cache->set('eligibility_list', $eligList, 300);
} else {
  // Retrieve eligibility list from cache
  $eligList = $cache->get('eligibility_list');
}

// Check if the type list is cached
if (!$cache->has('type_list')) {
  // If not cached, fetch and cache the type list
  $typeList = fetchTypeList_tb();
  $cache->set('type_list', $typeList, 300);
} else {
  // Retrieve type list from cache
  $typeList = $cache->get('type_list');
}

$time_end = microtime(true);

echo 'Execution time: ' . number_format($time_end - $time_start, 10) . ' seconds';

// $yearList = getYearsList_tb();
// $eligList = getEligList_tb();
// //$gaitList = getGaitList_tb();
// $typeList = fetchTypeList_tb();

$sortList = array("ORank","FRank","CRank","SaleDate","SaleCode","Sire",  "Dam",
                  "Sex","Color","Type", "Elig", "Hip", "Price Desc", "ConsNo","Purlname","Purfname","Rating Desc");

echo "<br>";

echo '<div style= "margin:5px 30px 30px 30px;">';
echo '<h1 style="text-align:center;color:#D98880;">THOROUGHBRED INDIVIDUAL HORSE SALES REPORT</h1>';
?>
<select class="custom-select1" id="year" onchange="updateSalecode(this.value)"> 
	<option value="">Sale Year</option>
	<option  value="">All Years</option>
	<?php foreach($yearList as $row) {
	    echo '<option>'.$row['Year'].'</option>';
    } ?>
</select>
 <select class="custom-select1" id="salecode"> <!--onchange="location = this.value;" -->
	<option value="">Salecode Filter</option>
	<option value="">All Salecode</option>
	<?php foreach($resultList as $row) {
  	    echo '<option>'.$row['Salecode'].'</option>';
    } ?>
</select>
<select class="custom-select1" id="type"> 
	<option value="">Type Filter</option>
	<option value="">All Types</option>
<!-- 	<option value="B">B : Broodmare</option> -->
<!-- 	<option value="LB">LB : Lifetime Breeding</option> -->
<!-- 	<option value="M">M : Maiden Broodmare</option> -->
<!-- 	<option value="P">P : Broodmare Prospect</option> -->
<!-- 	<option value="R">R : Race Horse</option> -->
<!-- 	<option value="S">S : Share</option> -->
<!-- 	<option value="SEA">SEA : Season</option> -->
<!-- 	<option value="T">T : Stallion</option> -->
<!-- 	<option value="W">W : Weanling</option> -->
<!-- 	<option value="Y">Y : Yearling</option> -->
	<?php 
    foreach($typeList as $row) {
	    echo '<option>'.$row['Type'].'</option>';
    } 
    ?>
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

<hr>
<div style="max-height: calc(96.2vh - 96.2px);overflow:auto;">
       <div class="table" style="width: device-width;">
          <div class="row header blue" style="line-height: 25px;font-size: 12px;position: sticky;top: 0;">
        	  <div class="cell">
                No.
              </div>
              <div class="cell">
                R
              </div>
              <div class="cell">
                FR
              </div>
              <div class="cell">
                CR
              </div>
              <div class="cell">
                HIP
              </div>
              <div class="cell">
                Horse
              </div>
              <div class="cell">
                S
              </div>
              <div class="cell">
                C
              </div>
              <div class="cell">
                T
              </div>
              <div class="cell">
                DOB
              </div>
              <div class="cell">
                Elig
              </div>
              <div class="cell">
                Sire
              </div>
              <div class="cell">
                Dam
              </div>
              <div class="cell">
                SaleCode
              </div>
              <div class="cell">
                Consno
              </div>
              <div class="cell">
                Saledate
              </div>
              <div class="cell">
                Day
              </div>
              <div class="cell">
                Price
              </div>
              <div class="cell">
                Curr
              </div>
              <div class="cell">
                PLastName
              </div>
              <div class="cell">
                PFirstName
              </div>
              <div class="cell">
                Rate
              </div>
          </div>
          
          <?php
            setlocale(LC_MONETARY,"en_US");
            $number =0;
            foreach($resultFound as $row1) {
                  $elementCount = 0;
                  $number = $number+1;
                  
                  echo "<div class='row'>";
                  echo "<div class='cell'>".$number."</div>";
                  foreach($row1 as $elements) {
                      $elementCount =$elementCount+1;
                      if($elementCount == 17){
                          $elements = "$".number_format($elements);
                      }
                      if ($elements == "0000-00-00") {
                          $elements="";
                      }
                      if ($elementCount == 9 or $elementCount == 15) {
                          if ($elements != "") {
                              $date=date_create($elements);
                              $elements = date_format($date,"m/d/y");
                          }
                      }
                      if ($elementCount == 14) {
                          $elements= substr($elements, 0,4);
                      }
                      
                      echo "<div class='cell'>".$elements."</div>";
                      
                  }echo "</div>";
            }

          ?>
</div>
</div>
<br>
<script>
//alert(document.getElementById('elig').value);
	document.getElementById('year').value="<?php echo $year_param;?>";
	document.getElementById('salecode').value="<?php echo $salecode_param;?>";
	document.getElementById('type').value="<?php echo $type_param;?>";
	document.getElementById('elig').value="<?php echo $elig_param;?>";
	document.getElementById('sort1').value="<?php echo $sort1_param;?>";
	document.getElementById('sort2').value="<?php echo $sort2_param;?>";
	document.getElementById('sort3').value="<?php echo $sort3_param;?>";
	document.getElementById('sort4').value="<?php echo $sort4_param;?>";
	document.getElementById('sort5').value="<?php echo $sort5_param;?>";
	//alert(document.getElementById('year').value);

</script>
<script>
function getValues() {
    var year = document.getElementById('year').value;
	var salecode = document.getElementById('salecode').value;
	var type = document.getElementById('type').value;
	var elig = document.getElementById('elig').value;
	var sort1 = document.getElementById('sort1').value;
	var sort2 = document.getElementById('sort2').value;
	var sort3 = document.getElementById('sort3').value;
	var sort4 = document.getElementById('sort4').value;
	var sort5 = document.getElementById('sort5').value;

    var link ="individual_sales_report_tb.php?year="+year
    							+"&salecode="+salecode
    							+"&type="+type
    							+"&elig="+elig
    							+"&sort1="+sort1
    							+"&sort2="+sort2
    							+"&sort3="+sort3
    							+"&sort4="+sort4
    							+"&sort5="+sort5;
    //alert(link);
    
  							
  	window.open(link,"_self");
}
</script>


