<?php
require_once __DIR__ . '/db.php';

// One-time migration: add icon_svg_white column if not yet present
try { db()->exec("ALTER TABLE solution_pages ADD COLUMN icon_svg_white MEDIUMTEXT DEFAULT NULL"); } catch(Exception $e) {}

// Auto-seed white icons from Icons/ folder on first page load after deploy
function _sidis_seed_white_icons(): void {
    $chk = row('SELECT id FROM solution_pages WHERE is_active=1 AND (icon_svg_white IS NULL OR icon_svg_white="") LIMIT 1');
    if (!$chk) return; // already seeded
    $base = realpath(__DIR__ . '/../Icons');
    if (!$base || !is_dir($base)) return;
    $map   = ['Departments'=>'department','Industries'=>'industry','Solutions'=>'solution'];
    $pages = rows('SELECT sp.id, sp.type, sp.slug, spt.title FROM solution_pages sp LEFT JOIN solution_pages_t spt ON sp.id=spt.page_id AND spt.lang_id=1', []);
    $norm  = function(string $s): string {
        $s = strtolower($s);
        $s = str_replace(['&','-','_','(',')'],' ',$s);
        $s = preg_replace('/[^a-z0-9 ]/','',$s);
        return trim(preg_replace('/\s+/',' ',$s));
    };
    foreach ($map as $folder => $type) {
        $dir = $base.'/'.$folder;
        if (!is_dir($dir)) continue;
        $files = glob($dir.'/*-white.svg');
        if (!$files) continue;
        foreach ($files as $file) {
            $content = @file_get_contents($file);
            if (!$content) continue;
            $fn = basename($file,'.svg');
            $nf = $norm(substr($fn,0,-6)); // strip '-white'
            foreach ($pages as $p) {
                if ($p['type'] !== $type) continue;
                if ($norm($p['title'] ?: $p['slug']) !== $nf) continue;
                $svg = trim(preg_replace('/<\?xml[^?]*\?>\s*/i','',$content));
                try { db()->exec("UPDATE solution_pages SET icon_svg_white=".db()->quote($svg)." WHERE id={$p['id']}"); } catch(Exception $e) {}
                break;
            }
        }
    }
}
_sidis_seed_white_icons();

/* ─── Language ─────────────────────────────────────────────────────────── */

function get_languages(bool $active_only = true): array {
    $sql = 'SELECT * FROM languages' . ($active_only ? ' WHERE is_active=1' : '') . ' ORDER BY sort_order,id';
    return rows($sql);
}

function get_language(string $code): ?array {
    return row('SELECT * FROM languages WHERE code=? AND is_active=1', [$code]);
}

function get_default_language(): array {
    return row('SELECT * FROM languages WHERE is_default=1 LIMIT 1')
        ?? row('SELECT * FROM languages WHERE is_active=1 ORDER BY sort_order LIMIT 1')
        ?? ['id' => 1, 'code' => 'en', 'name' => 'English', 'is_default' => 1];
}

function detect_lang(array $languages): array {
    $uri_parts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
    $first = $uri_parts[0] ?? '';
    foreach ($languages as $lang) {
        if ($lang['code'] === $first) return $lang;
    }
    return get_default_language();
}

function lang_url(string $path, array $lang, array $default_lang): string {
    $path = '/' . ltrim($path, '/');
    if ((int)$lang['is_default']) return $path;
    return '/' . $lang['code'] . $path;
}

/* ─── Navigation ───────────────────────────────────────────────────────── */

function sp_type_prefix(string $type): string {
    return $type === 'industry' ? 'industries' : $type . 's';
}

