<?php
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';
admin_session_start();
admin_require_auth();

$method   = $_SERVER['REQUEST_METHOD'];
$action   = $_GET['action'] ?? '';
$id       = (int)($_GET['id'] ?? 0);
$lang_id  = (int)($_GET['lang_id'] ?? 1);
$location = $_GET['location'] ?? 'header';

if ($method === 'GET') {
    if ($action === 'mega_categories') {
        $cats = rows('SELECT * FROM nav_mega_categories WHERE nav_item_id=? AND lang_id=? ORDER BY sort_order', [$id, $lang_id]);
        foreach ($cats as &$cat) {
            $cat['subitems'] = rows('SELECT * FROM nav_mega_subitems WHERE category_id=? AND lang_id=? ORDER BY sort_order', [$cat['id'], $lang_id]);
        }
        json_response($cats);
    }
    $items = rows('SELECT * FROM nav_items WHERE lang_id=? AND location=? ORDER BY sort_order', [$lang_id, $location]);
    foreach ($items as &$item) {
        if ($item['has_mega']) {
            $item['mega_categories'] = rows('SELECT * FROM nav_mega_categories WHERE nav_item_id=? AND lang_id=? ORDER BY sort_order', [$item['id'], $lang_id]);
            foreach ($item['mega_categories'] as &$cat) {
                $cat['subitems'] = rows('SELECT * FROM nav_mega_subitems WHERE category_id=? AND lang_id=? ORDER BY sort_order', [$cat['id'], $lang_id]);
            }
        }
    }
    json_response($items);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    // Nav item CRUD
    if ($action === 'save_item') {
        $item_data = [
            'lang_id'    => $lang_id,
            'location'   => $data['location'] ?? 'header',
            'parent_id'  => ($data['parent_id'] ?? null) ?: null,
            'title'      => trim($data['title'] ?? ''),
            'url'        => trim($data['url'] ?? '#'),
            'target'     => $data['target'] ?? '_self',
            'sort_order' => (int)($data['sort_order'] ?? 0),
            'is_active'  => (int)($data['is_active'] ?? 1),
            'has_mega'   => (int)($data['has_mega'] ?? 0),
        ];
        if ($id) {
            update('nav_items', $item_data, ['id' => $id]);
            json_response(['ok' => true]);
        } else {
            $new_id = insert('nav_items', $item_data);
            json_response(['ok' => true, 'id' => $new_id]);
        }
    }

    if ($action === 'delete_item') {
        delete('nav_items', ['id' => $id]);
        json_response(['ok' => true]);
    }

    // Mega category CRUD
    if ($action === 'save_mega_category') {
        $cat_data = [
            'nav_item_id' => (int)($data['nav_item_id'] ?? 0),
            'lang_id'     => $lang_id,
            'title'       => trim($data['title'] ?? ''),
            'description' => trim($data['description'] ?? ''),
            'sort_order'  => (int)($data['sort_order'] ?? 0),
        ];
        if ($id) {
            update('nav_mega_categories', $cat_data, ['id' => $id]);
            json_response(['ok' => true]);
        } else {
            $new_id = insert('nav_mega_categories', $cat_data);
            json_response(['ok' => true, 'id' => $new_id]);
        }
    }

    if ($action === 'delete_mega_category') {
        delete('nav_mega_categories', ['id' => $id]);
        json_response(['ok' => true]);
    }

    // Mega subitem CRUD
    if ($action === 'save_mega_subitem') {
        $sub_data = [
            'category_id' => (int)($data['category_id'] ?? 0),
            'lang_id'     => $lang_id,
            'title'       => trim($data['title'] ?? ''),
            'description' => trim($data['description'] ?? ''),
            'url'         => trim($data['url'] ?? '#'),
            'icon_svg'    => $data['icon_svg'] ?? '',
            'sort_order'  => (int)($data['sort_order'] ?? 0),
        ];
        if ($id) {
            update('nav_mega_subitems', $sub_data, ['id' => $id]);
            json_response(['ok' => true]);
        } else {
            $new_id = insert('nav_mega_subitems', $sub_data);
            json_response(['ok' => true, 'id' => $new_id]);
        }
    }

    if ($action === 'delete_mega_subitem') {
        delete('nav_mega_subitems', ['id' => $id]);
        json_response(['ok' => true]);
    }

    // Reorder
    if ($action === 'reorder') {
        foreach ($data['order'] ?? [] as $pos => $item_id) {
            update('nav_items', ['sort_order' => $pos], ['id' => (int)$item_id]);
        }
        json_response(['ok' => true]);
    }
}
