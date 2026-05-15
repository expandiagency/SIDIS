<?php
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';
admin_session_start();
admin_require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Method not allowed', 405);
if (empty($_FILES['file'])) json_error('No file uploaded');

try {
    $id = upload_file($_FILES['file']);
    $media = row('SELECT * FROM media WHERE id=?', [$id]);
    $media['url'] = admin_url($media['path']);
    json_response(['ok' => true, 'media' => $media]);
} catch (Exception $e) {
    json_error($e->getMessage());
}
