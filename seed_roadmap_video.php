<?php
// Updates roadmap block video_path and video_poster for all solution/department/industry pages
// Trigger: /seed_roadmap_video.php?key=sidis2026
if (($_GET['key'] ?? '') !== 'sidis2026') { http_response_code(403); die('Forbidden'); }
require_once __DIR__ . '/includes/functions.php';

$video_path   = './assets/video/compressed-v2/Video-cover-roadmap.mp4';
$video_poster = './assets/video/compressed-v2/Video-cover-blog-roadmap-poster.webp';

header('Content-Type: text/plain; charset=utf-8');

$blocks = rows(
    "SELECT spb.id, spb.page_id, spb.content, sp.slug, sp.type
     FROM sol_page_blocks spb
     JOIN solution_pages sp ON sp.id = spb.page_id
     WHERE spb.block_key = 'roadmap' AND spb.lang_id = 1 AND sp.is_active = 1",
    []
);

echo "Found " . count($blocks) . " roadmap blocks\n\n";

$ok = 0;
foreach ($blocks as $b) {
    $c = json_decode($b['content'] ?: '{}', true) ?: [];
    $c['video_path']   = $video_path;
    $c['video_poster'] = $video_poster;
    update('sol_page_blocks', ['content' => json_encode($c, JSON_UNESCAPED_UNICODE)], ['id' => $b['id']]);
    echo "✅  [{$b['type']}] {$b['slug']}\n";
    $ok++;
}

echo "\n---\nUpdated: $ok blocks\n";
