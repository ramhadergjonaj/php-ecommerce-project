<?php
/**
 * Test SMTP – http://localhost/php-ecommerce-project-main/database/test-email.php
 */
require_once dirname(__DIR__) . '/functions/mailer.php';

header('Content-Type: text/plain; charset=UTF-8');

$cfg = getSmtpConfig();

echo "SMTP enabled: " . ($cfg['enabled'] ? 'po' : 'jo') . "\n";
echo "Host: {$cfg['host']}:{$cfg['port']}\n";
echo "User: {$cfg['user']}\n\n";

if (!$cfg['enabled'] || $cfg['user'] === '') {
    echo "Krijoni config/smtp.local.php nga config/smtp.local.example.php\n";
    exit;
}

$to = $cfg['from_email'];
$ok = sendAppEmail($to, 'Test PHP Shop SMTP', "Ky është një email test.\n\n" . date('Y-m-d H:i:s'));

echo $ok ? "Email u dërgua me sukses te $to\n" : "Dërgimi dështoi. Shihni error log.\n";
