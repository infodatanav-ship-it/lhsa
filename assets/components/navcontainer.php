<?php
// Nav container: expose Files link when permitted
$permFile = __DIR__ . '/../../backend/browser_permissions.php';
if (file_exists($permFile)) require_once $permFile;
$can = function_exists('userCanBrowse') ? userCanBrowse() : (!empty($_SESSION['user_id'] ?? false));
if ($can): ?>
	<nav class="nav-container">
		<ul>
			<li class="nav-item"><a href="./backend/browser.php" class="nav-link">Files</a></li>
		</ul>
	</nav>
<?php endif; ?>
