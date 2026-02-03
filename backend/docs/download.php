<?php
require_once '../includes/auth.php';
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT stored_name,filename FROM documents WHERE id=? AND user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);
$row = $stmt->fetch() or die('Document not found');

$path = __DIR__.'/../uploads/'.$row['stored_name'];
if (!file_exists($path)) die('File missing');

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.htmlspecialchars($row['filename']).'"');
header('Content-Length: '.filesize($path));
readfile($path);
exit;