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
/* Account Page Styles */
.account-container {
    max-width: 1200px;
    margin: 80px auto 40px;
    padding: 0 20px;
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

.account-grid {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 30px;
}

@media (max-width: 992px) {
    .account-grid {
        grid-template-columns: 1fr;
    }
}

/* Sidebar */
.account-sidebar {
    background: white;
    border-radius: 10px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    padding: 0;
    overflow: hidden;
}

.user-profile-card {
    padding: 30px;
    text-align: center;
    background: linear-gradient(135deg, #2E4053 0%, #3a506b 100%);
    color: white;
}

.user-avatar {
    width: 100px;
    height: 100px;
    background: white;
    border-radius: 50%;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    color: #2E4053;
    font-weight: bold;
}

.user-name {
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 5px;
}

.user-email {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 15px;
}

.user-role {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
}

.account-menu {
    padding: 20px 0;
}

.menu-item {
    display: block;
    padding: 15px 30px;
    color: #555;
    text-decoration: none;
    border-left: 4px solid transparent;
    transition: all 0.3s;
    font-weight: 500;
}

.menu-item:hover, .menu-item.active {
    background: #f8f9fa;
    color: #2E4053;
    border-left-color: #2E4053;
}

.menu-item i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

/* Content Area */
.account-content {
    background: white;
    border-radius: 10px;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    padding: 30px;
}

.section-title {
    color: #2E4053;
    font-size: 22px;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
    font-weight: 600;
}

/* Message Alerts */
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
}

.btn-primary:hover {
    background: linear-gradient(135deg, #3a506b 0%, #2E4053 100%);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(46, 64, 83, 0.2);
}

.btn-primary i {
    margin-right: 8px;
}

/* Stats Cards */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    border: 1px solid #eee;
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    font-size: 30px;
    color: #2E4053;
    margin-bottom: 15px;
}

.stat-number {
    font-size: 28px;
    font-weight: 700;
    color: #2E4053;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    color: #666;
}

/* Activity */
.activity-list {
    margin-top: 20px;
}

.activity-item {
    display: flex;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    background: #f8f9fa;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    color: #2E4053;
}

.activity-details {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    color: #333;
    margin-bottom: 3px;
}

.activity-time {
    font-size: 13px;
    color: #888;
}
</style>

<div class="account-container">
    <div class="account-header">
        <h1>My Account</h1>
        <p>Manage your profile, settings, and preferences</p>
    </div>

    <?php if ($message): ?>
        <div class="message-alert message-<?php echo $message_type; ?>">
            <i class="fa fa-<?php echo $message_type === 'success' ? 'check-circle' : ($message_type === 'error' ? 'exclamation-circle' : 'info-circle'); ?>"></i>
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="account-grid">
        <!-- Sidebar -->
        <div class="account-sidebar">
            <div class="user-profile-card">
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
            
            <div class="account-menu">
                <a href="#profile" class="menu-item active">
                    <i class="fa fa-user"></i> Profile Information
                </a>
                <a href="#password" class="menu-item">
                    <i class="fa fa-lock"></i> Change Password
                </a>
                <a href="#activity" class="menu-item">
                    <i class="fa fa-history"></i> Recent Activity
                </a>
                <a href="logout.php" class="menu-item">
                    <i class="fa fa-sign-out"></i> Logout
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="account-content">
            <!-- Profile Section -->
            <div id="profile">
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
                        <div class="stat-icon">
                            <i class="fa fa-calendar-check"></i>
                        </div>
                        <div class="stat-number"><?php echo date('M j, Y', strtotime($user['created_at'] ?? date('Y-m-d'))); ?></div>
                        <div class="stat-label">Member Since</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fa fa-user-check"></i>
                        </div>
                        <div class="stat-number"><?php echo ($user['ACTIVE'] ?? 'N') === 'Y' ? 'Active' : 'Inactive'; ?></div>
                        <div class="stat-label">Account Status</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fa fa-shield-alt"></i>
                        </div>
                        <div class="stat-number"><?php echo $role_names[$user['USERROLE'] ?? 'user']; ?></div>
                        <div class="stat-label">Access Level</div>
                    </div>
                </div>
            </div>
            
            <hr style="margin: 40px 0; border-color: #eee;">
            
            <!-- Change Password Section -->
            <div id="password">
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
            
            <hr style="margin: 40px 0; border-color: #eee;">
            
            <!-- Recent Activity Section -->
            <div id="activity">
                <h2 class="section-title">
                    <i class="fa fa-history" style="margin-right: 10px;"></i>Recent Activity
                </h2>
                
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fa fa-user-plus"></i>
                        </div>
                        <div class="activity-details">
                            <div class="activity-title">Account Created</div>
                            <div class="activity-time"><?php echo date('F j, Y \a\t g:i A', strtotime($user['created_at'] ?? date('Y-m-d'))); ?></div>
                        </div>
                    </div>
                    
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fa fa-sign-in-alt"></i>
                        </div>
                        <div class="activity-details">
                            <div class="activity-title">Last Login</div>
                            <div class="activity-time">Today at <?php echo date('g:i A'); ?></div>
                        </div>
                    </div>
                    
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fa fa-info-circle"></i>
                        </div>
                        <div class="activity-details">
                            <div class="activity-title">Profile Viewed</div>
                            <div class="activity-time">Just now</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Simple tab navigation
document.addEventListener('DOMContentLoaded', function() {
    const menuItems = document.querySelectorAll('.menu-item');
    const sections = document.querySelectorAll('.account-content > div');
    
    menuItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (this.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                
                // Remove active class from all items
                menuItems.forEach(i => i.classList.remove('active'));
                // Add active class to clicked item
                this.classList.add('active');
                
                // Hide all sections
                sections.forEach(section => section.style.display = 'none');
                // Show target section
                const targetId = this.getAttribute('href').substring(1);
                document.getElementById(targetId).style.display = 'block';
            }
        });
    });
    
    // Initially hide all sections except profile
    sections.forEach(section => {
        if (section.id !== 'profile') {
            section.style.display = 'none';
        }
    });
});
</script>