function get_nav(int $lang_id, string $location): array {
    $items = rows(
        'SELECT n.*, COALESCE(n.mega_type,"solutions") as mega_type FROM nav_items n WHERE lang_id=? AND location=? AND parent_id IS NULL AND is_active=1 ORDER BY sort_order',
        [$lang_id, $location]
    );
    foreach ($items as &$item) {
        $item['children'] = rows(
            'SELECT * FROM nav_items WHERE lang_id=? AND parent_id=? AND is_active=1 ORDER BY sort_order',
            [$lang_id, $item['id']]
        );
        if ($item['has_mega']) {
            $mega_type = $item['mega_type'] ?? 'solutions';
            if (in_array($mega_type, ['departments', 'industries'])) {
                $sp_type = $mega_type === 'departments' ? 'department' : 'industry';
                $pages = rows(
                    'SELECT sp.slug, sp.type, sp.icon_svg, sp.icon_svg_white, spt.title, spt.description
                     FROM solution_pages sp
                     LEFT JOIN solution_pages_t spt ON sp.id=spt.page_id AND spt.lang_id=?
                     WHERE sp.type=? AND sp.is_active=1 ORDER BY sp.sort_order',
                    [$lang_id, $sp_type]
                );
                $subitems = [];
                foreach ($pages as $p) {
                    $white = $p['icon_svg_white'] ?: '';
                    // Fallback: read white SVG directly from file if DB column is empty
                    if (!$white && !empty($p['title'])) {
                        $folder_map = ['department'=>'Departments','industry'=>'Industries','solution'=>'Solutions'];
                        $dir = __DIR__ . '/../Icons/' . ($folder_map[$p['type']] ?? '');
                        $fn = str_replace([' / ', '/'], [' - ', '-'], $p['title']);
                        $path = $dir . '/' . $fn . '-white.svg';
                        if (@file_exists($path)) {
                            $white = trim(preg_replace('/<\?xml[^?]*\?>\s*/i', '', @file_get_contents($path)));
                        }
                    }
                    $subitems[] = [
                        'title'          => $p['title'] ?: $p['slug'],
                        'description'    => $p['description'] ?: '',
                        'url'            => '/' . sp_type_prefix($p['type']) . '/' . $p['slug'] . '/',
                        'icon_svg'       => $p['icon_svg'] ?: '',
                        'icon_svg_white' => $white,
                    ];
                }
                $item['mega_categories'] = [['id'=>0,'title'=>'','description'=>'','subitems'=>$subitems]];
            } else {
                $item['mega_categories'] = get_mega_categories($lang_id, $item['id']);
            }
        }
    }
    return $items;
}

function get_mega_categories(int $lang_id, int $nav_item_id): array {
    $cats = rows(
        'SELECT * FROM nav_mega_categories WHERE nav_item_id=? AND lang_id=? ORDER BY sort_order',
        [$nav_item_id, $lang_id]
    );
    // Build maps from solution_pages: url→icon and title→url (for URL correction)
    $sp_icons    = [];
    $sp_title_url = [];
    $sp_icons_white = [];
    $all_sp = rows('SELECT sp.slug, sp.type, sp.icon_svg, sp.icon_svg_white, spt.title FROM solution_pages sp LEFT JOIN solution_pages_t spt ON sp.id=spt.page_id AND spt.lang_id=? WHERE sp.is_active=1', [$lang_id]);
    foreach ($all_sp as $sp) {
        $url = '/' . sp_type_prefix($sp['type']) . '/' . $sp['slug'] . '/';
        if ($sp['icon_svg'])       $sp_icons[$url]       = $sp['icon_svg'];
        if ($sp['icon_svg_white']) $sp_icons_white[$url] = $sp['icon_svg_white'];
        if ($sp['title'])          $sp_title_url[strtolower(trim($sp['title']))] = $url;
    }
    foreach ($cats as &$cat) {
        $cat['subitems'] = rows(
            'SELECT * FROM nav_mega_subitems WHERE category_id=? AND lang_id=? ORDER BY sort_order',
            [$cat['id'], $lang_id]
        );
        foreach ($cat['subitems'] as &$sub) {
            // Auto-correct URL if it doesn't point to a real solution page but title matches one
            if (empty($sp_icons[$sub['url']]) && !empty($sub['title'])) {
                $key = strtolower(trim($sub['title']));
                if (!empty($sp_title_url[$key])) {
                    $sub['url'] = $sp_title_url[$key];
                }
            }
            // Always prefer solution_pages icon over the stored one
            if (!empty($sp_icons[$sub['url']])) {
                $sub['icon_svg'] = $sp_icons[$sub['url']];
            }
            if (!empty($sp_icons_white[$sub['url']])) {
                $sub['icon_svg_white'] = $sp_icons_white[$sub['url']];
            }
        }
    }
    return $cats;
}

/* ─── Settings ─────────────────────────────────────────────────────────── */

function get_setting(string $key, int $lang_id = null): string {
    if ($lang_id !== null) {
        $row = row('SELECT value FROM settings WHERE `key`=? AND lang_id=?', [$key, $lang_id]);
        if ($row) return $row['value'];
    }
    $row = row('SELECT value FROM settings WHERE `key`=? AND lang_id IS NULL', [$key]);
    return $row['value'] ?? '';
}

function set_setting(string $key, string $value, int $lang_id = null): void {
    $existing = row('SELECT id FROM settings WHERE `key`=? AND ' .
        ($lang_id !== null ? 'lang_id=?' : 'lang_id IS NULL'),
        $lang_id !== null ? [$key, $lang_id] : [$key]
    );
    if ($existing) {
        $where = ['key' => $key];
        if ($lang_id !== null) $where['lang_id'] = $lang_id;
        update('settings', ['value' => $value], $where);
    } else {
        $data = ['key' => $key, 'value' => $value];
        if ($lang_id !== null) $data['lang_id'] = $lang_id;
        insert('settings', $data);
    }
}

/* ─── Home Page ─────────────────────────────────────────────────────────── */

function get_home(int $lang_id): array {
    return row('SELECT * FROM home_content WHERE lang_id=?', [$lang_id]) ?? [];
}

