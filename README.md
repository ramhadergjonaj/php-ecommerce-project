# PHP Shop — Dyqan Online

Aplikacion e-commerce me PHP, MySQL, OOP, autentikim, shportë, porosi dhe komunikim me email.

## Karakteristikat

- Katalog produktesh, shportë, checkout, porositë e mia
- Regjistrim, login, Google OAuth, rivendosje fjalëkalimi
- Panel admin: produkte, kategori, përdorues, porosi, kthime, mesazhe
- Komunikim email: kontakt, përgjigje admin, njoftime porosish/kthimesh
- Kthim porosie: 7 orë (direkt) / pas 7 orëve (miratim admin)
- Siguri: prepared statements, password_hash, XSS, validim server-side

## Instalim (XAMPP)

1. Vendosni projektin në `htdocs/php-ecommerce-project-main`
2. Ndizni Apache + MySQL
3. Importoni `database/ecommerce_db.sql` në phpMyAdmin
4. Kopjoni `config/database.local.example.php` → `database.local.php` (kredencialet MySQL)
5. Ekzekutoni një herë: `database/setup.php` (hash fjalëkalimesh)
6. Hapni: `http://localhost/php-ecommerce-project-main/`

### Migrime shtesë (DB ekzistuese)

Ekzekutoni në phpMyAdmin sipas nevojës:

- `database/add_password_reset.sql`
- `database/add_returns_and_google.sql`
- `database/add_contact_replies.sql`

### Email (SMTP)

`config/smtp.local.example.php` → `smtp.local.php` — shihni `docs/SMTP_XAMPP.md`

### Google Login

`config/google.local.example.php` → `google.local.php` — shihni `docs/GOOGLE_LOGIN.md`

## Llogari test

| Roli | Email | Fjalëkalimi |
|------|-------|-------------|
| Admin | admin@shop.com | admin123 |
| Klient | user@shop.com | user123 |

## Rruga kryesore

| Faqe | Përshkrim |
|------|-----------|
| `/` | Kryefaqja |
| `/login.php` | Hyrje |
| `/register.php` | Regjistrim |
| `/pages/cart.php` | Shporta |
| `/pages/contact.php` | Kontakt (vetëm klientë) |
| `/pages/dashboard.php` | Panel admin |
| `/pages/admin/messages.php` | Përgjigje mesazheve me email |

Dokumentim teknik: `DOKUMENTIMI_FAZA2.md`
