<?php

// Set session timeout duration (in seconds)
$timeout_duration = 1800; // 30 minutes

// Check if the user is logged in
if (!isset($_SESSION["UserName"])) {
    header("Location: login.php");
    exit();
}

// Session timeout logic
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();     // Unset $_SESSION
    session_destroy();   // Destroy session data
    header("Location: login.php?timeout=true");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity timestamp