<div align="center">

# 📊 Enterprise Accounting & Business ERP

**A Modern, Scalable, and Domain-Driven Enterprise Resource Planning & Financial Management System.**

[![Laravel](https://img.shields.io/badge/Laravel-v11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament PHP](https://img.shields.io/badge/Filament_PHP-v3.x-FDAE4B?style=for-the-badge&logo=laravel&logoColor=black)](https://filamentphp.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![PHP](https://img.shields.io/badge/PHP-v8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.style=for-the-badge)]()

</div>

---

## 👨‍💻 Developer Profile

| Attribute | Details |
| :--- | :--- |
| **Lead Architect & Developer** | **Syed Mobin Mian** |
| **Primary Focus** | Full-Stack Enterprise Systems, Financial Software & Cloud Solutions |
| **GitHub** | [@SyedMobinMian](https://github.com/SyedMobinMian) |

### 🛠️ Developer Tech Stack
* **Backend Framework:** Laravel 11.x (PHP 8.2+)
* **Admin & UI Framework:** Filament PHP v3 (TALL Stack)
* **Frontend & Interactivity:** Livewire, Alpine.js, Tailwind CSS
* **Database & ORM:** MySQL, Eloquent ORM (Optimized Eager Loading)
* **Security & Access Control:** Role-Based Access Control (RBAC via Filament Shield)
* **Document Generation:** Custom PDF Controllers with Dynamic Barcode & QR Integration

---

## 🚀 Project Overview

**Enterprise Accounting & Business ERP** is designed to streamline critical business operations, financial record-keeping, procurement workflows, and client relationships. Built using a **Domain-Driven Architecture**, the platform provides strict separation of concerns, high rendering speeds, and granular access controls suitable for international enterprise deployment.

---

## ✨ Key Features & Capabilities Achieved

### 💼 1. CRM & Sales Management
* **Client & Contact Management:** Centralized client databases with dedicated contact details.
* **Invoicing Engine:** Create, send, and track multi-item invoices with real-time tax and subtotal calculations.
* **Estimates & Quotations:** Flexible estimate generation convertable to active billing workflows.
* **Payment Recording:** Direct payment logging against invoices with automated status updates (`Unpaid`, `Partially Paid`, `Paid`).

### 📦 2. Procurement & Inventory Control
* **Product & Stock Tracking:** Itemized catalog with SKU tracking, dynamic measurement units, cost/selling price thresholds.
* **Low-Stock Alert Badges:** Visual indicators and custom filters for restock alerts based on minimum threshold values.
* **Dynamic Measurement Unit Master:** Custom unit definitions (Pcs, Kg, Meters, Boxes) with on-the-fly modal creation.
* **Automated Stock Deduction:** Real-time stock outwarding upon sales invoice settlement and inwarding on purchase orders.
* **Purchase Order Management:** Vendor procurement tracking linked directly to inventory updates.

### 🏦 3. Financials & Accounting Master
* **Double-Entry Bookkeeping:** Chart of accounts and journal entries for audit-ready accounting.
* **Bank & Cash Accounts Master:** Multi-bank account management with default routing for automated invoice printing.
* **Expense Tracking:** Categorized expense logging linked with asset accounts and vendor management.
* **Financial Reports:** Interactive dashboards showing revenue vs. expenses, balance tracking, and cashflow metrics.

### 💳 4. Dynamic UPI Barcode & PDF Payment Integration
* **Automated QR Code Generator:** Client invoices render a dynamic UPI payment QR code (GPay, PhonePe, Paytm) pre-filled with the exact payable invoice amount.
* **Professional PDF Generation:** Instant download/print support for clean, audit-ready invoices.

### 🔐 5. Security & Granular Access Control (RBAC)
* **Role-Based Permissions:** Granular CRUD policies (`View Any`, `View`, `Create`, `Update`, `Delete`) for Admins, Accountants, and Sales Staff.
* **Isolated Multi-Panel Design:** Architectural separation between Administrative Operations (`/admin`) and Customer Self-Service Portal (`/portal`).

---

## 🏗️ System Architecture & Grouping

```text
├── CRM & Sales
│   ├── Client Management
│   ├── Contact Directory
│   ├── Estimates & Quotations
│   └── Invoices & Sales Billing
│
├── Procurement & Inventory
│   ├── Products & Stock Management
│   ├── Purchase Orders
│   ├── Measurement Units Master
│   └── Vendor Catalog
│
├── Financials & Accounting
│   ├── Chart of Accounts
│   ├── Bank & Cash Accounts
│   ├── Journal Entries
│   └── Expense Logging
│
└── System Administration
    ├── Roles & Granular Permissions
    ├── Security Policies
    └── Financial Reports
