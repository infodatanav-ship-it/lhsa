<?php
function csrf() { // helper to insert a hidden token
	if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
	return $_SESSION['csrf'];
}
function csrf_check($token) {
	return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}
function redirect($url) {
	header("Location: $url"); exit;
}
/**
 * Redirect based on current user's role.
 * Admins -> $adminPath, others -> $defaultPath
 */
function redirect_after_login(string $adminPath = '../admin/users.php', string $defaultPath = '../') {
	if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') {
		redirect($adminPath);
	}
	redirect($defaultPath);
}
function humanSize(int $bytes): string {
	if ($bytes < 1048576) return number_format($bytes / 1024, 1) . ' KB';
	return number_format($bytes / 1048576, 2) . ' MB';
}

function ellipsis(string $name, int $max = 30): string {
	return mb_strlen($name) > $max ? mb_substr($name, 0, $max - 3) . '…' : $name;
}
