# Login me Google

## 1. Google Cloud Console

1. Hapni https://console.cloud.google.com/
2. Krijoni projekt (ose përdorni ekzistues)
3. **APIs & Services** → **Credentials** → **Create Credentials** → **OAuth client ID**
4. Lloji: **Web application**
5. **Authorized redirect URIs** (saktësisht):

```
http://localhost/php-ecommerce-project-main/auth/google-callback.php
```

(Ndryshoni nëse dosja juaj ka emër tjetër në `htdocs`.)

6. Kopjoni **Client ID** dhe **Client Secret**

## 2. Konfigurimi në projekt

```text
config/google.local.example.php  →  config/google.local.php
```

Vendosni `GOOGLE_CLIENT_ID` dhe `GOOGLE_CLIENT_SECRET`.

## 3. Databaza

Ekzekutoni `database/add_returns_and_google.sql` (shton kolonën `google_id` te `users`).

## 4. Test

1. Hapni `login.php`
2. Klikoni **Hyni me Google**
3. Përdorues i ri → email **Mirësevini në PHP Shop!**
4. Admin e sheh te Menaxho Përdoruesit

## Shënim

OAuth kërkon **internet**. SMTP veçmas për email mirëseardhjeje (`docs/SMTP_XAMPP.md`).
