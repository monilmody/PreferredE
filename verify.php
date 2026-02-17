<?php
session_start();
include("./header.php");

require_once("config.php");
require_once("db-settings.php");
require_once("cognito.php");

$message = '';
$message_type = '';
$email = $_SESSION['verification_email'] ?? $_GET['email'] ?? '';

// Handle verification form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_code'])) {
    $email = trim($_POST['email']);
    $code = trim($_POST['code']);

    if (empty($email) || empty($code)) {
        $message = "Email and verification code are required";
        $message_type = "error";
    } else {
        // Verify with Cognito
        $result = CognitoAuth::confirmSignUp($email, $code);

        if ($result['success']) {
            // Update database to mark email as verified
            $update_stmt = $mysqli->prepare("UPDATE users SET EMAIL_VERIFIED = 1 WHERE EMAIL = ?");
            $update_stmt->bind_param("s", $email);

            if ($update_stmt->execute()) {
                $message = "Email verified successfully! You can now login.";
                $message_type = "success";

                // Clear session
                unset($_SESSION['verification_email']);

                // Redirect to login after 3 seconds
                echo '<script>
                    setTimeout(function() {
                        window.location.href = "login.php";
                    }, 3000);
                </script>';
            } else {
                $message = "Database update failed. Please contact support.";
                $message_type = "error";
                error_log("Database update failed for verified user: " . $email);
            }
        } else {
            $message = $result['message'] ?? "Verification failed. Please try again.";
            $message_type = "error";
        }
    }
}

// Handle resend code
if (isset($_GET['resend']) && $email) {
    $result = CognitoAuth::resendConfirmationCode($email);

    if ($result['success']) {
        $message = "New verification code sent to your email!";
        $message_type = "success";
    } else {
        $message = $result['message'] ?? "Failed to resend code. Please try again.";
        $message_type = "error";
    }
}
?>

<style>
    .verification-container {
        max-width: 500px;
        margin: 100px auto 50px;
        padding: 40px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
        border: 1px solid #e0e0e0;
    }

    .verification-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .verification-header h1 {
        color: #2E4053;
        font-size: 28px;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .verification-header p {
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

    .code-input {
        text-align: center;
        font-size: 20px;
        letter-spacing: 5px;
        font-weight: bold;
    }

    .resend-link {
        text-align: center;
        margin-top: 20px;
    }

    .resend-link a {
        color: #2E4053;
        text-decoration: none;
        font-size: 14px;
    }

    .resend-link a:hover {
        text-decoration: underline;
    }

    .timer {
        text-align: center;
        color: #666;
        font-size: 14px;
        margin-top: 10px;
    }

    .verification-note {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 14px;
        color: #2E4053;
        border-left: 3px solid #2E4053;
    }
</style>

<div class="verification-container">
    <div class="verification-header">
        <h1>Verify Your Email</h1>
        <p>Enter the 6-digit verification code sent to your email</p>
    </div>

    <?php if (isset($_SESSION['verification_message'])): ?>
        <div class="message-alert message-info">
            <i class="fa fa-info-circle"></i>
            <?php echo $_SESSION['verification_message']; ?>
        </div>
        <?php unset($_SESSION['verification_message']); ?>
    <?php endif; ?>

    <?php if ($message): ?>
        <div class="message-alert message-<?php echo $message_type; ?>">
            <i class="fa fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="verification-note">
        <i class="fa fa-envelope"></i>
        <strong>Email:</strong> <?php echo htmlspecialchars($email); ?>
    </div>

    <form method="POST" action="" id="verificationForm">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">

        <div class="form-group">
            <label for="code">Verification Code</label>
            <input type="text" class="form-control code-input" id="code" name="code"
                placeholder="Enter 6-digit code" required maxlength="6" pattern="[0-9]{6}" inputmode="numeric">
            <small style="color: #666; font-size: 13px;">Check your email for the 6-digit verification code</small>
        </div>

        <button type="submit" name="verify_code" class="btn-primary">
            <i class="fa fa-check-circle"></i> Verify Email
        </button>
    </form>

    <div class="resend-link">
        <a href="?resend=1&email=<?php echo urlencode($email); ?>" id="resendLink">
            <i class="fa fa-refresh"></i> Resend verification code
        </a>
    </div>

    <div class="login-link">
        <p>Already verified? <a href="login.php">Sign in here</a></p>
    </div>
</div>

<script>
    // Auto-advance code input
    if (document.getElementById('code')) {
        const codeInput = document.getElementById('code');
        codeInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, ''); // Only numbers
        });
    }

    // Countdown timer for resend
    if (document.getElementById('resendLink')) {
        let seconds = 60;
        const resendLink = document.getElementById('resendLink');
        const originalText = resendLink.innerHTML;

        function updateTimer() {
            if (seconds > 0) {
                resendLink.innerHTML = `<i class="fa fa-hourglass-half"></i> Resend in ${seconds}s`;
                resendLink.style.pointerEvents = 'none';
                resendLink.style.opacity = '0.5';
                seconds--;
                setTimeout(updateTimer, 1000);
            } else {
                resendLink.innerHTML = originalText;
                resendLink.style.pointerEvents = 'auto';
                resendLink.style.opacity = '1';
            }
        }

        // Start timer when page loads
        updateTimer();
    }

    // Form validation
    if (document.getElementById('verificationForm')) {
        document.getElementById('verificationForm').addEventListener('submit', function(e) {
            const code = document.getElementById('code').value;
            if (!code || code.length !== 6) {
                e.preventDefault();
                alert('Please enter the 6-digit verification code');
            }
        });
    }
</script>

<?php
include("./footer.php");
?>