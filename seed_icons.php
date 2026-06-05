<?php
// Trigger: /seed_icons.php?key=sidis2026
// Add &dry for preview mode
// Add &fix_nav=1 to also fix nav subitem URLs (for Industries/Solutions menu links)
if (($_GET['key'] ?? '') !== 'sidis2026') { http_response_code(403); die('Forbidden'); }

require_once __DIR__ . '/includes/functions.php';

// Ensure white-icon column exists
try { db()->exec("ALTER TABLE solution_pages ADD COLUMN icon_svg_white MEDIUMTEXT DEFAULT NULL"); } catch(Exception $e) {}

$dry_run = isset($_GET['dry']);
$fix_nav = isset($_GET['fix_nav']);
$base    = __DIR__ . '/Icons';
$map     = ['Departments' => 'department', 'Industries' => 'industry', 'Solutions' => 'solution'];

function norm(string $s): string {
    $s = strtolower($s);
    $s = str_replace(['&', '-', '_', '(', ')'], ' ', $s);
    $s = preg_replace('/[^a-z0-9 ]/', '', $s);
    $s = preg_replace('/\s+/', ' ', $s);
    return trim($s);
}

function clean_svg(string $content): string {
    $content = preg_replace('/<\?xml[^?]*\?>\s*/i', '', $content);
    return trim($content);
}

function find_page_by_norm(array $pages, string $type, string $norm_name): ?array {
    foreach ($pages as $page) {
        if ($page['type'] !== $type) continue;
        if (norm($page['title'] ?? $page['slug']) === $norm_name) return $page;
    }
    foreach ($pages as $page) {
        if ($page['type'] !== $type) continue;
        $nt = norm($page['title'] ?? $page['slug']);
        if (strpos($nt, $norm_name) !== false || strpos($norm_name, $nt) !== false) return $page;
    }
    return null;
}

header('Content-Type: text/plain; charset=utf-8');
echo ($dry_run ? "=== DRY RUN ===" : "=== APPLIED ===") . "\n\n";

$pages = rows(
    'SELECT sp.id, sp.type, sp.slug, spt.title
     FROM solution_pages sp
     LEFT JOIN solution_pages_t spt ON sp.id = spt.page_id AND spt.lang_id = 1',
    []
);

// ── Part 1: Black icons → icon_svg ────────────────────────────────────────────
echo "── Part 1: Black icons → icon_svg ──\n";
$updated = 0;
$skipped = 0;

foreach ($map as $folder => $type) {
    $dir = $base . '/' . $folder;
    if (!is_dir($dir)) { echo "⚠️  Folder not found: $dir\n"; continue; }

    foreach (glob($dir . '/*.svg') as $file) {
        $filename = basename($file, '.svg');
        if (substr($filename, -6) === '-white') continue; // white icons handled in Part 1b

        $page = find_page_by_norm($pages, $type, norm($filename));
        if (!$page) { echo "❌  NO MATCH [$folder] \"$filename\"\n"; $skipped++; continue; }

        if (!$dry_run) {
            $svg = clean_svg(file_get_contents($file));
            db()->exec("UPDATE solution_pages SET icon_svg = " . db()->quote($svg) . " WHERE id = {$page['id']}");
        }
        echo "✅  [$folder] \"$filename\" → \"{$page['title']}\" (slug={$page['slug']})" . ($dry_run ? ' [DRY]' : '') . "\n";
        $updated++;
    }
}
echo "\nBlack matched: $updated | No match: $skipped\n";

// ── Part 1b: White icons → icon_svg_white ────────────────────────────────────
echo "\n── Part 1b: White icons → icon_svg_white ──\n";
$w_updated = 0;
$w_skipped = 0;

foreach ($map as $folder => $type) {
    $dir = $base . '/' . $folder;
    if (!is_dir($dir)) continue;

    foreach (glob($dir . '/*-white.svg') as $file) {
        $filename     = basename($file, '.svg');       // e.g. "HR-white"
        $base_name    = substr($filename, 0, -6);      // strip "-white" → "HR"
        $norm_name    = norm($base_name);

        $page = find_page_by_norm($pages, $type, $norm_name);
        if (!$page) { echo "❌  NO MATCH [$folder] \"$filename\"\n"; $w_skipped++; continue; }

        if (!$dry_run) {
            $svg = clean_svg(file_get_contents($file));
            db()->exec("UPDATE solution_pages SET icon_svg_white = " . db()->quote($svg) . " WHERE id = {$page['id']}");
        }
        echo "✅  [$folder] \"$filename\" → \"{$page['title']}\" (slug={$page['slug']})" . ($dry_run ? ' [DRY]' : '') . "\n";
        $w_updated++;
    }
}
echo "\nWhite matched: $w_updated | No match: $w_skipped\n";

// ── Part 2: Fix nav_mega_subitems URLs ────────────────────────────────────────
echo "\n── Part 2: Fix nav subitem URLs ──\n";

$sp_map = [];
$all_sp = rows('SELECT sp.slug, sp.type, spt.title FROM solution_pages sp LEFT JOIN solution_pages_t spt ON sp.id=spt.page_id AND spt.lang_id=1 WHERE sp.is_active=1', []);
foreach ($all_sp as $sp) {
    if ($sp['title']) {
        $sp_map[strtolower(trim($sp['title']))] = '/' . ($sp['type'] === 'industry' ? 'industries' : $sp['type'] . 's') . '/' . $sp['slug'] . '/';
    }
}

$subitems = rows('SELECT nms.id, nms.title, nms.url FROM nav_mega_subitems nms WHERE nms.title != "" ORDER BY nms.id', []);

$fixed = 0;
$ok    = 0;
foreach ($subitems as $sub) {
    $key      = strtolower(trim($sub['title']));
    $expected = $sp_map[$key] ?? null;
    if (!$expected) continue;

    if ($sub['url'] === $expected) { $ok++; continue; }

    echo ($dry_run ? '[DRY] ' : '') . "Fix URL: \"{$sub['title']}\" → {$expected} (was: {$sub['url']})\n";
    if (!$dry_run) {
        db()->exec("UPDATE nav_mega_subitems SET url = " . db()->quote($expected) . " WHERE id = {$sub['id']}");
    }
    $fixed++;
}

echo "\nURLs already correct: $ok | Fixed: $fixed\n";
echo "\nDone.\n";
