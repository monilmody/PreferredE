<?php
// Test Cognito Configuration
require_once("config.php");

echo "<h3>Cognito Configuration Test</h3>";
echo "Region: " . COGNITO_REGION . "<br>";
echo "User Pool ID: " . COGNITO_USER_POOL_ID . "<br>";
echo "App Client ID: " . COGNITO_APP_CLIENT_ID . "<br>";

// Test if we can make a curl request
if (function_exists('curl_version')) {
    echo "CURL: Enabled<br>";
} else {
    echo "CURL: NOT Enabled - You need to enable curl in PHP<br>";
}

// Test URL
$test_url = "https://cognito-idp." . COGNITO_REGION . ".amazonaws.com/";
echo "Test URL: " . $test_url . "<br>";

echo '<br><a href="login.php">Go to Login Page</a>';