<?php
require_once '../includes/auth.php';
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT stored_name FROM documents WHERE id=? AND user_id=?");
$stmt->execute([$id, $_SESSION['user_id']]);
$row = $stmt->fetch() or die('Document not found');

@unlink(__DIR__.'/../uploads/'.$row['stored_name']);
$pdo->prepare("DELETE FROM documents WHERE id=? AND user_id=?")->execute([$id, $_SESSION['user_id']]);
redirect('index.php');