# PHP E-Commerce pa Databazë

Ky është një projekt mini **E-Commerce** i ndërtuar me **PHP**, pa përdorur databazë. Projekti është krijuar për të demonstruar koncepte bazë dhe të avancuara të PHP-së, si **OOP**, **Sessions**, **Cookies**, **Validim me Regex**, **Role-Based Access Control** dhe organizim të kodit në folderë të ndarë.

---

## Veçoritë Kryesore

- **Arkitekturë pa Databazë**  
  Të gjitha të dhënat ruhen në array përmes funksioneve ndihmëse në `functions/helpers.php`.

- **Programim i Orientuar në Objekte - OOP**  
  Projekti përdor klasa për:
  - `Product`
  - `User`
  - `Admin`

- **Inheritance**  
  Klasa `Admin` trashëgon nga klasa bazë `User`.

- **Autentikim dhe Role**
  - Login me email dhe fjalëkalim.
  - Validim i email-it me Regex.
  - Përdoruesit ndahen në dy role:
    - `admin`
    - `customer`

- **Role-Based Access Control**
  - Admin ka qasje në `Dashboard`.
  - User i zakonshëm mund të shikojë produktet dhe detajet e tyre.

- **Menaxhim i Sesioneve**
  - Përdoret `$_SESSION` për të mbajtur përdoruesin të loguar.
  - Ruhet ID, emri, email-i dhe roli i përdoruesit.

- **Menaxhim i Cookies**
  - Ruajtja e preferencës për `Dark Mode` ose `Light Mode`.
  - Ruajtja e produktit të fundit të vizituar.
  - Ruajtja e kohës së logimit të fundit.

- **Renditja e Produkteve**
  - Produktet mund të renditen sipas çmimit:
    - Nga më i liri te më i shtrenjti.
    - Nga më i shtrenjti te më i liri.

- **Dizajn Modern dhe Responsive**
  - Projekti përdor Vanilla CSS.
  - Përdoren CSS Variables.
  - Mbështetet Dark Mode.
  - Dizajni përshtatet edhe për pajisje mobile.

---

## Llogaritë për Testim

| Roli | Email | Fjalëkalimi |
|------|-------|-------------|
| Admin | `admin@shop.com` | `admin123` |
| User | `user@shop.com` | `user123` |

---

## Struktura e Projektit

```text
/ecommerce
│
├── index.php                 # Entry point i projektit
├── login.php                 # Faqja e logimit
├── logout.php                # Funksioni për dalje nga llogaria
├── README.md                 # Dokumentacioni i projektit
│
├── pages/
│   ├── home.php              # Faqja kryesore me produkte të spikatura
│   ├── products.php          # Lista e të gjitha produkteve me renditje
│   ├── product_details.php   # Detajet e një produkti specifik
│   └── dashboard.php         # Paneli i adminit
│
├── includes/
│   ├── auth.php              # Menaxhimi i sesioneve dhe roleve
│   ├── header.php            # Pjesa fillestare e HTML dhe tema
│   ├── footer.php            # Pjesa përmbyllëse e HTML
│   └── navbar.php            # Navigimi dinamik
│
├── classes/
│   ├── Product.php           # Klasa e produktit
│   ├── User.php              # Klasa bazë e përdoruesit
│   └── Admin.php             # Klasa Admin që trashëgon nga User
│
├── functions/
│   ├── helpers.php           # Dummy data dhe funksione ndihmëse
│   └── validators.php        # Funksione për validim me Regex
│
└── assets/
    ├── css/
    │   └── style.css         # Stilizimi i projektit
    │
    └── images/               # Folder për imazhe lokale, nëse përdoren

    ---

## Si ta ekzekutoni në XAMPP

1. Ndizni modulin **Apache** në Control Panel të XAMPP.
2. Kopjoni dosjen e plotë `ecommerce` brenda dosjes `htdocs` të XAMPP (zakonisht `C:\xampp\htdocs\ecommerce`).
3. Hapni browser-in dhe shkoni te: `http://localhost/ecommerce`
4. Ose, mund ta përdorni me PHP Built-in Server nëse jeni direkt në dosje:
   ```bash
   cd c:\Users\ramha\OneDrive\Desktop\ecommerce
   php -S localhost:8000
   ```
   Dhe hapni `http://localhost:8000` në browser.