<?php
require_once __DIR__.'/../includes/auth.php';
header('Content-Type: application/json');

$parent = (int)($_GET['parent'] ?? 0);
$items  = $pdo->prepare(
	"SELECT d.id, d.filename, d.is_folder, d.size, d.uploaded_at,
			(SELECT COUNT(*) FROM documents c WHERE c.parent_id = d.id) AS children
	FROM documents d
	WHERE d.parent_id = ? AND d.user_id = ?
ORDER BY d.is_folder DESC, d.filename");
$items->execute([$parent, $_SESSION['user_id']]);

echo json_encode([
	'ok'    => true,
	'items' => array_map(fn($r)=>[
		'id'       => $r['id'],
		'filename' => $r['filename'],
		'is_folder'=> (int)$r['is_folder'],
		'ext'      => strtolower(pathinfo($r['filename'], PATHINFO_EXTENSION)),
		'hsize'    => humanSize($r['size']),
		'date'     => date('Y-m-d', strtotime($r['uploaded_at']))
	], $items->fetchAll())
]);