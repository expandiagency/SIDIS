<?php
require_once __DIR__ . '/config.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

function q(string $sql, array $params = []): PDOStatement {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

function row(string $sql, array $params = []): ?array {
    return q($sql, $params)->fetch() ?: null;
}

function rows(string $sql, array $params = []): array {
    return q($sql, $params)->fetchAll();
}

function insert(string $table, array $data): int {
    $cols = implode(',', array_map(fn($k) => "`$k`", array_keys($data)));
    $vals = implode(',', array_fill(0, count($data), '?'));
    q("INSERT INTO `$table` ($cols) VALUES ($vals)", array_values($data));
    return (int) db()->lastInsertId();
}

function update(string $table, array $data, array $where): int {
    $set   = implode(',', array_map(fn($k) => "`$k`=?", array_keys($data)));
    $cond  = implode(' AND ', array_map(fn($k) => "`$k`=?", array_keys($where)));
    $stmt  = q("UPDATE `$table` SET $set WHERE $cond",
                array_merge(array_values($data), array_values($where)));
    return $stmt->rowCount();
}

function delete(string $table, array $where): int {
    $cond = implode(' AND ', array_map(fn($k) => "`$k`=?", array_keys($where)));
    $stmt = q("DELETE FROM `$table` WHERE $cond", array_values($where));
    return $stmt->rowCount();
}
