<?php
// One-off database importer. Trigger: /import_db.php?key=sidis2026
// Imports flowerwo_sidis.sql into the configured database via PDO.
// DELETE this file and the .sql dump after a successful import.
if (($_GET['key'] ?? '') !== 'sidis2026') { http_response_code(403); die('Forbidden'); }
require_once __DIR__ . '/includes/db.php';

@set_time_limit(0);
header('Content-Type: text/plain; charset=utf-8');

$file = __DIR__ . '/flowerwo_sidis.sql';
if (!is_file($file)) { die("SQL file not found: $file\n"); }

$sql = file_get_contents($file);
if ($sql === false) { die("Could not read SQL file\n"); }

// --- Split SQL into individual statements, respecting quotes/comments ---
function split_sql(string $sql): array {
    $stmts = [];
    $buf = '';
    $len = strlen($sql);
    $inSingle = false; $inDouble = false; $inBacktick = false;
    $inLineComment = false; $inBlockComment = false;
    for ($i = 0; $i < $len; $i++) {
        $c = $sql[$i];
        $next = $i + 1 < $len ? $sql[$i + 1] : '';

        if ($inLineComment) {
            if ($c === "\n") { $inLineComment = false; }
            continue;
        }
        if ($inBlockComment) {
            if ($c === '*' && $next === '/') { $inBlockComment = false; $i++; }
            continue;
        }
        if (!$inSingle && !$inDouble && !$inBacktick) {
            // comment starts
            if ($c === '-' && $next === '-' && (($i + 2 >= $len) || $sql[$i + 2] === ' ' || $sql[$i + 2] === "\t" || $sql[$i + 2] === "\n" || $sql[$i + 2] === "\r")) {
                $inLineComment = true; continue;
            }
            if ($c === '#') { $inLineComment = true; continue; }
            if ($c === '/' && $next === '*') { $inBlockComment = true; $i++; continue; }
        }

        if ($inSingle) {
            $buf .= $c;
            if ($c === '\\') { if ($next !== '') { $buf .= $next; $i++; } continue; }
            if ($c === "'") { $inSingle = false; }
            continue;
        }
        if ($inDouble) {
            $buf .= $c;
            if ($c === '\\') { if ($next !== '') { $buf .= $next; $i++; } continue; }
            if ($c === '"') { $inDouble = false; }
            continue;
        }
        if ($inBacktick) {
            $buf .= $c;
            if ($c === '`') { $inBacktick = false; }
            continue;
        }

        if ($c === "'") { $inSingle = true; $buf .= $c; continue; }
        if ($c === '"') { $inDouble = true; $buf .= $c; continue; }
        if ($c === '`') { $inBacktick = true; $buf .= $c; continue; }

        if ($c === ';') {
            $t = trim($buf);
            if ($t !== '') { $stmts[] = $t; }
            $buf = '';
            continue;
        }
        $buf .= $c;
    }
    $t = trim($buf);
    if ($t !== '') { $stmts[] = $t; }
    return $stmts;
}

$pdo = db();
$pdo->exec('SET NAMES utf8mb4');
$pdo->exec('SET FOREIGN_KEY_CHECKS=0');

$statements = split_sql($sql);
echo "Parsed " . count($statements) . " statements\n\n";

$ok = 0; $errors = 0;
foreach ($statements as $idx => $stmt) {
    // skip pure SET/START TRANSACTION/COMMIT control lines that phpMyAdmin adds (safe to run anyway)
    try {
        $pdo->exec($stmt);
        $ok++;
    } catch (Throwable $e) {
        $errors++;
        $preview = substr(preg_replace('/\s+/', ' ', $stmt), 0, 120);
        echo "ERROR [#$idx]: " . $e->getMessage() . "\n  -> $preview\n\n";
    }
}

$pdo->exec('SET FOREIGN_KEY_CHECKS=1');

echo "\n---\nExecuted OK: $ok\nErrors: $errors\n";
echo $errors === 0 ? "IMPORT SUCCESSFUL\n" : "IMPORT COMPLETED WITH ERRORS\n";
