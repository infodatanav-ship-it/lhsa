<?php
require_once __DIR__.'/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['type'] == 'folders' ) {

	$directories = $pdo->query("SELECT `id`, `name`, `type`, `size`, `parent_id`, `file_path` FROM `files` where `type` = 'directory'; ")->fetchAll();
	echo json_encode(['status'=>true,'data'=> $directories]);

}