function get_why_slides(int $lang_id): array {
    return rows('SELECT * FROM home_why_slides WHERE lang_id=? ORDER BY sort_order', [$lang_id]);
}

function case_link(array $case): array {
    $b = $case['link_behavior'] ?? 'case_page';
    if ($b === 'inactive') return ['href' => null, 'external' => false];
    if ($b === 'external_url' && !empty($case['external_url'])) return ['href' => $case['external_url'], 'external' => true];
    return ['href' => '/cases/' . ($case['slug'] ?? '') . '/', 'external' => false];
}

function get_partner_logos(): array {
    return rows('SELECT l.*, m.path as image_path FROM home_partner_logos l LEFT JOIN media m ON l.image_id=m.id ORDER BY l.sort_order');
}

function get_automation_images(): array {
    return rows('SELECT a.*, m.path as image_path FROM home_automation_images a LEFT JOIN media m ON a.image_id=m.id ORDER BY a.sort_order');
}

function get_reviews(int $lang_id): array {
    return rows(
        'SELECT r.*, rt.quote, rt.text, rt.author_name, rt.author_title,
                m.path as author_image_path, rm.path as rating_image_path
         FROM reviews r
         LEFT JOIN reviews_t rt ON r.id=rt.review_id AND rt.lang_id=?
         LEFT JOIN media m ON r.author_image_id=m.id
         LEFT JOIN media rm ON r.rating_image_id=rm.id
         WHERE r.is_active=1 ORDER BY r.sort_order',
        [$lang_id]
    );
}

/* ─── Solutions ─────────────────────────────────────────────────────────── */

function get_sol_page_blocks(int $page_id, int $lang_id): array {
    if (!$page_id) return [];
    $blocks = rows('SELECT * FROM sol_page_blocks WHERE page_id=? AND lang_id=? ORDER BY sort_order', [$page_id, $lang_id]);
    if (empty($blocks)) {
        // Lazy-init: insert default blocks so the page renders with placeholders
        $defaults = [
            ['promo',      'Hero / Promo',       0,  '{"title":"","text":"","btn1_text":"View Our Presentation","btn1_url":"/assets/Sidis-Group.pdf","btn2_text":"Free audit","btn2_url":"#getintouch","image_id":null,"image_url":""}'],
            ['features',   'Features Slider',    1,  '{"title":"What you get","slides":[{"title":"Manual & Repetitive Tasks","text":"RPA automates repetitive tasks instantly, saving 30–50% of time spent on admin."},{"title":"Inaccurate Data & Human Errors","text":"Automated workflows eliminate human error and ensure consistent data quality across all systems."},{"title":"Slow Processes & Delays","text":"Smart automation cuts processing times from hours to minutes, accelerating every workflow."},{"title":"Lack of Integration Between Systems","text":"We connect your tools so data flows automatically, removing silos and manual data entry."}]}'],
            ['planning',   'Planning / Process', 2,  '{"items":[{"title":"Enterprise Resource Planning (ERP)","text":"We help integrate a customized ERP system that includes specialized modules for inventory control, production scheduling, and supply chain management."},{"title":"CRM Integration","text":"Connect your customer data across all touchpoints for a unified view of every client relationship and interaction."}],"info_title":"Curious which RPA solution fits your business needs?","info_btn1_text":"View Our Presentation","info_btn1_url":"/assets/Sidis-Group.pdf","info_btn2_text":"Free audit","info_btn2_url":"#getintouch"}'],
            ['solved',     'Challenges Solved',  3,  '{"title":"Business Challenges Solved with RPA","slides":[{"title":"Maximise operational efficiency","text":"Accelerate time-to-market and reduce operational costs while eliminating bottlenecks in processes. We implement solutions powered by machine learning and intelligent automation to optimize scheduling and resource allocation."},{"title":"Make compliance-driven decisions","text":"Minimize risks and avoid costly incidents with built-in controls embedded directly into your business processes. Our compliance management expertise delivers comprehensive audit trails and automated documentation."},{"title":"Power business growth with integration","text":"Grow your business by eliminating data silos that limit strategic decision-making. Our integrated software solutions unlock new revenue opportunities by connecting all your systems into a single platform."}]}'],
            ['roadmap',    'Roadmap',            4,  '{"title":"Our Development Process","btn1_text":"Get PDF","btn1_url":"#","btn2_text":"Free audit","btn2_url":"#getintouch","steps":[{"title":"Discovery Call","text":"Initial consultation. Project assessment. Solution overview."},{"title":"Strategy & Proposal","text":"Refine project scope. Define clear goals. Deliver tailored solutions."},{"title":"Integration","text":"Optimize workflows. Enhance collaboration. Accelerate growth."},{"title":"Support, Monitor & Scale","text":"Maximize uptime. Ensure peak performance. Drive continuous improvement."}],"video_path":"./assets/video/1-hero.mp4"}'],
            ['projects',   'Case Studies',       5,  '{"title":"Implemented\nWorkflows"}'],
            ['reviews',    'Client Reviews',     6,  '{"title":"What clients say about us"}'],
            ['getintouch', 'Contact Form',       7,  '{}'],
            ['faq',        'FAQ',                8,  '{"title":"Questions & answers","items":[{"q":"What are your pricing options?","a":"Our team is based in Eastern Europe, which gives us an advantage in operational costs — low taxes, rent, and payroll. We maintain a compact team of professionals and pass the savings to our clients."},{"q":"What is your typical project timeline?","a":"Most automation projects take 4–8 weeks from discovery to deployment. Complex integrations may take longer. We provide a clear timeline in the proposal stage."},{"q":"How do you handle customer feedback?","a":"We work in iterative cycles with regular check-ins. Client feedback is incorporated at each stage, and we offer a post-launch support period for adjustments."},{"q":"Can you provide case studies or testimonials?","a":"Yes — see our Case Studies section and client testimonials below. We can also provide references upon request."}]}'],
            ['articles',   'Latest Articles',    9,  '{"title":"Latest Automation\nInsights"}'],
        ];
        foreach ($defaults as [$key, $label, $order, $content]) {
            insert('sol_page_blocks', ['page_id'=>$page_id,'lang_id'=>$lang_id,'block_key'=>$key,'label'=>$label,'sort_order'=>$order,'is_active'=>1,'content'=>$content]);
        }
        $blocks = rows('SELECT * FROM sol_page_blocks WHERE page_id=? AND lang_id=? ORDER BY sort_order', [$page_id, $lang_id]);
    }
    $result = [];
    foreach ($blocks as $b) {
        if (!$b['is_active']) continue;
        $c = json_decode($b['content'] ?: '{}', true) ?: [];
        // Migrate planning block: add missing btn2 fields
        if ($b['block_key'] === 'planning' && empty($c['info_btn2_text'])) {
            $c['info_btn2_text'] = 'Free audit';
            $c['info_btn2_url']  = '#getintouch';
            update('sol_page_blocks', ['content' => json_encode($c, JSON_UNESCAPED_UNICODE)], ['id' => $b['id']]);
        }
        $b['content'] = $c;
        $result[] = $b;
    }
    return $result;
}

