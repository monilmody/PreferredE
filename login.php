<?php
session_start();
ob_start();
include("./header.php");
echo '<br>';
echo '<br>';
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("config.php"); // This now includes Cognito settings
require_once("cognito.php"); // Our simple Cognito helper

// If user is already logged in, redirect
if(isset($_SESSION['UserName'])) {
    $redirect_url = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : "index.php";
    header("Location: $redirect_url");
    die();
}

// Handle form submission
$errors = [];
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
			
			// Set session variables
			$_SESSION["UserActive"] = 'Y';
			$_SESSION["UserName"] = $username;
			$_SESSION["UserEmail"] = $username;
			$_SESSION["UserRole"] = $dbUserDetails["USERROLE"] ?? 'user';
			
			setcookie("LoggedInUser", $username, time() + 3600, "/");
			
			$redirect_url = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : "index.php";
			header("Location: $redirect_url");
			exit();
		} else {
			$errors[] = $authResult['error'];
		}
	}
}
ob_end_flush();
?>

<style type="text/css" media="screen">
    @import url("style/css/style.css");
    
    .error-message {
        color: red;
        text-align: center;
        margin-bottom: 10px;
        font-weight: bold;
    }
</style>

<body>
    <div class="main-block">
        <?php if(!empty($errors)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($errors[0]); ?>
            </div>
        <?php endif; ?>
        
        <form name="login" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?><?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="post">
            <hr>
            <hr>
            <div style="text-align:center;">
                <input type="text" name="username" id="username" placeholder="Email or Username" required 
                       style="padding: 10px; width: 80%; max-width: 300px;" />
            </div>
            <div style="text-align:center; margin-top: 15px;">
                <input type="password" name="password" id="password" placeholder="Password" required 
                       style="padding: 10px; width: 80%; max-width: 300px;" />
            </div>
            <hr>

            <?php if(isset($_GET['redirect'])): ?>
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect']); ?>">
            <?php endif; ?>
            
            <div class="btn-block" style="text-align:center;">
                <button type="submit" style="padding: 10px 30px; font-size: 16px;">Login</button>
            </div>
            
            <div style="text-align: center; margin-top: 15px;">
                <a href="forgot_password.php" style="color: #0066c0; text-decoration: none;">
                    Forgot Password?
                </a>
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
    </script>
</body>