<?php
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/permissions.php';

if (!can('users.view')) { http_response_code(403); exit('Access denied'); }

/* -------- delete -------- */
if (isset($_GET['del'])) {
	if (!can('users.delete')) exit('No permission');
	$pdo->prepare("DELETE FROM users WHERE id = ? AND id != ?")->execute([$_GET['del'], $_SESSION['user_id']]);
	header('Location: users.php'); exit;
}

/* -------- pagination vars -------- */
$page     = isset($_GET['page'])     ? max(1, intval($_GET['page'])) : 1;
$pageSize = isset($_GET['pageSize']) ? intval($_GET['pageSize'])    : 10;
$offset   = ($page - 1) * $pageSize;

/* -------- total users -------- */
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalPages = max(1, ceil($totalUsers / $pageSize));

/* -------- page of users -------- */
$users = $pdo->prepare("SELECT id, username, email, role, created_at
						FROM users
					ORDER BY created_at DESC
						LIMIT :limit OFFSET :offset");
$users->bindValue(':limit',  $pageSize,  PDO::PARAM_INT);
$users->bindValue(':offset', $offset,    PDO::PARAM_INT);
$users->execute();
$users = $users->fetchAll();
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Manage Users</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../auth/auth.css">
	<link rel="stylesheet" href="admin.css">
	<link rel="stylesheet" href="modal.css">
	<link rel="stylesheet" href="pagination.css">
	<link rel="stylesheet" href="./../includes/nav.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

	<!-- include jquery library -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
<div class="wrapper">

	<?php include '../includes/nav.php'; ?>

	<header class="topbar">
		<h1>Manage Users</h1>

		<div class="actions">

			<a class="icon-btn users" href="#" title="Members" id="addUserBtn">
				<i class="fa-solid fa-user-check"></i>
			</a>

			<a class="icon-btn groups" href="#" id="addGroupBtn">
				<i class="fa-solid fa-user-group"></i>
			</a>

			<a class="icon-btn members" href="#" title="Members" id="manageMembersBtn">
				<i class="fa-solid fa-users-viewfinder"></i>
			</a>

		</div>


	</header>

	<main class="card-list">

		<!-- ----------------------------------- -->

		<!-- page controls -->
		<div class="pageBar">
			<div class="pageSize">
				<label>Show
					<select id="pageSize" onchange="location='?page=<?= $page ?>&pageSize='+this.value">
						<option value="10"  <?= $pageSize==10 ? 'selected' : '' ?>>10</option>
						<option value="25"  <?= $pageSize==25 ? 'selected' : '' ?>>25</option>
						<option value="50"  <?= $pageSize==50 ? 'selected' : '' ?>>50</option>
					</select> entries
				</label>
			</div>

			<div class="pageLinks">
				<?php if ($page > 1): ?>
					<a href="?page=<?= $page-1 ?>&pageSize=<?= $pageSize ?>">Prev</a>
				<?php else: ?>
					<span class="disabled">Prev</span>
				<?php endif; ?>

				<?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
					<a class="<?= $p === $page ? 'active' : '' ?>" href="?page=<?= $p ?>&pageSize=<?= $pageSize ?>"><?= $p ?></a>
				<?php endfor; ?>

				<?php if ($page < $totalPages): ?>
					<a href="?page=<?= $page+1 ?>&pageSize=<?= $pageSize ?>">Next</a>
				<?php else: ?>
					<span class="disabled">Next</span>
				<?php endif; ?>
			</div>
		</div>

		<!-- ----------------------------------- -->

		<div class="table-wrap">
			<table class="tbl">
				<thead><tr><th>ID<th>Username<th>Email<th>Role<th>Created<th class="actions-col">Actions</tr></thead>
				<tbody>
				<?php foreach ($users as $u): ?>
					<tr>
						<td><?= $u['id'] ?></td>
						<td><?= htmlspecialchars($u['username']) ?></td>
						<td><?= htmlspecialchars($u['email']) ?></td>
						<td><span class="pill role-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
						<td><?= date('Y-m-d', strtotime($u['created_at'])) ?></td>
						<td class="actions-col">
							<a class="icon-btn edit"  href="user_form.php?id=<?= $u['id'] ?>" title="Edit">✎</a>
							<a class="icon-btn delete" href="?del=<?= $u['id'] ?>" onclick="return confirm('Delete?')" title="Delete">🗑</a>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</main>
</div>

<!-- ======  ADD-USER MODAL  ====== -->
<div id="addModal" class="modal">
	<div class="modal-content">
		<span class="modal-close">&times;</span>
		<h2>Add User</h2>

		<form id="addUserForm" method="post" action="user_save.php" class="user-form">
			<input type="hidden" name="csrf" value="<?= csrf() ?>">

			<div class="form-group">
				<label>Username</label>
				<input type="text" name="username" required minlength="3">
			</div>

			<div class="form-group">
				<label>Email</label>
				<input type="email" name="email" required>
			</div>

			<div class="form-group">
				<label>Role</label>
				<select name="role">
					<option value="user">User</option>
					<option value="admin">Admin</option>
				</select>
			</div>

			<div class="form-group">
				<label>Password</label>
				<input type="password" name="password" required minlength="6">
			</div>

			<button type="submit" class="btn btn-primary">Create User</button>
		</form>
	</div>
</div>

<!-- ======  ADD / EDIT MODAL  ====== -->
<div id="groupModal" class="modal">
	<div class="modal-content">
		<span class="modal-close">&times;</span>
		<h2 id="modalTitle">Add Group</h2>
		<form id="groupForm">
			<input type="hidden" name="csrf" value="<?= csrf() ?>">
			<input type="hidden" name="group_id" id="group_id" value="">
			<div class="form-group">
				<label>Name</label>
				<input type="text" name="name" id="gname" required maxlength="60">
			</div>
			<button type="submit" class="btn btn-primary">Save</button>
		</form>
	</div>
</div>

<!-- ======  GROUP-MEMBERS MODAL  ====== -->
<div id="membersModal" class="modal">
	<div class="modal-content lg">
		<span class="modal-close">&times;</span>
		<h2 id="membersTitle">Manage Members</h2>

		<form id="membersForm">
			<input type="hidden" name="csrf" value="<?= csrf() ?>">
			<input type="hidden" name="group_id" id="group_id" value="">

			<div class="dual-wrap">
				<!-- Available users -->
				<div class="dual-box">
					<h3>Availablea</h3>
					<select id="availList" class="dual-list" multiple></select>
				</div>

				<!-- move buttons -->
				<div class="dual-arrows">
					<button type="button" id="addBtn"  class="btn-icon"><i class="fa-solid fa-chevron-right"></i>a</button>
					<button type="button" id="remBtn"  class="btn-icon"><i class="fa-solid fa-chevron-left"></i>b</button>
				</div>

				<!-- Current members -->
				<div class="dual-box">
					<h3>Members</h3>
					<select id="memberList" class="dual-list" multiple></select>
				</div>
			</div>

			<button type="submit" class="btn btn-primary">Save Changes</button>
		</form>
	</div>
</div>

<script type="text/javascript" src="./groups.js"></script>
<script type="text/javascript" src="./users.js"></script>
</body>
</html>