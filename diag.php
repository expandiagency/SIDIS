<?php
if (($_GET['key'] ?? '') !== 'sidis2026') { http_response_code(403); die('Forbidden'); }
$t0 = microtime(true);
require_once __DIR__ . '/includes/functions.php';
$t1 = microtime(true);

header('Content-Type: text/plain');
echo "bootstrap (functions.php incl. DB connect + seed_white_icons): " . round(($t1-$t0)*1000,1) . "ms\n\n";

echo "--- active solution_pages with empty icon_svg_white ---\n";
print_r(rows("SELECT id,slug,type,is_active FROM solution_pages WHERE is_active=1 AND (icon_svg_white IS NULL OR icon_svg_white='')"));

$t2 = microtime(true);
get_home(1);
$t3 = microtime(true);
echo "\nget_home(1): " . round(($t3-$t2)*1000,1) . "ms\n";

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

echo "\n--- ALTER icon_svg test ---\n";
try { db()->exec("ALTER TABLE solution_pages ADD COLUMN icon_svg_test_xyz MEDIUMTEXT DEFAULT NULL"); echo "ALTER OK\n"; db()->exec("ALTER TABLE solution_pages DROP COLUMN icon_svg_test_xyz"); echo "DROP OK\n"; } catch (Exception $e) { echo "ALTER FAIL: ".$e->getMessage()."\n"; }

echo "\n--- CREATE TABLE test ---\n";
try {
    db()->exec("CREATE TABLE IF NOT EXISTS sol_page_blocks_test (id INT AUTO_INCREMENT PRIMARY KEY, x INT) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "CREATE OK\n";
    db()->exec("DROP TABLE sol_page_blocks_test");
    echo "DROP TABLE OK\n";
} catch (Exception $e) { echo "CREATE FAIL: ".$e->getMessage()."\n"; }

echo "\n--- sol_page_blocks exists? ---\n";
try {
    $r = row("SHOW TABLES LIKE 'sol_page_blocks'");
    echo $r ? "EXISTS\n" : "MISSING\n";
} catch (Exception $e) { echo $e->getMessage(); }

echo "\n--- full sol_pages.php list query ---\n";
try {
    $lang_id = 1;
    $sql  = 'SELECT sp.*, spt.title, spt.description, m.path as image_path
             FROM solution_pages sp
             LEFT JOIN solution_pages_t spt ON sp.id=spt.page_id AND spt.lang_id=?
             LEFT JOIN media m ON sp.image_id=m.id
             WHERE 1 ORDER BY sp.type, sp.sort_order';
    $pages = rows($sql, [$lang_id]);
    echo "rows: " . count($pages) . "\n";
} catch (Exception $e) { echo "QUERY FAIL: ".$e->getMessage()."\n"; }
