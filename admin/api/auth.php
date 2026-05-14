<?php
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
admin_session_start();

$action = $_GET['action'] ?? $_POST['action'] ?? 'check';

if ($action === 'login') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    if (admin_login($email, $pass)) {
        json_response(['ok' => true, 'name' => $_SESSION['admin_name']]);
    }
    json_error('Invalid credentials', 401);
}

if ($action === 'logout') {
    admin_logout();
    json_response(['ok' => true]);
}

if ($action === 'check') {
    if (admin_logged_in()) {
        $u = admin_current();
        json_response(['ok' => true, 'user' => $u]);
    }
    json_response(['ok' => false], 200);
}

if ($action === 'change_password') {
    admin_require_auth();
    $old = $_POST['old_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $u = admin_current();
    $full = row('SELECT password_hash FROM admin_users WHERE id=?', [$u['id']]);
    if (!password_verify($old, $full['password_hash'])) json_error('Current password is wrong');
    if (strlen($new) < 6) json_error('New password too short');
    update('admin_users', ['password_hash' => password_hash($new, PASSWORD_BCRYPT)], ['id' => $u['id']]);
    json_response(['ok' => true]);
}
