<?php
// register_action.php
session_start();
require_once("config.php");
require_once("cognito.php");
require_once("db-settings.php");

// Get form data
$username = trim($_POST['user'] ?? '');
$first_name = trim($_POST['fname'] ?? '');
$last_name = trim($_POST['lname'] ?? '');
$password = $_POST['password'] ?? '';
$user_role = $_POST['userrole'] ?? 'N';

// Validation
$errors = [];

// Email validation
if (empty($username)) {
    $errors[] = "Email is required";
} elseif (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Please enter a valid email address";
}

// Name validation
if (empty($first_name)) $errors[] = "First name is required";
if (empty($last_name)) $errors[] = "Last name is required";

// Password validation
if (empty($password)) {
    $errors[] = "Password is required";
} else {
    if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters";
    if (!preg_match('/[A-Z]/', $password)) $errors[] = "Password must contain at least one uppercase letter";
    if (!preg_match('/[a-z]/', $password)) $errors[] = "Password must contain at least one lowercase letter";
    if (!preg_match('/[0-9]/', $password)) $errors[] = "Password must contain at least one number";
    if (!preg_match('/[\W_]/', $password)) $errors[] = "Password must contain at least one special character";
}

// Role validation
if ($user_role === 'N') $errors[] = "Please select a user role";

// Check if user already exists in database
if (empty($errors)) {
    $check_stmt = $mysqli->prepare("SELECT id FROM users WHERE EMAIL = ?");
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $errors[] = "Email already registered. Please <a href='login.php'>login</a>.";
    }
    $check_stmt->close();
}

// If no validation errors, proceed with registration
if (empty($errors)) {
    // Register in Cognito (NO auto-confirm - requires email verification)
    $cognito_result = CognitoAuth::register($username, $password, $first_name, $last_name, $user_role);

    if ($cognito_result['success']) {
        // Store in database with EMAIL_VERIFIED = 0 (unverified)

        // First, ensure the database has the required columns
        $check_column = $mysqli->query("SHOW COLUMNS FROM users LIKE 'EMAIL_VERIFIED'");
        if ($check_column->num_rows == 0) {
            $mysqli->query("ALTER TABLE users ADD COLUMN EMAIL_VERIFIED TINYINT DEFAULT 0 AFTER PASSWORD");
        }

        $check_sub = $mysqli->query("SHOW COLUMNS FROM users LIKE 'COGNITO_SUB'");
        if ($check_sub->num_rows == 0) {
            $mysqli->query("ALTER TABLE users ADD COLUMN COGNITO_SUB VARCHAR(255) NULL AFTER EMAIL_VERIFIED");
        }

        // Insert user with unverified status
        $sql = "INSERT INTO users (USERNAME, FNAME, LNAME, EMAIL, PASSWORD, ACTIVE, USERROLE, EMAIL_VERIFIED, COGNITO_SUB) 
                VALUES (?, ?, ?, ?, ?, 'Y', ?, 0, ?)";

        $stmt = $mysqli->prepare($sql);

        if ($stmt) {
            $cognito_sub = $cognito_result['user_sub'] ?? '';
            $stmt->bind_param("ssssssss", $username, $first_name, $last_name, $username, $password, $user_role, $cognito_sub);

            if ($stmt->execute()) {
                // Success - store email in session and redirect to verification page
                $_SESSION['verification_email'] = $username;
                $_SESSION['verification_message'] = "Registration successful! We've sent a verification code to {$username}. Please check your email and enter the code below.";

                // Redirect to verification page
                header("Location: verify.php");
                exit();
            } else {
                $errors[] = "Database error: " . $stmt->error;
                error_log("Database error: " . $stmt->error);

                // Try to clean up Cognito user if database insert fails
                try {
                    require_once 'vendor/autoload.php';
                    $client = new Aws\CognitoIdentityProvider\CognitoIdentityProviderClient([
                        'region' => COGNITO_REGION,
                        'version' => 'latest'
                    ]);

                    $client->adminDeleteUser([
                        'UserPoolId' => COGNITO_USER_POOL_ID,
                        'Username' => $username
                    ]);
                } catch (Exception $cleanupError) {
                    error_log("Failed to cleanup Cognito user: " . $cleanupError->getMessage());
                }
            }
        } else {
            $errors[] = "Database preparation failed";
            error_log("Database preparation failed: " . $mysqli->error);
        }
    } else {
        // Handle Cognito errors
        if ($cognito_result['error'] === 'UsernameExistsException') {
            $errors[] = "This email is already registered. Please <a href='login.php'>login</a> or use a different email.";
        } elseif ($cognito_result['error'] === 'InvalidPasswordException') {
            $errors[] = "Password does not meet requirements: " . ($cognito_result['message'] ?? '');
        } elseif ($cognito_result['error'] === 'InvalidParameterException') {
            $errors[] = "Invalid email format or user information. Please check and try again.";
        } else {
            $errors[] = "Registration failed: " . ($cognito_result['message'] ?? $cognito_result['error']);
        }
        error_log("Cognito registration error: " . ($cognito_result['message'] ?? $cognito_result['error']));
    }
}

// If there are errors, store in session and redirect back to registration page
if (!empty($errors)) {
    $_SESSION['registration_errors'] = $errors;
    $_SESSION['form_data'] = [
        'user' => $username,
        'fname' => $first_name,
        'lname' => $last_name,
        'userrole' => $user_role
    ];
    header("Location: register.php");
    exit();
}