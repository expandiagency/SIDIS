<?php
/**
 * Article patch — run once to seed author, tags and rich content for first article.
 * Visit: https://sidis.expandi.agency/article_patch.php
 * Self-deletes after successful run.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/includes/functions.php';

// Ensure extras column exists
try { db()->exec("ALTER TABLE posts_t ADD COLUMN extras MEDIUMTEXT DEFAULT NULL"); } catch(Exception $e) {}

echo '<pre>';
$lang_id   = 1;
$post_slug = 'automate-repetitive-tasks-with-rpa';

// ── 1. Author ────────────────────────────────────────────────────────────────
$author = row('SELECT a.id FROM authors a JOIN authors_t at_ ON a.id=at_.author_id AND at_.lang_id=? WHERE at_.name=?', [$lang_id, 'Alex Kovalsky']);
if (!$author) {
    $author_id = insert('authors', ['linkedin_url'=>'https://linkedin.com/in/alex-kovalsky','image_id'=>null,'sort_order'=>0,'is_active'=>1]);
    insert('authors_t', ['author_id'=>$author_id,'lang_id'=>$lang_id,'name'=>'Alex Kovalsky','title'=>'Head of Marketing, NovaTech Industries']);
    echo "Author 'Alex Kovalsky' created (id=$author_id)\n";
} else {
    $author_id = (int)$author['id'];
    echo "Author already exists (id=$author_id)\n";
}

// ── 2. Find post ─────────────────────────────────────────────────────────────
$post = row('SELECT p.id FROM posts p WHERE p.slug=?', [$post_slug]);
if (!$post) {
    echo "Post '$post_slug' not found — nothing to do.\n";
    echo '</pre>';
    exit;
}
$post_id = (int)$post['id'];
echo "Found post id=$post_id\n";

// ── 3. Link author ───────────────────────────────────────────────────────────
update('posts', ['author_id'=>$author_id], ['id'=>$post_id]);
echo "Author linked.\n";

// ── 4. Tags ──────────────────────────────────────────────────────────────────
$tags_to_add = ['Marketing automation','Customer Support','Affiliate Marketing'];
$existing    = rows('SELECT tag_text FROM post_tags WHERE post_id=? AND lang_id=?', [$post_id,$lang_id]);
$existing_tx = array_column($existing, 'tag_text');
$sort        = count($existing);
foreach ($tags_to_add as $tag) {
    if (!in_array($tag, $existing_tx)) {
        insert('post_tags', ['post_id'=>$post_id,'lang_id'=>$lang_id,'tag_text'=>$tag,'sort_order'=>$sort++]);
        echo "Tag '$tag' added.\n";
    } else {
        echo "Tag '$tag' already exists.\n";
    }
}

// ── 5. Content with shortcodes ───────────────────────────────────────────────
$pt = row('SELECT id, content FROM posts_t WHERE post_id=? AND lang_id=?', [$post_id,$lang_id]);
if ($pt) {
    $content = $pt['content'] ?? '';
    $gallery = '[gallery img="/assets/img/blog/image-1.jpg,/assets/img/blog/image-2.webp,/assets/img/blog/image-3.webp"]';
    $media   = '[media img="/assets/img/blog/Image-1.webp" video="/assets/video/1-hero.mp4"]';
    $cta     = '[cta]';

    if (strpos($content, '[gallery') === false) {
        $content = $gallery . "\n\n" . $content;
        echo "Gallery shortcode added.\n";
    }
    if (strpos($content, '[media') === false) {
        $pos = strpos($content, '</h2>');
        if ($pos !== false) {
            $ins     = $pos + strlen('</h2>');
            $content = substr($content, 0, $ins) . "\n" . $media . "\n" . substr($content, $ins);
        } else {
            $content .= "\n\n" . $media;
        }
        echo "Media shortcode added.\n";
    }
    if (strpos($content, '[cta]') === false) {
        $content .= "\n\n" . $cta;
        echo "CTA shortcode added.\n";
    }
    update('posts_t', ['content'=>$content], ['id'=>$pt['id']]);
    echo "Content updated.\n";
}

// ── 6. Extras (FAQ for first article) ────────────────────────────────────────
$pt2 = row('SELECT id, extras FROM posts_t WHERE post_id=? AND lang_id=?', [$post_id,$lang_id]);
if ($pt2 && !$pt2['extras']) {
    $extras = [
        'faq_title' => 'Questions & answers',
        'faq' => [
            ['q'=>'What are your pricing options?',        'a'=>'Our team is based in Eastern Europe, which gives us an advantage in operational costs — low taxes, rent, and payroll. We pass the savings to our clients.'],
            ['q'=>'What is your typical project timeline?','a'=>'Most automation projects take 4–8 weeks from discovery to deployment. Complex integrations may take longer.'],
            ['q'=>'How do you handle customer feedback?',  'a'=>'We work in iterative cycles with regular check-ins. Client feedback is incorporated at each stage.'],
            ['q'=>'Can you provide case studies?',         'a'=>'Yes — see our Case Studies section. We can also provide references upon request.'],
        ],
        'articles_title' => "Latest Automation\nInsights",
        'related_post_ids' => [],
        'cta_title'     => 'Curious which RPA solution fits your business needs?',
        'cta_btn1_text' => 'Try AI assistant',
        'cta_btn1_url'  => '#',
        'cta_btn2_text' => 'Free audit',
        'cta_btn2_url'  => '#getintouch',
    ];
    update('posts_t', ['extras'=>json_encode($extras, JSON_UNESCAPED_UNICODE)], ['id'=>$pt2['id']]);
    echo "Extras (FAQ + CTA config) saved.\n";
} else {
    echo "Extras already set.\n";
}

echo "\nPatch complete! Visit /blog/automate-repetitive-tasks-with-rpa/ to verify.\n";
echo '</pre>';

// Self-delete
unlink(__FILE__);
echo '<p style="color:green">Script self-deleted successfully.</p>';
