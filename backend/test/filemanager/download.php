<?php

// var_dump('tttt');

if(isset($_POST['file']) && $_POST['action'] === 'download') {
	$filename = basename($_POST['file']);
	$filepath = 'files/' . $filename;
	
	if(file_exists($filepath)) {
		// Set headers for file download
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: ' . filesize($filepath));
		
		// Clear output buffer
		if (ob_get_level()) {
			ob_end_clean();
		}
		
		readfile($filepath);
		exit;
	} else {
		http_response_code(404);
		echo json_encode(['error' => 'File not found']);
	}
} else {
	http_response_code(400);
	echo json_encode(['error' => 'Invalid request']);
}

/*
if(isset($_GET['file'])) {

	$filename = basename($_GET['file']);
	$filepath = 'files/' . $filename;
	
	// Check if file exists
	if (!file_exists($filepath)) {
		die("File not found.");
	}

	// Get file information
	$filename = basename($filepath);
	$filesize = filesize($filepath);
	$file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	
	// Set headers to force download
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=\"$filename\"");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: " . $filesize);
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: 0");
	header("Pragma: no-cache");
	
	// Clear output buffer
	while (ob_get_level()) {
		ob_end_clean();
	}
	
	// Read the file
	readfile($filepath);
	exit;

} else {
	http_response_code(400);
	echo "No file specified.";
}
?>*/