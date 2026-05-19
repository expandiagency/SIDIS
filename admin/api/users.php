<?php
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';
admin_session_start();
admin_require_auth();

// Auto-migrate role column
try { db()->exec("ALTER TABLE admin_users ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'administrator'"); } catch(Exception $e) {}

// Only administrators can manage users
if ($_SESSION['admin_role'] !== 'administrator') {
    json_response(['error' => 'Forbidden'], 403);
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id     = (int)($_GET['id'] ?? 0);

if ($method === 'GET') {
    $users = rows('SELECT id, name, email, role, is_active, created_at FROM admin_users ORDER BY id');
    json_response($users);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];

    if ($action === 'save') {
        $name  = trim($data['name'] ?? '');
        $email = trim($data['email'] ?? '');
        $role  = in_array($data['role'] ?? '', ['administrator','manager']) ? $data['role'] : 'manager';
        $active = (int)($data['is_active'] ?? 1);

        if (!$name || !$email) { json_response(['error' => 'Name and email are required'], 400); }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { json_response(['error' => 'Invalid email'], 400); }

        if ($id) {
            // Update existing
            $upd = ['name'=>$name, 'email'=>$email, 'role'=>$role, 'is_active'=>$active];
            if (!empty(trim($data['password'] ?? ''))) {
                $upd['password_hash'] = password_hash(trim($data['password']), PASSWORD_DEFAULT);
            }
            update('admin_users', $upd, ['id'=>$id]);
        } else {
            // Create new
            $pass = trim($data['password'] ?? '');
            if (!$pass) { json_response(['error' => 'Password is required for new users'], 400); }
            $dupe = row('SELECT id FROM admin_users WHERE email=?', [$email]);
            if ($dupe) { json_response(['error' => 'Email already exists'], 400); }
            $id = insert('admin_users', [
                'name'          => $name,
                'email'         => $email,
                'password_hash' => password_hash($pass, PASSWORD_DEFAULT),
                'role'          => $role,
                'is_active'     => $active,
            ]);
        }
        json_response(['ok' => true, 'id' => $id]);
    }

    if ($action === 'delete') {
        if ($id === (int)$_SESSION['admin_id']) {
            json_response(['error' => 'Cannot delete your own account'], 400);
        }
        delete('admin_users', ['id' => $id]);
        json_response(['ok' => true]);
    }
}
