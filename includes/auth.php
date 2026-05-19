<?php
require_once __DIR__ . '/functions.php';

// Converts any stored media path to an absolute URL safe for admin use
function admin_url(string $path): string {
    if (!$path) return '';
    if (strpos($path, 'http') === 0 || $path[0] === '/') return $path;
    if (strpos($path, './') === 0) return '/' . ltrim($path, './');
    return UPLOAD_URL . $path;
}

function admin_session_start(): void {
    session_name(SESSION_NAME);
    session_start();
}

function admin_logged_in(): bool {
    return !empty($_SESSION['admin_id']);
}

function admin_require_auth(): void {
    if (!admin_logged_in()) {
        if (strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false) {
            json_response(['error' => 'Unauthorized'], 401);
        }
        header('Location: /admin/login.php');
        exit;
    }
}

function admin_login(string $email, string $pass): bool {
    $user = row('SELECT * FROM admin_users WHERE email=? AND is_active=1', [$email]);
    if (!$user || !password_verify($pass, $user['password_hash'])) return false;
    $_SESSION['admin_id']    = $user['id'];
    $_SESSION['admin_email'] = $user['email'];
    $_SESSION['admin_name']  = $user['name'];
    $_SESSION['admin_role']  = $user['role'] ?? 'administrator';
    return true;
}

function admin_logout(): void {
    session_destroy();
}

function admin_current(): ?array {
    if (!admin_logged_in()) return null;
    $u = row('SELECT id, name, email, role FROM admin_users WHERE id=?', [$_SESSION['admin_id']]);
    if ($u && empty($_SESSION['admin_role'])) $_SESSION['admin_role'] = $u['role'] ?? 'administrator';
    return $u;
}

function admin_is_admin(): bool {
    return ($_SESSION['admin_role'] ?? '') === 'administrator';
}

