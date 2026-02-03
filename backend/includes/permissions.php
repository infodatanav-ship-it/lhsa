<?php
/* load permissions for current user into $_SESSION once per login */
function loadPermissions(PDO $pdo, string $role): void
{
	$sql = "SELECT p.name
			FROM permissions p
			JOIN role_permissions rp ON rp.permission_id = p.id
			WHERE rp.role = ?";
	try {
		$stmt = $pdo->prepare($sql);
		$stmt->execute([$role]);
		$_SESSION['permissions'] = array_column($stmt->fetchAll(), 'name');
	} catch (PDOException $e) {
		// If the permissions table is missing or another DB error occurs,
		// fall back to sensible defaults: admins get a wildcard, others get none.
		if ($role === 'admin') {
			$_SESSION['permissions'] = ['*'];
		} else {
			$_SESSION['permissions'] = [];
		}
	}
}

/* check permission anywhere */
function can(string $permission): bool
{
	$perms = $_SESSION['permissions'] ?? [];
	if (in_array('*', $perms, true)) return true;
	return in_array($permission, $perms, true);
}