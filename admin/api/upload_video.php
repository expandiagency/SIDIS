<?php
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';
admin_session_start();
admin_require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_error('Method not allowed', 405);
if (empty($_FILES['file'])) json_error('No file uploaded');

$file = $_FILES['file'];
$allowed_mime = ['video/mp4', 'video/webm', 'video/quicktime'];
if (!in_array($file['type'], $allowed_mime)) json_error('Only MP4/WebM video files are allowed');
if ($file['size'] > 500 * 1024 * 1024) json_error('File too large (max 500MB)');

$ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$name = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$dir  = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/assets/video/';
if (!is_dir($dir)) mkdir($dir, 0755, true);

if (!move_uploaded_file($file['tmp_name'], $dir . $name)) {
    json_error('Failed to save file');
}

json_response(['ok' => true, 'path' => '/assets/video/' . $name]);
