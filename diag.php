<?php
if (($_GET['key'] ?? '') !== 'sidis2026') { http_response_code(403); die('Forbidden'); }
require_once __DIR__ . '/includes/functions.php';

header('Content-Type: text/plain');

$tables = ['solution_pages','solution_pages_t','solution_items','solution_items_t','solution_features','nav_items','nav_mega_categories','nav_mega_subitems','settings','posts','cases','media'];
foreach ($tables as $t) {
    try {
        $c = row("SELECT COUNT(*) c FROM `$t`");
        echo "$t: " . $c['c'] . "\n";
    } catch (Exception $e) {
        echo "$t: ERROR " . $e->getMessage() . "\n";
    }
}

echo "\n--- solution_pages sample ---\n";
try {
    print_r(rows("SELECT id,slug,type,is_active FROM solution_pages LIMIT 5"));
} catch (Exception $e) { echo $e->getMessage(); }

echo "\n--- icon_svg_white nulls ---\n";
try {
    print_r(rows("SELECT COUNT(*) c FROM solution_pages WHERE icon_svg_white IS NULL OR icon_svg_white=''"));
} catch (Exception $e) { echo $e->getMessage(); }
