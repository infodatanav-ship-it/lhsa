<?php
require_once __DIR__.'/../includes/auth.php';
if ($_SESSION['role'] !== 'admin') die('Admins only');

$id   = $_GET['id'] ?? null;
$user = null;
if ($id) {
	$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
	$stmt->execute([$id]);
	$user = $stmt->fetch() or die('User not found');
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!csrf_check($_POST['csrf'])) die('CSRF mismatch');

	$username = trim($_POST['username']);
	$email    = trim($_POST['email']);
	$role     = in_array($_POST['role'], ['user','admin']) ? $_POST['role'] : 'user';

	if (strlen($username) < 3)  $errors[] = 'Username too short';
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email';

	$stmt = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
	$stmt->execute([$username, $email, $id ?: 0]);
	if ($stmt->fetch()) $errors[] = 'Username / email already taken';

	if (!$errors) {
		if ($id) { // update
			$sql = "UPDATE users SET username = ?, email = ?, role = ?";
			$params = [$username, $email, $role];
			if (!empty($_POST['password'])) {
				$sql .= ", password = ?";
				$params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
			}
			$sql .= " WHERE id = ?";
			$params[] = $id;
			$pdo->prepare($sql)->execute($params);
		} else { // insert
			$hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
			$pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)")
				->execute([$username, $email, $hash, $role]);
		}
		redirect('users.php');
	}
}
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?= $id ? 'Edit' : 'Add' ?> User</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../auth/auth.css"> <!-- root tokens -->
	<link rel="stylesheet" href="admin.css">       <!-- shared admin styles -->
	<link rel="stylesheet" href="form.css">        <!-- form-specific styles -->
</head>
<body>
<div class="wrapper">
	<header class="topbar">
		<h1><?= $id ? 'Edit User' : 'Add User' ?></h1>
		<div class="actions">
			<a class="btn btn-outline" href="users.php">← Back</a>
		</div>
	</header>

	<main class="card">
		<?php if ($errors): ?>
			<div class="error-box">
				<?php foreach ($errors as $e): ?>
					<div><?= htmlspecialchars($e) ?></div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<form method="post" class="user-form" novalidate>
			<input type="hidden" name="csrf" value="<?= csrf() ?>">

			<div class="form-group">
				<label for="username">Username</label>
				<input id="username" type="text" name="username" required
					value="<?= htmlspecialchars($user['username'] ?? '') ?>">
			</div>

			<div class="form-group">
				<label for="email">Email</label>
				<input id="email" type="email" name="email" required
					value="<?= htmlspecialchars($user['email'] ?? '') ?>">
			</div>

			<div class="form-group">
				<label for="role">Role</label>
				<select id="role" name="role">
					<option value="user" <?= ($user['role'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
					<option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
				</select>
			</div>

			<div class="form-group">
				<label for="password">
					Password <?= $id ? '(leave empty to keep current)' : '' ?>
				</label>
				<input id="password" type="password" name="password" <?= $id ? '' : 'required' ?> minlength="6">
			</div>

			<button type="submit" class="btn btn-primary"><?= $id ? 'Save Changes' : 'Create User' ?></button>
		</form>
	</main>
</div>
</body>
</html>