<?php
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/permissions.php';

// var_dump($_SESSION);
// var_dump(can('users.view'));
// var_dump(can('dashboard.view'));

if (!can('users.view')) {
    http_response_code(403); exit('Access denied');
}

/* ------- counts ------- */
$stats = $pdo->query("SELECT
        (SELECT COUNT(*) FROM users)        AS users,
        (SELECT COUNT(*) FROM documents)    AS docs,
        (SELECT COUNT(*) FROM `groups`)     AS grps")->fetch();

/* ------- latest users ------- */
$latestUsers = $pdo->query(
    "SELECT id,username,email,role,created_at
       FROM users
   ORDER BY created_at DESC
      LIMIT 5")->fetchAll();

/* ------- latest docs (with owner) ------- */
$latestDocs = $pdo->query(
    "SELECT d.id, d.filename, d.size, d.uploaded_at, u.username
       FROM documents d
       JOIN users u ON u.id = d.user_id
   ORDER BY d.uploaded_at DESC
      LIMIT 5")->fetchAll();

/* ------- groups + member count ------- */
$groups = $pdo->query(
    "SELECT g.id, g.name, g.created_at, COUNT(gu.user_id) AS members
       FROM `groups` g
  LEFT JOIN group_users gu ON gu.group_id = g.id
   GROUP BY g.id
   ORDER BY g.name")->fetchAll();

$currency = 'R';
// $incomes['total'] = $pdo->query("SELECT SUM(amount) AS total FROM incomes")->fetchColumn() ?: '0.00';
$incomes['total'] = '0.00';

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
	<link rel="stylesheet" href="./../includes/nav.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
		<div class="card-count">
			<i class="fa-solid fa-users"></i>
			<div>
				<div class="num"><?= $stats['users'] ?></div>
				<div class="label">Users</div>
			</div>
		</div>
		<div class="card-count">
			<i class="fa-solid fa-file-lines"></i>
			<div>
				<div class="num"><?= $stats['docs'] ?></div>
				<div class="label">Documents</div>
			</div>
		</div>
		<div class="card-count">
			<i class="fa-solid fa-layer-group"></i>
			<div>
				<div class="num"><?= $stats['grps'] ?></div>
				<div class="label">Groups</div>
			</div>
		</div>
		<div class="card-count">
			<i class="fa-solid fa-money-bill"></i>
			<div>
				<div class="num"><?= $currency . ' ' . $incomes['total'] ?></div>
				<div class="label">Income</div>
			</div>
		</div>
	</section>

	<!-- ======  LATEST USERS  ====== -->
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

	<!-- ======  LATEST DOCUMENTS  ====== -->
	<section class="card-list">
		<h3>Latest Documents</h3>
		<div class="table-wrap">
			<table class="tbl">
				<thead><tr><th>Name<th>Owner<th>Size<th>Uploaded</tr></thead>
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
						<td><?= humanSize($d['size']) ?></td>
						<td><?= date('Y-m-d H:i', strtotime($d['uploaded_at'])) ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</section>

	<!-- ======  GROUPS  ====== -->
	<section class="card-list">
		<h3>Groups</h3>
		<div class="table-wrap">
			<table class="tbl">
				<thead><tr><th>ID<th>Name<th>Members<th>Created</tr></thead>
				<tbody>
				<?php foreach ($groups as $g): ?>
					<tr>
						<td><?= $g['id'] ?></td>
						<td><?= htmlspecialchars($g['name']) ?></td>
						<td><?= $g['members'] ?></td>
						<td><?= date('Y-m-d', strtotime($g['created_at'])) ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</section>
</div>
</body>
</html>