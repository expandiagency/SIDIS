<?php
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';
admin_session_start();
admin_require_auth();

$pdo     = db();
$method  = $_SERVER['REQUEST_METHOD'];
$action  = $_GET['action'] ?? '';
$lang_id = (int)($_GET['lang_id'] ?? 1);
$id      = (int)($_GET['id'] ?? 0);

// ── Ensure sol_page_blocks table exists ─────────────────────────────────────
$pdo->exec("CREATE TABLE IF NOT EXISTS sol_page_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    lang_id INT NOT NULL,
    block_key VARCHAR(50) NOT NULL,
    label VARCHAR(100) NOT NULL DEFAULT '',
    sort_order INT DEFAULT 0,
    is_active TINYINT DEFAULT 1,
    content MEDIUMTEXT DEFAULT '{}',
    UNIQUE KEY uk_spb (page_id, lang_id, block_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Default blocks for a new solution page
function default_sol_blocks(): array {
    return [
        ['promo',      'Hero / Promo',       0,  '{"title":"","text":"","btn1_text":"Try AI assistant","btn1_url":"#","btn2_text":"Free audit","btn2_url":"#getintouch","image_id":null,"image_url":""}'],
        ['features',   'Features Slider',    1,  '{"title":"What you get","slides":[{"title":"","text":""},{"title":"","text":""}]}'],
        ['planning',   'Planning / Process', 2,  '{"items":[{"title":"","text":""}],"info_title":"","info_btn1_text":"Contact us","info_btn1_url":"#getintouch"}'],
        ['solved',     'Challenges Solved',  3,  '{"title":"Business Challenges Solved","slides":[{"title":"","text":""},{"title":"","text":""}]}'],
        ['roadmap',    'Roadmap',            4,  '{"title":"Our Process","btn1_text":"Get PDF","btn1_url":"#","btn2_text":"Free audit","btn2_url":"#getintouch","steps":[{"title":"Discovery Call","text":""},{"title":"Strategy & Proposal","text":""},{"title":"Integration","text":""},{"title":"Support & Scale","text":""}],"video_path":"./assets/video/1-hero.mp4"}'],
        ['projects',   'Case Studies',       5,  '{"title":"Implemented Workflows"}'],
        ['reviews',    'Client Reviews',     6,  '{"title":"What clients say about us"}'],
        ['getintouch', 'Contact Form',       7,  '{}'],
        ['faq',        'FAQ',                8,  '{"title":"Questions & answers","items":[{"q":"What are your pricing options?","a":"Our team is based in Eastern Europe..."},{"q":"What is your typical project timeline?","a":""}]}'],
        ['articles',   'Latest Articles',    9,  '{"title":"Latest Automation Insights"}'],
    ];
}

// ── GET ──────────────────────────────────────────────────────────────────────
if ($method === 'GET') {
    if ($action === 'list') {
        $type = $_GET['type'] ?? null;
        $sql  = 'SELECT sp.*, spt.title, spt.description, m.path as image_path
                 FROM solution_pages sp
                 LEFT JOIN solution_pages_t spt ON sp.id=spt.page_id AND spt.lang_id=?
                 LEFT JOIN media m ON sp.image_id=m.id
                 WHERE 1';
        $params = [$lang_id];
        if ($type) { $sql .= ' AND sp.type=?'; $params[] = $type; }
        $sql .= ' ORDER BY sp.type, sp.sort_order';
        $pages = rows($sql, $params);
        foreach ($pages as &$p) $p['image_url'] = $p['image_path'] ? admin_url($p['image_path']) : '';
        json_response($pages);
    }

    if ($action === 'blocks' && $id) {
        $blocks = rows('SELECT * FROM sol_page_blocks WHERE page_id=? AND lang_id=? ORDER BY sort_order', [$id, $lang_id]);
        if (empty($blocks)) {
            // Init default blocks for this page
            foreach (default_sol_blocks() as [$key, $label, $order, $content]) {
                insert('sol_page_blocks', ['page_id'=>$id,'lang_id'=>$lang_id,'block_key'=>$key,'label'=>$label,'sort_order'=>$order,'is_active'=>1,'content'=>$content]);
            }
            $blocks = rows('SELECT * FROM sol_page_blocks WHERE page_id=? AND lang_id=? ORDER BY sort_order', [$id, $lang_id]);
        }
        foreach ($blocks as &$b) {
            $c = json_decode($b['content'] ?: '{}', true) ?: [];
            if (!empty($c['image_id'])) $c['image_url'] = admin_url($c['image_path'] ?? '');
            $b['content_obj'] = $c;
        }
        json_response($blocks);
    }

    if ($action === 'page' && $id) {
        $p = row('SELECT sp.*, spt.title, spt.description, spt.btn1_text, spt.btn2_text, spt.meta_title, spt.meta_description, m.path as image_path
                  FROM solution_pages sp
                  LEFT JOIN solution_pages_t spt ON sp.id=spt.page_id AND spt.lang_id=?
                  LEFT JOIN media m ON sp.image_id=m.id
                  WHERE sp.id=?', [$lang_id, $id]);
        if ($p) $p['image_url'] = $p['image_path'] ? admin_url($p['image_path']) : '';
        json_response($p ?: []);
    }
    json_response([]);
}

// ── POST ─────────────────────────────────────────────────────────────────────
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    if ($action === 'save_page') {
        $d = [
            'slug'       => $data['slug'] ?? '',
            'type'       => $data['type'] ?? 'solution',
            'sort_order' => (int)($data['sort_order'] ?? 0),
            'is_active'  => (int)($data['is_active'] ?? 1),
            'image_id'   => ($data['image_id'] ?? null) ?: null,
        ];
        if (!$id) $id = insert('solution_pages', $d);
        else update('solution_pages', $d, ['id' => $id]);

        $t = [
            'page_id'          => $id, 'lang_id' => $lang_id,
            'title'            => $data['title'] ?? '',
            'description'      => $data['description'] ?? '',
            'btn1_text'        => $data['btn1_text'] ?? '',
            'btn2_text'        => $data['btn2_text'] ?? '',
            'meta_title'       => $data['meta_title'] ?? '',
            'meta_description' => $data['meta_description'] ?? '',
        ];
        $et = row('SELECT id FROM solution_pages_t WHERE page_id=? AND lang_id=?', [$id, $lang_id]);
        if ($et) update('solution_pages_t', $t, ['id' => $et['id']]);
        else     insert('solution_pages_t', $t);
        json_response(['ok' => true, 'id' => $id]);
    }

    if ($action === 'save_block') {
        $page_id   = (int)($data['page_id'] ?? 0);
        $block_key = $data['block_key'] ?? '';
        $content   = json_encode($data['content'] ?? [], JSON_UNESCAPED_UNICODE);
        $existing  = row('SELECT id FROM sol_page_blocks WHERE page_id=? AND lang_id=? AND block_key=?', [$page_id, $lang_id, $block_key]);
        if ($existing) {
            update('sol_page_blocks', ['content' => $content, 'is_active' => (int)($data['is_active'] ?? 1), 'sort_order' => (int)($data['sort_order'] ?? 0)], ['id' => $existing['id']]);
        } else {
            insert('sol_page_blocks', ['page_id'=>$page_id,'lang_id'=>$lang_id,'block_key'=>$block_key,'label'=>$data['label']??$block_key,'sort_order'=>(int)($data['sort_order']??0),'is_active'=>(int)($data['is_active']??1),'content'=>$content]);
        }
        json_response(['ok' => true]);
    }

    if ($action === 'reorder_blocks') {
        foreach ($data['blocks'] ?? [] as $i => $blk) {
            $r = row('SELECT id FROM sol_page_blocks WHERE page_id=? AND lang_id=? AND block_key=?', [(int)$blk['page_id'], $lang_id, $blk['block_key']]);
            if ($r) update('sol_page_blocks', ['sort_order' => $i, 'is_active' => (int)($blk['is_active'] ?? 1)], ['id' => $r['id']]);
        }
        json_response(['ok' => true]);
    }

    if ($action === 'delete_page') {
        delete('sol_page_blocks', ['page_id' => $id]);
        delete('solution_pages_t', ['page_id' => $id]);
        delete('solution_features_t', ['feature_id' => 0]); // safe no-op
        delete('solution_pages', ['id' => $id]);
        json_response(['ok' => true]);
    }
}
