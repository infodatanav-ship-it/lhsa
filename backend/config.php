<?php
/* Adjust to your environment */
// $DB_HOST = 'localhost';
// $DB_NAME = 'lovestyl_datanav';
// $DB_USER = 'lovestyl_datanav';
// $DB_PASS = 'bMasS6Q99LWZ8fYbwsTF';

// $DB_HOST = getenv('DB_HOST') ?: 'db';
// $DB_NAME = getenv('DB_NAME') ?: 'lhsa_web';
// $DB_USER = getenv('DB_USER') ?: 'root';
// $DB_PASS = getenv('DB_PASS') ?: 'password';

$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_NAME = getenv('DB_NAME') ?: 'lovestyl_lhsa';
$DB_USER = getenv('DB_USER') ?: 'lovestyl_lhsa';
$DB_PASS = getenv('DB_PASS') ?: 'VeeUWZVgwpfDzLNJ5Aj6';

try {
	$pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
	]);
} catch (PDOException $e) {
	exit('DB connection failed: ' . $e->getMessage());
}
session_start();