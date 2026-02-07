<?php
// register_action.php
session_start();
require_once("config.php");
require_once("cognito.php"); // Use your existing file

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

if (empty($errors)) {
    // 1. Register in Cognito using your existing class
    $cognito_result = CognitoAuth::register($username, $password, $first_name, $last_name, $user_role);
    
    if ($cognito_result['success']) {
        // 2. Store in YOUR database
        require_once("db-settings.php");
        
        // Insert into users table
        $sql = "INSERT INTO users (USERNAME, FNAME, LNAME, EMAIL, PASSWORD, ACTIVE, USERROLE) 
                VALUES (?, ?, ?, ?, ?, 'Y', ?)";
        
        $stmt = $mysqli->prepare($sql);
        
        // Use plain password (as your old system does)
        $db_password = $password;
        
        $stmt->bind_param("ssssss", $username, $first_name, $last_name, $username, $db_password, $user_role);
        
        if ($stmt->execute()) {
            // Success!
            $_SESSION['registration_success'] = "Registration successful! You can now login.";
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
    } else {
        $errors[] = $cognito_result['error'];
    }
}

// If errors, go back to registration page
$_SESSION['registration_errors'] = $errors;
$_SESSION['form_data'] = [
    'user' => $username,
    'fname' => $first_name,
    'lname' => $last_name,
    'userrole' => $user_role
];
header("Location: registration.php");
exit();