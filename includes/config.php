<?php
// Database — fill in after creating DB in cPanel
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
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
