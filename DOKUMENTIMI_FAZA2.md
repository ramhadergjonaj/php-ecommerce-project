# Dokumentimi – Faza II (PHP E-Commerce)

## 1. Integrimi me MySQL (4 pikë)

### Tabelat dhe relacionet
| Tabela | Relacioni |
|--------|-----------|
| `users` | Përdorues / role |
| `categories` | Kategori produktesh |
| `products` | FK → `categories.id` |
| `orders` | FK → `users.id` |
| `order_items` | FK → `orders.id`, `products.id` |
| `contact_messages` | FK → `users.id` (opsional) |

### CRUD i plotë (2 entitete)
- **Produkte** – `pages/admin/products.php` + `api/products.php`
- **Kategori** – `pages/admin/categories.php`

### Prepared statements
Të gjitha query-t në `ProductRepository`, `CategoryRepository`, `UserRepository` përdorin `$stmt->prepare()` dhe parametra të lidhur.

---

## 2. Siguria (2 pikë)

| Teknika | Implementimi |
|---------|----------------|
| SQL Injection | PDO prepared statements, `ATTR_EMULATE_PREPARES => false` |
| XSS | Funksioni `e()` në `functions/security.php`, `htmlspecialchars` kudo në output |
| Validim server-side | `functions/validators.php` (email, telefon, çmim, emër produkti) |
| Fjalëkalime | `password_hash()` / `password_verify()` në `classes/User.php`, `database/setup.php` |

---

## 3. Koncepte të avancuara (3 pikë)

| Kërkesa | Skedari |
|---------|---------|
| Upload skedarësh | `handleProductImageUpload()` në `functions/helpers.php` → `assets/uploads/` |
| Error handling | `try/catch` në `config/database.php`, login, API, forma admin |
| Dërgim email | `pages/contact.php` + `sendContactEmailAndSave()` – `mail()` + ruajtje në DB |

---

## 4. AJAX & Web API (3 pikë)

| Kërkesa | Detaje |
|---------|--------|
| CRUD AJAX | Fshirja e produktit pa refresh – `assets/js/admin-products.js` → `DELETE api/products.php` |
| Web API e jashtme | [Frankfurter API](https://www.frankfurter.app/) – konvertim USD→EUR në `fetchUsdToEurRate()` |

---

## 5. Struktura e re e projektit

```
config/          – app.php, database.php
database/        – ecommerce_db.sql, setup.php
api/             – products.php (JSON)
pages/admin/     – products.php, categories.php
pages/contact.php
classes/         – Repository + Category
functions/       – security.php, helpers.php, validators.php
assets/uploads/  – imazhe të ngarkuara
```

---

## 6. Komunikim email (final)

| Rrjedha | Përshkrim |
|---------|-----------|
| Kontakt | Klienti dërgon → ruhet DB → email admin + konfirmim klienti |
| Mesazhet | Admin: `pages/admin/messages.php` → përgjigje me email |
| Përdorues | Admin: `pages/admin/email-user.php` nga lista e përdoruesve |
| Porosi / kthime | Email automatik te klienti |

Skedarët: `functions/messaging.php`, `config/shop.php`

---

## 7. Shtesë (shportë, përdorues, SMTP)

| Veçori | Skedarët |
|--------|----------|
| Shportë + checkout | `includes/cart.php`, `pages/cart.php`, `pages/checkout.php`, `actions/cart-add.php` |
| Porositë e klientit | `pages/my-orders.php`, `classes/OrderRepository.php` |
| Admin përdorues | `pages/admin/users.php` |
| Admin porosi | `pages/admin/orders.php` |
| SMTP | `config/smtp.local.php`, `functions/mailer.php`, `docs/SMTP_XAMPP.md` |

---

## 8. Dorëzimi

- Kodi final në këtë repo
- SQL dump: `database/ecommerce_db.sql`
- Ky dokument + `README.md`
