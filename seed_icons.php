<?php
// Trigger: /seed_icons.php?key=sidis2026
// Add &dry for preview mode
// Add &fix_nav=1 to also fix nav subitem URLs (for Industries/Solutions menu links)
if (($_GET['key'] ?? '') !== 'sidis2026') { http_response_code(403); die('Forbidden'); }

require_once __DIR__ . '/includes/functions.php';

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

header('Content-Type: text/plain; charset=utf-8');

// ── Part 1: Assign icons to solution_pages ────────────────────────────────────
echo ($dry_run ? "=== DRY RUN ===" : "=== APPLIED ===") . "\n\n";
echo "── Part 1: Icons → solution_pages ──\n";

$pages = rows(
    'SELECT sp.id, sp.type, sp.slug, spt.title
     FROM solution_pages sp
     LEFT JOIN solution_pages_t spt ON sp.id = spt.page_id AND spt.lang_id = 1',
    []
);

$updated = 0;
$skipped = 0;

foreach ($map as $folder => $type) {
    $dir = $base . '/' . $folder;
    if (!is_dir($dir)) { echo "⚠️  Folder not found: $dir\n"; continue; }

    $files = glob($dir . '/*.svg');
    foreach ($files as $file) {
        $filename  = basename($file, '.svg');
        $norm_file = norm($filename);
        $best_id = $best_title = $best_slug = null;

        foreach ($pages as $page) {
            if ($page['type'] !== $type) continue;
            if (norm($page['title'] ?? $page['slug']) === $norm_file) {
                $best_id = $page['id']; $best_title = $page['title']; $best_slug = $page['slug'];
                break;
            }
        }
        if (!$best_id) {
            foreach ($pages as $page) {
                if ($page['type'] !== $type) continue;
                $nt = norm($page['title'] ?? $page['slug']);
                if (strpos($nt, $norm_file) !== false || strpos($norm_file, $nt) !== false) {
                    $best_id = $page['id']; $best_title = $page['title']; $best_slug = $page['slug'];
                    break;
                }
            }
        }

        if (!$best_id) { echo "❌  NO MATCH [$folder] \"$filename\"\n"; $skipped++; continue; }

        if (!$dry_run) {
            $svg = clean_svg(file_get_contents($file));
            db()->exec("UPDATE solution_pages SET icon_svg = " . db()->quote($svg) . " WHERE id = $best_id");
        }
        echo "✅  [$folder] \"$filename\" → \"$best_title\" (slug=$best_slug)" . ($dry_run ? ' [DRY]' : '') . "\n";
        $updated++;
    }
}

echo "\nMatched: $updated | No match: $skipped\n";

// ── Part 2: Fix nav_mega_subitems URLs ────────────────────────────────────────
echo "\n── Part 2: Fix nav subitem URLs ──\n";

// Build title→url map from solution_pages
$sp_map = [];
$all_sp = rows('SELECT sp.slug, sp.type, spt.title FROM solution_pages sp LEFT JOIN solution_pages_t spt ON sp.id=spt.page_id AND spt.lang_id=1 WHERE sp.is_active=1', []);
foreach ($all_sp as $sp) {
    if ($sp['title']) {
        $sp_map[strtolower(trim($sp['title']))] = '/' . $sp['type'] . 's/' . $sp['slug'] . '/';
    }
}

// Get all subitems that have a title but URL doesn't match a known solution page
$subitems = rows('SELECT nms.id, nms.title, nms.url FROM nav_mega_subitems nms WHERE nms.title != "" ORDER BY nms.id', []);

$fixed = 0;
$ok    = 0;
foreach ($subitems as $sub) {
    $key      = strtolower(trim($sub['title']));
    $expected = $sp_map[$key] ?? null;
    if (!$expected) continue; // title doesn't match any solution page

    if ($sub['url'] === $expected) { $ok++; continue; }

    echo ($dry_run ? '[DRY] ' : '') . "Fix URL: \"{$sub['title']}\" → {$expected} (was: {$sub['url']})\n";
    if (!$dry_run) {
        db()->exec("UPDATE nav_mega_subitems SET url = " . db()->quote($expected) . " WHERE id = {$sub['id']}");
    }
    $fixed++;
}

echo "\nURLs already correct: $ok | Fixed: $fixed\n";
echo "\nDone.\n";
