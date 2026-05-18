<?php
require_once dirname(dirname(__DIR__)) . '/includes/auth.php';
require_once dirname(dirname(__DIR__)) . '/includes/functions.php';
admin_session_start();
admin_require_auth();

$pdo     = db();
$method  = $_SERVER['REQUEST_METHOD'];
$action  = $_GET['action'] ?? '';
$lang_id = (int)($_GET['lang_id'] ?? 1);
$id      = (int)($_GET['id'] ?? 0);

// ── Ensure icon_svg column exists on solution_pages ─────────────────────────
try { $pdo->exec("ALTER TABLE solution_pages ADD COLUMN icon_svg MEDIUMTEXT DEFAULT NULL"); } catch(Exception $e) {}

// ── Ensure sol_page_blocks table exists ─────────────────────────────────────
$pdo->exec("CREATE TABLE IF NOT EXISTS sol_page_blocks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_id INT NOT NULL,
    lang_id INT NOT NULL,
    block_key VARCHAR(50) NOT NULL,
    label VARCHAR(100) NOT NULL DEFAULT '',
    sort_order INT DEFAULT 0,
    is_active TINYINT DEFAULT 1,
    content MEDIUMTEXT DEFAULT '{}',
    UNIQUE KEY uk_spb (page_id, lang_id, block_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// Default blocks for a new solution page
function default_sol_blocks(): array {
    return [
        ['promo',      'Hero / Promo',       0, '{"title":"","text":"","btn1_text":"Try AI assistant","btn1_url":"#","btn2_text":"Free audit","btn2_url":"#getintouch","image_id":null,"image_url":""}'],
        ['features',   'Features Slider',    1, '{"title":"What you get","slides":[{"title":"Manual & Repetitive Tasks","text":"RPA automates repetitive tasks instantly, saving 30–50% of time spent on admin."},{"title":"Inaccurate Data & Human Errors","text":"Automated workflows eliminate human error and ensure consistent data quality."},{"title":"Slow Processes & Delays","text":"Smart automation cuts processing times from hours to minutes."},{"title":"Lack of Integration Between Systems","text":"We connect your tools so data flows automatically, removing silos and manual entry."}]}'],
        ['planning',   'Planning / Process', 2, '{"items":[{"title":"Enterprise Resource Planning (ERP)","text":"We help integrate a customized ERP system that includes specialized modules for inventory control, production scheduling, and supply chain management."},{"title":"CRM Integration","text":"Connect your customer data across all touchpoints for a unified view of every client relationship."}],"info_title":"Curious which RPA solution fits your business needs?","info_btn1_text":"Try AI assistant","info_btn1_url":"#","info_btn2_text":"Free audit","info_btn2_url":"#getintouch"}'],
        ['solved',     'Challenges Solved',  3, '{"title":"Business Challenges Solved with RPA","slides":[{"title":"Maximise operational efficiency","text":"Accelerate time-to-market and reduce operational costs while eliminating bottlenecks in processes that slow down your operations."},{"title":"Make compliance-driven decisions","text":"Minimize risks with built-in controls embedded directly into your business processes with comprehensive audit trails."},{"title":"Power business growth with integration","text":"Grow your business by eliminating data silos that limit strategic decision-making across your entire value chain."}]}'],
        ['roadmap',    'Roadmap',            4, '{"title":"Our Development Process","btn1_text":"Get PDF","btn1_url":"#","btn2_text":"Free audit","btn2_url":"#getintouch","steps":[{"title":"Discovery Call","text":"Initial consultation. Project assessment. Solution overview."},{"title":"Strategy & Proposal","text":"Refine project scope. Define clear goals. Deliver tailored solutions."},{"title":"Integration","text":"Optimize workflows. Enhance collaboration. Accelerate growth."},{"title":"Support, Monitor & Scale","text":"Maximize uptime. Ensure peak performance. Drive continuous improvement."}],"video_path":"./assets/video/1-hero.mp4"}'],
        ['projects',   'Case Studies',       5, '{"title":"Implemented\nWorkflows"}'],
        ['reviews',    'Client Reviews',     6, '{"title":"What clients say about us"}'],
        ['getintouch', 'Contact Form',       7, '{}'],
        ['faq',        'FAQ',                8, '{"title":"Questions & answers","items":[{"q":"What are your pricing options?","a":"Our team is based in Eastern Europe, which gives us an advantage in operational costs — low taxes, rent, and payroll. We pass the savings directly to our clients."},{"q":"What is your typical project timeline?","a":"Most automation projects take 4–8 weeks from discovery to deployment. Complex integrations may take longer."},{"q":"How do you handle customer feedback?","a":"We work in iterative cycles with regular check-ins. Client feedback is incorporated at each stage with a post-launch support period."},{"q":"Can you provide case studies or testimonials?","a":"Yes — see our Case Studies section and client testimonials below. We can also provide references upon request."}]}'],
        ['articles',   'Latest Articles',    9, '{"title":"Latest Automation\nInsights"}'],
    ];
}

// ── GET ──────────────────────────────────────────────────────────────────────
if ($method === 'GET') {
    if ($action === 'list') {
        $type = $_GET['type'] ?? null;
        $sql  = 'SELECT sp.*, spt.title, spt.description, m.path as image_path
                 FROM solution_pages sp
                 LEFT JOIN solution_pages_t spt ON sp.id=spt.page_id AND spt.lang_id=?
                 LEFT JOIN media m ON sp.image_id=m.id
                 WHERE 1';
        $params = [$lang_id];
        if ($type) { $sql .= ' AND sp.type=?'; $params[] = $type; }
        $sql .= ' ORDER BY sp.type, sp.sort_order';
        $pages = rows($sql, $params);
        foreach ($pages as &$p) $p['image_url'] = $p['image_path'] ? admin_url($p['image_path']) : '';
        json_response($pages);
    }

    if ($action === 'blocks' && $id) {
        $blocks = rows('SELECT * FROM sol_page_blocks WHERE page_id=? AND lang_id=? ORDER BY sort_order', [$id, $lang_id]);
        if (empty($blocks)) {
            // Init default blocks for this page
            foreach (default_sol_blocks() as [$key, $label, $order, $content]) {
                insert('sol_page_blocks', ['page_id'=>$id,'lang_id'=>$lang_id,'block_key'=>$key,'label'=>$label,'sort_order'=>$order,'is_active'=>1,'content'=>$content]);
            }
            $blocks = rows('SELECT * FROM sol_page_blocks WHERE page_id=? AND lang_id=? ORDER BY sort_order', [$id, $lang_id]);
        } else {
            // Check if any default blocks are missing (add them)
            $existing_keys = array_column($blocks, 'block_key');
            foreach (default_sol_blocks() as [$key, $label, $order, $content]) {
                if (!in_array($key, $existing_keys)) {
                    insert('sol_page_blocks', ['page_id'=>$id,'lang_id'=>$lang_id,'block_key'=>$key,'label'=>$label,'sort_order'=>$order,'is_active'=>1,'content'=>$content]);
                }
            }
            $blocks = rows('SELECT * FROM sol_page_blocks WHERE page_id=? AND lang_id=? ORDER BY sort_order', [$id, $lang_id]);
        }
        // Migrate planning block: add missing btn2 fields if absent
        foreach ($blocks as $b) {
            if ($b['block_key'] === 'planning') {
                $c = json_decode($b['content'] ?: '{}', true) ?: [];
                if (empty($c['info_btn2_text'])) {
                    $c['info_btn2_text'] = 'Free audit';
                    $c['info_btn2_url']  = '#getintouch';
                    update('sol_page_blocks', ['content' => json_encode($c, JSON_UNESCAPED_UNICODE)], ['id' => $b['id']]);
                }
                break;
            }
        }
        $blocks = rows('SELECT * FROM sol_page_blocks WHERE page_id=? AND lang_id=? ORDER BY sort_order', [$id, $lang_id]);
        foreach ($blocks as &$b) {
            $c = json_decode($b['content'] ?: '{}', true) ?: [];
            if (!empty($c['image_url'])) $c['image_url'] = admin_url($c['image_url']);
            $b['content_obj'] = $c;
        }
        json_response($blocks);
    }

    if ($action === 'page' && $id) {
        $p = row('SELECT sp.*, sp.icon_svg, spt.title, spt.description, spt.btn1_text, spt.btn2_text, spt.meta_title, spt.meta_description, m.path as image_path
                  FROM solution_pages sp
                  LEFT JOIN solution_pages_t spt ON sp.id=spt.page_id AND spt.lang_id=?
                  LEFT JOIN media m ON sp.image_id=m.id
                  WHERE sp.id=?', [$lang_id, $id]);
        if ($p) $p['image_url'] = $p['image_path'] ? admin_url($p['image_path']) : '';
        json_response($p ?: []);
    }
    json_response([]);
}

// ── POST ─────────────────────────────────────────────────────────────────────
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    if ($action === 'save_page') {
        $d = [
            'slug'       => $data['slug'] ?? '',
            'type'       => $data['type'] ?? 'solution',
            'sort_order' => (int)($data['sort_order'] ?? 0),
            'is_active'  => (int)($data['is_active'] ?? 1),
            'image_id'   => ($data['image_id'] ?? null) ?: null,
            'icon_svg'   => $data['icon_svg'] ?? null,
        ];
        if (!$id) $id = insert('solution_pages', $d);
        else update('solution_pages', $d, ['id' => $id]);

        $t = [
            'page_id'          => $id, 'lang_id' => $lang_id,
            'title'            => $data['title'] ?? '',
            'description'      => $data['description'] ?? '',
            'btn1_text'        => $data['btn1_text'] ?? '',
            'btn2_text'        => $data['btn2_text'] ?? '',
            'meta_title'       => $data['meta_title'] ?? '',
            'meta_description' => $data['meta_description'] ?? '',
        ];
        $et = row('SELECT id FROM solution_pages_t WHERE page_id=? AND lang_id=?', [$id, $lang_id]);
        if ($et) update('solution_pages_t', $t, ['id' => $et['id']]);
        else     insert('solution_pages_t', $t);
        json_response(['ok' => true, 'id' => $id]);
    }

    if ($action === 'save_block') {
        $page_id   = (int)($data['page_id'] ?? 0);
        $block_key = $data['block_key'] ?? '';
        $content   = json_encode($data['content'] ?? [], JSON_UNESCAPED_UNICODE);
        $existing  = row('SELECT id FROM sol_page_blocks WHERE page_id=? AND lang_id=? AND block_key=?', [$page_id, $lang_id, $block_key]);
        if ($existing) {
            update('sol_page_blocks', ['content' => $content, 'is_active' => (int)($data['is_active'] ?? 1), 'sort_order' => (int)($data['sort_order'] ?? 0)], ['id' => $existing['id']]);
        } else {
            insert('sol_page_blocks', ['page_id'=>$page_id,'lang_id'=>$lang_id,'block_key'=>$block_key,'label'=>$data['label']??$block_key,'sort_order'=>(int)($data['sort_order']??0),'is_active'=>(int)($data['is_active']??1),'content'=>$content]);
        }
        json_response(['ok' => true]);
    }

    if ($action === 'reorder_blocks') {
        foreach ($data['blocks'] ?? [] as $i => $blk) {
            $r = row('SELECT id FROM sol_page_blocks WHERE page_id=? AND lang_id=? AND block_key=?', [(int)$blk['page_id'], $lang_id, $blk['block_key']]);
            if ($r) update('sol_page_blocks', ['sort_order' => $i, 'is_active' => (int)($blk['is_active'] ?? 1)], ['id' => $r['id']]);
        }
        json_response(['ok' => true]);
    }

    if ($action === 'delete_page') {
        delete('sol_page_blocks', ['page_id' => $id]);
        delete('solution_pages_t', ['page_id' => $id]);
        delete('solution_features_t', ['feature_id' => 0]); // safe no-op
        delete('solution_pages', ['id' => $id]);
        json_response(['ok' => true]);
    }
}
