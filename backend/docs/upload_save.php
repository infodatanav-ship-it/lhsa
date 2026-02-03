<?php
require_once __DIR__.'/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!csrf_check($_POST['csrf'])) die('CSRF mismatch');

	$f = $_FILES['file'];
	if ($f['error'] !== UPLOAD_ERR_OK)                    $_SESSION['flash'] = 'Upload error';
	elseif ($f['size'] > 5 * 1024 * 1024)                $_SESSION['flash'] = 'Max 5 MB';
	else {
		$ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
		$allowed = ['jpg','jpeg','png','gif','pdf','doc','docx','txt','zip'];
		if (!in_array($ext, $allowed))                    $_SESSION['flash'] = 'Extension not allowed';
	}

	if (empty($_SESSION['flash'])) {
		$stored = bin2hex(random_bytes(16)) . '.' . $ext;
		move_uploaded_file($f['tmp_name'], __DIR__.'/../uploads/'.$stored);
		$stmt = $pdo->prepare("INSERT INTO documents (user_id, filename, stored_name, size) VALUES (?, ?, ?, ?)");
		$stmt->execute([$_SESSION['user_id'], $f['name'], $stored, $f['size']]);
	}
}
redirect('index.php');