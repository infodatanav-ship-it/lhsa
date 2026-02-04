<?php
// Integrate permission checks with the backend auth/permissions loader.
// This helper uses the existing `loadPermissions()`/`can()` helpers
// located in backend/includes/permissions.php. Ensure sessions are
// started before calling these functions.

// Start session if not already started. Guard against "headers already sent"
// The strict fix is to ensure no output appears before this include; as a
// defensive measure we only call `session_start()` when headers aren't sent
// and fall back to a suppressed call to avoid noisy warnings in production.
if (session_status() === PHP_SESSION_NONE) {
    if (!headers_sent()) {
        session_start();
    } else {
        @session_start();
    }
}

// include permission helpers (loadPermissions and can()) if available
// Prefer a local permissions helper inside the browser folder, but fall back
// to the shared backend/includes directory which is the canonical location
// for the app's permission helpers.
$permShared = __DIR__ . '/../includes/permissions.php';
if (file_exists($permShared)) {
    require_once $permShared;
}

function userCanModify(): bool {
    // Admin role always allowed
    if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') return true;

    // If the permission loader is available, check common file-management perms
    if (function_exists('can')) {
        if (can('manage_files') || can('files.manage') || can('files.upload') || can('files.delete')) return true;
        $perms = $_SESSION['permissions'] ?? [];
        if (in_array('*', $perms, true)) return true;
    }

    // Backwards-compatible flag
    if (!empty($_SESSION['is_admin'])) return true;

    return false;
}

function userCanBrowse(): bool {
    // Require a logged-in user by default; change to true to allow anonymous browsing
    return !empty($_SESSION['user_id']);
}

function requireModify(): void {
    if (!userCanModify()) {
        $_SESSION['browser_error'] = 'Permission denied.';
        if (!headers_sent()) {
            $loc = $_SERVER['HTTP_REFERER'] ?? '/backend/';
            header('Location: ' . $loc);
        }
        exit;
    }
}

?>
