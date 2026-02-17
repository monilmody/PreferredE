<?php
session_start();
ob_start();
include("./header.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("config.php"); // This now includes Cognito settings
require_once("cognito.php"); // Our simple Cognito helper
require_once("db-settings.php"); // Add this for database queries

// If user is already logged in, redirect
if(isset($_SESSION['UserName'])) {
    $redirect_url = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : "index.php";
    header("Location: $redirect_url");
    die();
}

// Function to fetch user details from database
function fetchUserDetails($username) {
    global $mysqli;
    
    $stmt = $mysqli->prepare("SELECT USERROLE, EMAIL_VERIFIED FROM users WHERE EMAIL = ? OR USERNAME = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return ['USERROLE' => 'user', 'EMAIL_VERIFIED' => 0];
}

// Handle form submission
$errors = [];
$form_data = $_POST ?? [];

if(!empty($_POST)) {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // Basic validation
    if(empty($username)) {
        $errors[] = "Enter Username/Email";
    }
    if(empty($password)) {
        $errors[] = "Enter Password";
    }
    
    if(count($errors) == 0) {
        require_once("cognito.php");
        $authResult = CognitoAuth::authenticate($username, $password);
        
        if($authResult['success']) {
            // Get user details from your existing database
            $dbUserDetails = fetchUserDetails($username);
            
            // Check if email is verified
            if (isset($dbUserDetails['EMAIL_VERIFIED']) && $dbUserDetails['EMAIL_VERIFIED'] == 0) {
                // User exists but email not verified
                $errors[] = "Please verify your email before logging in. 
                    <a href='verify.php?email=" . urlencode($username) . "' style='color: #c53030; font-weight: bold; text-decoration: underline;'>Click here to verify</a>";
            } else {
                // Set session variables
                $_SESSION["UserActive"] = 'Y';
                $_SESSION["UserName"] = $username;
                $_SESSION["UserEmail"] = $username;
                $_SESSION["UserRole"] = $dbUserDetails["USERROLE"] ?? 'user';
                
                setcookie("LoggedInUser", $username, time() + 3600, "/");
                
                $redirect_url = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : "index.php";
                header("Location: $redirect_url");
                exit();
            }
        } else {
            // Check for specific Cognito errors
            if ($authResult['error'] === 'UserNotConfirmedException') {
                $errors[] = "Please verify your email before logging in. 
                    <a href='verify.php?email=" . urlencode($username) . "' style='color: #c53030; font-weight: bold; text-decoration: underline;'>Click here to verify</a>";
            } else {
                $errors[] = $authResult['error'] ?? "Invalid username or password";
            }
        }
    }
}
ob_end_flush();
?>

<style>
/* Custom Styles for Login Page - Matching Registration Page */
.login-container {
    max-width: 500px;
    margin: 80px auto 40px;
    padding: 30px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.1);
    border: 1px solid #e0e0e0;
}

.login-header {
    text-align: center;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #2E4053;
}

.login-header h1 {
    color: #2E4053;
    font-size: 28px;
    margin-bottom: 10px;
    font-weight: 600;
}

.login-header p {
    color: #666;
    font-size: 16px;
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #444;
    font-size: 15px;
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 15px;
    transition: all 0.3s;
    box-sizing: border-box;
}

.form-control:focus {
    border-color: #2E4053;
    box-shadow: 0 0 0 3px rgba(46, 64, 83, 0.1);
    outline: none;
}

.error-alert {
    background-color: #fff5f5;
    border: 1px solid #fed7d7;
    color: #c53030;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 25px;
    font-weight: 500;
}

.error-alert a {
    color: #c53030;
    font-weight: bold;
    text-decoration: underline;
}

.error-alert a:hover {
    text-decoration: none;
}

.submit-btn {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #2E4053 0%, #3a506b 100%);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 10px;
}

.submit-btn:hover {
    background: linear-gradient(135deg, #3a506b 0%, #2E4053 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(46, 64, 83, 0.2);
}

.submit-btn:active {
    transform: translateY(0);
}

.login-links {
    text-align: center;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    color: #666;
}

.login-links a {
    color: #2E4053;
    font-weight: 600;
    text-decoration: none;
    display: block;
    margin: 8px 0;
}

.login-links a:hover {
    text-decoration: underline;
}

.form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    font-size: 14px;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 8px;
}

.remember-me input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: #2E4053;
}

