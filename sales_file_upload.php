<?php
include("./header.php");
?>

<head>
  <meta name="viewport" content="width=device-width</h4> initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="assets/css/table.css">
  
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>


</head>
<?php
include_once("config.php");
?>
<br>

<div style= "margin:5px 30px 30px 30px;">
<h1 style="text-align:center;color:#D98880;">STANDARD BRED SALES FILE UPLOAD</h1>
<hr>

<h2>Import CSV File</h2>
<div id="response"
        class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>">
        <?php if(!empty($message)) { echo $message; } ?>
        </div>
<form class="form-horizontal" action="import_csv.php" method="post"
                name="frmCSVImport" id="frmCSVImport"
                enctype="multipart/form-data">
    <table border="1">
        <tr >
        <td colspan="2" align="center"><strong>Import CSV file</strong></td>
        </tr>
        <tr>
        <td align="center">CSV File:</td><td><input type="file" name="file" id="file" accept=".csv"></td></tr>
        <tr >
        <td colspan="2" align="center"><input type="submit" value="submit" name="import"></td>
        </tr>
    </table>
</form>

<div style="text-align:center;align:right;font-size: 12px;">
<h2><a href="file_download.php?bred=S">Download Sample CSV file</a></h2>
<!-- <h2><a href="http://www.preferredequineresults.com/PreferredE/sampleFileUpload.csv">Download Sample CSV file</a></h2> -->
</div>
<div style="text-align:left;align:left;font-size: 12px;">
    	<h3>Please Upload data in below format:</h3>
    	
    	<div class="table" style="width: 15%;align:right;">
            <div class="row header green" style="line-height: 25px;font-size: 12px;border: 1px solid white;position: sticky;top: 0;">
              <div class="cell" style="width: 100%;">
                COLUMNS
              </div>
            </div>
            <div class='row'>
              <div class="cell">"SALEYEAR" int(4) </div> </div> <div class='row'>
              <div class="cell">"SALETYPE" varchar(1) </div> </div> <div class='row'>
              <div class="cell">"SALECODE" varchar(20) </div> </div> <div class='row'>
              <div class="cell">"SALEDATE" date (YYYY-MM-DD)</div> </div> <div class='row'>
              <div class="cell">"BOOK" varchar(2) </div> </div> <div class='row'>
              <div class="cell">"DAY" tinyint </div> </div> <div class='row'>
              <div class="cell">"HIP" varchar(6) </div> </div> <div class='row'>
              <div class="cell">"HIPNUM" int </div> </div> <div class='row'>
              <div class="cell">"HORSE" varchar(35) </div> </div> <div class='row'>
              <div class="cell">"CHORSE" varchar(35) </div> </div> <div class='row'>
              <div class="cell">"RATING" varchar(5) </div> </div> <div class='row'>
              <div class="cell">"TATTOO" varchar(6) </div> </div> <div class='row'>
              <div class="cell">"DATEFOAL" date (YYYY-MM-DD) </div> </div> <div class='row'>
              <div class="cell">"AGE" int  default 0</div> </div> <div class='row'>
              <div class="cell">"COLOR" varchar(5) </div> </div> <div class='row'>
              <div class="cell">"SEX" varchar(3) </div> </div> <div class='row'>
              <div class="cell">"GAIT" varchar(3) </div> </div> <div class='row'>	
              <div class="cell">"TYPE" varchar(3) </div> </div> <div class='row'>
              <div class="cell">"RECORD" varchar(25) </div> </div> <div class='row'>
              <div class="cell">"ET" varchar(1) </div> </div> <div class='row'>
              <div class="cell">"ELIG" varchar(2) </div> </div> <div class='row'>
              <div class="cell">"SIRE" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"CSIRE" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"DAM" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"CDAM" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"SIREOFDAM" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"CSIREOFDAM" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"DAMOFDAM" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"CDAMOFDAM" varchar(50) </div> </div> <div class='row'>
              <div class="cell">"DAMTATT" varchar(6) </div> </div> <div class='row'>
              <div class="cell">"DAMYOF" int (YYYY)</div> </div> <div class='row'>
              <div class="cell">"DDAMTATT" varchar(6)</div> </div> <div class='row'>
              <div class="cell">"BREDTO" varchar(20) </div> </div> <div class='row'>
              <div class="cell">"LASTBRED" date (YYYY-MM-DD)</div> </div> <div class='row'>
              <div class="cell">"CONSLNAME" varchar(60) </div> </div> <div class='row'>
              <div class="cell">"CONSNO" varchar(20) </div> </div> <div class='row'>
              <div class="cell">"PEMCODE" varchar(15) </div> </div> <div class='row'>
              <div class="cell">"PURFNAME" varchar(30) </div> </div> <div class='row'>
              <div class="cell">"PURLNAME" varchar(70) </div> </div> <div class='row'>
              <div class="cell">"SBCITY" varchar(25) </div> </div> <div class='row'>
              <div class="cell">"SBSTATE" varchar(10) </div> </div> <div class='row'>
              <div class="cell">"SBCOUNTRY" varchar(15) </div> </div> <div class='row'>
              <div class="cell">"PRICE" double </div> </div> <div class='row'>
              <div class="cell">"CURRENCY" varchar(3) </div> </div> <div class='row'>
              <div class="cell">"URL" varchar(150) </div> </div> <div class='row'>
              <div class="cell">"NFFM" varchar(2) </div> </div> <div class='row'>
              <div class="cell">"PRIVATESALE" varchar(2) </div> </div> <div class='row'>
              <div class="cell">"BREED" varchar(2) </div> </div> <div class='row'>
              <div class="cell">"YEARFOAL" int(YYYY) </div> </div> 
        </div>
    </div>



