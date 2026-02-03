<?php
require_once '../config.php';
require_once '../includes/functions.php';

// if user is already logged in, redirect according to role
if (isset($_SESSION['user_id'])) {
	redirect_after_login();
}

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Sign In</title>
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<link rel="stylesheet" href="auth.css">
	<!-- include jquery library -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
	
	<script type="text/javascript" src="../includes/script.js" defer></script>
</head>
<body>
	<div class="card">
		<h2>Sign In</h2>

		<div class="displayTxt"></div>

		<form id="loginForm" method="post" novalidate>
			<input type="hidden" name="csrf" value="<?= csrf() ?>">

			<label for="username">Username or Email</label>
			<input id="username" type="text" name="username" required autofocus>

			<label for="password">Password</label>
			<input id="password" type="password" name="password" required>

			<button id="submitBtn" type="submit">Sign In</button>
		</form>

		<div class="link">
			Don't have an account? <a href="register.php">Create one</a>
		</div>
	</div>
</body>
</html>