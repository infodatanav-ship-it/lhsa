<?php
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/permissions.php';

$currency = 'R';
// $incomes['total'] = $pdo->query("SELECT SUM(amount) AS total FROM incomes")->fetchColumn() ?: '0.00';
$incomes['total'] = '0.00';

// Provide safe defaults in case previous data-loading code wasn't run
$stats = $stats ?? ['users' => 0, 'docs' => 0, 'grps' => 0];
$latestUsers = $latestUsers ?? [];
$latestDocs  = $latestDocs  ?? [];
$groups      = $groups      ?? [];

$latestUsers = $pdo->query("SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();

$latestDocs = $pdo->query(
	"SELECT d.*, u.username, GROUP_CONCAT(g.name SEPARATOR ', ') AS group_names
	 FROM documents d
	 JOIN users u ON d.user_id = u.id
	 LEFT JOIN document_groups dg ON dg.document_id = d.id
	 LEFT JOIN `groups` g ON g.id = dg.group_id
	 GROUP BY d.id
	 ORDER BY d.uploaded_at DESC
	 LIMIT 5"
)->fetchAll();

/* ------- all groups for upload dropdown ------- */
// $allGroups = $pdo->query("SELECT `id`,`name`,`created_at` FROM `groups` ORDER BY `name`;")->fetchAll();

$groupQuery = "SELECT `group_id`, COUNT(`group_id`) as `Number`, `groups`.`name`, `groups`.`created_at`
FROM `group_users`
INNER JOIN `groups` ON `groups`.`id` = `group_users`.`group_id` 
GROUP BY `group_id`
HAVING COUNT(*) > 0
ORDER BY `group_id`;";

$allGroups = $pdo->query($groupQuery)->fetchAll();

?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Dashboard</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../auth/auth.css">
	<link rel="stylesheet" href="../docs/docs.css">
	<!-- re-use table styles -->
	<link rel="stylesheet" href="./dashboard.css">
	<link rel="stylesheet" href="modal.css">
	<link rel="stylesheet" href="./../includes/nav.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
	<style>
	/* override modal top margin so dashboard modal centers exactly */
	#uploadModal .modal-content { margin-top: 0; }
	</style>
</head>
<body>

<div style="display: flex; flex-direction: column; align-items: center; border: 1px solid #000000; max-width: 1600px;">

	<?php 
		// include '../includes/nav.php';
	?>
</div>

<div class="wrapper">

	<?php include '../includes/nav.php'; ?>

	<!-- ======  COUNT CARDS  ====== -->
	<section class="cards">
		<?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
		<div class="card-count">
			<i class="fa-solid fa-users"></i>
			<div>
				<div class="num"><?= $stats['users'] ?></div>
				<div class="label">Users</div>
			</div>
		</div>
		<?php endif; ?>
		<div class="card-count">
			<i class="fa-solid fa-file-lines"></i>
			<div>
				<div class="num"><?= $stats['docs'] ?></div>
				<div class="label">Documents</div>
			</div>
		</div>
		<?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
		<div class="card-count">
			<i class="fa-solid fa-layer-group"></i>
			<div>
				<div class="num"><?= $stats['grps'] ?></div>
				<div class="label">Groups</div>
			</div>
		</div>
		<?php endif; ?>
		<?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
		<div class="card-count">
			<i class="fa-solid fa-money-bill"></i>
			<div>
				<div class="num"><?= $currency . ' ' . $incomes['total'] ?></div>
				<div class="label">Income</div>
			</div>
		</div>
		<?php endif; ?>
	</section>

	<!-- ======  LATEST USERS  ====== -->
	<?php if (can('users.view')): ?>
		<section class="card-list">
			<h3>Latest Users</h3>
			<div class="table-wrap">
				<table class="tbl">
					<thead><tr><th>ID<th>Username<th>Email<th>Role<th>Joined</tr></thead>
					<tbody>
					<?php foreach ($latestUsers as $u): ?>
						<tr>
							<td><?= $u['id'] ?></td>
							<td><?= htmlspecialchars($u['username']) ?></td>
							<td><?= htmlspecialchars($u['email']) ?></td>
							<td><span class="pill role-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
							<td><?= date('Y-m-d', strtotime($u['created_at'])) ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</section>
	<?php endif; ?>

	<!-- ======  LATEST DOCUMENTS  ====== -->
	<section class="card-list">
		<h3 style="display:flex;align-items:center;justify-content:space-between;">
			<span>Latest Documents</span>
			<?php if (can('documents.upload')): ?>
				<button id="uploadDocBtn" class="btn btn-primary" style="margin-left:12px;padding:6px 10px;font-size:13px;width: 15%;">＋ Upload</button>
			<?php endif; ?>
		</h3>
		<div class="table-wrap">
			<table class="tbl">
				<thead><tr><th>Name<th>Owner<th>Group<th>Size<th>Uploaded</tr></thead>
				<tbody>
				<?php foreach ($latestDocs as $d):
					$ext = strtolower(pathinfo($d['filename'], PATHINFO_EXTENSION));
					$icon = match($ext){
						'pdf'=>'fa-file-pdf text-danger','doc','docx'=>'fa-file-word text-primary',
						'xls','xlsx'=>'fa-file-excel text-success','ppt','pptx'=>'fa-file-powerpoint text-warning',
						'zip','rar'=>'fa-file-zipper text-muted','jpg','jpeg','png','gif'=>'fa-file-image text-info',
						'mp4','mov'=>'fa-file-video text-dark','mp3','wav'=>'fa-file-audio text-secondary',
						'txt','md'=>'fa-file-lines text-muted',default=>'fa-file text-muted'
					};
				?>
					<tr>
						<td class="fname">
							<i class="fa-fw fa-regular <?= $icon ?>"></i>
							<span title="<?= htmlspecialchars($d['filename']) ?>">
								<?= htmlspecialchars(ellipsis($d['filename'])) ?>
							</span>
						</td>
						<td><?= htmlspecialchars($d['username']) ?></td>
						<td><?= htmlspecialchars($d['group_names'] ?? '-') ?></td>
						<td><?= humanSize($d['size']) ?></td>
						<td><?= date('Y-m-d H:i', strtotime($d['uploaded_at'])) ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</section>

	<!-- ======  GROUPS  ====== -->
	<?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
	<section class="card-list">
		<h3>Groups</h3>
		<div class="table-wrap">
			<table class="tbl">
				<thead><tr><th>ID</th><th>Name</th><th>Count</th><th>Created</th></tr></thead>
				<tbody>
				<?php foreach ($allGroups as $g): ?>
					<tr>
						<td><?= htmlspecialchars($g['group_id']) ?></td>
						<td><?= htmlspecialchars($g['name']) ?></td>
						<td><?= htmlspecialchars($g['Number']) ?></td>
						<td><?= date('Y-m-d', strtotime($g['created_at'])) ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</section>
	<?php endif; ?>

<!-- ======  UPLOAD MODAL (for dashboard) ====== -->
<?php if (can('documents.upload')): ?>

	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script>
		$(function(){
			var $modal = $('#uploadModal');
			console.log('upload modal js loaded');
			$('#uploadDocBtn').on('click', function(){ 
				console.log('show upload modal');
				$modal.addClass('show');
			});
			$modal.on('click', '.modal-close', function(){ $modal.removeClass('show'); });
			// hide when clicking outside
			$modal.on('click', function(e){ if (e.target === this) $modal.removeClass('show'); });
		});
	</script>


	<div id="uploadModal" class="modal">
		<div class="modal-content">
			<span class="modal-close">&times;</span>
			<h2>Upload Document</h2>

			<form id="uploadForm" method="post" action="../docs/upload_save.php" enctype="multipart/form-data" class="user-form">
				<input type="hidden" name="csrf" value="<?= csrf() ?>">

				<div class="form-group">
					<label>Choose file (max 5 MBs)</label>
					<input type="file" name="file" required accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.txt,.zip">
				</div>

				<div class="form-group">
					<label>Group Permissions (choose one or more)</label>
					<select name="groups[]" id="upload-groups" multiple>
						<?php foreach ($allGroups as $ag): ?>
							<option value="<?= $ag['group_id'] ?>"><?= htmlspecialchars($ag['name']) ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<button type="submit" class="btn btn-primary">Upload</button>
			</form>
		</div>
	</div>


<?php endif; ?>
</div>
</body>
</html>