function get_solution_items(int $lang_id, string $type = null, array $ids = []): array {
    $sql = "SELECT sp.id, sp.type, sp.sort_order, sp.is_active, sp.icon_svg, sp.icon_svg_white,
                   CASE WHEN sp.type='industry' THEN CONCAT('/industries/', sp.slug, '/') ELSE CONCAT('/', sp.type, 's/', sp.slug, '/') END as btn_url,
                   spt.title, spt.description, spt.btn1_text as btn_text
            FROM solution_pages sp
            LEFT JOIN solution_pages_t spt ON sp.id=spt.page_id AND spt.lang_id=?
            WHERE sp.is_active=1";
    $params = [$lang_id];
    if ($type) { $sql .= ' AND sp.type=?'; $params[] = $type; }
    if (!empty($ids)) {
        $ph = implode(',', array_fill(0, count($ids), '?'));
        $sql .= " AND sp.id IN ($ph)";
        $params = array_merge($params, array_map('intval', $ids));
    }
    $sql .= ' ORDER BY sp.sort_order';
    return rows($sql, $params);
}

function get_solution_page(int $lang_id, string $slug): ?array {
    $page = row(
        'SELECT sp.*, spt.title, spt.description, spt.btn1_text, spt.btn2_text, spt.meta_title, spt.meta_description,
                m.path as image_path
         FROM solution_pages sp
         LEFT JOIN solution_pages_t spt ON sp.id=spt.page_id AND spt.lang_id=?
         LEFT JOIN media m ON sp.image_id=m.id
         WHERE sp.slug=? AND sp.is_active=1',
        [$lang_id, $slug]
    );
    if ($page) {
        $page['features'] = rows(
            'SELECT sf.*, sft.title, sft.text FROM solution_features sf
             LEFT JOIN solution_features_t sft ON sf.id=sft.feature_id AND sft.lang_id=?
             WHERE sf.page_id=? ORDER BY sf.sort_order',
            [$lang_id, $page['id']]
        );
        $page['items'] = get_solution_items($lang_id);
    }
    return $page;
}

/* ─── Cases ─────────────────────────────────────────────────────────────── */

