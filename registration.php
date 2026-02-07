<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("./header.php");

// Get errors and form data from session
$errors = $_SESSION['registration_errors'] ?? [];
$form_data = $_SESSION['form_data'] ?? [];

// Clear session data
unset($_SESSION['registration_errors']);
unset($_SESSION['form_data']);

// Show success message if exists
if (isset($_SESSION['registration_success'])) {
    echo '<div style="color: green; padding: 10px; text-align: center; border: 1px solid green; margin: 10px;">' 
         . $_SESSION['registration_success'] . '</div>';
    unset($_SESSION['registration_success']);
}
?>

<!-- Your existing HTML starts here -->
<br>
<form action="register_action.php" method="POST">
<div class="container">
<h1>Register</h1>
<p>Please fill in this form to create an account.</p>

<!-- Show errors if any -->
<?php if (!empty($errors)): ?>
    <div style="color: red; padding: 10px; border: 1px solid red; margin-bottom: 15px;">
        <strong>Please fix the following errors:</strong>
        <ul style="margin: 5px 0; padding-left: 20px;">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
<hr>

<label for="user"><b>Email Address</b></label>
<input type="text" placeholder="Enter Email" name="user" id="user" 
       value="<?php echo htmlspecialchars($form_data['user'] ?? ''); ?>" required>

<label for="fname"><b>First Name</b></label>
<input type="text" placeholder="Enter First Name" name="fname" id="fname" 
       value="<?php echo htmlspecialchars($form_data['fname'] ?? ''); ?>" required>

<label for="lname"><b>Last Name</b></label>
<input type="text" placeholder="Enter Last Name" name="lname" id="lname" 
       value="<?php echo htmlspecialchars($form_data['lname'] ?? ''); ?>" required>

<label for="password"><b>Password</b></label>
<input type="password" placeholder="Enter Password (min 8 chars, uppercase, lowercase, number, special)" 
       name="password" id="password" required>

<label for="userrole"><b>User Role</b></label>
<select class="custom-select1" id="userrole" name="userrole" required>
	<option value="N" <?php echo (($form_data['userrole'] ?? '') == 'N') ? 'selected' : ''; ?>>SELECT USER ROLE</option>
	<option value="A" <?php echo (($form_data['userrole'] ?? '') == 'A') ? 'selected' : ''; ?>>ADMIN</option>
	<option value="T" <?php echo (($form_data['userrole'] ?? '') == 'T') ? 'selected' : ''; ?>>THROUGHBRED</option>
	<option value="S" <?php echo (($form_data['userrole'] ?? '') == 'S') ? 'selected' : ''; ?>>STANDARDBRED</option>
	<option value="ST" <?php echo (($form_data['userrole'] ?? '') == 'ST') ? 'selected' : ''; ?>>STANDARDBRED & THOROUGHBRED</option>
</select>

<hr>
<button type="submit" class="registerbtn">Register</button>
</div>

<div class="container signin">
<p>Already have an account? <a href="login.php">Sign in</a>.</p>
</div>
</form>