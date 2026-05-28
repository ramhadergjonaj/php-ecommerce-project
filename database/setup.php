<?php
/**
 * Ekzekutoni një herë pas importit të ecommerce_db.sql:
 * http://localhost/php-ecommerce-project-main/database/setup.php
 */
require_once dirname(__DIR__) . '/config/database.php';

header('Content-Type: text/plain; charset=UTF-8');
//.
try {
    $db = getDB();

    $accounts = [
        ['admin@shop.com', 'admin123'],
        ['user@shop.com', 'user123'],
        ['test@shop.com', 'test123'],
    ];

    $stmt = $db->prepare('UPDATE users SET password = ? WHERE email = ?');

    foreach ($accounts as [$email, $plain]) {
        $hash = password_hash($plain, PASSWORD_DEFAULT);
        $stmt->execute([$hash, $email]);
        echo "Përditësuar: $email\n";
    }

    echo "\nFjalëkalimet u hash-uan me password_hash(). Fshijeni këtë skedar në prodhim.\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo 'Gabim: ' . $e->getMessage();
}