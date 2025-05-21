<?php
// Set session timeout duration (in seconds)
$timeout_duration = 1800; // 30 minutes

// Get the current page URL
$current_url = urlencode($_SERVER['REQUEST_URI']); // Encode it for use in URL

// Check if the user is logged in
if (!isset($_SESSION["UserName"])) {
    header("Location: login.php?redirect=$current_url");
    exit();
}

// Session timeout logic
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=true&redirect=$current_url");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time(); // Update last activity timestamp