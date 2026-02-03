<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lhsa_web');


// define('DB_HOST', 'localhost');
// define('DB_USER', 'lovestyl_datanav');
// define('DB_PASS', 'bMasS6Q99LWZ8fYbwsTF');
// define('DB_NAME', 'lovestyl_datanav');


// Create database connection
try {
	$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	die("Connection failed: " . $e->getMessage());
}

// Function to get files by parent_id
function getFilesByParentId($pdo, $parent_id = null) {
	$sql = "SELECT * FROM files WHERE parent_id " . ($parent_id ? "= ?" : "IS NULL") . " ORDER BY type DESC, name ASC";
	$stmt = $pdo->prepare($sql);
	
	if ($parent_id) {
		$stmt->execute([$parent_id]);
	} else {
		$stmt->execute();
	}
	
	return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get breadcrumb trail
function getBreadcrumb($pdo, $current_id) {
	$breadcrumb = [];
	
	if (!$current_id) {
		return [['id' => null, 'name' => 'Root']];
	}
	
	$current = $pdo->prepare("SELECT * FROM files WHERE id = ?");
	$current->execute([$current_id]);
	$file = $current->fetch(PDO::FETCH_ASSOC);
	
	if ($file) {
		if ($file['parent_id']) {
			$breadcrumb = array_merge($breadcrumb, getBreadcrumb($pdo, $file['parent_id']));
		} else {
			$breadcrumb[] = ['id' => null, 'name' => 'Root'];
		}
		$breadcrumb[] = ['id' => $file['id'], 'name' => $file['name']];
	}
	
	return $breadcrumb;
}
?>