.forgot-password {
    color: #2E4053;
    font-weight: 500;
}

/* Info alert for verification messages */
.info-alert {
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 25px;
    font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
    .login-container {
        margin: 60px 20px 30px;
        padding: 20px;
    }
    
    .login-header h1 {
        font-size: 24px;
    }
    
    .form-options {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
}
</style>

<div class="login-container">
    <div class="login-header">
        <h1>Welcome Back</h1>
        <p>Sign in to your Preferred Equine account</p>
    </div>

    <!-- Show any session messages (like from registration) -->
    <?php if (isset($_SESSION['registration_success'])): ?>
        <div class="info-alert">
            <?php echo $_SESSION['registration_success']; ?>
        </div>
        <?php unset($_SESSION['registration_success']); ?>
    <?php endif; ?>

    <!-- Show login errors if any -->
    <?php if (!empty($errors)): ?>
        <div class="error-alert">
            <?php 
            // Display errors - handle potential HTML links
            foreach ($errors as $error) {
                echo $error . "<br>";
            }
            ?>
        </div>
    <?php endif; ?>

    <!-- Show verification reminder if email is in session -->
    <?php if (isset($_SESSION['verification_email'])): ?>
        <div class="info-alert">
            <i class="fa fa-info-circle"></i> 
            Please verify your email (<?php echo htmlspecialchars($_SESSION['verification_email']); ?>) before logging in.
            <a href="verify.php" style="color: #0c5460; font-weight: bold; text-decoration: underline;">Click here to verify</a>
        </div>
    <?php endif; ?>

    <form name="login" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?><?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="post">
        <div class="form-group">
            <label for="username">Email Address *</label>
            <input type="text" class="form-control" 
                   id="username" 
                   name="username" 
                   placeholder="Enter your email address" 
                   value="<?php echo htmlspecialchars($form_data['username'] ?? ''); ?>" 
                   required 
                   autofocus>
        </div>

        <div class="form-group">
            <label for="password">Password *</label>
            <input type="password" class="form-control" 
                   id="password" 
                   name="password" 
                   placeholder="Enter your password" 
                   required>
        </div>

        <div class="form-options">
            <label class="remember-me">
                <input type="checkbox" name="remember" id="remember">
                Remember me
            </label>
            <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
        </div>

        <?php if(isset($_GET['redirect'])): ?>
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect']); ?>">
        <?php endif; ?>

        <button type="submit" class="submit-btn">Sign In</button>

        <div class="login-links">
            <p>Don't have an account? <a href="registration.php">Register here</a></p>
            <p>Didn't receive verification email? <a href="verify.php?resend=1&email=<?php echo urlencode($form_data['username'] ?? ''); ?>">Resend code</a></p>
            <p><a href="index.php">← Back to Home</a></p>
        </div>
    </form>
</div>

<script>
// Focus on username field when page loads
document.getElementById("username").focus();

// Submit form when Enter is pressed in password field
document.getElementById("password").addEventListener("keypress", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();
        document.forms["login"].submit();
    }
});

// Add a subtle animation to the login button on hover
const submitBtn = document.querySelector('.submit-btn');
if (submitBtn) {
    submitBtn.addEventListener('mouseenter', function() {
        this.style.transition = 'all 0.3s ease';
    });

    submitBtn.addEventListener('mouseleave', function() {
        this.style.transition = 'all 0.3s ease';
    });
}

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.info-alert, .error-alert');
    alerts.forEach(function(alert) {
        alert.style.transition = 'opacity 1s';
        alert.style.opacity = '0';
        setTimeout(function() {
            alert.style.display = 'none';
        }, 1000);
    });
}, 5000);
</script>