</div>
<br>

<?php
// use Phppot\DataSource;

// require_once 'DataSource.php';
// $db = new DataSource();
// $conn = $db->getConnection();
// $rowCount=0;

// if (isset($_POST["import"])) {
    
//     $fileName = $_FILES["file"]["tmp_name"];
    
//     if ($_FILES["file"]["size"] > 0) {
        
//         $file = fopen($fileName, "r");
        
//         while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            
//             $rowCount = $rowCount + 1;
//             echo $rowCount;
//             if($rowCount == 1)
//             {
//                 continue;
//             }
            
//             $sire = "";
//             if (isset($column[31])) {
//                 $sire = mysqli_real_escape_string($conn, $column[31]);
//             }
//             $csire = "";
//             if (isset($column[32])) {
//                 $csire = mysqli_real_escape_string($conn, $column[32]);
//             }
//             $dam = "";
//             if (isset($column[33])) {
//                 $dam = mysqli_real_escape_string($conn, $column[33]);
//             }
//             $cdam = "";
//             if (isset($column[34])) {
//                 $cdam = mysqli_real_escape_string($conn, $column[34]);
//             }
//             $sireofdam = "";
//             if (isset($column[35])) {
//                 $sireofdam = mysqli_real_escape_string($conn, $column[35]);
//             }
//             $csireofdam = "";
//             if (isset($column[36])) {
//                 $csireofdam = mysqli_real_escape_string($conn, $column[36]);
//             }
//             $damofdam = "";
//             if (isset($column[37])) {
//                 $damofdam = mysqli_real_escape_string($conn, $column[37]);
//             }
//             $cdamofdam = "";
//             if (isset($column[38])) {
//                 $cdamofdam = mysqli_real_escape_string($conn, $column[38]);
//             }
//             $damtatt = "";
//             if (isset($column[39])) {
//                 $damtatt = mysqli_real_escape_string($conn, $column[39]);
//             }
//             $damyof = 0;
//             if (isset($column[40])) {
//                 $damyof = mysqli_real_escape_string($conn, $column[40]);
//             }
//             $ddamtatt = "";
//             if (isset($column[41])) {
//                 $ddamtatt = mysqli_real_escape_string($conn, $column[41]);
//             }
            
//             $damsire_ID = getDamsireID($sire, $dam);
//             echo 'exist--'.$damsire_ID;
//             echo '<br>';
            
            
//             echo $sire."--".
//                 $csire."--".
//                 $dam."--".
//                 $cdam."--".
//                 $sireofdam."--".
//                 $csireofdam."--".
//                 $damofdam."--".
//                 $cdamofdam."--".
//                 $damtatt."--".
//                 $ddamtatt;
//             if ($damsire_ID == "") {
//                 $sqlInsertDamsire = "INSERT into damsire
//             (SIRE,CSIRE,DAM,CDAM,SIREOFDAM,CSIREOFDAM,DAMOFDAM,CDAMOFDAM,DAMTATT,DAMYOF,DDAMTATT)
//             VALUES (?,?,?,?,?,?,?,?,?,?,?)";
//                 $paramType = "sssssssssis";
                
