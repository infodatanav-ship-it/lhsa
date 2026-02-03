<?php
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/permissions.php';

if (!can('users.manage')) { http_response_code(403); exit('Access denied'); }
if (!csrf_check($_POST['csrf'])) die('CSRF mismatch');

/* -------- add/edit permission -------- */
if (isset($_POST['name'])) {
	$id = $_POST['id'] ?? null;
	$name = trim($_POST['name']);
	$description = trim($_POST['description'] ?? '');

	if (empty($name)) {
		header('Location: permissions.php'); exit;
	}

	if ($id) {
		// update existing permission
		$stmt = $pdo->prepare("UPDATE permissions SET name=?, description=? WHERE id=?");
		$stmt->execute([$name, $description, $id]);
	} else {
		// insert new permission
		$stmt = $pdo->prepare("INSERT INTO permissions (name, description) VALUES (?, ?)");
		$stmt->execute([$name, $description]);
	}

	header('Location: permissions.php'); exit;
}

/* -------- assign permission to role -------- */
if (isset($_POST['action']) && $_POST['action'] === 'add_role_perm') {
	$role = trim($_POST['role']);
	$permissionId = intval($_POST['permission_id']);

	if ($role && $permissionId) {
		$stmt = $pdo->prepare("INSERT IGNORE INTO role_permissions (role, permission_id) VALUES (?, ?)");
		$stmt->execute([$role, $permissionId]);
	}

	header('Location: permissions.php'); exit;
}

header('Location: permissions.php'); exit;
