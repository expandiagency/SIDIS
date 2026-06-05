<?php
// One-time: create Procurement department page with icon, description, hero block
// Trigger: /seed_procurement.php?key=sidis2026
if (($_GET['key'] ?? '') !== 'sidis2026') { http_response_code(403); die('Forbidden'); }
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: text/plain; charset=utf-8');

// Ensure extra columns and tables exist
try { db()->exec("ALTER TABLE solution_pages ADD COLUMN icon_svg MEDIUMTEXT DEFAULT NULL"); } catch(Exception $e) {}
db()->exec("CREATE TABLE IF NOT EXISTS sol_page_blocks (
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

$lang_id = 1;
$slug    = 'procurement';
$icon    = '<svg enable-background="new 0 0 24 24" height="44" viewBox="-1.202 -1.202 26.405 26.405" width="44" xmlns="http://www.w3.org/2000/svg"><path d="m13.25 18h-6.5c-.414 0-.75-.336-.75-.75v-16.5c0-.414.336-.75.75-.75h6.5c.414 0 .75.336.75.75v16.5c0 .414-.336.75-.75.75z"/><path d="m23.25 18h-7c-.414 0-.75-.336-.75-.75v-6c0-.414.336-.75.75-.75h7c.414 0 .75.336.75.75v6c0 .414-.336.75-.75.75z"/><path d="m22.25 9h-4.5c-.414 0-.75-.336-.75-.75v-3.5c0-.414.336-.75.75-.75h4.5c.414 0 .75.336.75.75v3.5c0 .414-.336.75-.75.75z"/><path d="m22.93 21-17.43-.003c-1.654 0-3-1.346-3-3v-9.997h-1.5c-.553 0-1-.448-1-1s.447-1 1-1h1.5c1.103 0 2 .897 2 2v9.997c0 .551.448 1 1 1l17.43.003c.553 0 1 .448 1 1s-.449 1-1 1z"/><circle cx="9" cy="22" r="1.25"/><path d="m9 24c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2zm0-2.5c-.275 0-.5.224-.5.5s.225.5.5.5.5-.224.5-.5-.225-.5-.5-.5z"/><circle cx="19" cy="22" r="1.25"/><path d="m19 24c-1.103 0-2-.897-2-2s.897-2 2-2 2 .897 2 2-.897 2-2 2zm0-2.5c-.275 0-.5.224-.5.5s.225.5.5.5.5-.224.5-.5-.225-.5-.5-.5z"/></svg>';
$title      = 'Procurement';
$hero_title = 'Procurement Process Automation';
$hero_desc  = 'Procurement involves high volumes of repetitive steps — vendor onboarding, purchase order creation, approval routing, invoice matching, and contract management — that are time-consuming and error-prone when handled manually. We build automation systems that standardize and accelerate the full procurement lifecycle. Faster approvals, cleaner vendor data, better compliance tracking, and full visibility into spend — without the manual overhead that makes procurement one of the most process-heavy departments to run.';

// ── 1. solution_pages ─────────────────────────────────────────────────────────
$existing = row('SELECT id FROM solution_pages WHERE slug=?', [$slug]);
if ($existing) {
    $page_id = $existing['id'];
    update('solution_pages', ['icon_svg' => $icon], ['id' => $page_id]);
    echo "✅  solution_pages: already exists (id=$page_id), icon updated\n";
} else {
    $max = row('SELECT MAX(sort_order) as m FROM solution_pages WHERE type="department"');
    $page_id = insert('solution_pages', [
        'type'       => 'department',
        'slug'       => $slug,
        'icon_svg'   => $icon,
        'sort_order' => (int)($max['m'] ?? 0) + 1,
        'is_active'  => 1,
    ]);
    echo "✅  solution_pages: created (id=$page_id)\n";
}

// ── 2. solution_pages_t ───────────────────────────────────────────────────────
$existing_t = row('SELECT id FROM solution_pages_t WHERE page_id=? AND lang_id=?', [$page_id, $lang_id]);
if ($existing_t) {
    update('solution_pages_t', ['title' => $title], ['id' => $existing_t['id']]);
    echo "✅  solution_pages_t: updated\n";
} else {
    insert('solution_pages_t', [
        'page_id'          => $page_id,
        'lang_id'          => $lang_id,
        'title'            => $title,
        'description'      => '',
        'btn1_text'        => 'Try AI assistant',
        'btn2_text'        => 'Free audit',
        'meta_title'       => '',
        'meta_description' => '',
    ]);
    echo "✅  solution_pages_t: created\n";
}

// ── 3. Hero / Promo block ─────────────────────────────────────────────────────
$promo_content = json_encode([
    'title'     => $hero_title,
    'text'      => $hero_desc,
    'btn1_text' => 'Try AI assistant',
    'btn1_url'  => '#',
    'btn2_text' => 'Free audit',
    'btn2_url'  => '#getintouch',
    'image_id'  => null,
    'image_url' => '',
], JSON_UNESCAPED_UNICODE);

$block = row('SELECT id FROM sol_page_blocks WHERE page_id=? AND lang_id=? AND block_key="promo"', [$page_id, $lang_id]);
if ($block) {
    update('sol_page_blocks', ['content' => $promo_content], ['id' => $block['id']]);
    echo "✅  promo block: updated\n";
} else {
    insert('sol_page_blocks', [
        'page_id'   => $page_id,
        'lang_id'   => $lang_id,
        'block_key' => 'promo',
        'label'     => 'Hero / Promo',
        'sort_order'=> 0,
        'is_active' => 1,
        'content'   => $promo_content,
    ]);
    echo "✅  promo block: created\n";
}

echo "\nDone. Page: /departments/procurement/\n";
