<?php
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/permissions.php';

if (!can('groups.view')) { http_response_code(403); exit('Access denied'); }

/* ---- delete ---- */
if (isset($_POST['delete_id'])) {
    if (!can('groups.delete'))  exit(json_encode(['ok'=>false,'msg'=>'No permission']));
    $pdo->prepare("DELETE FROM `groups` WHERE id = ?")->execute([$_POST['delete_id']]);
    exit(json_encode(['ok'=>true]));
}

$groups = $pdo->query(
    "SELECT g.id, g.name, g.created_at, COUNT(gu.user_id) AS members
       FROM `groups` g
  LEFT JOIN group_users gu ON gu.group_id = g.id
   GROUP BY g.id
   ORDER BY g.name")->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Manage Groups</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../auth/auth.css">
	<link rel="stylesheet" href="../docs/docs.css">
	<link rel="stylesheet" href="members.css">
	<link rel="stylesheet" href="modal.css">
	<link rel="stylesheet" href="pagination.css">
	<link rel="stylesheet" href="./../includes/nav.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="wrapper">

	<?php include '../includes/nav.php'; ?>

	<!-- <header class="topbar">
		<h1>Groups</h1>
		<div class="actions">
			<?php if (can('groups.create')): ?>
				<button class="btn btn-primary" id="addGroupBtn">＋ Add Group</button>
			<?php endif; ?>


			<a class="icon-btn users" href="#" title="Members" data-id="<?= $g['id'] ?>" data-name="<?= htmlspecialchars($g['name']) ?>">
				<i class="fa-solid fa-user-check"></i>
			</a>


			<a class="btn btn-outline" href="dashboard.php">Dashboard</a>
			<a class="btn btn-outline" href="../auth/logout.php">Logout</a>
		</div>
	</header> -->

	<main class="card-list">
		<div class="table-wrap">
			<table class="tbl">
				<thead><tr><th>ID<th>Name<th>Members<th>Created<?php if (can('groups.edit')||can('groups.delete')): ?><th class="actions-col">Actions<?php endif; ?></tr></thead>
				<tbody>
				<?php foreach ($groups as $g): ?>
					<tr data-id="<?= $g['id'] ?>">
						<td><?= $g['id'] ?></td>
						<td class="gname"><?= htmlspecialchars($g['name']) ?></td>
						<td><?= $g['members'] ?></td>
						<td><?= date('Y-m-d', strtotime($g['created_at'])) ?></td>
						<?php if (can('groups.edit')||can('groups.delete')): ?>
							<td class="actions-col">
								<?php if (can('groups.edit')):   ?><a class="icon-btn edit"   title="Edit"   href="#"><i class="fa-solid fa-pen"></i></a><?php endif; ?>
								<?php if (can('groups.delete')): ?><a class="icon-btn delete" title="Delete" href="#"><i class="fa-solid fa-trash"></i></a><?php endif; ?>
							</td>
						<?php endif; ?>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</main>
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="groups.js"></script>
</body>
</html>


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
					<h3>Available</h3>
					<select id="availList" class="dual-list" multiple></select>
				</div>

				<!-- move buttons -->
				<div class="dual-arrows">
					<button type="button" id="addBtn"  class="btn-icon"><i class="fa-solid fa-chevron-right"></i></button>
					<button type="button" id="remBtn"  class="btn-icon"><i class="fa-solid fa-chevron-left"></i></button>
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