<?php
// api.php
require_once 'config.php';

header('Content-Type: application/json');

// Get all users
if ($_GET['action'] == 'getUsers') {
	$stmt = $pdo->query("SELECT `id`, `username`, `email` FROM `users` ORDER BY `username`");
	$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
	echo json_encode($users);
}

// Get all groups
elseif ($_GET['action'] == 'getGroups') {
	$stmt = $pdo->query("SELECT `id`, `name` FROM `groups` ORDER BY `name`");
	$groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
	echo json_encode($groups);
}

// Get users in a specific group
elseif ($_GET['action'] == 'getGroupUsers' && isset($_GET['group_id'])) {
	$stmt = $pdo->prepare("
		SELECT u.id, u.username, u.email 
		FROM users u 
		INNER JOIN group_users ug ON u.id = ug.user_id 
		WHERE ug.group_id = ? 
		ORDER BY u.username
	");
	$stmt->execute([$_GET['group_id']]);
	$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
	echo json_encode($users);
}

?>