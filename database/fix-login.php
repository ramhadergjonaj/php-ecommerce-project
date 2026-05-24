<?php
/**
 * Rivendos fjalëkalimet dhe teston login-in.
 * Hapni: http://localhost/php-ecommerce-project-main/database/fix-login.php
 */
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/classes/User.php';
require_once dirname(__DIR__) . '/classes/Admin.php';

header('Content-Type: text/html; charset=UTF-8');
echo '<pre style="font-family:monospace;padding:1rem;">';

$accounts = [
    ['admin@shop.com', 'admin123', 'admin'],
    ['user@shop.com', 'user123', 'customer'],
    ['test@shop.com', 'test123', 'customer'],
];

try {
    $db = getDB();
    $update = $db->prepare('UPDATE users SET password = ? WHERE LOWER(email) = LOWER(?)');

    echo "=== Rivendosje fjalëkalimesh (password_hash) ===\n\n";

    foreach ($accounts as [$email, $plain, $role]) {
        $hash = password_hash($plain, PASSWORD_DEFAULT);
        $update->execute([$hash, $email]);

        $stmt = $db->prepare('SELECT * FROM users WHERE LOWER(email) = LOWER(?)');
        $stmt->execute([$email]);
        $row = $stmt->fetch();

        if (!$row) {
            echo "[X] $email – nuk ekziston në tabelën users!\n";
            continue;
        }

        $user = $role === 'admin'
            ? new Admin((int) $row['id'], $row['name'], $row['email'], $row['password'])
            : new User((int) $row['id'], $row['name'], $row['email'], $row['role'], $row['password']);

        $ok = $user->verifyPassword($plain);
        echo ($ok ? '[OK]' : '[GABIM]') . " $email / $plain – hash: " . ($user->isPasswordHashed() ? 'po' : 'jo') . "\n";
    }

    echo "\n=== Gati ===\n";
    echo "Tani provoni login:\n";
    echo "  admin@shop.com / admin123\n";
    echo "  user@shop.com / user123\n";
} catch (Throwable $e) {
    echo 'Gabim: ' . $e->getMessage();
}

echo '</pre>';
//fix-login