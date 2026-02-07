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
    // Debug: Show what we're trying
    error_log("Attempting to register user: $username");
    
    // 1. Register in Cognito
    $cognito_result = CognitoAuth::register($username, $password, $first_name, $last_name, $user_role);
    
    // Debug: Show result
    error_log("Cognito registration result: " . print_r($cognito_result, true));
    
    if ($cognito_result['success']) {
        // 2. Store in YOUR database
        require_once("db-settings.php");
        
        // Insert into users table
        $sql = "INSERT INTO users (USERNAME, FNAME, LNAME, EMAIL, PASSWORD, ACTIVE, USERROLE) 
                VALUES (?, ?, ?, ?, ?, 'Y', ?)";
        
        $stmt = $mysqli->prepare($sql);
        
        if ($stmt) {
            // Use plain password (as your old system does)
            $db_password = $password;
            
            $stmt->bind_param("ssssss", $username, $first_name, $last_name, $username, $db_password, $user_role);
            
            if ($stmt->execute()) {
                // Success!
                $_SESSION['registration_success'] = "Registration successful! You can now login.";
                
                // Auto-login the user immediately
                $_SESSION['UserActive'] = 'Y';
                $_SESSION['UserName'] = $username;
                $_SESSION['UserEmail'] = $username;
                $_SESSION['UserRole'] = $user_role;
                
                setcookie("LoggedInUser", $username, time() + 3600, "/");
                
                // Redirect to home page
                header("Location: index.php");
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

// If errors, show them on same page for debugging
echo "<h2>Registration Failed</h2>";
echo "<ul>";
foreach ($errors as $error) {
    echo "<li style='color: red;'>" . htmlspecialchars($error) . "</li>";
}
echo "</ul>";
echo "<p><a href='registration.php'>Go back</a></p>";

// Also store in session for redirect (if you want to redirect back)
$_SESSION['registration_errors'] = $errors;
$_SESSION['form_data'] = [
    'user' => $username,
    'fname' => $first_name,
    'lname' => $last_name,
    'userrole' => $user_role
];