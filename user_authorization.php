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

$resultFound = getUserData();

if(!empty($_POST)) {
    $salecode = trim($_POST["salecode"]);
}
?>
<br>

<div style= "margin:5px 30px 30px 30px;">
<h1 style="text-align:center;color:#D98880;">User Authorization</h1>

<hr>
<div style="max-height: calc(96.2vh - 96.2px);overflow:auto;">
	<div class="table" style="width: device-width;">
          <div class="row header blue" style="line-height: 25px;font-size: 12px;position: sticky;top: 0;">
        	  <div class="cell" style="width: device-width;">
                No.
              </div>
              <div class="cell" style="width: device-width;">
                USERID
              </div>
              <div class="cell" style="width: device-width;">
                USERNAME
              </div>
              <div class="cell" style="width: device-width;">
                FIRST NAME
              </div>
              <div class="cell" style="width: device-width;">
                LAST NAME
              </div>
              <div class="cell" style="width: device-width;">
                ACTIVE
              </div>
              <div class="cell" style="width: device-width;">
                USER ROLE
              </div>
              <div class="cell" style="width: device-width;">
                ACTION
              </div>
          </div>
          
          <?php
            setlocale(LC_MONETARY,"en_US");
            $number =0;  
            foreach($resultFound as $row) {
                $number = $number+1;
                $elementCount = 0;
                echo "<div class='row'>";
                echo "<div class='cell'>".$number."</div>";
                foreach($row as $elements) {
                    $elementCount =$elementCount+1;
                    echo "<div class='cell'>".$elements."</div>";
                }
                //echo "<div class='cell'><a href='javascript:deleteSaleData(`".$row[Salecode]."`);'>Delete</a></div>";
                //echo "</div>";
            
          
          ?>
          <form name="myform" action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="POST">
            <input type="hidden" name="USER_ID_UNAUTH" id="USER_ID_UNAUTH" value="<?php echo $row[USER_ID];?>" />
            <button type="submit" >Unauthorize</button>
         </form>
         <form name="myform" action="<?php echo $_SERVER['$PHP_SELF']; ?>" method="POST">
            <input type="hidden" name="USER_ID_AUTH" id="USER_ID_AUTH" value="<?php echo $row[USER_ID];?>" />
            <button type="submit">Authorize</button>
         </form>
          </div>
          <?php }?>
    </div>
 </div>
</div>
<br>


<script>
<?php if(!empty($_POST)) {
    if ($_POST['USER_ID_UNAUTH'] != "") {?>
	//alert('<?php echo $_POST['USER_ID_UNAUTH'];?>');
	
	var result = "";
    if (confirm("Are you sure, you want to unathorize user -"+'<?php echo $_POST['USER_ID_UNAUTH'];?>'+"?")) {
      result = "You pressed OK!";
      result = "<?php echo unauthorizeUser($_POST['USER_ID_UNAUTH']); ?>";
    } else {
      result = "You pressed Cancel!";
    }alert(result);
    window.location.href="user_authorization.php";
    
<?php } elseif ($_POST['USER_ID_AUTH'] != "") {?>
    //alert('<?php echo $_POST['USER_ID_AUTH'];?>');
    var result = "";
    if (confirm("Are you sure, you want to authorize user -"+'<?php echo $_POST['USER_ID_UNAUTH'];?>'+"?")) {
      result = "You pressed OK!";
      result = "<?php echo authorizeUser($_POST['USER_ID_AUTH']); ?>";
    } else {
      result = "You pressed Cancel!";
    }alert(result);
    window.location.href="user_authorization.php";
<?php 
    }
}
?>
</script>


