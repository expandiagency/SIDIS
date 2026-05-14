<?php
/**
 * SIDIS CMS — Database Setup
 * Run once: https://sidis.expandi.agency/setup.php
 * DELETE this file after installation!
 */
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/config.php';

$errors = [];
$done   = [];

try {
    $pdo = db();
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

    /* ── Schema ─────────────────────────────────────────────────────────── */
    $tables = [

'languages' => "CREATE TABLE IF NOT EXISTS languages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(10) NOT NULL UNIQUE,
  name VARCHAR(60) NOT NULL,
  is_active TINYINT DEFAULT 1,
  is_default TINYINT DEFAULT 0,
  sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'media' => "CREATE TABLE IF NOT EXISTS media (
  id INT AUTO_INCREMENT PRIMARY KEY,
  filename VARCHAR(255) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  path VARCHAR(500) NOT NULL,
  mime_type VARCHAR(100) NOT NULL,
  file_size INT NOT NULL,
  alt_text VARCHAR(500) DEFAULT '',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'settings' => "CREATE TABLE IF NOT EXISTS settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  `key` VARCHAR(100) NOT NULL,
  value TEXT,
  lang_id INT DEFAULT NULL,
  UNIQUE KEY unique_setting (`key`, lang_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'admin_users' => "CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  is_active TINYINT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'nav_items' => "CREATE TABLE IF NOT EXISTS nav_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  lang_id INT NOT NULL,
  location ENUM('header','footer') DEFAULT 'header',
  parent_id INT DEFAULT NULL,
  title VARCHAR(200) NOT NULL,
  url VARCHAR(500) DEFAULT '#',
  target VARCHAR(20) DEFAULT '_self',
  sort_order INT DEFAULT 0,
  is_active TINYINT DEFAULT 1,
  has_mega TINYINT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'nav_mega_categories' => "CREATE TABLE IF NOT EXISTS nav_mega_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nav_item_id INT NOT NULL,
  lang_id INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT DEFAULT '',
  sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'nav_mega_subitems' => "CREATE TABLE IF NOT EXISTS nav_mega_subitems (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  lang_id INT NOT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT DEFAULT '',
  url VARCHAR(500) DEFAULT '#',
  icon_svg TEXT DEFAULT '',
  sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'home_content' => "CREATE TABLE IF NOT EXISTS home_content (
  id INT AUTO_INCREMENT PRIMARY KEY,
  lang_id INT NOT NULL UNIQUE,
  hero_title TEXT DEFAULT '',
  hero_subtitle TEXT DEFAULT '',
  hero_btn1_text VARCHAR(100) DEFAULT '',
  hero_btn1_url VARCHAR(300) DEFAULT '#',
  hero_btn2_text VARCHAR(100) DEFAULT '',
  hero_btn2_url VARCHAR(300) DEFAULT '#',
  hero_video_path VARCHAR(500) DEFAULT '',
  hero_badge1_id INT DEFAULT NULL,
  hero_badge2_id INT DEFAULT NULL,
  why_title VARCHAR(300) DEFAULT '',
  automation_title TEXT DEFAULT '',
  automation_text TEXT DEFAULT '',
  automation_loc1_icon VARCHAR(100) DEFAULT '',
  automation_loc1_title VARCHAR(100) DEFAULT '',
  automation_loc1_city VARCHAR(100) DEFAULT '',
  automation_loc2_icon VARCHAR(100) DEFAULT '',
  automation_loc2_title VARCHAR(100) DEFAULT '',
  automation_loc2_city VARCHAR(100) DEFAULT '',
  solutions_title VARCHAR(300) DEFAULT '',
  projects_title VARCHAR(300) DEFAULT '',
  projects_btn_text VARCHAR(100) DEFAULT '',
  projects_btn_url VARCHAR(300) DEFAULT '/cases/',
  reviews_title VARCHAR(300) DEFAULT '',
  cta_title TEXT DEFAULT '',
  cta_subtitle TEXT DEFAULT '',
  cta_btn_text VARCHAR(100) DEFAULT '',
  cta_btn_url VARCHAR(300) DEFAULT '#',
  why_subtitle VARCHAR(300) DEFAULT '',
  why_text TEXT DEFAULT '',
  solutions_text TEXT DEFAULT '',
  automation_btn1_text VARCHAR(100) DEFAULT '',
  automation_btn2_text VARCHAR(100) DEFAULT '',
  roadmap_title VARCHAR(200) DEFAULT '',
  roadmap_btn1_text VARCHAR(100) DEFAULT '',
  roadmap_btn1_url VARCHAR(300) DEFAULT '#',
  roadmap_btn2_text VARCHAR(100) DEFAULT '',
  roadmap_btn2_url VARCHAR(300) DEFAULT '#',
  roadmap_steps TEXT DEFAULT '',
  cta_text TEXT DEFAULT '',
  cta_item2 TEXT DEFAULT '',
  cta_item3 TEXT DEFAULT '',
  cta_form_title VARCHAR(200) DEFAULT '',
  presentation_title VARCHAR(200) DEFAULT '',
  presentation_subtitle VARCHAR(200) DEFAULT '',
  presentation_text TEXT DEFAULT '',
  presentation_video VARCHAR(500) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'home_blocks' => "CREATE TABLE IF NOT EXISTS home_blocks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  block_key VARCHAR(50) NOT NULL,
  label VARCHAR(100) NOT NULL,
  sort_order INT DEFAULT 0,
  is_active TINYINT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'home_why_slides' => "CREATE TABLE IF NOT EXISTS home_why_slides (
  id INT AUTO_INCREMENT PRIMARY KEY,
  lang_id INT NOT NULL,
  sort_order INT DEFAULT 0,
  icon_svg TEXT DEFAULT '',
  title VARCHAR(300) NOT NULL DEFAULT '',
  text TEXT DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'home_partner_logos' => "CREATE TABLE IF NOT EXISTS home_partner_logos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  image_id INT DEFAULT NULL,
  alt_text VARCHAR(200) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'home_automation_images' => "CREATE TABLE IF NOT EXISTS home_automation_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  image_id INT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'reviews' => "CREATE TABLE IF NOT EXISTS reviews (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sort_order INT DEFAULT 0,
  author_image_id INT DEFAULT NULL,
  linkedin_url VARCHAR(300) DEFAULT '',
  rating_image_id INT DEFAULT NULL,
  is_active TINYINT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'reviews_t' => "CREATE TABLE IF NOT EXISTS reviews_t (
  id INT AUTO_INCREMENT PRIMARY KEY,
  review_id INT NOT NULL,
  lang_id INT NOT NULL,
  quote VARCHAR(500) DEFAULT '',
  text TEXT DEFAULT '',
  author_name VARCHAR(150) DEFAULT '',
  author_title VARCHAR(200) DEFAULT '',
  UNIQUE KEY unique_review_lang (review_id, lang_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'terms' => "CREATE TABLE IF NOT EXISTS terms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type ENUM('solution','department','industry') NOT NULL,
  slug VARCHAR(100) NOT NULL,
  sort_order INT DEFAULT 0,
  is_active TINYINT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'terms_t' => "CREATE TABLE IF NOT EXISTS terms_t (
  id INT AUTO_INCREMENT PRIMARY KEY,
  term_id INT NOT NULL,
  lang_id INT NOT NULL,
  name VARCHAR(200) NOT NULL,
  UNIQUE KEY unique_term_lang (term_id, lang_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'solution_items' => "CREATE TABLE IF NOT EXISTS solution_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  type ENUM('solution','department','industry') NOT NULL,
  icon_svg TEXT DEFAULT '',
  sort_order INT DEFAULT 0,
  is_active TINYINT DEFAULT 1,
  page_slug VARCHAR(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'solution_items_t' => "CREATE TABLE IF NOT EXISTS solution_items_t (
  id INT AUTO_INCREMENT PRIMARY KEY,
  item_id INT NOT NULL,
  lang_id INT NOT NULL,
  title VARCHAR(300) DEFAULT '',
  description TEXT DEFAULT '',
  btn_text VARCHAR(100) DEFAULT 'Learn more',
  btn_url VARCHAR(300) DEFAULT '#',
  UNIQUE KEY unique_item_lang (item_id, lang_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'solution_pages' => "CREATE TABLE IF NOT EXISTS solution_pages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(150) NOT NULL UNIQUE,
  type ENUM('solution','department','industry') NOT NULL DEFAULT 'solution',
  sort_order INT DEFAULT 0,
  is_active TINYINT DEFAULT 1,
  image_id INT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'solution_pages_t' => "CREATE TABLE IF NOT EXISTS solution_pages_t (
  id INT AUTO_INCREMENT PRIMARY KEY,
  page_id INT NOT NULL,
  lang_id INT NOT NULL,
  title VARCHAR(300) DEFAULT '',
  description TEXT DEFAULT '',
  btn1_text VARCHAR(100) DEFAULT '',
  btn2_text VARCHAR(100) DEFAULT '',
  meta_title VARCHAR(300) DEFAULT '',
  meta_description TEXT DEFAULT '',
  UNIQUE KEY unique_page_lang (page_id, lang_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'solution_features' => "CREATE TABLE IF NOT EXISTS solution_features (
  id INT AUTO_INCREMENT PRIMARY KEY,
  page_id INT NOT NULL,
  sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'solution_features_t' => "CREATE TABLE IF NOT EXISTS solution_features_t (
  id INT AUTO_INCREMENT PRIMARY KEY,
  feature_id INT NOT NULL,
  lang_id INT NOT NULL,
  title VARCHAR(300) DEFAULT '',
  text TEXT DEFAULT '',
  UNIQUE KEY unique_feature_lang (feature_id, lang_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'authors' => "CREATE TABLE IF NOT EXISTS authors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  image_id INT DEFAULT NULL,
  linkedin_url VARCHAR(300) DEFAULT '',
  is_active TINYINT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'authors_t' => "CREATE TABLE IF NOT EXISTS authors_t (
  id INT AUTO_INCREMENT PRIMARY KEY,
  author_id INT NOT NULL,
  lang_id INT NOT NULL,
  name VARCHAR(150) DEFAULT '',
  title VARCHAR(200) DEFAULT '',
  UNIQUE KEY unique_author_lang (author_id, lang_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'cases' => "CREATE TABLE IF NOT EXISTS cases (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(150) NOT NULL UNIQUE,
  is_active TINYINT DEFAULT 1,
  is_featured TINYINT DEFAULT 0,
  sort_order INT DEFAULT 0,
  company_logo_id INT DEFAULT NULL,
  featured_image_id INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'cases_t' => "CREATE TABLE IF NOT EXISTS cases_t (
  id INT AUTO_INCREMENT PRIMARY KEY,
  case_id INT NOT NULL,
  lang_id INT NOT NULL,
  title VARCHAR(300) DEFAULT '',
  description TEXT DEFAULT '',
  overview_text TEXT DEFAULT '',
  location VARCHAR(200) DEFAULT '',
  cooperation_period VARCHAR(200) DEFAULT '',
  meta_title VARCHAR(300) DEFAULT '',
  meta_description TEXT DEFAULT '',
  UNIQUE KEY unique_case_lang (case_id, lang_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'case_key_results' => "CREATE TABLE IF NOT EXISTS case_key_results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  case_id INT NOT NULL,
  lang_id INT NOT NULL,
  text TEXT DEFAULT '',
  sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'case_challenges' => "CREATE TABLE IF NOT EXISTS case_challenges (
  id INT AUTO_INCREMENT PRIMARY KEY,
  case_id INT NOT NULL,
  sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'case_challenges_t' => "CREATE TABLE IF NOT EXISTS case_challenges_t (
  id INT AUTO_INCREMENT PRIMARY KEY,
  challenge_id INT NOT NULL,
  lang_id INT NOT NULL,
  title VARCHAR(300) DEFAULT '',
  text TEXT DEFAULT '',
  UNIQUE KEY unique_ch_lang (challenge_id, lang_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'case_tech_items' => "CREATE TABLE IF NOT EXISTS case_tech_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  case_id INT NOT NULL,
  lang_id INT NOT NULL,
  name VARCHAR(150) DEFAULT '',
  icon_svg TEXT DEFAULT '',
  sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'case_services' => "CREATE TABLE IF NOT EXISTS case_services (
  id INT AUTO_INCREMENT PRIMARY KEY,
  case_id INT NOT NULL,
  lang_id INT NOT NULL,
  service_name VARCHAR(200) DEFAULT '',
  sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'case_terms' => "CREATE TABLE IF NOT EXISTS case_terms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  case_id INT NOT NULL,
  term_id INT NOT NULL,
  type ENUM('solution','department','industry') NOT NULL,
  UNIQUE KEY unique_case_term (case_id, term_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'posts' => "CREATE TABLE IF NOT EXISTS posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(150) NOT NULL UNIQUE,
  is_active TINYINT DEFAULT 1,
  author_id INT DEFAULT NULL,
  featured_image_id INT DEFAULT NULL,
  published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'posts_t' => "CREATE TABLE IF NOT EXISTS posts_t (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT NOT NULL,
  lang_id INT NOT NULL,
  title VARCHAR(300) DEFAULT '',
  subtitle TEXT DEFAULT '',
  excerpt TEXT DEFAULT '',
  content LONGTEXT DEFAULT '',
  meta_title VARCHAR(300) DEFAULT '',
  meta_description TEXT DEFAULT '',
  UNIQUE KEY unique_post_lang (post_id, lang_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'post_tags' => "CREATE TABLE IF NOT EXISTS post_tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT NOT NULL,
  lang_id INT NOT NULL,
  tag_text VARCHAR(150) DEFAULT '',
  sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

'post_toc' => "CREATE TABLE IF NOT EXISTS post_toc (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT NOT NULL,
  lang_id INT NOT NULL,
  title VARCHAR(300) DEFAULT '',
  anchor VARCHAR(150) DEFAULT '',
  sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    ];

    foreach ($tables as $name => $sql) {
        $pdo->exec($sql);
        $done[] = "Table <code>$name</code> — OK";
    }

    /* ── Seed data ──────────────────────────────────────────────────────── */

    // Default language
    $existing = row('SELECT id FROM languages WHERE code=?', ['en']);
    if (!$existing) {
        insert('languages', ['code' => 'en', 'name' => 'English', 'is_active' => 1, 'is_default' => 1, 'sort_order' => 0]);
        $done[] = 'Seeded default language: English';
    }

    // Admin user
    $admin = row('SELECT id FROM admin_users WHERE email=?', [ADMIN_DEFAULT_EMAIL]);
    if (!$admin) {
        insert('admin_users', [
            'name'          => 'Admin',
            'email'         => ADMIN_DEFAULT_EMAIL,
            'password_hash' => password_hash(ADMIN_DEFAULT_PASS, PASSWORD_BCRYPT),
            'is_active'     => 1,
        ]);
        $done[] = 'Created admin user: <strong>' . ADMIN_DEFAULT_EMAIL . '</strong> / ' . ADMIN_DEFAULT_PASS;
    }

    // English home content placeholder
    $lang = row('SELECT id FROM languages WHERE code=?', ['en']);
    if ($lang) {
        $existing_home = row('SELECT id FROM home_content WHERE lang_id=?', [$lang['id']]);
        if (!$existing_home) {
            insert('home_content', [
                'lang_id'          => $lang['id'],
                'hero_title'       => 'Business process automation',
                'hero_subtitle'    => 'We build custom automation software that eliminates manual work, connects your tools, and scales your operations.',
                'hero_btn1_text'   => 'Try AI assistant',
                'hero_btn1_url'    => '#',
                'hero_btn2_text'   => 'Free audit',
                'hero_btn2_url'    => '#',
                'hero_video_path'  => './assets/video/1-hero.mp4',
                'why_title'        => 'Why automation?',
                'automation_title' => 'Sidis Automation Hub',
                'automation_text'  => 'Our hubs are strategically located to serve global clients with local expertise.',
                'automation_loc1_title' => 'Estonia',
                'automation_loc1_city'  => 'Tallinn',
                'automation_loc2_title' => 'Switzerland',
                'automation_loc2_city'  => 'Lausanne',
                'solutions_title'  => 'Our solutions',
                'projects_title'   => 'Recent projects',
                'projects_btn_text'=> 'View all projects',
                'projects_btn_url' => '/cases/',
                'reviews_title'    => 'Client reviews',
                'cta_title'        => 'Ready to automate?',
                'cta_btn_text'     => 'Get a free audit',
                'cta_btn_url'      => '#',
            ]);
            $done[] = 'Seeded home content (EN)';
        }
    }

    // Home blocks order
    $blocks_count = row('SELECT COUNT(*) as c FROM home_blocks')['c'] ?? 0;
    if ($blocks_count == 0) {
        $default_blocks = [
            ['hero','Hero Section'],['why','Why Us (Carousel)'],['automation','Automation Hub'],
            ['solutions','Solutions / Tabs'],['projects','Projects Grid'],['reviews','Client Reviews'],
            ['roadmap','Roadmap'],['getintouch','Get In Touch Form'],['presentation','Video Presentation'],
        ];
        foreach ($default_blocks as $i => [$key, $label]) {
            insert('home_blocks', ['block_key'=>$key,'label'=>$label,'sort_order'=>$i,'is_active'=>1]);
        }
        $done[] = 'Seeded home blocks order';
    }

    // Default nav items (EN header)
    $nav_count = row('SELECT COUNT(*) as c FROM nav_items WHERE lang_id=?', [$lang['id'] ?? 1]);
    if (($nav_count['c'] ?? 0) == 0) {
        $header_items = [
            ['title' => 'Solutions', 'url' => '/solutions/', 'sort_order' => 0, 'has_mega' => 1],
            ['title' => 'Case Studies', 'url' => '/cases/', 'sort_order' => 1, 'has_mega' => 0],
            ['title' => 'Blog', 'url' => '/blog/', 'sort_order' => 2, 'has_mega' => 0],
            ['title' => 'Contact', 'url' => '#contact', 'sort_order' => 3, 'has_mega' => 0],
        ];
        foreach ($header_items as $item) {
            insert('nav_items', array_merge($item, ['lang_id' => $lang['id'] ?? 1, 'location' => 'header', 'is_active' => 1]));
        }
        $footer_items = [
            ['title' => 'Solutions', 'url' => '/solutions/', 'sort_order' => 0],
            ['title' => 'Case Studies', 'url' => '/cases/', 'sort_order' => 1],
            ['title' => 'Blog', 'url' => '/blog/', 'sort_order' => 2],
            ['title' => 'Privacy Policy', 'url' => '/privacy/', 'sort_order' => 3],
        ];
        foreach ($footer_items as $item) {
            insert('nav_items', array_merge($item, ['lang_id' => $lang['id'] ?? 1, 'location' => 'footer', 'is_active' => 1, 'has_mega' => 0]));
        }
        $done[] = 'Seeded default navigation (EN)';
    }

    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');

} catch (PDOException $e) {
    $errors[] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SIDIS CMS — Setup</title>
<style>
  body { font-family: system-ui, sans-serif; max-width: 800px; margin: 40px auto; padding: 0 20px; background: #0f0f0f; color: #e0e0e0; }
  h1 { color: #772885; }
  .ok { color: #4caf50; margin: 4px 0; }
  .err { color: #f44336; margin: 4px 0; }
  .warn { background: #772885; color: #fff; padding: 16px; border-radius: 8px; margin-top: 24px; }
  a { color: #772885; }
</style>
</head>
<body>
<h1>SIDIS CMS — Installation</h1>
<?php foreach ($errors as $e): ?>
  <p class="err">❌ <?= e($e) ?></p>
<?php endforeach; ?>
<?php foreach ($done as $d): ?>
  <p class="ok">✅ <?= $d ?></p>
<?php endforeach; ?>
<?php if (!$errors): ?>
  <div class="warn">
    <strong>⚠ IMPORTANT:</strong> Delete <code>setup.php</code> from the server immediately!<br>
    <a href="/admin/">Go to Admin Panel →</a>
  </div>
<?php endif; ?>
</body>
</html>
<?php
// e() is defined in includes/functions.php
