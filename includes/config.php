<?php
// Database — fill in after creating DB in cPanel
define('DB_HOST', 'sidisgr.mysql.tools');
define('DB_NAME', 'sidisgr_git');
define('DB_USER', 'sidisgr_git');
define('DB_PASS', '3Hgg8@i3H_');
define('DB_CHARSET', 'utf8mb4');

// Site URL (no trailing slash)
define('SITE_URL', 'https://sidistech.group/www');

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
