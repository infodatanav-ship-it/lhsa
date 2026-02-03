<?php
require_once '../config.php';
require_once '../includes/functions.php';
require_once '../includes/permissions.php';

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {

	// print_r('process login');
	// var_dump($_POST);
	if (!csrf_check($_POST['csrf'])) die('CSRF mismatch');

	// ----- existing authentication logic -----
	$user = trim($_POST['username']);
	$pass = $_POST['password'];

	$stmt = $pdo->prepare("SELECT `id`,`username`,`password`,`role` FROM `users` WHERE `username`=? OR `email`=?");

	$stmt->execute([$user,$user]);
	$row = $stmt->fetch();

	if ($row && password_verify($pass,$row['password'])) {

		// login successful
		
		session_regenerate_id(true);
		loadPermissions($pdo, $row['role']);
		$_SESSION['user_id']   = $row['id'];
		$_SESSION['username']  = $row['username'];
		$_SESSION['role']      = $row['role'];
		
		// print_r('login successful & set session');

		// return ['status' => 'success', 'message' => 'Login successful', 'redirect' => './../admin/users.php'];
		// return ['status' => 'success'];
		// print_r(json_encode(['status' => true, 'message' => 'Login successful', 'redirect' => './../admin/users.php']));
		// redirect($_SESSION['after_login'] ?? './../admin/users.php');

		echo json_encode(['status' => true, 'message' => 'Login successful', 'redirect' => './../admin/dashboard.php']);

	} else {

		// login failed
		$error = 'Invalid credentials';
		echo json_encode(['status' => false, 'message' => $error]);

	}

} else {
	// show the login form
	// var_dump('show form');
	echo json_encode(['status' => false, 'message' => 'Invalid request method']);
}	

