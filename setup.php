<?php
/**
 * Ekzekutoni një herë pas importit të SQL: vendos fjalëkalimet me password_hash().
 * Hapni: http://localhost/php-ecommerce-project-main/setup.php
 */

require_once __DIR__ . '/includes/bootstrap.php';

header('Content-Type: text/html; charset=UTF-8');

$accounts = [
    'admin@shop.com' => 'admin123',
    'user@shop.com' => 'user123',
    'test@shop.com' => 'test123',
];

try {
    $db = getDB();
    $stmt = $db->prepare('UPDATE users SET password = ? WHERE email = ?');

    foreach ($accounts as $email => $plain) {
        $stmt->execute([password_hash($plain, PASSWORD_DEFAULT), $email]);
    }

    echo '<h1>Setup u përfundua</h1>';
    echo '<p>Fjalëkalimet u hash-uan me <code>password_hash()</code>.</p>';
    echo '<p><a href="' . htmlspecialchars(base_url('login.php')) . '">Shko te Login</a></p>';
    echo '<p><strong>Fshijeni këtë skedar (setup.php) pas përdorimit në prodhim.</strong></p>';
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>Gabim setup</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<p>Importoni fillimisht <code>database/ecommerce_db.sql</code> në phpMyAdmin.</p>';
}
