# Konfigurimi i SMTP në XAMPP

## Hapi 1 – Kopjoni konfigurimin

```text
config/smtp.local.example.php  →  config/smtp.local.php
```

Plotësoni me të dhënat e email-it tuaj.

## Hapi 2 – Gmail (rekomanduar për testim)

1. Aktivizoni **2FA** në llogarinë Google.
2. Krijoni **App Password**: https://myaccount.google.com/apppasswords
3. Në `smtp.local.php`:

```php
define('SMTP_ENABLED', true);
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'emaili@gmail.com');
define('SMTP_PASS', 'xxxx xxxx xxxx xxxx'); // App Password 16 shifra
define('SMTP_FROM_EMAIL', 'emaili@gmail.com');
define('SMTP_FROM_NAME', 'PHP Shop');
```

## Hapi 3 – Testoni

Hapni në shfletues:

```
http://localhost/php-ecommerce-project-main/database/test-email.php
```

## Hapi 4 – Përdorimi në aplikacion

SMTP përdoret automatikisht për:

- Formën **Kontakti** (`pages/contact.php`)
- **Konfirmimin e porosisë** pas checkout

Nëse SMTP nuk është aktiv, mesazhi ruhet në databazë dhe përdoret `mail()` si rezervë.

## Alternativa: php.ini (mail() pa SMTP në kod)

Në `C:\xampp\php\php.ini` (rrugë relative te instalimi juaj):

```ini
[mail function]
SMTP = smtp.gmail.com
smtp_port = 587
sendmail_from = emaili@gmail.com
```

Rinisni Apache pas ndryshimit.

> Për projektin shkollor, **smtp.local.php** në projekt është më e qartë për demonstrim.

## Probleme të zakonshme

| Problem | Zgjidhje |
|---------|----------|
| Access denied | Përdorni App Password, jo fjalëkalimin e Gmail |
| Connection timeout | Kontrolloni firewall / internet |
| Email në spam | Normal për testim; kontrolloni folder Spam |
