<?php
// One-off migration: duplicate all per-language content from $SRC lang_id to $DST lang_id.
// Run once via browser with the secret token, then DELETE this file.
require __DIR__ . '/includes/functions.php';

$SECRET = 'sidis-migrate-2026';
if (($_GET['key'] ?? '') !== $SECRET) { http_response_code(403); exit('forbidden'); }

$SRC = (int)($_GET['src'] ?? 1);
$DST = (int)($_GET['dst'] ?? 2);

header('Content-Type: text/plain; charset=utf-8');
echo "Migrating lang_id $SRC -> $DST\n\n";

function table_empty_for_lang(string $table, int $lang_id): bool {
    $r = row("SELECT id FROM $table WHERE lang_id=? LIMIT 1", [$lang_id]);
    return !$r;
}

function copy_simple(string $table, int $src, int $dst): int {
    if (!table_empty_for_lang($table, $dst)) {
        echo "  SKIP $table (dst already has rows)\n";
        return 0;
    }
    $count = 0;
    foreach (rows("SELECT * FROM $table WHERE lang_id=?", [$src]) as $r) {
        unset($r['id']);
        $r['lang_id'] = $dst;
        insert($table, $r);
        $count++;
    }
    echo "  $table: $count rows\n";
    return $count;
}

// ── Simple per-language tables (shared base table FK stays the same) ───────
$simple_tables = [
    'home_content', 'home_why_slides', 'reviews_t', 'terms_t',
    'solution_items_t', 'solution_pages_t', 'solution_features_t',
    'sol_page_blocks', 'authors_t', 'cases_t', 'case_key_results',
    'case_challenges_t', 'case_tech_items', 'case_services',
    'posts_t', 'post_tags', 'post_toc',
];
echo "Simple tables:\n";
foreach ($simple_tables as $t) {
    try { copy_simple($t, $SRC, $DST); }
    catch (Exception $e) { echo "  ERROR $t: " . $e->getMessage() . "\n"; }
}

// ── settings (lang_id nullable; only copy rows that ARE per-language) ──────
echo "\nsettings:\n";
if (table_empty_for_lang('settings', $DST)) {
    $count = 0;
    foreach (rows("SELECT * FROM settings WHERE lang_id=?", [$SRC]) as $r) {
        unset($r['id']);
        $r['lang_id'] = $DST;
        try { insert('settings', $r); $count++; } catch (Exception $e) {}
    }
    echo "  settings: $count rows\n";
} else {
    echo "  SKIP settings (dst already has rows)\n";
}

// ── Navigation: nav_items -> nav_mega_categories -> nav_mega_subitems ──────
// These need ID remapping since nav_items itself is fully per-language (not split base+_t).
echo "\nNavigation:\n";
if (table_empty_for_lang('nav_items', $DST)) {
    $navItemMap = [];
    $items = rows("SELECT * FROM nav_items WHERE lang_id=?", [$SRC]);
    foreach ($items as $old) {
        $oldId = $old['id'];
        unset($old['id']);
        $old['lang_id'] = $DST;
        // parent_id remap (if it points to another nav_item already migrated)
        if (!empty($old['parent_id']) && isset($navItemMap[$old['parent_id']])) {
            $old['parent_id'] = $navItemMap[$old['parent_id']];
        }
        $newId = insert('nav_items', $old);
        $navItemMap[$oldId] = $newId;
    }
    echo "  nav_items: " . count($items) . " rows\n";

    $catMap = [];
    $cats = rows("SELECT * FROM nav_mega_categories WHERE lang_id=?", [$SRC]);
    foreach ($cats as $old) {
        $oldId = $old['id'];
        unset($old['id']);
        $old['lang_id'] = $DST;
        if (isset($navItemMap[$old['nav_item_id']])) {
            $old['nav_item_id'] = $navItemMap[$old['nav_item_id']];
            $newId = insert('nav_mega_categories', $old);
            $catMap[$oldId] = $newId;
        }
    }
    echo "  nav_mega_categories: " . count($catMap) . " rows\n";

    $subCount = 0;
    $subs = rows("SELECT * FROM nav_mega_subitems WHERE lang_id=?", [$SRC]);
    foreach ($subs as $old) {
        unset($old['id']);
        $old['lang_id'] = $DST;
        if (isset($catMap[$old['category_id']])) {
            $old['category_id'] = $catMap[$old['category_id']];
            insert('nav_mega_subitems', $old);
            $subCount++;
        }
    }
    echo "  nav_mega_subitems: $subCount rows\n";
} else {
    echo "  SKIP navigation (dst nav_items already has rows)\n";
}

echo "\nDone.\n";