//                 $paramArray1 = array(
//                     $sire,
//                     $csire,
//                     $dam,
//                     $cdam,
//                     $sireofdam,
//                     $csireofdam,
//                     $damofdam,
//                     $cdamofdam,
//                     $damtatt,
//                     $damyof,
//                     $ddamtatt
//                 );
//                 $insertId = $db->insert($sqlInsertDamsire, $paramType, $paramArray1);
                
//                 if (! empty($insertId)) {
//                     echo "success";
//                     echo "CSV Data Imported into the Database";
//                 } else {
//                     echo "error";
//                     echo "Problem in Importing CSV Data";
//                 }
                
//                 //echo gettype((int) $damsire_ID[ID]);
//                 $damsire_ID=(int)getLastDamsireID+1;
//                 echo "|newDamsire_ID--".$damsire_ID;
//             }
            
//             //--------
            
//             $tattoo = "";
//             if (isset($column[0])) {
//                 $tattoo = mysqli_real_escape_string($conn, $column[0]);
//             }
//             $hip = "";
//             if (isset($column[1])) {
//                 $hip = mysqli_real_escape_string($conn, $column[1]);
//             }
//             $horse = "";
//             if (isset($column[2])) {
//                 $horse = mysqli_real_escape_string($conn, $column[2]);
//             }
//             $chorse = "";
//             if (isset($column[3])) {
//                 $chorse = mysqli_real_escape_string($conn, $column[3]);
//             }
//             $sex = "";
//             if (isset($column[4])) {
//                 $sex = mysqli_real_escape_string($conn, $column[4]);
//             }
//             $type = "";
//             if (isset($column[5])) {
//                 $type = mysqli_real_escape_string($conn, $column[5]);
//             }
//             $color = "";
//             if (isset($column[6])) {
//                 $color = mysqli_real_escape_string($conn, $column[6]);
//             }
//             $gait = "";
//             if (isset($column[7])) {
//                 $gait = mysqli_real_escape_string($conn, $column[7]);
//             }
//             $price = 0;
//             if ($column[8] != "" and isset($column[8])) {
//                 $price = mysqli_real_escape_string($conn, $column[8]);
//             }
//             $salecode = "";
//             if (isset($column[9])) {
//                 $salecode = mysqli_real_escape_string($conn, $column[9]);
//             }
//             $saledate = 0;
//             if ($column[10] != "" and isset($column[10])) {
//                 $saledate = mysqli_real_escape_string($conn, $column[10]);
//                 $date=date_create($saledate);
//                 $saledate = date_format($date,"Y-m-d");
//             }
//             $record = "";
//             if (isset($column[11])) {
//                 $record = mysqli_real_escape_string($conn, $column[11]);
//             }
//             $datefoal = "0000-00-00";
//             if ($column[12] != "" and isset($column[12])) {
//                 $datefoal = mysqli_real_escape_string($conn, $column[12]);
//                 $date=date_create($datefoal);
//                 $datefoal = date_format($date,"Y-m-d");
//             }
//             $bredto = "0000-00-00";
//             if (isset($column[13])) {
//                 $bredto = mysqli_real_escape_string($conn, $column[13]);
//             }
//             $lastbred = "0000-00-00";
//             if ($column[14] != "" and isset($column[14])) {
//                 $lastbred = mysqli_real_escape_string($conn, $column[14]);
//                 $date=date_create($lastbred);
//                 $lastbred = date_format($date,"Y-m-d");
//                 if ($lastbred == "") {
//                     $lastbred="0000-00-00";
//                 }
//             }
//             $sbcity = "";
//             if (isset($column[15])) {
//                 $sbcity = mysqli_real_escape_string($conn, $column[15]);
//             }
//             $sbstate = "";
//             if (isset($column[16])) {
//                 $sbstate = mysqli_real_escape_string($conn, $column[16]);
//             }
//             $sbcountry = "";
//             if (isset($column[17])) {
//                 $sbcountry = mysqli_real_escape_string($conn, $column[17]);
//             }
//             $purfname = "";
//             if (isset($column[18])) {
//                 $purfname = mysqli_real_escape_string($conn, $column[18]);
//             }
//             $purlname = "";
//             if (isset($column[19])) {
//                 $purlname = mysqli_real_escape_string($conn, $column[19]);
//             }
//             $conslname = "";
//             if (isset($column[20])) {
//                 $conslname = mysqli_real_escape_string($conn, $column[20]);
//             }
//             $consno = "";
//             if (isset($column[21])) {
//                 $consno = mysqli_real_escape_string($conn, $column[21]);
//             }
//             $pemcode = "";
//             if (isset($column[22])) {
//                 $pemcode = mysqli_real_escape_string($conn, $column[22]);
//             }
//             $age = 0;
//             if ($column[23] != "" and isset($column[23])) {
//                 $age = mysqli_real_escape_string($conn, $column[23]);
//             }
//             $saletype = "";
//             if (isset($column[24])) {
//                 $saletype = mysqli_real_escape_string($conn, $column[24]);
//             }
//             $et = "";
//             if (isset($column[25])) {
//                 $et = mysqli_real_escape_string($conn, $column[25]);
//             }
//             $hipnum = 0;
//             if ($column[26] != "" and isset($column[26])) {
//                 $hipnum = mysqli_real_escape_string($conn, $column[26]);
//             }
//             $day = 0;
//             if ($column[27] != "" and isset($column[27])) {
//                 $day = mysqli_real_escape_string($conn, $column[27]);
//             }
//             $elig = "";
//             if (isset($column[28])) {
//                 $elig = mysqli_real_escape_string($conn, $column[28]);
//             }
//             $rating = "";
//             if (isset($column[29])) {
//                 $rating = mysqli_real_escape_string($conn, $column[29]);
//             }
//             $url = "";
//             if (isset($column[30])) {
//                 $url = mysqli_real_escape_string($conn, $column[30]);
//             }
            
