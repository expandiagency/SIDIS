<?php
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
admin_session_start();
admin_require_auth();

$method = $_SERVER['REQUEST_METHOD'];
$id     = (int)($_GET['id'] ?? 0);
$type   = $_GET['type'] ?? '';
$action = $_GET['action'] ?? '';

// Create solution_page_blocks table if it doesn't exist
db()->exec("CREATE TABLE IF NOT EXISTS solution_page_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_type VARCHAR(30) NOT NULL,
    block_key VARCHAR(50) NOT NULL,
    label VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT DEFAULT 1,
    UNIQUE KEY uk_page_block (page_type, block_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

if ($method === 'GET') {
    if ($type === 'solution_pages') {
        $result = [];
        foreach (['solutions','departments','industries'] as $pt) {
            $result[$pt] = rows('SELECT * FROM solution_page_blocks WHERE page_type=? ORDER BY sort_order', [$pt]);
        }
        json_response($result);
    }
    json_response(rows('SELECT * FROM home_blocks ORDER BY sort_order'));
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    if ($action === 'toggle') {
        $block = row('SELECT * FROM home_blocks WHERE id=?', [$id]);
        if ($block) update('home_blocks', ['is_active' => $block['is_active'] ? 0 : 1], ['id' => $id]);
        json_response(['ok' => true]);
    }

    if ($action === 'reorder') {
        foreach ($data['order'] ?? [] as $pos => $block_id) {
            update('home_blocks', ['sort_order' => $pos], ['id' => (int)$block_id]);
        }
        json_response(['ok' => true]);
    }

    if ($action === 'update_label') {
        update('home_blocks', ['label' => $data['label'] ?? ''], ['id' => $id]);
        json_response(['ok' => true]);
    }

    if ($action === 'save_sol_blocks') {
        foreach (['solutions','departments','industries'] as $pt) {
            $blocks = $data[$pt] ?? [];
            foreach ($blocks as $i => $blk) {
                $row = row('SELECT id FROM solution_page_blocks WHERE page_type=? AND block_key=?', [$pt, $blk['block_key']]);
                if ($row) {
                    update('solution_page_blocks', [
                        'sort_order' => $i,
                        'is_active'  => (int)($blk['is_active'] ?? 1),
                        'label'      => $blk['label'] ?? $blk['block_key'],
                    ], ['id' => $row['id']]);
                } else {
                    insert('solution_page_blocks', [
                        'page_type'  => $pt,
                        'block_key'  => $blk['block_key'],
                        'label'      => $blk['label'] ?? $blk['block_key'],
                        'sort_order' => $i,
                        'is_active'  => (int)($blk['is_active'] ?? 1),
                    ]);
                }
            }
        }
        json_response(['ok' => true]);
    }
}
