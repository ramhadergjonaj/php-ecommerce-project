<?php
/**
 * Kopjojeni si config/smtp.local.php dhe plotësoni të dhënat.
 *
 * Gmail: përdorni "App Password" (2FA duhet aktiv).
 * https://myaccount.google.com/apppasswords
 */
define('SMTP_ENABLED', true);
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls'); // tls ose ssl
define('SMTP_USER', 'emaili.juaj@gmail.com');
define('SMTP_PASS', 'app_password_16_chars');
define('SMTP_FROM_EMAIL', 'emaili.juaj@gmail.com');
define('SMTP_FROM_NAME', 'PHP Shop');
