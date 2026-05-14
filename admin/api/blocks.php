<?php
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
admin_session_start();
admin_require_auth();

$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($_GET['id'] ?? 0);

if ($method === 'GET') {
    json_response(rows('SELECT * FROM home_blocks ORDER BY sort_order'));
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $action = $_GET['action'] ?? '';

    if ($action === 'toggle') {
        $block = row('SELECT * FROM home_blocks WHERE id=?', [$id]);
        if ($block) {
            update('home_blocks', ['is_active' => $block['is_active'] ? 0 : 1], ['id' => $id]);
        }
        json_response(['ok' => true]);
    }

    if ($action === 'reorder') {
        foreach ($data['order'] ?? [] as $pos => $block_id) {
            update('home_blocks', ['sort_order' => $pos], ['id' => (int)$block_id]);
        }
        json_response(['ok' => true]);
    }

    if ($action === 'update_label') {
        update('home_blocks', ['label' => $data['label'] ?? ''], ['id' => $id]);
        json_response(['ok' => true]);
    }
}
