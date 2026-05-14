<?php
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';
admin_session_start();
admin_require_auth();

$method  = $_SERVER['REQUEST_METHOD'];
$action  = $_GET['action'] ?? '';
$lang_id = (int)($_GET['lang_id'] ?? 1);
$id      = (int)($_GET['id'] ?? 0);

if ($method === 'GET') {
    $terms = rows('SELECT t.*, tt.name FROM terms t LEFT JOIN terms_t tt ON t.id=tt.term_id AND tt.lang_id=? WHERE t.is_active=1 ORDER BY t.type, t.sort_order', [$lang_id]);
    json_response($terms);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    if ($action === 'save') {
        $d = ['type'=>$data['type']??'solution','slug'=>slug($data['slug']??$data['name']??'term'),'sort_order'=>(int)($data['sort_order']??0),'is_active'=>(int)($data['is_active']??1)];
        if (!$id) $id = insert('terms', $d); else update('terms', $d, ['id'=>$id]);
        $t = ['term_id'=>$id,'lang_id'=>$lang_id,'name'=>$data['name']??''];
        $et = row('SELECT id FROM terms_t WHERE term_id=? AND lang_id=?', [$id,$lang_id]);
        if ($et) update('terms_t', $t, ['id'=>$et['id']]); else insert('terms_t', $t);
        json_response(['ok'=>true,'id'=>$id]);
    }

    if ($action === 'delete') {
        delete('terms', ['id'=>$id]);
        json_response(['ok'=>true]);
    }
}
