<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$parent_id = isset($_GET['parent_id']) ? (int)$_GET['parent_id'] : null;
	
	try {
		$files = getFilesByParentId($pdo, $parent_id);
		
		// Format file sizes
		foreach ($files as &$file) {
			$file['formatted_size'] = formatFileSize($file['size']);
			$file['icon'] = getFileIcon($file['type'], $file['name']);
		}
		
		// Get breadcrumb if parent_id is provided
		$breadcrumb = [];
		if ($parent_id) {
			$breadcrumb = getBreadcrumb($pdo, $parent_id);
		} else {
			$breadcrumb = [['id' => null, 'name' => 'Root']];
		}
		
		echo json_encode([
			'success' => true,
			'files' => $files,
			'breadcrumb' => $breadcrumb
		]);
		
	} catch (Exception $e) {
		echo json_encode([
			'success' => false,
			'message' => 'Error fetching files: ' . $e->getMessage()
		]);
	}
}

function formatFileSize($bytes) {
	if ($bytes == 0) return '0 B';
	
	$sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
	$i = floor(log($bytes, 1024));
	
	return round($bytes / pow(1024, $i), 2) . ' ' . $sizes[$i];
}

function getFileIcon($type, $name) {
	if ($type === 'directory') {
		return '📁';
	}
	
	$extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
	
	$icons = [
		'pdf' => '📄',
		'txt' => '📝',
		'jpg' => '🖼️',
		'jpeg' => '🖼️',
		'png' => '🖼️',
		'gif' => '🖼️',
		'js' => '📜',
		'html' => '🌐',
		'css' => '🎨',
		'zip' => '📦',
		'doc' => '📘',
		'docx' => '📘'
	];
	
	return $icons[$extension] ?? '📄';
}
?>