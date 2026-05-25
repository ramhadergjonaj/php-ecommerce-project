<?php
/**
 * Hapni në shfletues për të testuar lidhjen me MySQL:
 * http://localhost/php-ecommerce-project-main/database/test-connection.php
 * ose http://localhost:8000/database/test-connection.php
 */
header('Content-Type: text/html; charset=UTF-8');

$configs = [
    'A: root, pa fjalëkalim, localhost' => ['host' => 'localhost', 'user' => 'root', 'pass' => ''],
    'B: root, pa fjalëkalim, 127.0.0.1' => ['host' => '127.0.0.1', 'user' => 'root', 'pass' => ''],
];

echo '<h1>Test lidhje MySQL</h1><pre>';

foreach ($configs as $label => $c) {
    echo "\n=== $label ===\n";
    try {
        $dsn = 'mysql:host=' . $c['host'] . ';dbname=ecommerce_db;charset=utf8mb4';
        $pdo = new PDO($dsn, $c['user'], $c['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $count = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
        echo "OK – Produktet në DB: $count\n";
        echo "→ Përdorni në config/database.local.php:\n";
        echo "   DB_HOST = '{$c['host']}'\n";
        echo "   DB_USER = '{$c['user']}'\n";
        echo "   DB_PASS = ''  (bosh)\n";
    } catch (PDOException $e) {
        echo 'DËSHTOI: ' . $e->getMessage() . "\n";
    }
}

echo "\n\nPHP që po ekzekuton: " . PHP_VERSION . "\n";
echo "Skedari php.ini: " . (php_ini_loaded_file() ?: 'i panjohur') . "\n";
echo '</pre><p><strong>Nëse të dyja dështojnë:</strong> vendosni fjalëkalimin që përdorni për phpMyAdmin në database.local.php</p>';
