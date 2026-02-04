<?php
require_once __DIR__.'/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (!csrf_check($_POST['csrf'])) die('CSRF mismatch');

	// basic validation
	if (empty($_FILES['file'])) {
		$_SESSION['flash'] = 'No file uploaded';
	} else {
		$f = $_FILES['file'];
		if ($f['error'] !== UPLOAD_ERR_OK) {
			$_SESSION['flash'] = 'Upload error';
		} elseif ($f['size'] > 5 * 1024 * 1024) {
			$_SESSION['flash'] = 'Max 5 MB';
		} else {
			$ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
			$allowed = ['jpg','jpeg','png','gif','pdf','doc','docx','txt','zip'];
			if (!in_array($ext, $allowed)) {
				$_SESSION['flash'] = 'Extension not allowed';
			}
		}

		if (empty($_SESSION['flash'])) {
			$stored = bin2hex(random_bytes(16)) . '.' . $ext;
			$dest = __DIR__.'/../uploads/'.$stored;

			// ensure uploaded file can be moved; handle failure gracefully
			if (!is_uploaded_file($f['tmp_name']) || !move_uploaded_file($f['tmp_name'], $dest)) {
				$_SESSION['flash'] = 'Unable to save uploaded file (check directory permissions)';
			} else {
				$stmt = $pdo->prepare("INSERT INTO documents (user_id, filename, stored_name, size) VALUES (?, ?, ?, ?)");
				$stmt->execute([$_SESSION['user_id'], $f['name'], $stored, $f['size']]);

				// handle optional group assignments (groups[])
				$docId = (int)$pdo->lastInsertId();
				if (!empty($_POST['groups']) && is_array($_POST['groups'])) {
					$ins = $pdo->prepare("INSERT INTO document_groups (document_id, group_id) VALUES (?, ?)");
					foreach ($_POST['groups'] as $gid) {
						$ins->execute([(int)$docId, (int)$gid]);
					}
				}
			}
		}
	}
}
redirect('index.php');