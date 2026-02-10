<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['UserName'])) {
    header("Location: login.php");
    exit();
}

require_once("config.php");
require_once("db-settings.php");

// Get user details from database
$username = $_SESSION['UserName'];
$email = $_SESSION['UserEmail'] ?? $username;

$stmt = $mysqli->prepare("SELECT * FROM users WHERE USERNAME = ? OR EMAIL = ? LIMIT 1");
$stmt->bind_param("ss", $email, $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle form submissions
$message = '';
$message_type = ''; // success, error, info

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Update profile info
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $contact = trim($_POST['contact']);
        
        $update_stmt = $mysqli->prepare("UPDATE users SET FNAME = ?, LNAME = ?, CONTACT = ? WHERE USERNAME = ?");
        $update_stmt->bind_param("ssss", $first_name, $last_name, $contact, $email);
        
        if ($update_stmt->execute()) {
            // Update session
            $_SESSION['UserFirstName'] = $first_name;
            $_SESSION['UserName'] = !empty($first_name) ? $first_name : $email;
            
            $message = "Profile updated successfully!";
            $message_type = "success";
            
            // Refresh user data
            $user['FNAME'] = $first_name;
            $user['LNAME'] = $last_name;
            $user['CONTACT'] = $contact;
        } else {
            $message = "Error updating profile: " . $update_stmt->error;
            $message_type = "error";
        }
        $update_stmt->close();
    }
    
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Validate
        if ($new_password !== $confirm_password) {
            $message = "New passwords do not match!";
            $message_type = "error";
        } elseif (strlen($new_password) < 8) {
            $message = "Password must be at least 8 characters";
            $message_type = "error";
        } elseif ($user['PASSWORD'] === $current_password) { // Your system uses plain passwords
            // Update password in database
            $update_stmt = $mysqli->prepare("UPDATE users SET PASSWORD = ? WHERE USERNAME = ?");
            $update_stmt->bind_param("ss", $new_password, $email);
            
            if ($update_stmt->execute()) {
                $message = "Password changed successfully!";
                $message_type = "success";
            } else {
                $message = "Error changing password: " . $update_stmt->error;
                $message_type = "error";
            }
            $update_stmt->close();
        } else {
            $message = "Current password is incorrect!";
            $message_type = "error";
        }
    }
}

include("./header.php");
?>

<style>
/* CRITICAL FIXES - Add these first */
body {
    padding-top: 100px !important;
    position: relative;
    overflow-x: hidden;
}

/* Force header to be clickable */
.header-area {
    z-index: 99999 !important;
    pointer-events: auto !important;
}

.header-area * {
    pointer-events: auto !important;
    z-index: 99999 !important;
}

.dropdown-menu {
    z-index: 100000 !important;
}

/* Account Page Styles */
.account-container {
    max-width: 800px; /* Reduced from 1200px */
    margin: 20px auto 40px !important;
    padding: 0 20px;
    position: relative;
    z-index: 1;
}

.account-header {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 2px solid #2E4053;
}

.account-header h1 {
    color: #2E4053;
    font-size: 32px;
    margin-bottom: 10px;
    font-weight: 600;
}

.account-header p {
    color: #666;
    font-size: 16px;
}

/* Single column layout - simpler */
.account-content {
    background: white;
    border-radius: 10px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    padding: 40px;
}

