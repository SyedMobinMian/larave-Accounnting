# Perfex CRM Migration Project (Laravel)

## 1. Environment & Technical Stack
- **Framework:** Laravel 11/13
- **Admin Panel Framework:** Filament PHP v3
- **Local Environment:** Laragon
- **PHP Version:** 8.3.30
- **Database:** MySQL 8.4.3
- **Web Server:** Apache 2.4.66

---

## 2. Installed Packages & Dependencies

### Core UI & Admin Panel
- `filament/filament:"^3.2"`: Admin Panel Dashboard, Authentication, Forms, aur Dynamic Tables ke liye.

### Accounting & Security Packages
- `spatie/laravel-permission`: Staff, Admin, Accountant ke Role-based Access Control (RBAC) ke liye.
- `spatie/laravel-activitylog`: Audit Logging aur Activity Tracking ke liye.
- `spatie/laravel-medialibrary`: Invoices, Bills aur Receipts ke File Uploads/Attachments ke liye.
- `barryvdh/laravel-dompdf`: Invoices, Quotes, aur Financial Reports ki PDF Generation ke liye.
- `maatwebsite/excel`: Accounting Reports ko Excel/CSV Format mein Export/Import karne ke liye.
- `brick/money`: Financial Decimal Precision aur Currency Calculations ke liye.

---

## 3. Setup & Troubleshooting Steps Completed

1. **Environment Setup:** Laragon MySQL 8.4 server configure karke `perfex_crm_db` database link kiya.
2. **File-Locking Fix:** Windows Defender aur Composer archive extraction issue ko resolve karne ke liye `--prefer-dist` / `--prefer-source` flags ke saath dependencies install kiye.
3. **Panel Provider Registration:** `bootstrap/providers.php` file mein `AdminPanelProvider::class` ko manually verify aur link kiya.
4. **Admin Setup:** `php artisan filament:install --panels` se panel publish kiya aur `php artisan make:filament-user` se Superadmin user create kiya.

---

## 4. Useful Terminal Commands

```bash
# Local Development Server Run karne ke liye
php artisan serve

# Cache Clear karne ke liye
php artisan optimize:clear

# Naya Filament User Banane ke liye
php artisan make:filament-user

# Database Migrations Run karne ke liye
php artisan migrate



## Phase 2 Completed
- Created `Client` and `Contact` Models & Database Migrations.
- Executed `php artisan migrate` for Client and Contact schemas.
- Generated Filament CRUD Resources (`ClientResource`, `ContactResource`) for UI management.



## Phase 3 Completed
- Created `Invoice` and `InvoiceItem` Models & Migrations.
- Executed migrations for Invoices database structure.
- Generated Filament CRUD Resource (`InvoiceResource`) for Managing Invoices.



## Phase 4 Completed
- Created `Account`, `JournalEntry`, and `JournalItem` Models & Migrations for Double-Entry Bookkeeping.
- Executed database migrations for Chart of Accounts (COA) and Ledger structure.
- Generated Filament CRUD Resources (`AccountResource`, `JournalEntryResource`).



Sandbox@chancellor MINGW64 /c/laragon/www/perfex-laravel
$ php artisan shield:generate --all

  Which panel do you want to generate permissions/policies for?
  admin .......................................................................................... 0  
❯ 



- **Fix:** Assigned `super_admin` role to initial admin user via Tinker to resolve panel policy hiding resources.



## Completed Features
- **Authentication & RBAC:** Filament Shield integrated with Super Admin permissions.
- **Client & Contact Management:** CRUD with relationships and mass assignment protection.
- **Invoicing System:** Dynamic repeater line-items with real-time `qty * rate` and auto-totalling (`subtotal`, `total_tax`, `total`).
- **Accounting Engine:** Automated `InvoiceObserver` posting Debit/Credit entries on Creation and Payment.
- **PDF Generation:** Installed DomPDF for printable/downloadable invoice views.



