<?php
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';
admin_session_start();
admin_require_auth();

$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($_GET['id'] ?? 0);

if ($method === 'GET') {
    $limit  = (int)($_GET['limit'] ?? 60);
    $offset = (int)($_GET['offset'] ?? 0);
    $items  = rows('SELECT * FROM media ORDER BY created_at DESC LIMIT ? OFFSET ?', [$limit, $offset]);
    $total  = row('SELECT COUNT(*) as c FROM media')['c'];
    foreach ($items as &$item) $item['url'] = UPLOAD_URL . $item['path'];
    json_response(['items' => $items, 'total' => $total]);
}

if ($method === 'POST' && ($action = $_GET['action'] ?? '') === 'delete') {
    if (!$id) json_error('Missing id');
    $media = row('SELECT * FROM media WHERE id=?', [$id]);
    if (!$media) json_error('Not found', 404);
    $file_path = UPLOAD_DIR . $media['path'];
    if (file_exists($file_path)) unlink($file_path);
    delete('media', ['id' => $id]);
    json_response(['ok' => true]);
}

if ($method === 'POST' && ($action ?? '') === 'update_alt') {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];
    if (!$id) json_error('Missing id');
    update('media', ['alt_text' => $data['alt_text'] ?? ''], ['id' => $id]);
    json_response(['ok' => true]);
}
