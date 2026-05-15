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
    $authors = rows('SELECT a.*, at_.name, at_.title as author_title, m.path as image_path FROM authors a LEFT JOIN authors_t at_ ON a.id=at_.author_id AND at_.lang_id=? LEFT JOIN media m ON a.image_id=m.id WHERE a.is_active=1 ORDER BY a.id', [$lang_id]);
    foreach ($authors as &$a) $a['image_url'] = $a['image_path'] ? admin_url($a['image_path']) : '';
    json_response($authors);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    if ($action === 'save') {
        $d = ['image_id'=>($data['image_id']??null)?:null,'linkedin_url'=>$data['linkedin_url']??'','is_active'=>(int)($data['is_active']??1)];
        if (!$id) $id = insert('authors', $d); else update('authors', $d, ['id'=>$id]);
        $t = ['author_id'=>$id,'lang_id'=>$lang_id,'name'=>$data['name']??'','title'=>$data['title']??''];
        $et = row('SELECT id FROM authors_t WHERE author_id=? AND lang_id=?', [$id,$lang_id]);
        if ($et) update('authors_t', $t, ['id'=>$et['id']]); else insert('authors_t', $t);
        json_response(['ok'=>true,'id'=>$id]);
    }

    if ($action === 'delete') {
        update('authors', ['is_active'=>0], ['id'=>$id]);
        json_response(['ok'=>true]);
    }
}
