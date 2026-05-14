<?php
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';
admin_session_start();
admin_require_auth();

$method  = $_SERVER['REQUEST_METHOD'];
$action  = $_GET['action'] ?? '';
$lang_id = (int)($_GET['lang_id'] ?? 1);
$id      = (int)($_GET['id'] ?? 0);

function case_full(int $id, int $lang_id): array {
    $case = row('SELECT c.*, ct.title,ct.description,ct.overview_text,ct.location,ct.cooperation_period,ct.meta_title,ct.meta_description,
                        logo.path as logo_path, img.path as image_path
                 FROM cases c
                 LEFT JOIN cases_t ct ON c.id=ct.case_id AND ct.lang_id=?
                 LEFT JOIN media logo ON c.company_logo_id=logo.id
                 LEFT JOIN media img ON c.featured_image_id=img.id
                 WHERE c.id=?', [$lang_id, $id]) ?? [];
    if ($case) {
        $case['logo_url']  = $case['logo_path']  ? UPLOAD_URL . $case['logo_path']  : '';
        $case['image_url'] = $case['image_path'] ? UPLOAD_URL . $case['image_path'] : '';
        $case['key_results']= rows('SELECT * FROM case_key_results WHERE case_id=? AND lang_id=? ORDER BY sort_order', [$id,$lang_id]);
        $case['challenges'] = rows('SELECT ch.*,ct.title as ch_title,ct.text as ch_text FROM case_challenges ch LEFT JOIN case_challenges_t ct ON ch.id=ct.challenge_id AND ct.lang_id=? WHERE ch.case_id=? ORDER BY ch.sort_order', [$lang_id,$id]);
        $case['tech_items'] = rows('SELECT * FROM case_tech_items WHERE case_id=? AND lang_id=? ORDER BY sort_order', [$id,$lang_id]);
        $case['services']   = rows('SELECT * FROM case_services WHERE case_id=? AND lang_id=? ORDER BY sort_order', [$id,$lang_id]);
        $case['term_ids']   = array_column(rows('SELECT term_id FROM case_terms WHERE case_id=?', [$id]), 'term_id');
    }
    return $case;
}

if ($method === 'GET') {
    if ($id) { json_response(case_full($id, $lang_id)); }
    $list = rows('SELECT c.*, ct.title, img.path as image_path FROM cases c LEFT JOIN cases_t ct ON c.id=ct.case_id AND ct.lang_id=? LEFT JOIN media img ON c.featured_image_id=img.id ORDER BY c.sort_order, c.created_at DESC', [$lang_id]);
    foreach ($list as &$item) $item['image_url'] = $item['image_path'] ? UPLOAD_URL . $item['image_path'] : '';
    json_response($list);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    if ($action === 'save') {
        $c = ['slug'=>slug($data['slug']??$data['title']??'case'),
              'is_active'=>(int)($data['is_active']??1), 'is_featured'=>(int)($data['is_featured']??0),
              'sort_order'=>(int)($data['sort_order']??0),
              'company_logo_id'=>($data['company_logo_id']??null)?:null,
              'featured_image_id'=>($data['featured_image_id']??null)?:null];
        if (!$id) $id = insert('cases', $c);
        else update('cases', $c, ['id'=>$id]);

        $t = ['case_id'=>$id,'lang_id'=>$lang_id,
              'title'=>$data['title']??'','description'=>$data['description']??'',
              'overview_text'=>$data['overview_text']??'','location'=>$data['location']??'',
              'cooperation_period'=>$data['cooperation_period']??'',
              'meta_title'=>$data['meta_title']??'','meta_description'=>$data['meta_description']??''];
        $et = row('SELECT id FROM cases_t WHERE case_id=? AND lang_id=?', [$id,$lang_id]);
        if ($et) update('cases_t', $t, ['id'=>$et['id']]); else insert('cases_t', $t);

        // Key results
        delete('case_key_results', ['case_id'=>$id, 'lang_id'=>$lang_id]);
        foreach ($data['key_results']??[] as $i=>$kr) {
            if (trim($kr)) insert('case_key_results', ['case_id'=>$id,'lang_id'=>$lang_id,'text'=>$kr,'sort_order'=>$i]);
        }

        // Challenges
        $ch_ids = [];
        foreach ($data['challenges']??[] as $i=>$ch) {
            $ch_id = (int)($ch['id']??0);
            $ch_r = ['case_id'=>$id,'sort_order'=>$i];
            if ($ch_id) update('case_challenges', $ch_r, ['id'=>$ch_id]);
            else $ch_id = insert('case_challenges', $ch_r);
            $ch_ids[] = $ch_id;
            $cht = ['challenge_id'=>$ch_id,'lang_id'=>$lang_id,'title'=>$ch['title']??'','text'=>$ch['text']??''];
            $ech = row('SELECT id FROM case_challenges_t WHERE challenge_id=? AND lang_id=?', [$ch_id,$lang_id]);
            if ($ech) update('case_challenges_t', $cht, ['id'=>$ech['id']]); else insert('case_challenges_t', $cht);
        }
        if ($ch_ids) q('DELETE FROM case_challenges WHERE case_id=? AND id NOT IN (' . implode(',',array_fill(0,count($ch_ids),'?')) . ')', array_merge([$id], $ch_ids));

        // Tech items
        delete('case_tech_items', ['case_id'=>$id,'lang_id'=>$lang_id]);
        foreach ($data['tech_items']??[] as $i=>$ti) {
            insert('case_tech_items', ['case_id'=>$id,'lang_id'=>$lang_id,'name'=>$ti['name']??'','icon_svg'=>$ti['icon_svg']??'','sort_order'=>$i]);
        }

        // Services
        delete('case_services', ['case_id'=>$id,'lang_id'=>$lang_id]);
        foreach ($data['services']??[] as $i=>$s) {
            if (trim($s)) insert('case_services', ['case_id'=>$id,'lang_id'=>$lang_id,'service_name'=>$s,'sort_order'=>$i]);
        }

        // Terms
        delete('case_terms', ['case_id'=>$id]);
        foreach ($data['term_ids']??[] as $term_id) {
            $term = row('SELECT type FROM terms WHERE id=?', [(int)$term_id]);
            if ($term) insert('case_terms', ['case_id'=>$id,'term_id'=>(int)$term_id,'type'=>$term['type']]);
        }

        json_response(['ok'=>true, 'id'=>$id]);
    }

    if ($action === 'delete') {
        delete('cases', ['id'=>$id]);
        json_response(['ok'=>true]);
    }
}
