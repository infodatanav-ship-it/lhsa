<?php
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/permissions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	echo json_encode(['ok'=>false,'msg'=>'Method not allowed']); exit;
}
if (!csrf_check($_POST['csrf'] ?? '')) {
	echo json_encode(['ok'=>false,'msg'=>'CSRF error']); exit;
}

$id   = !empty($_POST['group_id']) ? (int)$_POST['group_id'] : 0;
$name = trim($_POST['name'] ?? '');

if ($id) {   /* ---------- EDIT ---------- */
	if (!can('groups.edit'))   { echo json_encode(['ok'=>false,'msg'=>'No permission']); exit; }
	$stmt = $pdo->prepare("UPDATE `groups` SET name = ? WHERE id = ?");
	$ok   = $stmt->execute([$name, $id]);
} else {     /* ---------- ADD ---------- */
	if (!can('groups.create')) { echo json_encode(['ok'=>false,'msg'=>'No permission']); exit; }
	$stmt = $pdo->prepare("INSERT INTO `groups` (name) VALUES (?)");
	$ok   = $stmt->execute([$name]);
}

if ($ok) echo json_encode(['ok'=>true]);
else     echo json_encode(['ok'=>false,'msg'=>'DB error']);