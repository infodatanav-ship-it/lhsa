<?php
require_once __DIR__.'/../includes/auth.php';
if ($_SESSION['role'] !== 'admin') die('Admins only');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!csrf_check($_POST['csrf'])) die('CSRF mismatch');

	$username = trim($_POST['username']);
	$email    = trim($_POST['email']);
	$role     = in_array($_POST['role'], ['user','admin']) ? $_POST['role'] : 'user';
	$pass     = $_POST['password'];

	$errors = [];
	if (strlen($username) < 3)  $errors[] = 'Username too short';
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email';
	if (strlen($pass) < 6)      $errors[] = 'Password too short';

	$stmt = $pdo->prepare("SELECT id FROM users WHERE username=? OR email=?");
	$stmt->execute([$username, $email]);
	if ($stmt->fetch()) $errors[] = 'Username / email already taken';

	if (!$errors) {
		$hash = password_hash($pass, PASSWORD_DEFAULT);
		$pdo->prepare("INSERT INTO users (username,email,password,role) VALUES (?,?,?,?)")
			->execute([$username, $email, $hash, $role]);
	} else {
		// store once-only flash (session) and redirect back
		$_SESSION['flash'] = implode('<br>', $errors);
	}
}
redirect('users.php');