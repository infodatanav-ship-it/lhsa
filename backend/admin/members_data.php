<?php
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/permissions.php';

if (!can('groups.edit')) { http_response_code(403); exit(json_encode(['ok'=>false,'msg'=>'No permission'])); }

$gid = (int)($_GET['group_id'] ?? 0);

$avail = $pdo->prepare(
	"SELECT id, username FROM users
	WHERE id NOT IN (SELECT user_id FROM group_users WHERE group_id = ?)
ORDER BY username");
$avail->execute([$gid]);

$members = $pdo->prepare(
	"SELECT u.id, u.username
	FROM users u
	JOIN group_users gu ON gu.user_id = u.id
	WHERE gu.group_id = ?
ORDER BY u.username");
$members->execute([$gid]);

echo json_encode([
	'ok'      => true,
	'avail'   => $avail->fetchAll(PDO::FETCH_ASSOC),
	'members' => $members->fetchAll(PDO::FETCH_ASSOC)
]);