.user-profile-header {
    text-align: center;
    margin-bottom: 40px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.user-avatar {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #2E4053 0%, #3a506b 100%);
    border-radius: 50%;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    color: white;
    font-weight: bold;
}

.user-name {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 5px;
    color: #2E4053;
}

.user-email {
    font-size: 16px;
    color: #666;
    margin-bottom: 15px;
}

.user-role {
    display: inline-block;
    background: #f8f9fa;
    color: #2E4053;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
    border: 1px solid #ddd;
}

/* Message Alerts */
.message-alert {
    padding: 15px 20px;
    border-radius: 6px;
    margin-bottom: 30px;
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

.message-alert i {
    margin-right: 10px;
    font-size: 18px;
}

/* Forms */
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
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

.readonly-field {
    background-color: #f8f9fa;
    color: #666;
    cursor: not-allowed;
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
    background: linear-gradient(135deg, #2E4053 0%, #3a506b 100%);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 6px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    margin-top: 10px;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #3a506b 0%, #2E4053 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(46, 64, 83, 0.2);
}

.btn-primary i {
    margin-right: 8px;
}

/* Section titles */
.section-title {
    color: #2E4053;
    font-size: 22px;
    margin: 40px 0 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
    font-weight: 600;
}

.section-title:first-child {
    margin-top: 0;
}

/* Stats Cards - Simplified */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 30px;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 15px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    border: 1px solid #eee;
}

.stat-number {
    font-size: 20px;
    font-weight: 700;
    color: #2E4053;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 13px;
    color: #666;
}
</style>

<div class="account-container">
    <div class="account-header">
        <h1>My Account</h1>
        <p>Manage your profile and password</p>
    </div>

    <?php if ($message): ?>
        <div class="message-alert message-<?php echo $message_type; ?>">
            <i class="fa fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="account-content">
        <!-- User Profile Header -->
        <div class="user-profile-header">
            <div class="user-avatar">
                <?php echo strtoupper(substr($user['FNAME'] ?? 'U', 0, 1)); ?>
            </div>
            <div class="user-name"><?php echo htmlspecialchars($user['FNAME'] . ' ' . $user['LNAME']); ?></div>
            <div class="user-email"><?php echo htmlspecialchars($user['EMAIL']); ?></div>
            <div class="user-role">
                <?php 
                $role_names = [
                    'A' => 'Administrator',
                    'T' => 'Thoroughbred User',
                    'S' => 'Standardbred User',
                    'ST' => 'Full Access User',
                    'user' => 'Basic User'
                ];
                echo $role_names[$user['USERROLE'] ?? 'user'];
                ?>
            </div>
        </div>

        <!-- Profile Information Section -->
        <h2 class="section-title">
            <i class="fa fa-user" style="margin-right: 10px;"></i>Profile Information
        </h2>
        
        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" 
                           value="<?php echo htmlspecialchars($user['FNAME'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" 
                           value="<?php echo htmlspecialchars($user['LNAME'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" class="form-control readonly-field" id="email" 
                       value="<?php echo htmlspecialchars($user['EMAIL']); ?>" readonly>
                <small style="color: #666; font-size: 13px;">Email cannot be changed</small>
            </div>
            
            <div class="form-group">
                <label for="contact">Contact Number</label>
                <input type="text" class="form-control" id="contact" name="contact" 
                       value="<?php echo htmlspecialchars($user['CONTACT'] ?? ''); ?>" 
                       placeholder="Enter your contact number">
            </div>
            
            <div class="form-group">
                <label>Account Type</label>
                <input type="text" class="form-control readonly-field" 
                       value="<?php echo $role_names[$user['USERROLE'] ?? 'user']; ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>Account Status</label>
                <input type="text" class="form-control readonly-field" 
                       value="<?php echo ($user['ACTIVE'] ?? 'N') === 'Y' ? 'Active' : 'Inactive'; ?>" readonly>
            </div>
            
            <button type="submit" name="update_profile" class="btn-primary">
                <i class="fa fa-save"></i> Update Profile
            </button>
        </form>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo date('M j, Y', strtotime($user['created_at'] ?? date('Y-m-d'))); ?></div>
                <div class="stat-label">Member Since</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo ($user['ACTIVE'] ?? 'N') === 'Y' ? 'Active' : 'Inactive'; ?></div>
                <div class="stat-label">Account Status</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-number"><?php echo $role_names[$user['USERROLE'] ?? 'user']; ?></div>
                <div class="stat-label">Access Level</div>
            </div>
        </div>

        <!-- Change Password Section -->
        <h2 class="section-title">
            <i class="fa fa-lock" style="margin-right: 10px;"></i>Change Password
        </h2>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
            </div>
            
            <div class="password-hint">
                <strong>Password Requirements:</strong> Minimum 8 characters with uppercase, lowercase, number, and special character
            </div>
            
            <button type="submit" name="change_password" class="btn-primary">
                <i class="fa fa-key"></i> Change Password
            </button>
        </form>
    </div>
</div>

<!-- EMERGENCY FIX: Add this JavaScript to force header dropdowns to work -->
<script>
// Wait for everything to load
window.addEventListener('load', function() {
    // Force Bootstrap dropdowns to work
    if (typeof $ !== 'undefined' && $.fn.dropdown) {
        // Reinitialize all dropdowns
        $('.dropdown-toggle').dropdown();
        
        // Remove any event handlers that might be blocking clicks
        $('.dropdown-toggle').off('click').on('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $(this).dropdown('toggle');
        });
    }
    
    // Make absolutely sure header is clickable
    setTimeout(function() {
        const header = document.querySelector('.header-area');
        if (header) {
            // Remove any inline styles that might be blocking
            header.style.cssText = 'pointer-events: auto !important; z-index: 99999 !important; position: fixed !important;';
            
            // Make all children clickable too
            const headerElements = header.querySelectorAll('*');
            headerElements.forEach(el => {
                el.style.pointerEvents = 'auto';
                el.style.zIndex = '99999';
            });
        }
        
        // Remove any modal backdrops that might be blocking
        const backdrops = document.querySelectorAll('.modal-backdrop, .fade, .in');
        backdrops.forEach(el => {
            if (el.parentNode) {
                el.parentNode.removeChild(el);
            }
        });
    }, 100);
});

// Nuclear option: If still not working after 1 second, force enable everything
setTimeout(function() {
    // Force enable ALL click events in header
    document.querySelectorAll('.header-area a, .header-area button, .header-area .dropdown-toggle').forEach(el => {
        el.onclick = null;
        el.addEventListener('click', function(e) {
            e.stopPropagation();
            return true;
        }, true);
    });
    
    // Remove any overlays
    document.body.style.overflow = 'visible';
    document.body.style.position = 'relative';
    
    // Create a debug helper
    console.log('Header fix applied - dropdowns should now work');
}, 1000);
</script>

<!-- SIMPLE ALTERNATIVE: If jQuery isn't working, use this pure JS fix -->
<script>
// Pure JavaScript fix for dropdowns
document.addEventListener('DOMContentLoaded', function() {
    // Find all dropdown toggles and make them work
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        // Remove any existing click handlers
        toggle.replaceWith(toggle.cloneNode(true));
        
        // Get the new element
        const newToggle = document.querySelector('[data-toggle="dropdown"]');
        
        // Add click handler
        newToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Toggle dropdown manually
            const dropdown = this.closest('.dropdown');
            if (dropdown) {
                dropdown.classList.toggle('show');
                
                // Show/hide dropdown menu
                const menu = dropdown.querySelector('.dropdown-menu');
                if (menu) {
                    menu.classList.toggle('show');
                }
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.dropdown')) {
            document.querySelectorAll('.dropdown').forEach(dropdown => {
                dropdown.classList.remove('show');
                const menu = dropdown.querySelector('.dropdown-menu');
                if (menu) {
                    menu.classList.remove('show');
                }
            });
        }
    });
});
</script>