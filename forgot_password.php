<?php
session_start();
include("./header.php");

require_once("config.php");
require_once("cognito.php");

$message = '';
$message_type = ''; // success, error
$show_reset_form = false;
$email = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['send_code'])) {
        $email = trim($_POST['email']);
        
        if (empty($email)) {
            $message = "Please enter your email address";
            $message_type = "error";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Please enter a valid email address";
            $message_type = "error";
        } else {
            try {
                require_once 'vendor/autoload.php';
                
                $client = new Aws\CognitoIdentityProvider\CognitoIdentityProviderClient([
                    'region' => COGNITO_REGION,
                    'version' => 'latest'
                ]);
                
                // Send reset code
                $client->forgotPassword([
                    'ClientId' => COGNITO_APP_CLIENT_ID,
                    'Username' => $email
                ]);
                
                $_SESSION['reset_email'] = $email;
                $message = "Reset code sent to your email! Please check your inbox.";
                $message_type = "success";
                $show_reset_form = true;
                
            } catch (Exception $e) {
                $message = "Error: " . $e->getMessage();
                $message_type = "error";
            }
        }
    }
    
    if (isset($_POST['reset_password'])) {
        $email = $_SESSION['reset_email'] ?? '';
        $code = trim($_POST['code']);
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        $errors = [];
        
        if (empty($email)) {
            $errors[] = "Email session expired. Please start over.";
        }
        if (empty($code)) {
            $errors[] = "Reset code is required";
        }
        if (empty($new_password)) {
            $errors[] = "New password is required";
        }
        if ($new_password !== $confirm_password) {
            $errors[] = "Passwords do not match";
        }
        
        // Password validation
        if (strlen($new_password) < 8) {
            $errors[] = "Password must be at least 8 characters";
        }
        if (!preg_match('/[A-Z]/', $new_password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        if (!preg_match('/[a-z]/', $new_password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        if (!preg_match('/[0-9]/', $new_password)) {
            $errors[] = "Password must contain at least one number";
        }
        if (!preg_match('/[\W_]/', $new_password)) {
            $errors[] = "Password must contain at least one special character";
        }
        
        if (empty($errors)) {
            try {
                require_once 'vendor/autoload.php';
                
                $client = new Aws\CognitoIdentityProvider\CognitoIdentityProviderClient([
                    'region' => COGNITO_REGION,
                    'version' => 'latest'
                ]);
                
                // Confirm password reset
                $client->confirmForgotPassword([
                    'ClientId' => COGNITO_APP_CLIENT_ID,
                    'Username' => $email,
                    'ConfirmationCode' => $code,
                    'Password' => $new_password
                ]);
                
                // Also update password in your database
                require_once("db-settings.php");
                $update_stmt = $mysqli->prepare("UPDATE users SET PASSWORD = ? WHERE EMAIL = ?");
                $update_stmt->bind_param("ss", $new_password, $email);
                $update_stmt->execute();
                $update_stmt->close();
                
                // Clear session
                unset($_SESSION['reset_email']);
                
                $message = "Password reset successfully! You can now login with your new password.";
                $message_type = "success";
                $show_reset_form = false;
                
                // Redirect to login after 3 seconds
                echo '<script>
                    setTimeout(function() {
                        window.location.href = "login.php";
                    }, 3000);
                </script>';
                
            } catch (Exception $e) {
                $message = "Error: " . $e->getMessage();
                $message_type = "error";
                $show_reset_form = true;
            }
        } else {
            $message = implode("<br>", $errors);
            $message_type = "error";
            $show_reset_form = true;
        }
    }
}

// Get email from session if available
if (!$email && isset($_SESSION['reset_email'])) {
    $email = $_SESSION['reset_email'];
    $show_reset_form = true;
}
?>

<style>
/* Forgot Password Styles */
.forgot-container {
    max-width: 500px;
    margin: 100px auto 50px;
    padding: 40px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.1);
    border: 1px solid #e0e0e0;
}

.forgot-header {
    text-align: center;
    margin-bottom: 30px;
}

.forgot-header h1 {
    color: #2E4053;
    font-size: 28px;
    margin-bottom: 10px;
    font-weight: 600;
}

.forgot-header p {
    color: #666;
    font-size: 16px;
    line-height: 1.5;
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

.password-hint {
    font-size: 13px;
    color: #666;
    margin-top: 5px;
    margin-bottom: 15px;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 4px;
    border-left: 3px solid #2E4053;
}

.btn-primary {
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

.btn-primary:hover {
    background: linear-gradient(135deg, #3a506b 0%, #2E4053 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(46, 64, 83, 0.2);
}

.btn-secondary {
    width: 100%;
    padding: 14px;
    background: #6c757d;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin-top: 10px;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

.message-alert {
    padding: 15px 20px;
    border-radius: 6px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
}

.message-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.message-info {
    background-color: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.message-alert i {
    margin-right: 10px;
    font-size: 18px;
}

.login-link {
    text-align: center;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    color: #666;
}

.login-link a {
    color: #2E4053;
    font-weight: 600;
    text-decoration: none;
}

.login-link a:hover {
    text-decoration: underline;
}

.steps {
    display: flex;
    justify-content: space-between;
    margin: 30px 0;
    position: relative;
}

.steps:before {
    content: '';
    position: absolute;
    top: 20px;
    left: 10%;
    right: 10%;
    height: 2px;
    background: #e0e0e0;
    z-index: 1;
}

.step {
    text-align: center;
    position: relative;
    z-index: 2;
    flex: 1;
}

.step-number {
    width: 40px;
    height: 40px;
    background: #e0e0e0;
    color: #666;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-weight: bold;
    font-size: 18px;
}

.step.active .step-number {
    background: #2E4053;
    color: white;
}

.step-label {
    font-size: 14px;
    color: #666;
}

.step.active .step-label {
    color: #2E4053;
    font-weight: 600;
}

.code-input-group {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.code-input {
    flex: 1;
    text-align: center;
    font-size: 20px;
    letter-spacing: 5px;
    font-weight: bold;
}

@media (max-width: 768px) {
    .forgot-container {
        margin: 60px 20px 30px;
        padding: 25px;
    }
    
    .steps {
        flex-direction: column;
        gap: 20px;
    }
    
    .steps:before {
        display: none;
    }
}
</style>

<div class="forgot-container">
    <div class="forgot-header">
        <h1>Reset Your Password</h1>
        <p>Enter your email address and we'll send you a code to reset your password.</p>
    </div>

    <!-- Progress Steps -->
    <div class="steps">
        <div class="step <?php echo !$show_reset_form ? 'active' : ''; ?>">
            <div class="step-number">1</div>
            <div class="step-label">Enter Email</div>
        </div>
        <div class="step <?php echo $show_reset_form ? 'active' : ''; ?>">
            <div class="step-number">2</div>
            <div class="step-label">Reset Password</div>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="message-alert message-<?php echo $message_type; ?>">
            <i class="fa fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Step 1: Request Reset Code -->
    <?php if (!$show_reset_form): ?>
    <form method="POST" action="" id="requestForm">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" 
                   placeholder="Enter your email address" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        
        <button type="submit" name="send_code" class="btn-primary">
            <i class="fa fa-paper-plane"></i> Send Reset Code
        </button>
    </form>
    <?php endif; ?>

    <!-- Step 2: Reset Password Form -->
    <?php if ($show_reset_form): ?>
    <form method="POST" action="" id="resetForm">
        <div class="form-group">
            <label>Email Address</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($email); ?>" readonly>
            <small style="color: #666; font-size: 13px;">Reset code sent to this email</small>
        </div>
        
        <div class="form-group">
            <label for="code">Reset Code</label>
            <input type="text" class="form-control code-input" id="code" name="code" 
                   placeholder="Enter 6-digit code" required maxlength="6">
            <small style="color: #666; font-size: 13px;">Check your email for the reset code</small>
        </div>
        
        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" class="form-control" id="new_password" name="new_password" 
                   placeholder="Enter new password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                   placeholder="Confirm new password" required>
        </div>
        
        <div class="password-hint">
            <strong>Password Requirements:</strong> Minimum 8 characters with uppercase, lowercase, number, and special character
        </div>
        
        <button type="submit" name="reset_password" class="btn-primary">
            <i class="fa fa-key"></i> Reset Password
        </button>
        
        <button type="button" onclick="window.location.href='forgot_password.php'" class="btn-secondary">
            <i class="fa fa-redo"></i> Start Over
        </button>
    </form>
    <?php endif; ?>

    <div class="login-link">
        <p>Remember your password? <a href="login.php">Back to Login</a></p>
    </div>
</div>

<script>
// Password strength indicator for reset form
if (document.getElementById('new_password')) {
    document.getElementById('new_password').addEventListener('input', function() {
        const password = this.value;
        const confirm = document.getElementById('confirm_password').value;
        
        // Check if passwords match
        if (confirm && password !== confirm) {
            document.getElementById('confirm_password').style.borderColor = '#e74c3c';
        } else if (confirm) {
            document.getElementById('confirm_password').style.borderColor = '#27ae60';
        }
        
        // Password strength logic could be added here
    });
    
    document.getElementById('confirm_password').addEventListener('input', function() {
        const password = document.getElementById('new_password').value;
        const confirm = this.value;
        
        if (password !== confirm) {
            this.style.borderColor = '#e74c3c';
        } else {
            this.style.borderColor = '#27ae60';
        }
    });
}

// Auto-advance code input
if (document.getElementById('code')) {
    document.getElementById('code').addEventListener('input', function() {
        if (this.value.length === 6) {
            document.getElementById('new_password').focus();
        }
    });
}

// Form validation
if (document.getElementById('requestForm')) {
    document.getElementById('requestForm').addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        if (!email || !email.includes('@')) {
            e.preventDefault();
            alert('Please enter a valid email address');
            document.getElementById('email').focus();
        }
    });
}

if (document.getElementById('resetForm')) {
    document.getElementById('resetForm').addEventListener('submit', function(e) {
        const code = document.getElementById('code').value;
        const newPass = document.getElementById('new_password').value;
        const confirmPass = document.getElementById('confirm_password').value;
        const errors = [];
        
        if (!code || code.length < 6) {
            errors.push('Please enter the 6-digit reset code');
        }
        if (!newPass) {
            errors.push('New password is required');
        }
        if (newPass !== confirmPass) {
            errors.push('Passwords do not match');
        }
        if (newPass.length < 8) {
            errors.push('Password must be at least 8 characters');
        }
        
        if (errors.length > 0) {
            e.preventDefault();
            alert('Please fix the following errors:\n\n' + errors.join('\n'));
        }
    });
}
</script>
