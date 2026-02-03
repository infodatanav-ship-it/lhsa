<?php
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/permissions.php';

header('Content-Type: application/json');

if (!can('groups.edit')) { echo json_encode(['ok'=>false,'msg'=>'No permission']); exit; }
if (!csrf_check($_POST['csrf'] ?? '')) { echo json_encode(['ok'=>false,'msg'=>'CSRF']); exit; }

$gid   = (int)($_POST['group_id'] ?? 0);
$users = array_map('intval', (array)($_POST['users'] ?? []));

$pdo->beginTransaction();
// remove old
$pdo->prepare("DELETE FROM group_users WHERE group_id = ?")->execute([$gid]);
// insert new
$stmt = $pdo->prepare("INSERT INTO group_users (group_id, user_id) VALUES (?, ?)");
foreach ($users as $uid) $stmt->execute([$gid, $uid]);
$pdo->commit();

echo json_encode(['ok'=>true]);