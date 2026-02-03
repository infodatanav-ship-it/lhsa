<?php
require_once '../includes/auth.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['file'])) {
	if (!csrf_check($_POST['csrf'])) die('CSRF mismatch');
	$f = $_FILES['file'];
	if ($f['error'] !== UPLOAD_ERR_OK) die('Upload error');
	$max = 5 * 1024 * 1024; // 5 MB
	if ($f['size'] > $max) die('File too big');

	$ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
	$allowed = ['jpg','jpeg','png','gif','pdf','doc','docx','txt','zip'];
	if (!in_array($ext, $allowed)) die('Extension not allowed');

	$stored = bin2hex(random_bytes(16)).'.'.$ext;
	move_uploaded_file($f['tmp_name'], __DIR__.'/../uploads/'.$stored);

	$stmt = $pdo->prepare("INSERT INTO documents (user_id,filename,stored_name,size) VALUES (?,?,?,?)");
	$stmt->execute([$_SESSION['user_id'], $f['name'], $stored, $f['size']]);
	redirect('index.php');
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Upload</title></head>
<body>
	<h2>Upload Document</h2>
	<form method="post" enctype="multipart/form-data">
		<input type="hidden" name="csrf" value="<?=csrf()?>">
		<input type="file" name="file" required><br>
		<button>Upload</button> <a href="index.php">Cancel</a>
	</form>
</body>
</html>