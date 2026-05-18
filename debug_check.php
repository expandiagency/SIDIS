<?php
// Diagnostic script - shows PHP errors and DB status
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo '<pre>';
echo 'PHP: ' . PHP_VERSION . "\n";
echo 'Time: ' . date('Y-m-d H:i:s') . "\n\n";

// Test config load
try {
    require_once __DIR__ . '/includes/config.php';
    echo "config.php: OK\n";
} catch (Throwable $e) {
    echo "config.php ERROR: " . $e->getMessage() . "\n";
}

// Test db load
try {
    require_once __DIR__ . '/includes/db.php';
    echo "db.php: OK\n";
} catch (Throwable $e) {
    echo "db.php ERROR: " . $e->getMessage() . "\n";
}

// Test DB connection
try {
    $pdo = db();
    echo "DB connection: OK\n";
} catch (Throwable $e) {
    echo "DB ERROR: " . $e->getMessage() . "\n";
}

// Test functions load
try {
    require_once __DIR__ . '/includes/functions.php';
    echo "functions.php: OK\n";
} catch (Throwable $e) {
    echo "functions.php ERROR: " . $e->getMessage() . "\n";
}

// Check extras column
try {
    $col = $pdo->query("SHOW COLUMNS FROM posts_t LIKE 'extras'")->fetch();
    echo 'extras column: ' . ($col ? 'EXISTS' : 'MISSING') . "\n";
} catch (Throwable $e) {
    echo "Column check ERROR: " . $e->getMessage() . "\n";
}

// Try adding extras column
try {
    $pdo->exec("ALTER TABLE posts_t ADD COLUMN extras MEDIUMTEXT DEFAULT NULL");
    echo "extras column: CREATED\n";
} catch (Throwable $e) {
    echo "ALTER: " . $e->getMessage() . "\n";
}

// Test get_post
try {
    $post = get_post(1, 'automate-repetitive-tasks-with-rpa');
    echo "get_post: " . ($post ? 'OK (title: ' . $post['title'] . ')' : 'NOT FOUND') . "\n";
} catch (Throwable $e) {
    echo "get_post ERROR: " . $e->getMessage() . "\n";
    echo "  at " . $e->getFile() . ':' . $e->getLine() . "\n";
}

echo "\nDone.";
echo '</pre>';
// Self-delete after 5 minutes of creation
if (time() - filemtime(__FILE__) > 300) unlink(__FILE__);
