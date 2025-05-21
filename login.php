<?php
session_start();
ob_start();
include("./header.php");
echo '<br>';
echo '<br>';
?>
<!-- <head> -->
<!--   <meta name="viewport" content="width=device-width initial-scale=1"> -->
<!--   <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> -->
<!--   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
<!--   <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script> -->
<!--   <link rel="stylesheet" href="assets/css/table.css"> -->

<!--   <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css"> -->
<!--   <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script> -->


<!-- </head> -->

<?php

require_once("config.php");
//Prevent the user visiting the logged in page if he/she is already logged in

// if(isUserLoggedIn()) {
// 	header("Location: myaccount.php");
// 	die();
// }

//Forms posted
if (!empty($_POST)) {
	$errors = array();
	$username = trim($_POST["username"]);
	$password = trim($_POST["password"]);

	//Perform some validation
	if ($username == "") {
		$errors[] = "Enter Username";
	}
	if ($password == "") {
		$errors[] = "Enter Password";
	}
	if (count($errors) == 0) {
		//retrieve the records of the user who is trying to login
		$userdetails = fetchUserDetails($username);
		if ($userdetails["USERNAME"] == "") {
			$errors[] = "Account Not Found";
		}
		//See if the user"s account is activated
		//$errors[]= $userdetails["Active"];
		if ($userdetails["ACTIVE"] == "N") {
			$errors[] = "Account Not Authorized";
		} else {
			//Hash the password and use the salt from the database to compare the password.
			//$entered_pass = generateHash($password, $userdetails["Password"]);

			if ($password != $userdetails["PASSWORD"]) {
				$errors[] = "Invalid Password";
			} else {
				//header("Location:index.php");
				//Passwords match! we"re good to go"
				// 				$loggedInUser = new loggedInuser();
				// 				$loggedInUser->email = $userdetails["EMAIL"];
				// 				$loggedInUser->user_id = $userdetails["USER_ID"];
				// 				$loggedInUser->password = $userdetails["PASSWORD"];
				// 				$loggedInUser->first_name = $userdetails["FNAME"];
				// 				$loggedInUser->last_name = $userdetails["LNAME"];
				// 				$loggedInUser->username = $userdetails["USERNAME"];

				//pass the values of $loggedInUser into the session -
				// you can directly pass the values into the array as well.

				//$_SESSION["ThisUser"] = $loggedInUser;
				//echo  $userdetails["USERNAME"];
				$_SESSION["UserActive"] = $userdetails["ACTIVE"];
				$_SESSION["UserName"] = $userdetails["USERNAME"];
				$_SESSION["UserRole"] = $userdetails["USERROLE"];

				//echo $_SESSION["UserName"];

				setcookie("LoggedInUser", $userdetails["USERNAME"], time() + 3600, "/");

				//now that a session for this user is created
				//Redirect to this users account page
				$redirect_url = isset($_GET['redirect']) ? urldecode($_GET['redirect']) : "index.php";
				header("Location: $redirect_url");
				exit();
				//exit();
			}
		}
	}
}
ob_end_flush();
?>


<style type="text/css" media="screen">
	@import url("style/css/style.css");
</style>

<body>
	<div class="main-block">
		<blockquote>
			<?php print_r($errors[0]); ?>
		</blockquote>
		<form name="login" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?><?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="post">
			<hr>
			<hr>
			<div style="text-align:center;border:black">
				<input type="text" name="username" id="username" placeholder="user name" required />
			</div>
			<div style="text-align:center;">
				<input type="password" name="password" id="password" placeholder="Password" required />
			</div>
			<hr>

			<?php if (isset($_GET['redirect'])): ?>
				<input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_GET['redirect']); ?>">
			<?php endif; ?>
			<div class="btn-block" style="text-align:center;">
				<button type="submit" href="/">Submit</button>

			</div>
		</form>
	</div>

	<script>
		document.getElementById("username").focus();
	</script>