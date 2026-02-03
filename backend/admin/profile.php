<?php
// define('DS','DIRECTORY_SEPARATOR', 'DS');
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'auth.php';
require_once __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'permissions.php';

$currentPage = 'profile';
$uid = $_SESSION['user_id'];

/* ---------- update handler ---------- */
$errors = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!csrf_check($_POST['csrf'] ?? '')) die('CSRF mismatch');

	$username = trim($_POST['username'] ?? '');
	$email    = trim($_POST['email']    ?? '');
	$passNow  = $_POST['pass_now']  ?? '';
	$passNew  = $_POST['pass_new']  ?? '';

	/* basic validation */
	if (strlen($username) < 3)  $errors = 'Username too short';
	elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors = 'Invalid email';
	else {
		/* email/username taken by someone else? */
		$stmt = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
		$stmt->execute([$username, $email, $uid]);
		if ($stmt->fetch()) $errors = 'Username/email already taken';
	}

	/* password change requested */
	if (!$errors && $passNew) {
		/* verify current password */
		$stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
		$stmt->execute([$uid]);
		$hash = $stmt->fetchColumn();
		if (!password_verify($passNow, $hash)) {
			$errors = 'Current password incorrect';
		} elseif (strlen($passNew) < 6) {
			$errors = 'New password too short';
		}
	}

	/* save */
	if (!$errors) {
		$sql = "UPDATE users SET username = ?, email = ?";
		$args = [$username, $email];
		if ($passNew) { $sql .= ", password = ?"; $args[] = password_hash($passNew, PASSWORD_DEFAULT); }
		$sql .= " WHERE id = ?";
		$args[] = $uid;
		$pdo->prepare($sql)->execute($args);
		$_SESSION['username'] = $username;          // keep session in sync
		$success = 'Profile updated';
	}
}

/* ---------- user data ---------- */
$stmtUsr = $pdo->prepare("SELECT `id`, `username`, `email`, `role` FROM users WHERE id = ?");
$stmtUsr->execute([$uid]);
$user = $stmtUsr->fetch();



// var_dump($user);

// if (!$user) redirect('auth/logout.php');

/* ---------- groups + docs ---------- */
$groups = $pdo->prepare(
	"SELECT g.name
	FROM `groups` g
	JOIN group_users gu ON gu.group_id = g.id
	WHERE gu.user_id = ?
ORDER BY g.name")->fetchAll(PDO::FETCH_COLUMN);

$docs = $pdo->prepare(
	"SELECT filename, size, uploaded_at
	FROM documents
	WHERE user_id = ?
ORDER BY uploaded_at DESC
	LIMIT 5")->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>My Profile</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../auth/auth.css">
	<link rel="stylesheet" href="../includes/nav.css">
	<link rel="stylesheet" href="profile.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="wrapper">

	<section class="card">

		<?php include '../includes/nav.php'; ?>

		<h2>My Profile</h2>

		<?php if ($errors): ?>
			<div class="error-box"><?= htmlspecialchars($errors) ?></div>
		<?php elseif ($success): ?>
			<div class="success-box"><?= htmlspecialchars($success) ?></div>
		<?php endif; ?>

		<form method="post" class="user-form">
			<input type="hidden" name="csrf" value="<?= csrf() ?>">

			<div class="form-group">
				<label>Username</label>
				<input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required minlength="3">
			</div>

			<div class="form-group">
				<label>Email</label>
				<input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
			</div>

			<hr class="sep">

			<div class="form-group">
				<label>Current Password <small>(only if changing password)</small></label>
				<input type="password" name="pass_now" placeholder="Leave blank to keep current">
			</div>

			<div class="form-group">
				<label>New Password</label>
				<input type="password" name="pass_new" placeholder="Min 6 characters">
			</div>

			<button type="submit" class="btn btn-primary">Save Changes</button>
		</form>
	</section>

	<!-- Groups -->
	<section class="card">
		<h3>My Groups</h3>
		<?php if ($groups): ?>
			<div class="tag-list">
				<?php foreach ($groups as $g): ?>
					<span class="tag"><?= htmlspecialchars($g) ?></span>
				<?php endforeach; ?>
			</div>
		<?php else: ?>
			<p class="empty">Not a member of any group yet.</p>
		<?php endif; ?>
	</section>

	<!-- Latest documents -->
	<section class="card">
		<h3>My Latest Documents</h3>
		<?php if ($docs): ?>
			<table class="tbl">
				<thead><tr><th>Name<th>Size<th>Uploaded</tr></thead>
				<tbody>
				<?php
				foreach ($docs as $d):
					$ext = strtolower(pathinfo($d['filename'], PATHINFO_EXTENSION));
					$icon = match($ext){
						'pdf'=>'fa-file-pdf text-danger','doc','docx'=>'fa-file-word text-primary',
						'xls','xlsx'=>'fa-file-excel text-success','jpg','jpeg','png','gif'=>'fa-file-image text-info',
						'zip'=>'fa-file-zipper text-muted',default=>'fa-file text-muted'
					};
				?>
					<tr>
						<td class="fname">
							<i class="fa-fw fa-regular <?= $icon ?>"></i>
							<span title="<?= htmlspecialchars($d['filename']) ?>"><?= htmlspecialchars(ellipsis($d['filename'])) ?></span>
						</td>
						<td><?= humanSize($d['size']) ?></td>
						<td><?= date('Y-m-d H:i', strtotime($d['uploaded_at'])) ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php else: ?>
			<p class="empty">No documents uploaded yet.</p>
		<?php endif; ?>
	</section>
</div>
</body>
</html>