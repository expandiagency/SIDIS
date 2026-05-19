<?php
require_once __DIR__ . '/includes/functions.php';
echo '<pre>';
$lang_id = 1;
$slugs = ['automate-repetitive-tasks-with-rpa','crm-integration-guide-for-smbs','ai-chatbots-for-customer-support','erp-implementation-lessons-learned'];

foreach ($slugs as $slug) {
    $post = row('SELECT p.id FROM posts p WHERE p.slug=?', [$slug]);
    if (!$post) { echo "Not found: $slug\n"; continue; }
    $pt = row('SELECT id, content FROM posts_t WHERE post_id=? AND lang_id=?', [(int)$post['id'], $lang_id]);
    if (!$pt) continue;

    $content = $pt['content'];
    // Check if gallery is at the very start (before first heading)
    if (strpos($content, '[gallery') !== 0) { echo "$slug: gallery not at start, skipping\n"; continue; }

    // Extract gallery shortcode
    $end = strpos($content, ']');
    if ($end === false) continue;
    $gallery_sc = substr($content, 0, $end + 1);
    $rest = ltrim(substr($content, $end + 1));

    // Place gallery AFTER the first closing </p> tag (after first paragraph)
    $p_pos = strpos($rest, '</p>');
    if ($p_pos !== false) {
        $insert_at = $p_pos + 4;
        $new_content = substr($rest, 0, $insert_at) . "\n" . $gallery_sc . "\n" . substr($rest, $insert_at);
    } else {
        // Fallback: after first </h2>
        $h_pos = strpos($rest, '</h2>');
        if ($h_pos !== false) {
            $insert_at = $h_pos + 5;
            $new_content = substr($rest, 0, $insert_at) . "\n" . $gallery_sc . "\n" . substr($rest, $insert_at);
        } else {
            $new_content = $rest . "\n" . $gallery_sc;
        }
    }

    update('posts_t', ['content' => $new_content], ['id' => $pt['id']]);
    echo "$slug: gallery moved after first paragraph ✓\n";
}

echo "\nDone.";
echo '</pre>';
unlink(__FILE__);
echo '<p style="color:green;font-family:monospace">Script self-deleted.</p>';
