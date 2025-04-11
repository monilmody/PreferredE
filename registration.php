<?php
include("./header.php");
include("./session_page.php");
?>

<head>
  <meta name="viewport" content="width=device-width initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="assets/css/table.css">
  
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>


</head>
<?php
include_once("config.php");
?>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 14;
}

* {
  box-sizing: border-box;
}

/* Add padding to containers */
.container {
  padding: 16px;
  background-color: white;
}

/* Full-width input fields */
input[type=text], input[type=password] {
  width: 100%;
  padding: 15px;
  margin: 5px 0 22px 0;
  display: inline-block;
  border: none;
  background: #f1f1f1;
}

input[type=text]:focus, input[type=password]:focus {
  background-color: #ddd;
  outline: none;
}

/* Overwrite default styles of hr */
hr {
  border: 1px solid #f1f1f1;
  margin-bottom: 25px;
}

/* Set a style for the submit button */
.registerbtn {
  background-color: #04AA6D;
  color: white;
  padding: 16px 20px;
  margin: 8px 0;
  border: none;
  cursor: pointer;
  width: 100%;
  opacity: 0.9;
}

.registerbtn:hover {
  opacity: 1;
}

/* Add a blue text color to links */
a {
  color: dodgerblue;
}

/* Set a grey background color and center the text of the "sign in" section */
.signin {
  background-color: #f1f1f1;
  text-align: center;
}
</style>
</head>

<br>
<form action="regitration_action.php">
<div class="container">
<h1>Register</h1>
<p>Please fill in this form to create an account.</p>
<hr>

<label for="user"><b>User Name</b></label>
<input type="text" placeholder="Enter User Name" name="user" id="user" required>

<label for="fname"><b>First Name</b></label>
<input type="text" placeholder="Enter First Name" name="fname" id="fname" required>

<label for="lname"><b>Last Name</b></label>
<input type="text" placeholder="Enter Last Name" name="lname" id="lname" required>

<label for="password"><b>Password</b></label>
<input type="password" placeholder="Enter Password" name="password" id="password" required>

<label for="userrole"><b>User Role</b></label>
<select class="custom-select1" id="userrole" name="userrole">
	<option value="N">SELECT USER ROLE</option>
	<option value="A">ADMIN</option>
	<option value="T">THROUGHBRED</option>
	<option value="S">STANDARDBRED</option>
	<option value="ST">STANDARDBRED & THOROUGHBRED</option>
</select>

<!-- <label for="psw-repeat"><b>Repeat Password</b></label> -->
<!-- <input type="password" placeholder="Repeat Password" name="psw-repeat" id="psw-repeat" required> -->
<hr>
<!-- <p>By creating an account you agree to our <a href="#">Terms & Privacy</a>.</p> -->

<button type="submit" class="registerbtn">Register</button>
</div>

<div class="container signin">
<p>Already have an account? <a href="login.php">Sign in</a>.</p>
</div>
</form>

</body>
</html>