<?php
require_once '../config.php';
require_once '../includes/functions.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD']==='POST') {
	if (!csrf_check($_POST['csrf'])) die('CSRF mismatch');
	$username = trim($_POST['username']);
	$email    = trim($_POST['email']);
	$pass     = $_POST['password'];

	if (strlen($username)<3)  $errors[] = 'Username too short';
	if (!filter_var($email,FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email';
	if (strlen($pass)<6)      $errors[] = 'Password too short';

	$stmt = $pdo->prepare("SELECT id FROM users WHERE username=? OR email=?");
	$stmt->execute([$username,$email]);
	if ($stmt->fetch()) $errors[] = 'Username or email already taken';

	if (!$errors) {
		$hash = password_hash($pass,PASSWORD_DEFAULT);
		$stmt = $pdo->prepare("INSERT INTO users (username,email,password) VALUES (?,?,?)");
		$stmt->execute([$username,$email,$hash]);
		$_SESSION['user_id']  = $pdo->lastInsertId();
		$_SESSION['username'] = $username;
		$_SESSION['role']     = 'user';
		redirect_after_login();
	}
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Create Account</title>
	<meta name="viewport" content="width=device-width,initial-scale=1">
	<link rel="stylesheet" href="auth.css">
</head>
<body>
	<div class="card">
		<h2>Create Account</h2>

		<?php if ($errors): ?>
			<div class="error">
				<?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
			</div>
		<?php endif; ?>

		<form method="post" novalidate>
			<input type="hidden" name="csrf" value="<?= csrf() ?>">

			<label for="username">Username</label>
			<input id="username" type="text" name="username" value="<?= htmlspecialchars($_POST['username']??'') ?>" required>

			<label for="email">Email</label>
			<input id="email" type="email" name="email" value="<?= htmlspecialchars($_POST['email']??'') ?>" required>

			<label for="password">Password</label>
			<input id="password" type="password" name="password" required minlength="6">

			<button type="submit">Register</button>
		</form>

		<div class="link">
			Already have an account? <a href="login.php">Sign in</a>
		</div>
	</div>
</body>
</html>