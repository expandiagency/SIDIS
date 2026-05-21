<?php
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';
admin_session_start();
admin_require_auth();

$method  = $_SERVER['REQUEST_METHOD'];
$action  = $_GET['action'] ?? '';
$lang_id = (int)($_GET['lang_id'] ?? 1);
$id      = (int)($_GET['id'] ?? 0);


try { db()->exec("ALTER TABLE reviews ADD COLUMN clutch_url VARCHAR(500) DEFAULT NULL"); } catch(Exception $e) {}
try { db()->exec("ALTER TABLE home_content ADD COLUMN hero_poster_url VARCHAR(500) DEFAULT NULL"); } catch(Exception $e) {}
try { db()->exec("ALTER TABLE home_content ADD COLUMN solutions_ids TEXT DEFAULT NULL"); } catch(Exception $e) {}
try { db()->exec("ALTER TABLE home_content ADD COLUMN departments_ids TEXT DEFAULT NULL"); } catch(Exception $e) {}
try { db()->exec("ALTER TABLE home_content ADD COLUMN industries_ids TEXT DEFAULT NULL"); } catch(Exception $e) {}

if ($method === 'GET') {
    if ($action === 'why_slides') {
        json_response(rows('SELECT * FROM home_why_slides WHERE lang_id=? ORDER BY sort_order', [$lang_id]));
    }
    if ($action === 'partner_logos') {
        $logos = rows('SELECT l.*, m.path as image_path FROM home_partner_logos l LEFT JOIN media m ON l.image_id=m.id ORDER BY l.sort_order');
        foreach ($logos as &$l) $l['image_url'] = $l['image_path'] ? admin_url($l['image_path']) : '';
        json_response($logos);
    }
    if ($action === 'auto_images') {
        $imgs = rows('SELECT a.*, m.path as image_path FROM home_automation_images a LEFT JOIN media m ON a.image_id=m.id ORDER BY a.sort_order');
        foreach ($imgs as &$i) $i['image_url'] = $i['image_path'] ? admin_url($i['image_path']) : '';
        json_response($imgs);
    }
    if ($action === 'reviews') {
        $reviews = rows(
            'SELECT r.*, rt.quote, rt.text, rt.author_name, rt.author_title,
                    m.path as author_image_path, rm.path as rating_image_path
             FROM reviews r
             LEFT JOIN reviews_t rt ON r.id=rt.review_id AND rt.lang_id=?
             LEFT JOIN media m ON r.author_image_id=m.id
             LEFT JOIN media rm ON r.rating_image_id=rm.id
             ORDER BY r.sort_order',
            [$lang_id]
        );
        foreach ($reviews as &$r) {
            $r['author_image_url'] = $r['author_image_path'] ? admin_url($r['author_image_path']) : '';
            $r['rating_image_url'] = $r['rating_image_path'] ? admin_url($r['rating_image_path']) : '';
        }
        json_response($reviews);
    }
    // Main home content
    $content = row('SELECT * FROM home_content WHERE lang_id=?', [$lang_id]) ?? [];
    foreach (['solutions_ids','departments_ids','industries_ids'] as $k) {
        $content[$k] = json_decode($content[$k] ?? '[]', true) ?: [];
    }
    json_response($content);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    // Save main content
    if ($action === 'save_content') {
        $fields = ['hero_title','hero_subtitle','hero_btn1_text','hero_btn1_url','hero_btn2_text','hero_btn2_url',
                   'hero_video_path','hero_poster_url','why_title','automation_title','automation_text',
                   'automation_loc1_title','automation_loc1_city','automation_loc2_title','automation_loc2_city',
                   'solutions_title','projects_title','projects_btn_text','projects_btn_url',
                   'reviews_title','cta_title','cta_subtitle','cta_btn_text','cta_btn_url'];
        $save = ['lang_id' => $lang_id];
        foreach ($fields as $f) $save[$f] = $data[$f] ?? '';
        $save['solutions_ids']  = json_encode(array_map('intval', $data['solutions_ids']  ?? []));
        $save['departments_ids'] = json_encode(array_map('intval', $data['departments_ids'] ?? []));
        $save['industries_ids'] = json_encode(array_map('intval', $data['industries_ids']  ?? []));
        $existing = row('SELECT id FROM home_content WHERE lang_id=?', [$lang_id]);
        if ($existing) {
            update('home_content', $save, ['lang_id' => $lang_id]);
        } else {
            insert('home_content', $save);
        }
        json_response(['ok' => true]);
    }

    // Why slides
    if ($action === 'save_why_slide') {
        $d = ['lang_id' => $lang_id, 'sort_order' => (int)($data['sort_order']??0),
              'icon_svg' => $data['icon_svg']??'', 'title' => $data['title']??'', 'text' => $data['text']??''];
        if ($id) { update('home_why_slides', $d, ['id'=>$id]); json_response(['ok'=>true]); }
        json_response(['ok'=>true, 'id'=>insert('home_why_slides', $d)]);
    }
    if ($action === 'delete_why_slide') { delete('home_why_slides', ['id'=>$id]); json_response(['ok'=>true]); }

    // Partner logos
    if ($action === 'save_partner_logo') {
        $d = ['sort_order'=>(int)($data['sort_order']??0), 'image_id'=>(int)($data['image_id']??0)??null, 'alt_text'=>$data['alt_text']??''];
        if ($id) { update('home_partner_logos', $d, ['id'=>$id]); json_response(['ok'=>true]); }
        json_response(['ok'=>true, 'id'=>insert('home_partner_logos', $d)]);
    }
    if ($action === 'delete_partner_logo') { delete('home_partner_logos', ['id'=>$id]); json_response(['ok'=>true]); }

    // Automation images
    if ($action === 'save_auto_image') {
        $d = ['sort_order'=>(int)($data['sort_order']??0), 'image_id'=>(int)($data['image_id']??0)??null];
        if ($id) { update('home_automation_images', $d, ['id'=>$id]); json_response(['ok'=>true]); }
        json_response(['ok'=>true, 'id'=>insert('home_automation_images', $d)]);
    }
    if ($action === 'delete_auto_image') { delete('home_automation_images', ['id'=>$id]); json_response(['ok'=>true]); }

    // Reviews
    if ($action === 'save_review') {
        $r = ['sort_order'=>(int)($data['sort_order']??0),
              'author_image_id'=>($data['author_image_id']??null)?:(null),
              'linkedin_url'=>$data['linkedin_url']??'',
              'clutch_url'=>$data['clutch_url']??'',
              'rating_image_id'=>($data['rating_image_id']??null)?:(null),
              'is_active'=>(int)($data['is_active']??1)];
        if (!$id) $id = insert('reviews', $r);
        else update('reviews', $r, ['id'=>$id]);
        $t = ['review_id'=>$id,'lang_id'=>$lang_id,'quote'=>$data['quote']??'',
              'text'=>$data['text']??'','author_name'=>$data['author_name']??'','author_title'=>$data['author_title']??''];
        $et = row('SELECT id FROM reviews_t WHERE review_id=? AND lang_id=?', [$id,$lang_id]);
        if ($et) update('reviews_t', $t, ['id'=>$et['id']]);
        else insert('reviews_t', $t);
        json_response(['ok'=>true, 'id'=>$id]);
    }
    if ($action === 'delete_review') { delete('reviews', ['id'=>$id]); json_response(['ok'=>true]); }
}
