<?php
// One-off: generate responsive (480w/960w) WebP variants for every existing
// image in the media library. Run once via browser, then DELETE this file.
require __DIR__ . '/includes/functions.php';

$SECRET = 'sidis-backfill-2026';
if (($_GET['key'] ?? '') !== $SECRET) { http_response_code(403); exit('forbidden'); }

header('Content-Type: text/plain; charset=utf-8');

$items = rows("SELECT * FROM media WHERE mime_type IN ('image/jpeg','image/png','image/webp')");
echo "Processing " . count($items) . " images...\n\n";

$done = 0;
$skipped = 0;
foreach ($items as $m) {
    $abs = UPLOAD_DIR . $m['path'];
    if (!file_exists($abs)) { echo "MISSING: {$m['path']}\n"; $skipped++; continue; }
    generate_responsive_variants($abs, $m['mime_type']);
    $done++;
}
echo "\nDone. Processed: $done, skipped: $skipped\n";