function get_cases(int $lang_id, array $filters = [], int $limit = 50, int $offset = 0): array {
    $sql = 'SELECT c.*, c.company_name, ct.title, ct.description, ct.location, ct.cooperation_period,
                   logo.path as logo_path, img.path as image_path
            FROM cases c
            LEFT JOIN cases_t ct ON c.id=ct.case_id AND ct.lang_id=?
            LEFT JOIN media logo ON c.company_logo_id=logo.id
            LEFT JOIN media img ON c.featured_image_id=img.id
            WHERE c.is_active=1';
    $params = [$lang_id];
    if (!empty($filters['solution'])) {
        $sql .= ' AND EXISTS (SELECT 1 FROM case_terms ctt WHERE ctt.case_id=c.id AND ctt.term_id=? AND ctt.type="solution")';
        $params[] = $filters['solution'];
    }
    if (!empty($filters['department'])) {
        $sql .= ' AND EXISTS (SELECT 1 FROM case_terms ctt WHERE ctt.case_id=c.id AND ctt.term_id=? AND ctt.type="department")';
        $params[] = $filters['department'];
    }
    if (!empty($filters['industry'])) {
        $sql .= ' AND EXISTS (SELECT 1 FROM case_terms ctt WHERE ctt.case_id=c.id AND ctt.term_id=? AND ctt.type="industry")';
        $params[] = $filters['industry'];
    }
    if (!empty($filters['ids']) && is_array($filters['ids'])) {
        $ph = implode(',', array_fill(0, count($filters['ids']), '?'));
        $sql .= " AND c.id IN ($ph)";
        $params = array_merge($params, array_map('intval', $filters['ids']));
    }
    if (!empty($filters['is_featured'])) {
        $sql .= ' AND c.is_featured=1';
    }
    $sql .= ' ORDER BY c.sort_order, c.created_at DESC LIMIT ? OFFSET ?';
    $params[] = $limit;
    $params[] = $offset;
    $cases = rows($sql, $params);
    foreach ($cases as &$case) {
        $case['terms'] = rows('SELECT ct.type, tt.name FROM case_terms ct JOIN terms_t tt ON ct.term_id=tt.term_id AND tt.lang_id=? WHERE ct.case_id=?', [$lang_id, $case['id']]);
    }
    return $cases;
}

function get_case(int $lang_id, string $slug): ?array {
    $case = row(
        'SELECT c.*, c.company_name, ct.title, ct.description, ct.overview_text, ct.location, ct.cooperation_period,
                ct.meta_title, ct.meta_description,
                logo.path as logo_path, img.path as image_path
         FROM cases c
         LEFT JOIN cases_t ct ON c.id=ct.case_id AND ct.lang_id=?
         LEFT JOIN media logo ON c.company_logo_id=logo.id
         LEFT JOIN media img ON c.featured_image_id=img.id
         WHERE c.slug=? AND c.is_active=1',
        [$lang_id, $slug]
    );
    if ($case) {
        $case['key_results'] = rows('SELECT * FROM case_key_results WHERE case_id=? AND lang_id=? ORDER BY sort_order', [$case['id'], $lang_id]);
        $case['challenges']  = rows('SELECT ch.*, ct.title, ct.text FROM case_challenges ch LEFT JOIN case_challenges_t ct ON ch.id=ct.challenge_id AND ct.lang_id=? WHERE ch.case_id=? ORDER BY ch.sort_order', [$lang_id, $case['id']]);
        $case['tech_items']  = rows('SELECT * FROM case_tech_items WHERE case_id=? AND lang_id=? ORDER BY sort_order', [$case['id'], $lang_id]);
        $case['services']    = rows('SELECT * FROM case_services WHERE case_id=? AND lang_id=? ORDER BY sort_order', [$case['id'], $lang_id]);
        $case['terms']       = rows('SELECT ct.type, tt.name FROM case_terms ct JOIN terms_t tt ON ct.term_id=tt.term_id AND tt.lang_id=? WHERE ct.case_id=?', [$lang_id, $case['id']]);
        // Load extras safely (column may not exist yet)
        $extras_raw = null;
        try {
            $er = row('SELECT extras FROM cases_t WHERE case_id=? AND lang_id=?', [$case['id'], $lang_id]);
            $extras_raw = $er['extras'] ?? null;
        } catch (Exception $e) {
            try { db()->exec("ALTER TABLE cases_t ADD COLUMN extras MEDIUMTEXT DEFAULT NULL"); } catch (Exception $e2) {}
        }
        $case['extras'] = $extras_raw ? (json_decode($extras_raw, true) ?: []) : [];
    }
    return $case;
}

/* ─── Blog ──────────────────────────────────────────────────────────────── */

function reading_time(string $content): int {
    $text = strip_tags($content);
    $words = preg_match_all('/\S+/u', $text, $m);
    return max(1, (int)ceil($words / 200));
}

