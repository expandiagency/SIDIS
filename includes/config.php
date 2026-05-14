<?php
// Database — fill in after creating DB in cPanel
define('DB_HOST', 'localhost');
define('DB_NAME', 'flowerwo_sidis');
define('DB_USER', 'flowerwo_sidisgroup');
define('DB_PASS', '9?Y^mm+UZ7}9w^O?');
define('DB_CHARSET', 'utf8mb4');

// Site URL (no trailing slash)
define('SITE_URL', 'https://sidis.expandi.agency');

// Upload paths
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');
define('UPLOAD_URL', '/uploads/');

// Admin credentials (initial — change after first login via admin panel)
define('ADMIN_DEFAULT_EMAIL', 'admin@sidis.agency');
define('ADMIN_DEFAULT_PASS', 'Sidis2025!');

// Default language
define('DEFAULT_LANG', 'en');

// Session name
define('SESSION_NAME', 'sidis_admin');
