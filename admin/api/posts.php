<?php
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';
admin_session_start();
admin_require_auth();

// Auto-migrate: add extras column if not exists
try { $pdo->exec("ALTER TABLE posts_t ADD COLUMN extras MEDIUMTEXT DEFAULT NULL"); } catch(Exception $e) {}

$method  = $_SERVER['REQUEST_METHOD'];
$action  = $_GET['action'] ?? '';
$lang_id = (int)($_GET['lang_id'] ?? 1);
$id      = (int)($_GET['id'] ?? 0);

function post_full(int $id, int $lang_id): array {
    $post = row('SELECT p.*,pt.title,pt.subtitle,pt.excerpt,pt.content,pt.meta_title,pt.meta_description,pt.extras,
                        img.path as image_path
                 FROM posts p
                 LEFT JOIN posts_t pt ON p.id=pt.post_id AND pt.lang_id=?
                 LEFT JOIN media img ON p.featured_image_id=img.id
                 WHERE p.id=?', [$lang_id, $id]) ?? [];
    if ($post) {
        $post['image_url'] = $post['image_path'] ? admin_url($post['image_path']) : '';
        $post['tags'] = rows('SELECT * FROM post_tags WHERE post_id=? AND lang_id=? ORDER BY sort_order', [$id,$lang_id]);
        $post['toc']  = rows('SELECT * FROM post_toc WHERE post_id=? AND lang_id=? ORDER BY sort_order', [$id,$lang_id]);
        // Decode extras
        $extras_raw = $post['extras'] ?? null;
        $post['extras'] = $extras_raw ? (json_decode($extras_raw, true) ?: []) : [];
    }
    return $post;
}

if ($method === 'GET') {
    if ($id) { json_response(post_full($id, $lang_id)); }
    $list = rows('SELECT p.*,pt.title,img.path as image_path FROM posts p LEFT JOIN posts_t pt ON p.id=pt.post_id AND pt.lang_id=? LEFT JOIN media img ON p.featured_image_id=img.id ORDER BY p.published_at DESC', [$lang_id]);
    foreach ($list as &$item) $item['image_url'] = $item['image_path'] ? admin_url($item['image_path']) : '';
    json_response($list);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    if ($action === 'save') {
        $p = ['slug'=>slug($data['slug']??$data['title']??'post'),
              'is_active'=>(int)($data['is_active']??1),
              'author_id'=>($data['author_id']??null)?:null,
              'featured_image_id'=>($data['featured_image_id']??null)?:null,
              'published_at'=>$data['published_at']??date('Y-m-d H:i:s')];
        if (!$id) $id = insert('posts', $p);
        else update('posts', $p, ['id'=>$id]);

        $extras_val = null;
        if (!empty($data['extras']) && is_array($data['extras'])) {
            $extras_val = json_encode($data['extras'], JSON_UNESCAPED_UNICODE);
        }

        $t = ['post_id'=>$id,'lang_id'=>$lang_id,
              'title'=>$data['title']??'','subtitle'=>$data['subtitle']??'',
              'excerpt'=>$data['excerpt']??'','content'=>$data['content']??'',
              'meta_title'=>$data['meta_title']??'','meta_description'=>$data['meta_description']??'',
              'extras'=>$extras_val];
        $et = row('SELECT id FROM posts_t WHERE post_id=? AND lang_id=?', [$id,$lang_id]);
        if ($et) update('posts_t', $t, ['id'=>$et['id']]); else insert('posts_t', $t);

        // Tags
        delete('post_tags', ['post_id'=>$id,'lang_id'=>$lang_id]);
        foreach ($data['tags']??[] as $i=>$tag) {
            if (trim($tag)) insert('post_tags', ['post_id'=>$id,'lang_id'=>$lang_id,'tag_text'=>trim($tag),'sort_order'=>$i]);
        }

        // TOC
        delete('post_toc', ['post_id'=>$id,'lang_id'=>$lang_id]);
        foreach ($data['toc']??[] as $i=>$toc) {
            if (trim($toc['title']??'')) insert('post_toc', ['post_id'=>$id,'lang_id'=>$lang_id,'title'=>$toc['title'],'anchor'=>$toc['anchor']??slug($toc['title']),'sort_order'=>$i]);
        }

        json_response(['ok'=>true,'id'=>$id]);
    }

    if ($action === 'delete') {
        delete('posts', ['id'=>$id]);
        json_response(['ok'=>true]);
    }
}