function get_posts(int $lang_id, array $filters = [], int $limit = 50, int $offset = 0): array {
    $sql = 'SELECT p.*, pt.title, pt.subtitle, pt.excerpt, pt.content,
                   img.path as image_path, a.id as author_id,
                   at_.name as author_name, at_.title as author_title,
                   am.path as author_image_path
            FROM posts p
            LEFT JOIN posts_t pt ON p.id=pt.post_id AND pt.lang_id=?
            LEFT JOIN media img ON p.featured_image_id=img.id
            LEFT JOIN authors a ON p.author_id=a.id
            LEFT JOIN authors_t at_ ON a.id=at_.author_id AND at_.lang_id=?
            LEFT JOIN media am ON a.image_id=am.id
            WHERE p.is_active=1';
    $params = [$lang_id, $lang_id];
    $sql .= ' ORDER BY p.published_at DESC LIMIT ? OFFSET ?';
    $params[] = $limit; $params[] = $offset;
    $posts = rows($sql, $params);
    foreach ($posts as &$post) {
        $post['tags'] = rows('SELECT tag_text FROM post_tags WHERE post_id=? AND lang_id=? ORDER BY sort_order', [$post['id'], $lang_id]);
        $post['read_time'] = reading_time($post['content'] ?? '');
    }
    return $posts;
}

function get_post(int $lang_id, string $slug): ?array {
    $post = row(
        'SELECT p.*, pt.title, pt.subtitle, pt.excerpt, pt.content, pt.meta_title, pt.meta_description,
                img.path as image_path,
                at_.name as author_name, at_.title as author_title, a.linkedin_url as author_linkedin,
                am.path as author_image_path
         FROM posts p
         LEFT JOIN posts_t pt ON p.id=pt.post_id AND pt.lang_id=?
         LEFT JOIN media img ON p.featured_image_id=img.id
         LEFT JOIN authors a ON p.author_id=a.id
         LEFT JOIN authors_t at_ ON a.id=at_.author_id AND at_.lang_id=?
         LEFT JOIN media am ON a.image_id=am.id
         WHERE p.slug=? AND p.is_active=1',
        [$lang_id, $lang_id, $slug]
    );
    if ($post) {
        $post['tags'] = rows('SELECT tag_text FROM post_tags WHERE post_id=? AND lang_id=? ORDER BY sort_order', [$post['id'], $lang_id]);
        $post['toc']  = rows('SELECT * FROM post_toc WHERE post_id=? AND lang_id=? ORDER BY sort_order', [$post['id'], $lang_id]);
        // Fetch extras safely — column may not exist yet on first deploy
        $extras_raw = null;
        try {
            $er = row('SELECT extras FROM posts_t WHERE post_id=? AND lang_id=?', [$post['id'], $lang_id]);
            $extras_raw = $er['extras'] ?? null;
        } catch (Exception $e) {
            // extras column not yet migrated; auto-create it
            try { db()->exec("ALTER TABLE posts_t ADD COLUMN extras MEDIUMTEXT DEFAULT NULL"); } catch (Exception $e2) {}
        }
        $post['extras'] = $extras_raw ? (json_decode($extras_raw, true) ?: []) : [];
        // Inject id attributes into headings that lack them (needed for TOC anchors)
        $post['content'] = inject_heading_ids($post['content'] ?? '');
        // Auto-generate TOC from content headings if no TOC entries
        if (empty($post['toc'])) {
            $post['toc'] = extract_toc_from_content($post['content']);
        }
        // Process shortcodes in content
        $post['content'] = render_article_content($post['content'], $post['extras']);
    }
    return $post;
}

/**
 * Auto-inject id attributes into h2-h5 tags that don't have one.
 * Uses slugified heading text; deduplicates with a counter.
 */
