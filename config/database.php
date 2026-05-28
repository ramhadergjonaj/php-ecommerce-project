<?php

if (file_exists(__DIR__ . '/database.local.php')) {
    require_once __DIR__ . '/database.local.php';
} else {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'ecommerce_db');
    define('DB_USER', 'root');
    define('DB_PASS', '');
}

/**
 * Lidhje PDO me try/catch (Faza II – siguri & error handling).
 */
function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());

            $hint = '1045' === (string) ($e->errorInfo[1] ?? '')
                ? ' MySQL refuzoi përdoruesin/fjalëkalimin. Krijoni config/database.local.php (shih database.local.example.php) dhe vendosni DB_PASS të saktë. Sigurohuni që MySQL në XAMPP është i ndezur.'
                : ' Importoni database/ecommerce_db.sql në phpMyAdmin.';

            throw new RuntimeException(
                'Lidhja me databazën dështoi.' . $hint,
                0,
                $e
            );
        }
    }

    return $pdo;
}
//.