<?php
// api.php
require_once 'config.php';

header('Content-Type: application/json');

// Add user to group
if ($_POST['action'] == 'addUserToGroup') {

	$user_id = $_POST['user_id'];
	$group_id = $_POST['group_id'];

	// var_dump($user_id, $group_id);

	try {
		$stmt = $pdo->prepare("INSERT INTO group_users (user_id, group_id) VALUES (?, ?)");
		$stmt->execute([$user_id, $group_id]);
		
		echo json_encode(['success' => true, 'message' => 'User added to group successfully']);
	} catch (PDOException $e) {
		echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
	}
}

// Remove user from group
elseif ($_POST['action'] == 'removeUserFromGroup') {

	$user_id = $_POST['user_id'];
	$group_id = $_POST['group_id'];
	
	try {
		$stmt = $pdo->prepare("DELETE FROM `group_users` WHERE `user_id` = ? AND `group_id` = ?");
		$stmt->execute([$user_id, $group_id]);
		
		echo json_encode(['success' => true, 'message' => 'User removed from group successfully']);
	} catch (PDOException $e) {
		echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
	}
}
?>