function inject_heading_ids(string $html): string {
    if (!$html) return $html;
    $seen  = [];
    $parts = preg_split('/(<h[2-5][^>]*>)/i', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    $out   = '';
    for ($i = 0; $i < count($parts); $i++) {
        if ($i % 2 === 0) { $out .= $parts[$i]; continue; }
        $tag = $parts[$i];
        if (preg_match('/\bid=/i', $tag)) { $out .= $tag; continue; }
        // Build slug from inner text of this heading
        $after = $parts[$i + 1] ?? '';
        $close = strpos($after, '</h');
        $inner = $close !== false ? substr($after, 0, $close) : $after;
        $slug  = trim(preg_replace('/[^a-z0-9]+/', '-', strtolower(strip_tags($inner))), '-');
        if (!$slug) $slug = 'section';
        if (isset($seen[$slug])) { $seen[$slug]++; $slug .= '-' . $seen[$slug]; }
        else $seen[$slug] = 1;
        // Insert id before the closing >
        $tag = substr($tag, 0, -1) . ' id="' . htmlspecialchars($slug) . '">';
        $out .= $tag;
    }
    return $out;
}

/**
 * Extract TOC entries from heading tags (h2-h5) that have id attributes.
 */
function extract_toc_from_content(string $html): array {
    $toc = [];
    if (!$html) return $toc;
    // Split at every heading opening tag — safe, no nested quantifiers
    $parts = preg_split('/(<h[2-5][^>]*>)/i', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    for ($i = 1; $i < count($parts); $i += 2) {
        $tag   = $parts[$i];            // e.g. <h2 id="some-section-1">
        $after = $parts[$i + 1] ?? '';  // text until next heading
        // Extract id
        if (!preg_match('/\bid=["\']([^"\']+)["\']/i', $tag, $id_m)) continue;
        $anchor = trim($id_m[1]);
        // Text before first < in the continuation (handles plain & nested tags)
        $close = strpos($after, '</h');
        $inner = $close !== false ? substr($after, 0, $close) : '';
        $title = trim(strip_tags($inner));
        if ($title && $anchor) {
            $toc[] = ['title' => $title, 'anchor' => $anchor];
        }
    }
    return $toc;
}

/**
 * Render a single media slot: auto-detects image / local video / YouTube.
 */
function _render_media_slot(string $url): string {
    if (!$url) return '';
    $safe = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    // YouTube — position:absolute fills the min-height:350px container
    if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/i', $url, $ym)) {
        $vid = htmlspecialchars($ym[1], ENT_QUOTES, 'UTF-8');
        return '<div class="article-block__video" style="position:relative;height:100%;border-radius:30px;overflow:hidden"><iframe src="https://www.youtube.com/embed/' . $vid . '" frameborder="0" allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;border:none;border-radius:30px;display:block"></iframe></div>';
    }
    // Local video file
    if (preg_match('/\.(mp4|webm|mov|ogv)(\?.*)?$/i', $url)) {
        return '<div class="article-block__video video-paused"><div class="video-controlls"><video muted loop playsinline><source src="' . $safe . '" type="video/mp4"></video><div class="video-controlls__play"><button type="button" class="button button--icon button--white"><svg width="14" height="17" viewbox="0 0 14 17" fill="none"><path d="M1 1L13 8.5L1 16V1Z" fill="currentColor" stroke="currentColor" stroke-width="1.5"/></svg></button></div></div></div>';
    }
    // Image (default)
    return '<div class="article-block__img"><img src="' . $safe . '" alt="" loading="lazy"></div>';
}

/**
 * Process shortcodes in article content.
 * Supported: [gallery img="..."], [media img="..." video="..."], [cta]
 */
function render_article_content(string $content, array $extras = []): string {
    // [gallery img="/img1.webp,/img2.webp"]
    $content = preg_replace_callback('/\[gallery\s+img=["\']([^"\']+)["\']\s*\]/i', function($m) {
        $images = array_filter(array_map('trim', explode(',', $m[1])));
        if (empty($images)) return '';
        $arrow_prev = '<svg style="margin-right:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M9.53516 0.523193L0.535156 8.52319L9.53516 16.5232" stroke="currentColor" stroke-width="1.4"></path></svg>';
        $arrow_next = '<svg style="margin-left:4px" width="11" height="18" viewbox="0 0 11 18" fill="none"><path d="M0.464844 0.523193L9.46484 8.52319L0.464844 16.5232" stroke="currentColor" stroke-width="1.4"></path></svg>';
        $slides = '';
        foreach ($images as $img) {
            $slides .= '<div class="article-block__slide swiper-slide"><img src="' . htmlspecialchars($img, ENT_QUOTES, 'UTF-8') . '" alt="" loading="lazy"></div>';
        }
        return '<div class="article-block__slider swiper" data-fls-slider=""><div class="article-block__wrapper swiper-wrapper">' . $slides . '</div><button type="button" class="swiper-button-prev">' . $arrow_prev . '</button><button type="button" class="swiper-button-next">' . $arrow_next . '</button></div>';
    }, $content);

    // [media img="..." video="..."] — auto-detects image/video/youtube for each slot
    $content = preg_replace_callback('/\[media\s+img=["\']([^"\']*)["\'](?:\s+video=["\']([^"\']*)["\'])?\s*\]/i', function($m) {
        $left  = trim($m[1]);
        $right = isset($m[2]) ? trim($m[2]) : '';
        return '<div class="article-block__media">' . _render_media_slot($left) . _render_media_slot($right) . '</div>';
    }, $content);

    // [quote left="..." right="..." cols="2" left_size="16" right_size="26"]
    $content = preg_replace_callback('/\[quote([^\]]*)\]/i', function($m) {
        $attrs = [];
        preg_match_all('/(\w+)=["\']([^"\']*)["\']/', $m[1], $am, PREG_SET_ORDER);
        foreach ($am as $a) $attrs[$a[1]] = $a[2];
        $left       = nl2br(htmlspecialchars(html_entity_decode($attrs['left']  ?? '', ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));
        $right      = nl2br(htmlspecialchars(html_entity_decode($attrs['right'] ?? '', ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8'));
        $cols       = (int)($attrs['cols'] ?? 2) === 1 ? 1 : 2;
        $left_size  = (int)($attrs['left_size']  ?? 16);
        $right_size = (int)($attrs['right_size'] ?? 26);
        $left_size  = max(10, min(60, $left_size));
        $right_size = max(10, min(60, $right_size));
        $style = '--q-right-size:' . $right_size . 'px;';
        if ($cols === 1) {
            $style .= '--q-cols:1fr;';
        } else {
            $style .= '--q-left-size:' . $left_size . 'px;';
        }
        $inner = '<div class="article-block__quote-right">' . $right . '</div>';
        if ($cols === 2) $inner .= '<div class="article-block__quote-left">' . $left . '</div>';
        return '<div class="article-block__quote" style="' . $style . '">' . $inner . '</div>';
    }, $content);

    // [cta] or [cta title="..." btn1_text="..." btn1_url="..." btn2_text="..." btn2_url="..."]
    $content = preg_replace_callback('/\[cta([^\]]*)\]/i', function($m) use ($extras) {
        $attrs = [];
        if (preg_match_all('/(\w+)=["\']([^"\']*)["\']/', $m[1], $am, PREG_SET_ORDER)) {
            foreach ($am as $a) $attrs[$a[1]] = $a[2];
        }
        $title = htmlspecialchars($attrs['title'] ?? $extras['cta_title'] ?? 'Curious which solution fits your business needs?', ENT_QUOTES, 'UTF-8');
        $btn1_text = htmlspecialchars($attrs['btn1_text'] ?? $extras['cta_btn1_text'] ?? 'View Our Presentation', ENT_QUOTES, 'UTF-8');
        $btn1_url  = htmlspecialchars($attrs['btn1_url']  ?? $extras['cta_btn1_url']  ?? '#', ENT_QUOTES, 'UTF-8');
        $btn2_text = htmlspecialchars($attrs['btn2_text'] ?? $extras['cta_btn2_text'] ?? 'Free audit', ENT_QUOTES, 'UTF-8');
        $btn2_url  = htmlspecialchars($attrs['btn2_url']  ?? $extras['cta_btn2_url']  ?? '#getintouch', ENT_QUOTES, 'UTF-8');
        $arrow_svg = '<svg width="14" height="14" viewbox="0 0 14 14" fill="none"><path d="M0.566406 12.8L12.5664 0.800049M12.5664 0.800049L12.5664 12.8M12.5664 0.800049L0.679613 0.800049" stroke="currentColor" stroke-width="1.6"></path></svg>';
        return '<div class="planning__info"><div class="planning__info-img"><img alt="Image" loading="lazy" src="/assets/img/projects/image-6.webp"></div><div class="planning__info-body"><div class="planning__info-title">' . $title . '</div><div class="planning__info-btns"><a href="' . $btn1_url . '" target="_blank" class="planning__info-btn button">' . $btn1_text . '</a><a href="' . $btn2_url . '" class="planning__info-btn button button--icon"><span class="button__text">' . $btn2_text . '</span><span class="button__icon">' . $arrow_svg . '</span></a></div></div></div>';
    }, $content);

    return $content;
}

/* ─── Terms (Taxonomy) ─────────────────────────────────────────────────── */

function get_terms(int $lang_id, string $type): array {
    return rows(
        'SELECT t.*, tt.name FROM terms t LEFT JOIN terms_t tt ON t.id=tt.term_id AND tt.lang_id=? WHERE t.type=? AND t.is_active=1 ORDER BY t.sort_order',
        [$lang_id, $type]
    );
}

/* ─── Media ─────────────────────────────────────────────────────────────── */

function media_url($path): string {
    if (!$path) return '';
    if ($path[0] === '/') return $path;
    if (strpos($path, 'http') === 0) return $path;
    // ./assets/img/... → /assets/img/... (relative paths break on sub-URLs)
    if (strpos($path, './') === 0) return '/' . substr($path, 2);
    return UPLOAD_URL . $path;
}

function upload_file(array $file, string $subdir = ''): int {
    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml'];
    if (!in_array($file['type'], $allowed)) throw new Exception('File type not allowed');
    if ($file['size'] > 10 * 1024 * 1024) throw new Exception('File too large (max 10MB)');

    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $name = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dir  = UPLOAD_DIR . ($subdir ? rtrim($subdir, '/') . '/' : '');
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    move_uploaded_file($file['tmp_name'], $dir . $name);
    $rel_path = ($subdir ? rtrim($subdir, '/') . '/' : '') . $name;
    return insert('media', [
        'filename'      => $name,
        'original_name' => $file['name'],
        'path'          => $rel_path,
        'mime_type'     => $file['type'],
        'file_size'     => $file['size'],
        'alt_text'      => '',
    ]);
}

/* ─── Utilities ─────────────────────────────────────────────────────────── */

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function slug(string $s): string { return strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $s), '-')); }

function json_response($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function json_error(string $msg, int $status = 400): void {
    json_response(['error' => $msg], $status);
}
