<?php
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';
admin_session_start();
admin_require_auth();

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';
$id     = (int)($_GET['id'] ?? 0);

if ($method === 'GET') {
    json_response(get_languages(false));
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    if ($action === 'delete') {
        $lang = row('SELECT * FROM languages WHERE id=?', [$id]);
        if (!$lang) json_error('Not found', 404);
        if ($lang['is_default']) json_error('Cannot delete the default language');
        delete('languages', ['id' => $id]);
        json_response(['ok' => true]);
    }

    if ($action === 'set_default') {
        q('UPDATE languages SET is_default=0');
        update('languages', ['is_default' => 1], ['id' => $id]);
        json_response(['ok' => true]);
    }

    $code       = strtolower(trim($data['code'] ?? ''));
    $name       = trim($data['name'] ?? '');
    $is_active  = (int)($data['is_active'] ?? 1);
    $sort_order = (int)($data['sort_order'] ?? 0);

    if (!$code || !$name) json_error('Code and name are required');

    if ($id) {
        update('languages', compact('name', 'is_active', 'sort_order'), ['id' => $id]);
        json_response(['ok' => true]);
    } else {
        $exists = row('SELECT id FROM languages WHERE code=?', [$code]);
        if ($exists) json_error('Language code already exists');
        $is_default = row('SELECT COUNT(*) as c FROM languages') ['c'] == 0 ? 1 : 0;
        $new_id = insert('languages', compact('code', 'name', 'is_active', 'is_default', 'sort_order'));
        json_response(['ok' => true, 'id' => $new_id]);
    }
}
