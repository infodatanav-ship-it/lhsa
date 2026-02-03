<?php
require_once __DIR__.'/../includes/auth.php';
require_once __DIR__.'/../includes/permissions.php';
$currentPage = 'docs';
$folderId = isset($_GET['f']) ? (int)$_GET['f'] : 0;   // current folder

/* breadcrumb */
$crumbs = [];
$pid = $folderId;

$stmt = $pdo->prepare("SELECT `id`, `filename` FROM `documents` WHERE `is_folder` = ?");
if ($stmt && $stmt->execute([1])) {
	$folders = $stmt->fetchAll();
} else {
	$folders = [];
}

// var_dump($folders);
$folderList = [];

foreach ($folders as $folder) {
	$folderList[] = ['id' => $folder['id'], 'filename' => $folder['filename']];
}

// var_dump($folderList);

while ($pid) {
	$folderRow = $pdo->prepare("SELECT `id`, `filename` FROM `documents` WHERE `id` = ? AND `is_folder` = 1")->fetch();
	if (!$folderRow) break;
	array_unshift($crumbs, $folderRow);
	$pid = $folderRow['parent_id'] ?? 0;
}

/* items inside current folder */
$stmt = $pdo->prepare("SELECT d.id, d.filename, d.is_folder, d.size, d.uploaded_at, u.username,
			(SELECT COUNT(*) FROM documents c WHERE c.parent_id = d.id) AS children
	FROM documents d
	JOIN users u ON u.id = d.user_id
	WHERE d.user_id = ?
	ORDER BY d.is_folder DESC, d.filename");
if ($stmt && $stmt->execute([$_SESSION['user_id']])) {
	$items = $stmt->fetchAll();
} else {
	$items = [];
}

?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Documents - Tree</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="../auth/auth.css">
	<link rel="stylesheet" href="../includes/nav.css">
	<link rel="stylesheet" href="tree.css">
	<link rel="stylesheet" href="modal.css">
	<!-- <link rel="stylesheet" href="members.css"> -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="tree.js"></script>
</head>
<body>

<div class="wrapper">

	<?php include '../includes/nav.php'; ?>
	<header class="topbar">
		<h1>Documents</h1>
		<div class="actions">
			<button class="btn btn-primary" id="addFolderBtn">+ New Folder</button>
			<button class="btn btn-primary" id="uploadBtn">+ Upload File</button>
			<a class="btn btn-outline" href="tree.php">Root</a>
		</div>
	</header>

	<!-- breadcrumb -->
	<nav class="breadcrumb">
		<a href="tree.php"><i class="fa-solid fa-house"></i> Root</a>
		<?php foreach ($crumbs as $c): ?>
			/ <a href="tree.php?f=<?= $c['id'] ?>"><?= htmlspecialchars($c['filename']) ?></a>
		<?php endforeach; ?>
	</nav>

	<!-- tree -->
	<div class="tree" id="treeView">
		<?php foreach ($items as $it): renderNode($it); endforeach; ?>
	</div>
</div>

<!-- ====== MODALS ====== -->
<?php include 'tree_modals.php'; ?>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="tree.js"></script>
</body>
</html>

<?php
/* recursive node (only 1 level rendered – children loaded on expand) */
function renderNode($row, $depth = 0): void
{
	$margin = $depth * 24;
	$ext = strtolower(pathinfo($row['filename'], PATHINFO_EXTENSION));
	$icon = $row['is_folder']
		? 'fa-folder text-warning'
		: (match($ext){
			'pdf'=>'fa-file-pdf text-danger','doc'=>'fa-file-word text-primary',
			'jpg'=>'fa-file-image text-info','zip'=>'fa-file-zipper text-muted',default=>'fa-file text-muted'
		});
	?>
	<div class="node" style="margin-left:<?= $margin ?>px" data-id="<?= $row['id'] ?>" data-folder="<?= $row['is_folder'] ?>">
		<span class="toggle <?= $row['is_folder'] ? 'fa-fw fa-solid fa-chevron-right' : '' ?>"></span>
		<span class="icon"><i class="fa-fw fa-regular <?= $icon ?>"></i></span>
		<span class="name" title="<?= htmlspecialchars($row['filename']) ?>">
			<?= htmlspecialchars(ellipsis($row['filename'])) ?>
		</span>
		<?php if (!$row['is_folder']): ?>
			<span class="size"><?= humanSize($row['size']) ?></span>
			<span class="date"><?= date('Y-m-d', strtotime($row['uploaded_at'])) ?></span>
			<span class="actions">
				<a href="download.php?id=<?= $row['id'] ?>" title="Download"><i class="fa-solid fa-download"></i></a>
				<a href="#" class="delFile" data-id="<?= $row['id'] ?>" title="Delete"><i class="fa-solid fa-trash"></i></a>
			</span>
		<?php endif; ?>
	</div>
<?php } ?>