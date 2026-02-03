<?php
require_once __DIR__.'/../includes/auth.php';
header('Content-Type: application/json');

if (!csrf_check($_POST['csrf'] ?? '')) { echo json_encode(['ok'=>false,'msg'=>'CSRF']); exit; }

/* -------- new folder -------- */
if (isset($_POST['name'])) {
	$parent = (int)($_POST['parent_id'] ?? 0);
	$name   = trim($_POST['name']);

	if ( $parent == 0 ) {
		$parent = null;
	}

	$stmt   = $pdo->prepare("INSERT INTO `documents` (`user_id`, `filename`, `is_folder`, `parent_id`) VALUES (?, ?, 1, ?)");
	$stmt->execute([$_SESSION['user_id'], $name, $parent]);
	echo json_encode(['ok'=>true]);
	exit;
}

/* -------- upload file -------- */
if (isset($_FILES['file'])) {

	// var_dump($_POST);
	// echo 'sdsdsdsdsd';

	$f      = $_FILES['file'];
	$parent = (int)($_POST['parent_id'] ?? 0);

	if ( $parent == 0 ) {
		$parent = null;
	}

	if ($f['error'] !== UPLOAD_ERR_OK)                          { echo json_encode(['ok'=>false,'msg'=>'Upload error']); exit; }
	if ($f['size'] > 5 * 1024 * 1024)                          { echo json_encode(['ok'=>false,'msg'=>'Max 5 MB']); exit; }
	$ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
	if (!in_array($ext, ['jpg','jpeg','png','gif','pdf','doc','docx','txt','zip']))
																{ echo json_encode(['ok'=>false,'msg'=>'Extension not allowed']); exit; }
	$stored = bin2hex(random_bytes(16)).'.'.$ext;
	move_uploaded_file($f['tmp_name'], __DIR__.'/../uploads/'.$stored);
	$stmt = $pdo->prepare("INSERT INTO documents (user_id, filename, stored_name, size, parent_id) VALUES (?, ?, ?, ?, ?)");
	$stmt->execute([$_SESSION['user_id'], $f['name'], $stored, $f['size'], $parent]);
	echo json_encode(['ok'=>true]);
	exit;
}

/* -------- delete file -------- */
if (isset($_POST['delete_id'])) {
	$id = (int)$_POST['delete_id'];
	// delete physical file
	$row = $pdo->prepare("SELECT stored_name FROM documents WHERE id = ? AND user_id = ? AND is_folder = 0")->fetch();
	if ($row) @unlink(__DIR__.'/../uploads/'.$row['stored_name']);
	$pdo->prepare("DELETE FROM documents WHERE id = ? AND user_id = ?")->execute([$id, $_SESSION['user_id']]);
	echo json_encode(['ok'=>true]);
	exit;
}