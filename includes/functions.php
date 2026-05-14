<?php
require_once __DIR__ . '/db.php';

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

function get_nav(int $lang_id, string $location): array {
    $items = rows(
        'SELECT * FROM nav_items WHERE lang_id=? AND location=? AND parent_id IS NULL AND is_active=1 ORDER BY sort_order',
        [$lang_id, $location]
    );
    foreach ($items as &$item) {
        $item['children'] = rows(
            'SELECT * FROM nav_items WHERE lang_id=? AND parent_id=? AND is_active=1 ORDER BY sort_order',
            [$lang_id, $item['id']]
        );
        if ($item['has_mega']) {
            $item['mega_categories'] = get_mega_categories($lang_id, $item['id']);
        }
    }
    return $items;
}

function get_mega_categories(int $lang_id, int $nav_item_id): array {
    $cats = rows(
        'SELECT * FROM nav_mega_categories WHERE nav_item_id=? AND lang_id=? ORDER BY sort_order',
        [$nav_item_id, $lang_id]
    );
    foreach ($cats as &$cat) {
        $cat['subitems'] = rows(
            'SELECT * FROM nav_mega_subitems WHERE category_id=? AND lang_id=? ORDER BY sort_order',
            [$cat['id'], $lang_id]
        );
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

function get_solution_items(int $lang_id, string $type = null): array {
    $sql = 'SELECT si.*, sit.title, sit.description, sit.btn_text, sit.btn_url
            FROM solution_items si
            LEFT JOIN solution_items_t sit ON si.id=sit.item_id AND sit.lang_id=?
            WHERE si.is_active=1';
    $params = [$lang_id];
    if ($type) { $sql .= ' AND si.type=?'; $params[] = $type; }
    $sql .= ' ORDER BY si.sort_order';
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
    $sql = 'SELECT c.*, ct.title, ct.description, ct.location, ct.cooperation_period,
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
    $sql .= ' ORDER BY c.sort_order, c.created_at DESC LIMIT ? OFFSET ?';
    $params[] = $limit;
    $params[] = $offset;
    return rows($sql, $params);
}

function get_case(int $lang_id, string $slug): ?array {
    $case = row(
        'SELECT c.*, ct.title, ct.description, ct.overview_text, ct.location, ct.cooperation_period,
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
    }
    return $case;
}

/* ─── Blog ──────────────────────────────────────────────────────────────── */

function get_posts(int $lang_id, array $filters = [], int $limit = 50, int $offset = 0): array {
    $sql = 'SELECT p.*, pt.title, pt.subtitle, pt.excerpt,
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
    }
    return $post;
}

/* ─── Terms (Taxonomy) ─────────────────────────────────────────────────── */

function get_terms(int $lang_id, string $type): array {
    return rows(
        'SELECT t.*, tt.name FROM terms t LEFT JOIN terms_t tt ON t.id=tt.term_id AND tt.lang_id=? WHERE t.type=? AND t.is_active=1 ORDER BY t.sort_order',
        [$lang_id, $type]
    );
}

/* ─── Media ─────────────────────────────────────────────────────────────── */

function media_url(?string $path): string {
    if (!$path) return '';
    if (str_starts_with($path, '/') || str_starts_with($path, 'http')) return $path;
    return UPLOAD_URL . $path;
}

function upload_file(array $file, string $subdir = ''): array {
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

function json_response(mixed $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function json_error(string $msg, int $status = 400): void {
    json_response(['error' => $msg], $status);
}
