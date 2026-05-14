<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function admin_session_start(): void {
    session_name(SESSION_NAME);
    session_start();
}

function admin_logged_in(): bool {
    return !empty($_SESSION['admin_id']);
}

function admin_require_auth(): void {
    if (!admin_logged_in()) {
        if (str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
            json_response(['error' => 'Unauthorized'], 401);
        }
        header('Location: /admin/login.php');
        exit;
    }
}

function admin_login(string $email, string $pass): bool {
    $user = row('SELECT * FROM admin_users WHERE email=? AND is_active=1', [$email]);
    if (!$user || !password_verify($pass, $user['password_hash'])) return false;
    $_SESSION['admin_id']   = $user['id'];
    $_SESSION['admin_email'] = $user['email'];
    $_SESSION['admin_name'] = $user['name'];
    return true;
}

function admin_logout(): void {
    session_destroy();
}

function admin_current(): ?array {
    if (!admin_logged_in()) return null;
    return row('SELECT id, name, email FROM admin_users WHERE id=?', [$_SESSION['admin_id']]);
}

function json_response(mixed $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}
