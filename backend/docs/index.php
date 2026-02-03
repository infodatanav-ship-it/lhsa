<?php
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/permissions.php';

if (!can('docs.view')) { http_response_code(403); exit('Access denied'); }

$docs = $pdo->prepare("SELECT * FROM documents WHERE user_id=? ORDER BY uploaded_at DESC");
$docs->execute([$_SESSION['user_id']]);
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>My Documents</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../auth/auth.css">
	<link rel="stylesheet" href="docs.css">
	<link rel="stylesheet" href="modal.css">
	<!-- <link rel="stylesheet" href="pagination.css"> -->
	<link rel="stylesheet" href="./../includes/nav.css">
	<!-- add Font Awesome 6 CDN in <head> -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
	<!-- include jquery library -->
	<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>
<body>

<?php if (!empty($_SESSION['flash'])): ?>
	<div class="error-box"><?= htmlspecialchars($_SESSION['flash']) ?></div>
	<?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div class="wrapper">


	<?php include '../includes/nav.php'; ?>


	<header class="topbar">
		<h1>My Documents</h1>
		<div class="actions">
			<!-- trigger upload modal -->
			<button class="btn btn-primary" id="uploadBtn">＋ Upload File</button>
			<a class="btn btn-outline" href="../admin/users.php">Dashboard</a>
			<a class="btn btn-outline" href="../auth/logout.php">Logout</a>
		</div>
	</header>

	<main class="card-list">
		<div class="table-wrap">
			<table class="tbl">
				<thead><tr><th>Name<th>Size<th>Uploaded<th class="actions-col">Actions</tr></thead>
				<tbody>
					<?php if ($docs->rowCount()): ?>
						<?php foreach ($docs as $d): ?>

							<?php $ext = strtolower(pathinfo($d['filename'], PATHINFO_EXTENSION)); ?>

							<?php 

								// pick FA icon class
								$icon = match($ext){
									'pdf'                                  => 'fa-file-pdf text-danger',
									'doc','docx'                           => 'fa-file-word text-primary',
									'xls','xlsx'                           => 'fa-file-excel text-success',
									'ppt','pptx'                           => 'fa-file-powerpoint text-warning',
									'zip','rar','7z'                       => 'fa-file-zipper text-muted',
									'jpg','jpeg','png','gif','bmp','svg' => 'fa-file-image text-info',
									'mp4','mov','avi','mkv'                => 'fa-file-video text-dark',
									'mp3','wav','flac'                     => 'fa-file-audio text-secondary',
									'txt','md'                             => 'fa-file-lines text-muted',
									default                                => 'fa-file text-muted'
								};
							?>

							<tr>
								<td class="fname">
									<i class="fa-fw fa-regular <?= $icon ?>"></i>

									<span title="<?= htmlspecialchars($d['filename']) ?>">
										<?= htmlspecialchars(ellipsis($d['filename'])) ?>
									</span>

								</td>
								<td><?= humanSize($d['size']) ?></td>
								<td><?= date('Y-m-d H:i', strtotime($d['uploaded_at'])) ?></td>
								<td class="actions-col">
									<a class="icon-btn download" href="download.php?id=<?= $d['id'] ?>" title="Download">⤓</a>
									<a class="icon-btn delete"  href="delete.php?id=<?= $d['id'] ?>" onclick="return confirm('Delete?')" title="Delete">🗑</a>
								</td>
							</tr>


						<?php endforeach; ?>
					<?php else: ?>
						<tr><td colspan="4" class="empty">No documents yet - upload your first file!</td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</main>
</div>

<?php include './uploadFileModal.php'; ?>

<script type="text/javascript" src="loaddata.js"></script>
</body>
</html>