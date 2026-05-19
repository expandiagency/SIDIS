<?php
/**
 * Patch ALL blog posts: author avatar, tags, FAQ, CTA, gallery/media shortcodes.
 * Visit: https://sidis.expandi.agency/all_posts_patch.php
 * Self-deletes after run.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/includes/functions.php';

try { db()->exec("ALTER TABLE posts_t ADD COLUMN extras MEDIUMTEXT DEFAULT NULL"); } catch(Exception $e) {}

echo '<pre>';
$lang_id = 1;

// ── 1. Author image ──────────────────────────────────────────────────────────
// Reuse existing static asset via media table (path stored with leading / stays as-is in media_url)
$author = row('SELECT a.id, a.image_id FROM authors a JOIN authors_t at_ ON a.id=at_.author_id AND at_.lang_id=? WHERE at_.name=?', [$lang_id, 'Alex Kovalsky']);
if (!$author) {
    $author_id = insert('authors', ['linkedin_url'=>'https://linkedin.com/in/alex-kovalsky','image_id'=>null]);
    insert('authors_t', ['author_id'=>$author_id,'lang_id'=>$lang_id,'name'=>'Alex Kovalsky','title'=>'Head of Marketing, NovaTech Industries']);
    echo "Author created id=$author_id\n";
} else {
    $author_id = (int)$author['id'];
    echo "Author exists id=$author_id\n";
}

// Add author avatar if missing
if (!$author || !$author['image_id']) {
    // Check if media record for this image already exists
    $media = row("SELECT id FROM media WHERE path LIKE '%reviews/image-2%'");
    if (!$media) {
        $media_id = insert('media', [
            'filename'      => 'image-2.webp',
            'original_name' => 'alex-kovalsky.webp',
            'path'          => '/assets/img/reviews/image-2.webp',
            'mime_type'     => 'image/webp',
            'file_size'     => 0,
            'alt_text'      => 'Alex Kovalsky',
        ]);
        echo "Media record created id=$media_id\n";
    } else {
        $media_id = (int)$media['id'];
        echo "Media record exists id=$media_id\n";
    }
    update('authors', ['image_id'=>$media_id], ['id'=>$author_id]);
    echo "Author avatar set.\n";
} else {
    echo "Author already has avatar.\n";
}

// ── 2. Posts config ───────────────────────────────────────────────────────────
$default_faq = [
    ['q'=>'What are your pricing options?',        'a'=>'Our team is based in Eastern Europe, which gives us an advantage in operational costs — low taxes, rent, and payroll. We pass the savings to our clients.'],
    ['q'=>'What is your typical project timeline?','a'=>'Most automation projects take 4–8 weeks from discovery to deployment. Complex integrations may take longer.'],
    ['q'=>'How do you handle customer feedback?',  'a'=>'We work in iterative cycles with regular check-ins. Client feedback is incorporated at each stage.'],
    ['q'=>'Can you provide case studies?',         'a'=>'Yes — see our Case Studies section. We can also provide references upon request.'],
];
$default_extras_base = [
    'cta_title'      => 'Curious which RPA solution fits your business needs?',
    'cta_btn1_text'  => 'Try AI assistant',
    'cta_btn1_url'   => '#',
    'cta_btn2_text'  => 'Free audit',
    'cta_btn2_url'   => '#getintouch',
    'faq_title'      => 'Questions & answers',
    'faq'            => $default_faq,
    'articles_title' => "Latest Automation\nInsights",
    'related_post_ids' => [],
];

$posts_config = [
    'automate-repetitive-tasks-with-rpa' => [
        'tags'    => ['Marketing automation','Customer Support','Affiliate Marketing'],
        'gallery' => '/assets/img/blog/image-1.jpg,/assets/img/blog/image-2.webp',
        'img'     => '/assets/img/blog/Image-1.webp',
        'video'   => '/assets/video/1-hero.mp4',
    ],
    'crm-integration-guide-for-smbs' => [
        'tags'    => ['CRM','Integration','Automation'],
        'gallery' => '/assets/img/blog/image-2.webp,/assets/img/blog/image-3.webp',
        'img'     => '/assets/img/blog/Image-1.webp',
        'video'   => '/assets/video/1-hero.mp4',
    ],
    'ai-chatbots-for-customer-support' => [
        'tags'    => ['AI','Chatbots','Customer Support'],
        'gallery' => '/assets/img/blog/image-3.webp,/assets/img/blog/image-4.webp',
        'img'     => '/assets/img/blog/Image-1.webp',
        'video'   => '/assets/video/1-hero.mp4',
    ],
    'erp-implementation-lessons-learned' => [
        'tags'    => ['ERP','Enterprise','Implementation'],
        'gallery' => '/assets/img/blog/image-4.webp,/assets/img/blog/image-5.webp',
        'img'     => '/assets/img/blog/Image-1.webp',
        'video'   => '/assets/video/1-hero.mp4',
    ],
];

// ── 3. Patch each post ────────────────────────────────────────────────────────
foreach ($posts_config as $slug => $cfg) {
    $post = row('SELECT p.id FROM posts p WHERE p.slug=?', [$slug]);
    if (!$post) { echo "\nPost '$slug' not found — skipping.\n"; continue; }
    $post_id = (int)$post['id'];
    echo "\n--- $slug (id=$post_id) ---\n";

    // Link author
    update('posts', ['author_id'=>$author_id], ['id'=>$post_id]);
    echo "Author linked.\n";

    // Tags
    $existing_tx = array_column(rows('SELECT tag_text FROM post_tags WHERE post_id=? AND lang_id=?', [$post_id,$lang_id]), 'tag_text');
    $sort = count($existing_tx);
    foreach ($cfg['tags'] as $tag) {
        if (!in_array($tag, $existing_tx)) {
            insert('post_tags', ['post_id'=>$post_id,'lang_id'=>$lang_id,'tag_text'=>$tag,'sort_order'=>$sort++]);
            echo "Tag '$tag' added.\n";
        } else {
            echo "Tag '$tag' exists.\n";
        }
    }

    // Content shortcodes
    $pt = row('SELECT id, content FROM posts_t WHERE post_id=? AND lang_id=?', [$post_id,$lang_id]);
    if ($pt) {
        $content = $pt['content'] ?? '';
        $gallery = '[gallery img="' . $cfg['gallery'] . '"]';
        $media   = '[media img="' . $cfg['img'] . '" video="' . $cfg['video'] . '"]';

        if (strpos($content, '[gallery') === false) {
            $content = $gallery . "\n\n" . $content;
            echo "Gallery shortcode added.\n";
        }
        if (strpos($content, '[media') === false) {
            $pos = strpos($content, '</h2>');
            if ($pos !== false) {
                $ins = $pos + strlen('</h2>');
                $content = substr($content, 0, $ins) . "\n" . $media . "\n" . substr($content, $ins);
            } else {
                $content .= "\n\n" . $media;
            }
            echo "Media shortcode added.\n";
        }
        if (strpos($content, '[cta]') === false) {
            $content .= "\n\n[cta]";
            echo "CTA shortcode added.\n";
        }
        update('posts_t', ['content'=>$content], ['id'=>$pt['id']]);
        echo "Content saved.\n";
    }

    // Extras (FAQ, CTA config) — only if empty
    $pt2 = row('SELECT id, extras FROM posts_t WHERE post_id=? AND lang_id=?', [$post_id,$lang_id]);
    if ($pt2) {
        $existing_extras = $pt2['extras'] ? json_decode($pt2['extras'], true) : null;
        if (empty($existing_extras['faq'])) {
            update('posts_t', ['extras'=>json_encode($default_extras_base, JSON_UNESCAPED_UNICODE)], ['id'=>$pt2['id']]);
            echo "FAQ + CTA extras saved.\n";
        } else {
            echo "Extras already set.\n";
        }
    }
}

echo "\nAll done!\n";
echo '</pre>';

unlink(__FILE__);
echo '<p style="color:green;font-family:monospace">Script self-deleted. All posts patched!</p>';
