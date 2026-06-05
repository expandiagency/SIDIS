<?php
// Trigger: /seed_icons.php?key=sidis2026
if (($_GET['key'] ?? '') !== 'sidis2026') { http_response_code(403); die('Forbidden'); }

require_once __DIR__ . '/includes/functions.php';

$dry_run = isset($_GET['dry']);
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
    // strip XML declaration
    $content = preg_replace('/<\?xml[^?]*\?>\s*/i', '', $content);
    return trim($content);
}

// Load all solution_pages with titles
$pages = rows(
    'SELECT sp.id, sp.type, sp.slug, spt.title
     FROM solution_pages sp
     LEFT JOIN solution_pages_t spt ON sp.id = spt.page_id AND spt.lang_id = 1',
    []
);

$results = [];
$updated = 0;
$skipped = 0;

foreach ($map as $folder => $type) {
    $dir = $base . '/' . $folder;
    if (!is_dir($dir)) { $results[] = "⚠️  Folder not found: $dir"; continue; }

    $files = glob($dir . '/*.svg');
    foreach ($files as $file) {
        $filename = basename($file, '.svg');
        $norm_file = norm($filename);

        // Find best matching page
        $best_id    = null;
        $best_title = null;
        $best_slug  = null;

        foreach ($pages as $page) {
            if ($page['type'] !== $type) continue;
            $norm_title = norm($page['title'] ?? $page['slug']);
            if ($norm_title === $norm_file) {
                $best_id    = $page['id'];
                $best_title = $page['title'];
                $best_slug  = $page['slug'];
                break;
            }
        }

        // Fallback: partial match (file name contained in title or vice versa)
        if (!$best_id) {
            foreach ($pages as $page) {
                if ($page['type'] !== $type) continue;
                $norm_title = norm($page['title'] ?? $page['slug']);
                if (strpos($norm_title, $norm_file) !== false || strpos($norm_file, $norm_title) !== false) {
                    $best_id    = $page['id'];
                    $best_title = $page['title'];
                    $best_slug  = $page['slug'];
                    break;
                }
            }
        }

        if (!$best_id) {
            $results[] = "❌  NO MATCH [$folder] \"$filename\"";
            $skipped++;
            continue;
        }

        $svg = clean_svg(file_get_contents($file));

        if (!$dry_run) {
            db()->exec("UPDATE solution_pages SET icon_svg = " . db()->quote($svg) . " WHERE id = $best_id");
        }

        $results[] = "✅  [$folder] \"$filename\" → [{$type}] \"$best_title\" (id=$best_id, slug=$best_slug)" . ($dry_run ? ' [DRY RUN]' : '');
        $updated++;
    }
}

header('Content-Type: text/plain; charset=utf-8');
echo ($dry_run ? "=== DRY RUN MODE (add &dry to URL to preview without saving) ===" : "=== APPLIED ===") . "\n\n";
foreach ($results as $r) echo $r . "\n";
echo "\n---\n";
echo "Matched & " . ($dry_run ? 'would update' : 'updated') . ": $updated\n";
echo "No match: $skipped\n";
