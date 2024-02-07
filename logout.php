<?php
session_start();
require_once("config.php");

//Log the user out
//session_destroy();
echo 'aaaa';
echo $_SESSION["UserName"];
$_SESSION["UserName"]= "";
echo $_SESSION["UserName"];
// if(isUserLoggedIn())
// {
//     session_destroy();
// }
header("Location:index.php");
die();
?>