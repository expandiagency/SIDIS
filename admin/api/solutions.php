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
    if ($action === 'items') {
        $type = $_GET['type'] ?? null;
        $sql = 'SELECT si.*, sit.title,sit.description,sit.btn_text,sit.btn_url FROM solution_items si LEFT JOIN solution_items_t sit ON si.id=sit.item_id AND sit.lang_id=? WHERE 1';
        $params = [$lang_id];
        if ($type) { $sql .= ' AND si.type=?'; $params[] = $type; }
        $sql .= ' ORDER BY si.sort_order';
        json_response(rows($sql, $params));
    }
    if ($action === 'pages') {
        $pages = rows('SELECT sp.*,spt.title,spt.description,m.path as image_path FROM solution_pages sp LEFT JOIN solution_pages_t spt ON sp.id=spt.page_id AND spt.lang_id=? LEFT JOIN media m ON sp.image_id=m.id ORDER BY sp.sort_order', [$lang_id]);
        foreach ($pages as &$p) $p['image_url'] = $p['image_path'] ? admin_url($p['image_path']) : '';
        json_response($pages);
    }
    if ($action === 'page_features' && $id) {
        json_response(rows('SELECT sf.*,sft.title,sft.text FROM solution_features sf LEFT JOIN solution_features_t sft ON sf.id=sft.feature_id AND sft.lang_id=? WHERE sf.page_id=? ORDER BY sf.sort_order', [$lang_id,$id]));
    }
    json_response([]);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    // Solution items
    if ($action === 'save_item') {
        $d = ['type'=>$data['type']??'solution','icon_svg'=>$data['icon_svg']??'','sort_order'=>(int)($data['sort_order']??0),'is_active'=>(int)($data['is_active']??1),'page_slug'=>$data['page_slug']??null];
        if (!$id) $id = insert('solution_items', $d); else update('solution_items', $d, ['id'=>$id]);
        $t = ['item_id'=>$id,'lang_id'=>$lang_id,'title'=>$data['title']??'','description'=>$data['description']??'','btn_text'=>$data['btn_text']??'Learn more','btn_url'=>$data['btn_url']??'#'];
        $et = row('SELECT id FROM solution_items_t WHERE item_id=? AND lang_id=?', [$id,$lang_id]);
        if ($et) update('solution_items_t', $t, ['id'=>$et['id']]); else insert('solution_items_t', $t);
        json_response(['ok'=>true,'id'=>$id]);
    }
    if ($action === 'delete_item') { delete('solution_items', ['id'=>$id]); json_response(['ok'=>true]); }

    // Solution pages
    if ($action === 'save_page') {
        $d = ['slug'=>$data['slug']??'','type'=>$data['type']??'solution','sort_order'=>(int)($data['sort_order']??0),'is_active'=>(int)($data['is_active']??1),'image_id'=>($data['image_id']??null)?:null];
        if (!$id) $id = insert('solution_pages', $d); else update('solution_pages', $d, ['id'=>$id]);
        $t = ['page_id'=>$id,'lang_id'=>$lang_id,'title'=>$data['title']??'','description'=>$data['description']??'','btn1_text'=>$data['btn1_text']??'','btn2_text'=>$data['btn2_text']??'','meta_title'=>$data['meta_title']??'','meta_description'=>$data['meta_description']??''];
        $et = row('SELECT id FROM solution_pages_t WHERE page_id=? AND lang_id=?', [$id,$lang_id]);
        if ($et) update('solution_pages_t', $t, ['id'=>$et['id']]); else insert('solution_pages_t', $t);
        // Features
        foreach ($data['features']??[] as $i=>$f) {
            $fid = (int)($f['id']??0);
            if (!$fid) $fid = insert('solution_features', ['page_id'=>$id,'sort_order'=>$i]);
            else update('solution_features', ['sort_order'=>$i], ['id'=>$fid]);
            $ft = ['feature_id'=>$fid,'lang_id'=>$lang_id,'title'=>$f['title']??'','text'=>$f['text']??''];
            $eft = row('SELECT id FROM solution_features_t WHERE feature_id=? AND lang_id=?', [$fid,$lang_id]);
            if ($eft) update('solution_features_t', $ft, ['id'=>$eft['id']]); else insert('solution_features_t', $ft);
        }
        json_response(['ok'=>true,'id'=>$id]);
    }
    if ($action === 'delete_page') { delete('solution_pages', ['id'=>$id]); json_response(['ok'=>true]); }
}
