<?php


ob_start();
//error reporting and warning display.
//error_reporting(E_ALL);
ini_set('display_errors', 'off');

require_once("db-settings.php"); //Require DB connection
require_once("functions.php"); // database and other functions are written in this file

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
