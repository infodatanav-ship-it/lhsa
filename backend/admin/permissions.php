<?php
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/permissions.php';

if (!can('users.manage')) { http_response_code(403); exit('Access denied'); }

/* -------- delete permission -------- */
if (isset($_GET['del_perm'])) {
	$pdo->prepare("DELETE FROM permissions WHERE id = ?")->execute([$_GET['del_perm']]);
	header('Location: permissions.php'); exit;
}

/* -------- delete role_permission -------- */
if (isset($_GET['del_role_perm'])) {
	$pdo->prepare("DELETE FROM role_permissions WHERE id = ?")->execute([$_GET['del_role_perm']]);
	header('Location: permissions.php'); exit;
}

/* -------- fetch permissions -------- */
$permissions = $pdo->query("SELECT id, name, description FROM permissions ORDER BY name")->fetchAll();

/* -------- fetch role_permissions with permission names -------- */
$rolePerms = $pdo->query("
	SELECT rp.id, rp.role, rp.permission_id, p.name AS permission_name
	FROM role_permissions rp
	JOIN permissions p ON p.id = rp.permission_id
	ORDER BY rp.role, p.name
")->fetchAll();

/* group by role */
$rolePermsGrouped = [];
foreach ($rolePerms as $rp) {
	$rolePermsGrouped[$rp['role']][] = $rp;
}
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Manage Permissions</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../auth/auth.css">
	<link rel="stylesheet" href="admin.css">
	<link rel="stylesheet" href="modal.css">
	<link rel="stylesheet" href="./../includes/nav.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="wrapper">

	<?php include '../includes/nav.php'; ?>

	<header class="topbar">
		<h1>Manage Permissions</h1>
		<div class="actions">
			<button class="icon-btn" id="addPermBtn" title="Add Permission">
				<i class="fa-solid fa-plus"></i> Permission
			</button>
			<button class="icon-btn" id="addRolePermBtn" title="Assign Permission to Role">
				<i class="fa-solid fa-user-shield"></i> Role Permission
			</button>
		</div>
	</header>

	<main class="card-list">

		<!-- ======  PERMISSIONS TABLE  ====== -->
		<section class="card">
			<h2>Permissions</h2>
			<div class="table-wrap">
				<table class="tbl">
					<thead>
						<tr>
							<th>ID</th>
							<th>Name</th>
							<th>Description</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($permissions as $perm): ?>
						<tr>
							<td><?= $perm['id'] ?></td>
							<td><?= htmlspecialchars($perm['name']) ?></td>
							<td><?= htmlspecialchars($perm['description'] ?? '') ?></td>
							<td>
								<a href="#" class="edit-perm-btn" data-id="<?= $perm['id'] ?>" data-name="<?= htmlspecialchars($perm['name']) ?>" data-description="<?= htmlspecialchars($perm['description'] ?? '') ?>" title="Edit">
									<i class="fa-solid fa-pen"></i>
								</a>
								<a href="permissions.php?del_perm=<?= $perm['id'] ?>" onclick="return confirm('Delete this permission?');" title="Delete">
									<i class="fa-solid fa-trash"></i>
								</a>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</section>

		<!-- ======  ROLE PERMISSIONS TABLE  ====== -->
		<section class="card">
			<h2>Role Permissions</h2>
			<?php foreach ($rolePermsGrouped as $role => $perms): ?>
				<h3 style="margin-top: 1em; color: #555;"><?= ucfirst(htmlspecialchars($role)) ?></h3>
				<div class="table-wrap">
					<table class="tbl">
						<thead>
							<tr>
								<th>Permission</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($perms as $rp): ?>
							<tr>
								<td><?= htmlspecialchars($rp['permission_name']) ?></td>
								<td>
									<a href="permissions.php?del_role_perm=<?= $rp['id'] ?>" onclick="return confirm('Remove this permission from role?');" title="Remove">
										<i class="fa-solid fa-trash"></i>
									</a>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endforeach; ?>
		</section>

	</main>

</div>

<!-- ======  ADD/EDIT PERMISSION MODAL  ====== -->
<div id="permModal" class="modal">
	<div class="modal-content">
		<span class="close">&times;</span>
		<h2 id="permModalTitle">Add Permission</h2>
		<form id="permForm" method="post" action="permission_save.php">
			<input type="hidden" name="csrf" value="<?= csrf() ?>">
			<input type="hidden" name="id" id="permId">

			<label for="permName">Permission Name</label>
			<input type="text" id="permName" name="name" required placeholder="e.g. users.delete">

			<label for="permDescription">Description</label>
			<input type="text" id="permDescription" name="description" placeholder="Optional">

			<button type="submit">Save</button>
		</form>
	</div>
</div>

<!-- ======  ASSIGN ROLE PERMISSION MODAL  ====== -->
<div id="rolePermModal" class="modal">
	<div class="modal-content">
		<span class="close">&times;</span>
		<h2>Assign Permission to Role</h2>
		<form id="rolePermForm" method="post" action="permission_save.php">
			<input type="hidden" name="csrf" value="<?= csrf() ?>">
			<input type="hidden" name="action" value="add_role_perm">

			<label for="rolePermRole">Role</label>
			<select id="rolePermRole" name="role" required>
				<option value="">-- Select Role --</option>
				<option value="admin">Admin</option>
				<option value="user">User</option>
			</select>

			<label for="rolePermPermId">Permission</label>
			<select id="rolePermPermId" name="permission_id" required>
				<option value="">-- Select Permission --</option>
				<?php foreach ($permissions as $perm): ?>
					<option value="<?= $perm['id'] ?>"><?= htmlspecialchars($perm['name']) ?></option>
				<?php endforeach; ?>
			</select>

			<button type="submit">Assign</button>
		</form>
	</div>
</div>

<script>
$(document).ready(function() {
	const permModal = $('#permModal');
	const rolePermModal = $('#rolePermModal');

	// Add permission
	$('#addPermBtn').click(function(e) {
		e.preventDefault();
		$('#permModalTitle').text('Add Permission');
		$('#permId').val('');
		$('#permName').val('');
		$('#permDescription').val('');
		permModal.show();
	});

	// Edit permission
	$('.edit-perm-btn').click(function(e) {
		e.preventDefault();
		$('#permModalTitle').text('Edit Permission');
		$('#permId').val($(this).data('id'));
		$('#permName').val($(this).data('name'));
		$('#permDescription').val($(this).data('description'));
		permModal.show();
	});

	// Add role permission
	$('#addRolePermBtn').click(function(e) {
		e.preventDefault();
		rolePermModal.show();
	});

	// Close modals
	$('.close').click(function() {
		$(this).closest('.modal').hide();
	});

	$(window).click(function(e) {
		if ($(e.target).hasClass('modal')) {
			$('.modal').hide();
		}
	});
});
</script>

</body>
</html>
