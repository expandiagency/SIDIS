<?php
/**
 * Article patch script — run once then self-deletes.
 * - Adds author "Alex Kovalsky" if not exists
 * - Links author to post "automate-repetitive-tasks-with-rpa"
 * - Adds tags: Marketing automation, Customer Support, Affiliate Marketing
 * - Updates post content with [gallery], [cta], [media] shortcodes
 */

require_once __DIR__ . '/includes/functions.php';

$lang_id = 1;
$post_slug = 'automate-repetitive-tasks-with-rpa';

// ── 1. Add author if not exists ──────────────────────────────────────────────
$author = row('SELECT a.id FROM authors a JOIN authors_t at_ ON a.id=at_.author_id AND at_.lang_id=? WHERE at_.name=?', [$lang_id, 'Alex Kovalsky']);
if (!$author) {
    $author_id = insert('authors', ['linkedin_url' => '', 'image_id' => null, 'sort_order' => 0, 'is_active' => 1]);
    insert('authors_t', ['author_id' => $author_id, 'lang_id' => $lang_id, 'name' => 'Alex Kovalsky', 'title' => 'Head of Marketing, NovaTech Industries']);
    echo "Author 'Alex Kovalsky' created (id=$author_id)\n";
} else {
    $author_id = (int)$author['id'];
    echo "Author already exists (id=$author_id)\n";
}

// ── 2. Find post ─────────────────────────────────────────────────────────────
$post = row('SELECT p.id FROM posts p WHERE p.slug=?', [$post_slug]);
if (!$post) {
    echo "Post '$post_slug' not found — skipping.\n";
} else {
    $post_id = (int)$post['id'];
    echo "Found post id=$post_id\n";

    // ── 3. Link author ───────────────────────────────────────────────────────
    update('posts', ['author_id' => $author_id], ['id' => $post_id]);
    echo "Author linked to post.\n";

    // ── 4. Add tags ──────────────────────────────────────────────────────────
    $tags_to_add = ['Marketing automation', 'Customer Support', 'Affiliate Marketing'];
    $existing_tags = rows('SELECT tag_text FROM post_tags WHERE post_id=? AND lang_id=?', [$post_id, $lang_id]);
    $existing_tag_texts = array_map(function($t) { return $t['tag_text']; }, $existing_tags);
    $sort = count($existing_tags);
    foreach ($tags_to_add as $tag) {
        if (!in_array($tag, $existing_tag_texts)) {
            insert('post_tags', ['post_id' => $post_id, 'lang_id' => $lang_id, 'tag_text' => $tag, 'sort_order' => $sort++]);
            echo "Tag '$tag' added.\n";
        } else {
            echo "Tag '$tag' already exists.\n";
        }
    }

    // ── 5. Update content with shortcodes ────────────────────────────────────
    $pt = row('SELECT id, content FROM posts_t WHERE post_id=? AND lang_id=?', [$post_id, $lang_id]);
    if ($pt) {
        $current_content = $pt['content'] ?? '';

        // Only add shortcodes if not already present
        $gallery_sc = '[gallery img="/assets/img/blog/image-1.jpg,/assets/img/blog/image-2.webp,/assets/img/blog/image-3.webp"]';
        $media_sc   = '[media img="/assets/img/blog/image-4.webp" video="/assets/video/1-hero.mp4"]';
        $cta_sc     = '[cta]';

        if (strpos($current_content, '[gallery') === false) {
            $current_content = $gallery_sc . "\n\n" . $current_content;
            echo "Gallery shortcode prepended to content.\n";
        }
        if (strpos($current_content, '[media') === false) {
            // Insert media shortcode after the first closing heading tag
            $insert_after = '</h2>';
            $pos = strpos($current_content, $insert_after);
            if ($pos !== false) {
                $current_content = substr($current_content, 0, $pos + strlen($insert_after)) . "\n" . $media_sc . "\n" . substr($current_content, $pos + strlen($insert_after));
            } else {
                $current_content .= "\n\n" . $media_sc;
            }
            echo "Media shortcode inserted into content.\n";
        }
        if (strpos($current_content, '[cta]') === false) {
            $current_content .= "\n\n" . $cta_sc;
            echo "CTA shortcode appended to content.\n";
        }

        update('posts_t', ['content' => $current_content], ['id' => $pt['id']]);
        echo "Content updated.\n";
    }
}

// ── 6. Self-delete ───────────────────────────────────────────────────────────
unlink(__FILE__);
echo "Patch script self-deleted.\n";
