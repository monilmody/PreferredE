<?php

//Development Database Information
	$db_host = "preferred-equine-database.cdq66kiey6co.us-east-1.rds.amazonaws.com"; //Host address (most likely localhost)
	$db_name = "horse"; //Name of Database
	$db_user = "preferredequine"; //Name of database user
	$db_pass = "914MoniMaker77$$"; //Password for database user
	$db_table_prefix = ""; // if the table prefix exists use this variable as a global
	

//following variable declaration
	GLOBAL $errors;
	GLOBAL $successes;

	$errors = array();
	$successes = array();

// 1. Create a database connection
//$connection = mysql_connect(DB_HOST,DB_USER,DB_PSWD);
// $connection = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
// if (!$connection) {
// die("Database connection failed: " . mysqli_connect_error());
// }

/* Create a new mysqli object with database connection parameters */

	$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
	GLOBAL $mysqli;

	if(mysqli_connect_errno()) {
  //display the reason for mysql connection error.
	 echo "Connection Failed1: " . mysqli_connect_errno();
    exit();
	}else{
     //echo "Connection Successful";
	}
?>

