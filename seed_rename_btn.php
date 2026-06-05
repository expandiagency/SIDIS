<?php
// Renames "Try AI assistant" → "View Our Presentation" across all DB records
// Also updates btn1_url from "#" → "/assets/Sidis-Group.pdf" where applicable
// Trigger: /seed_rename_btn.php?key=sidis2026
if (($_GET['key'] ?? '') !== 'sidis2026') { http_response_code(403); die('Forbidden'); }
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: text/plain; charset=utf-8');

$OLD_TEXT = 'Try AI assistant';
$NEW_TEXT = 'View Our Presentation';
$OLD_URL  = '#';
$NEW_URL  = '/assets/Sidis-Group.pdf';

$total = 0;

// ── 1. sol_page_blocks — promo + planning blocks ──────────────────────────
$blocks = rows("SELECT id, block_key, content FROM sol_page_blocks WHERE content LIKE ?", ["%$OLD_TEXT%"]);
foreach ($blocks as $b) {
    $c = json_decode($b['content'], true);
    if (!$c) continue;
    $changed = false;
    // promo block
    if (($c['btn1_text'] ?? '') === $OLD_TEXT) { $c['btn1_text'] = $NEW_TEXT; $changed = true; }
    if (($c['btn1_url']  ?? '') === $OLD_URL)  { $c['btn1_url']  = $NEW_URL;  $changed = true; }
    // planning block
    if (($c['info_btn1_text'] ?? '') === $OLD_TEXT) { $c['info_btn1_text'] = $NEW_TEXT; $changed = true; }
    if (($c['info_btn1_url']  ?? '') === $OLD_URL)  { $c['info_btn1_url']  = $NEW_URL;  $changed = true; }
    if ($changed) {
        update('sol_page_blocks', ['content' => json_encode($c, JSON_UNESCAPED_UNICODE)], ['id' => $b['id']]);
        echo "✅  sol_page_blocks #{$b['id']} [{$b['block_key']}]\n";
        $total++;
    }
}

// ── 2. Home page settings ─────────────────────────────────────────────────
$v = get_setting('hero_btn1_text');
if ($v === $OLD_TEXT) {
    set_setting('hero_btn1_text', $NEW_TEXT);
    echo "✅  home hero_btn1_text\n"; $total++;
}
$v = get_setting('hero_btn1_url');
if ($v === $OLD_URL || $v === '') {
    set_setting('hero_btn1_url', $NEW_URL);
    echo "✅  home hero_btn1_url\n"; $total++;
}

// ── 3. Posts extras (cta_btn1_text) ──────────────────────────────────────
$posts = rows("SELECT id, extras FROM posts WHERE extras LIKE ?", ["%$OLD_TEXT%"]);
foreach ($posts as $p) {
    $ex = json_decode($p['extras'] ?? '{}', true);
    if (!$ex) continue;
    $changed = false;
    if (($ex['cta_btn1_text'] ?? '') === $OLD_TEXT) { $ex['cta_btn1_text'] = $NEW_TEXT; $changed = true; }
    if (($ex['cta_btn1_url']  ?? '') === $OLD_URL)  { $ex['cta_btn1_url']  = $NEW_URL;  $changed = true; }
    if ($changed) {
        db()->exec("UPDATE posts SET extras=" . db()->quote(json_encode($ex, JSON_UNESCAPED_UNICODE)) . " WHERE id={$p['id']}");
        echo "✅  post #{$p['id']} extras\n"; $total++;
    }
}

// ── 4. solution_pages_t btn1_text ─────────────────────────────────────────
$rows = rows("SELECT id FROM solution_pages_t WHERE btn1_text=?", [$OLD_TEXT]);
foreach ($rows as $r) {
    update('solution_pages_t', ['btn1_text' => $NEW_TEXT], ['id' => $r['id']]);
    echo "✅  solution_pages_t #{$r['id']}\n"; $total++;
}

echo "\n---\nTotal updated: $total\n";
