<?php
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';
admin_session_start();
admin_require_auth();

$method  = $_SERVER['REQUEST_METHOD'];
$lang_id = (int)($_GET['lang_id'] ?? 0);

$global_keys = [
    'site_name', 'site_email', 'site_password',
    'social_linkedin', 'social_whatsapp', 'social_twitter', 'social_facebook', 'social_instagram',
    'clutch_url', 'footer_design_url', 'footer_dev_url',
    'header_logo_url', 'footer_logo_url',
];
$lang_keys   = ['site_description', 'footer_copyright', 'contact_address', 'contact_phone'];

if ($method === 'GET') {
    $result = [];
    foreach ($global_keys as $k) $result[$k] = get_setting($k);
    if ($lang_id) {
        foreach ($lang_keys as $k) $result[$k] = get_setting($k, $lang_id);
    }
    json_response($result);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    foreach ($global_keys as $k) {
        if (array_key_exists($k, $data)) set_setting($k, $data[$k]);
    }
    if ($lang_id) {
        foreach ($lang_keys as $k) {
            if (array_key_exists($k, $data)) set_setting($k, $data[$k], $lang_id);
        }
    }
    json_response(['ok' => true]);
}
