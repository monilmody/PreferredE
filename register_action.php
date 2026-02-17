<?php
// register_action.php
session_start();
require_once("config.php");
require_once("cognito.php");

// Get form data
$username = trim($_POST['user'] ?? '');
$first_name = trim($_POST['fname'] ?? '');
$last_name = trim($_POST['lname'] ?? '');
$password = $_POST['password'] ?? '';
$user_role = $_POST['userrole'] ?? 'N';

// Validation
$errors = [];
if (empty($username)) $errors[] = "Email is required";
if (empty($first_name)) $errors[] = "First name is required";
if (empty($last_name)) $errors[] = "Last name is required";
if (empty($password)) $errors[] = "Password is required";
if ($user_role === 'N') $errors[] = "Please select user role";

// Password validation (Cognito requirements)
if (strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters";
}
if (!preg_match('/[A-Z]/', $password)) {
    $errors[] = "Password must contain at least one uppercase letter";
}
if (!preg_match('/[a-z]/', $password)) {
    $errors[] = "Password must contain at least one lowercase letter";
}
if (!preg_match('/[0-9]/', $password)) {
    $errors[] = "Password must contain at least one number";
}
if (!preg_match('/[\W_]/', $password)) { // Special character
    $errors[] = "Password must contain at least one special character";
}

if (empty($errors)) {
    error_log("Attempting to register user: $username");
    
    // Register in Cognito (NO AUTO-CONFIRM)
    $cognito_result = CognitoAuth::register($username, $password, $first_name, $last_name, $user_role);
    
    error_log("Cognito registration result: " . print_r($cognito_result, true));
    
    if ($cognito_result['success']) {
        // Store in YOUR database
        require_once("db-settings.php");
        
        // Insert into users table with 'N' for cognito_verified = 0 (not verified)
        $sql = "INSERT INTO users (USERNAME, FNAME, LNAME, EMAIL, PASSWORD, ACTIVE, USERROLE, cognito_verified) 
                VALUES (?, ?, ?, ?, ?, 'Y', ?, 0)";
        
        $stmt = $mysqli->prepare($sql);
        
        if ($stmt) {
            $db_password = $password; // Your existing password storage
            
            $stmt->bind_param("ssssss", $username, $first_name, $last_name, $username, $db_password, $user_role);
            
            if ($stmt->execute()) {
                // Store email in session for verification page
                $_SESSION['verify_email'] = $username;
                $_SESSION['verify_name'] = $first_name;
                
                // Redirect to verification page
                header("Location: verify.php");
                exit();
            } else {
                $errors[] = "Database error: " . $stmt->error;
                error_log("Database error: " . $stmt->error);
            }
        } else {
            $errors[] = "Database preparation failed";
            error_log("Database preparation failed: " . $mysqli->error);
        }
    } else {
        $errors[] = $cognito_result['error'];
        error_log("Cognito error: " . $cognito_result['error']);
    }
}

// If errors, show them
echo "<h2>Registration Failed</h2>";
echo "<ul>";
foreach ($errors as $error) {
    echo "<li style='color: red;'>" . htmlspecialchars($error) . "</li>";
}
echo "</ul>";
echo "<p><a href='registration.php'>Go back</a></p>";

$_SESSION['registration_errors'] = $errors;
$_SESSION['form_data'] = [
    'user' => $username,
    'fname' => $first_name,
    'lname' => $last_name,
    'userrole' => $user_role
];