//             echo '<br>';
//             echo $tattoo.'|'.
//             $hip.'|'.
//             $horse.'|'.
//             $chorse.'|'.
//             $sex.'|'.
//             $type.'|'.
//             $color.'|'.
//             $gait.'|'.
//             $price.'|'.
//             $salecode.'|'.
//             $saledate.'|'.
//             $record.'|'.
//             $datefoal.'|'.
//             $bredto.'|'.
//             $lastbred.'|'.
//             $sbcity.'|'.
//             $sbstate.'|'.
//             $sbcountry.'|'.
//             $purfname.'|'.
//             $purlname.'|'.
//             $conslname.'|'.
//             $consno.'|'.
//             $pemcode.'|'.
//             $age.'|'.
//             $saletype.'|'.
//             $et.'|'.
//             $hipnum.'|'.
//             $day.'|'.
//             $elig.'|'.
//             $rating.'|'.
//             $url.'|'.
//             $damsire_ID;
//             echo '<br>';
//             echo gettype($saledate);
//             $sqlInsert = "INSERT into sales
//             (TATTOO, HIP,HORSE,CHORSE,SEX,TYPE,COLOR,GAIT,PRICE,SALECODE,SALEDATE,RECORD,DATEFOAL,
//             BREDTO,LASTBRED,SBCITY,SBSTATE,SBCOUNTRY,PURFNAME,PURLNAME,CONSLNAME,CONSNO,PEMCODE,
//             AGE,SALETYPE,ET,HIPNUM,DAY,ELIG,RATING,URL,DAMSIRE_ID)
//             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
//             $paramType = "ssssssssdssssssssssssssissiisssi";
//             $paramArray = array(
//                 $tattoo,
//                 $hip,
//                 $horse,
//                 $chorse,
//                 $sex,
//                 $type,
//                 $color,
//                 $gait,
//                 $price,
//                 $salecode,
//                 $saledate,
//                 $record,
//                 $datefoal,
//                 $bredto,
//                 $lastbred,
//                 $sbcity,
//                 $sbstate,
//                 $sbcountry,
//                 $purfname,
//                 $purlname,
//                 $conslname,
//                 $consno,
//                 $pemcode,
//                 $age,
//                 $saletype,
//                 $et,
//                 $hipnum,
//                 $day,
//                 $elig,
//                 $rating,
//                 $url,
//                 $damsire_ID
//             );
            
//             $insertId = $db->insert($sqlInsert, $paramType, $paramArray);
//             print_r($conn->error_list);
//             if (! empty($insertId)) {
//                 echo "success";
//                 echo "CSV Data Imported into the Database";
//             } else {
//                 echo "error";
//                 echo "Problem in Importing CSV Data";
//             }
//         }
//     }
// }
?>