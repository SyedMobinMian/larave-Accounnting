# Project Vision & Planning Notes
We are working on perfex accounting but in laravel 13.
This document outlines the requirements, feedback, and strategic direction for the Enterprise Accounting & Business project.

---

## 1. Core Feature Requirements

- **Full Accounting Suite**: Sales, Purchase, Product Management, Client Management (CRM), and comprehensive Financial Reports.
- **Technical Upgrade**: The entire project must be upgraded to and compatible with **Laravel 13**.

---

## 2. Immediate Issues & Feedback

### 2.1. Super Admin Login Failure
- **Issue**: Unable to log in as Super Admin.
- **Credentials**:
  - **Email**: `super@gmail.com`
  - **User**: Ali
  - **Password**: sink
  - **Role**: super admin
- **Resolution**: 


### 2.2. Frontend/UI Feedback
- **Landing Page**: A beautiful, professional accounting-themed landing page is required.
- **Authentication**: The landing page must feature clear "Login" and "Register" options for users.

### 2.3. Project Structure
- **Critique**: The project's file structure is messy. Files are being created in incorrect locations (e.g., in the root directory).
- **Action**: All non-essential root files should be moved to appropriate directories (e.g., a `docs` folder) to clean up the project.

---

## 3. Development Strategy

### 3.1. Sequential Model Development
Start development with the most critical models first and proceed in a logical sequence. A suggested order:
1.  **Core Accounting**: `Account` (Chart of Accounts), `JournalEntry`, `JournalItem`.
2.  **Core Entities**: `Client`, `Vendor`, `Product`.
3.  **Transactions**: `Invoice`, `InvoiceItem`, `Payment`, `PurchaseOrder`, `Expense`.

### 3.2. Comprehensive Settings Module
A centralized "Settings" area should be created in the sidebar, inspired by Perfex, Odoo, and ERPNext. This will be the control panel for the entire application.

**Proposed Settings Structure:**

#### 1. General Settings
- Company Identity (Name, Logo, Address, etc.)
- Localization (Currency, Timezone, Date Format)
- System Defaults (Theme, Language, Landing Page)

#### 2. Company & Tenancy (for SaaS)
- Company Management (Create, Suspend, Delete)
- Subscription & Plan Limits (Users, Storage)
- Ownership Transfer

#### 3. Users & Security
- **Staff Management**: Invite, Disable, Reset Password, Login As.
- **Roles & Permissions (RBAC)**: Granular control over every action (View, Create, Edit, Delete, Approve, Export) for each module.
- **Security Policies**: 2FA, Password Policy, Session Timeout, IP Whitelisting.

#### 4. Finance & Accounting
- **Core Engine**: Fiscal Year, Chart of Accounts (COA), Opening/Closing Balances.
- **Transaction Mapping**: Define default accounts for sales, purchases, etc.
- **Tax Engine**: Multi-level taxes (inclusive/exclusive, compound).
- **Multi-Currency**: Manage exchange rates.
- **Fixed Assets**: Depreciation methods and schedules.

#### 5. Sales & Receivables
- **Invoice Settings**: Prefix, Auto-Numbering, PDF Templates, Terms.
- **Estimate/Quotation Settings**: Prefix, Expiry, PDF Layout.
- **Payment Gateways**: Stripe, PayPal, Razorpay, Bank Transfer (with keys management).
- **Customer Portal**: Settings for client access.

#### 6. Procurement & Payables
- **Purchase Settings**: PO prefixes, approval workflows.
- **Vendor Management**: Default terms, payment methods.

#### 7. Inventory & Stock
- **Product Settings**: SKU/Barcode generation, negative stock allowance, costing method (FIFO, Average).
- **Warehouse Management**: Racks, Bins, Transfer/Receiving rules.
- **Alerts**: Low stock notification thresholds.

#### 8. HR & Payroll
- Departments & Designations
- Leave & Attendance Rules
- Payroll Structure & Salary Components

#### 9. System & Operations
- **Email Settings**: SMTP configuration, email templates.
- **Notifications**: Control channels (Email, SMS, Push).
- **Automation & Cron Jobs**: Recurring invoices, payment reminders, auto-backups.
- **Backup & Restore**: Manual/Automatic backups to cloud storage (S3, Google Drive).
- **API & Integrations**: API key management, webhooks, rate limiting.
- **Module Manager**: Enable/disable core application modules.
- **System Health**: Logs, Cache, PHP Info, Storage usage.

---

## 4. Feature Deep-Dive (Based on Perfex Analysis)

The following is a detailed breakdown of features to be implemented within the Settings module, categorized for clarity.

### General Settings
- Company Name, Logo, Favicon, Address, Phone, Email, Website
- Registration Number (GST/VAT)
- Default Currency, Timezone, Date/Time Format, Language
- Maintenance Mode

### Staff & User Settings
- Create/Invite/Disable/Delete Staff
- Reset Password, Force Password Change, Login as User
- Session Management, Device Login History

### Roles & Permissions (RBAC)
- Create custom roles (e.g., Manager, Sales, Accountant).
- Assign granular permissions for every module (e.g., `invoice.view`, `invoice.create`, `invoice.approve`).

### Finance & Accounting Settings
- Fiscal Year, Chart of Accounts, Opening/Closing Balance
- Default Tax, Journal Settings, Ledger Lock
- Exchange Rates, Multi-Currency support
- Account Mapping (e.g., map sales to a specific revenue account)

### Invoice & Estimate Settings
- Prefix, Auto-Numbering, Starting Number
- Default Notes, Footer, Terms
- PDF Template, Watermark, Signature

### Payment Settings
- Online Gateways (Stripe, PayPal, etc.) with Sandbox/Production keys
- Offline Methods (Bank Transfer)
- Refund Options

### Tax Settings
- Tax Name, Rate, Type (Inclusive/Exclusive, Compound)

### Banking Settings
- Manage Bank & Cash Accounts
- Statement Import & Reconciliation tools

### Inventory Settings
- SKU/Barcode/QR Code settings
- Batch/Serial Number tracking
- Expiry Date management
- Negative Stock allowance, Low Stock Alerts
- Costing Method (FIFO, Average Cost)

### Purchase Settings
- Purchase Request/Order workflows
- Vendor & Goods Receive Approval Levels

### CRM Settings
- Lead Sources, Statuses
- Customer Groups
- Sales Pipeline & Deal Stages

### HR Settings
- Departments, Designations
- Attendance & Leave Types
- Payroll & Salary Structure

### Email & Notification Settings
- SMTP Configuration (Gmail, Microsoft, etc.)
- Email Queue & Templates
- Notification channels (Email, SMS, Push, Slack)

### Security & Backup
- 2FA, Password Policy, Session Timeout, IP Whitelisting
- Audit Logs
- Automated/Manual Backups (S3, Dropbox, etc.)

### System & API
- API Key Management (REST, OAuth)
- Cron Job Management
- Module Manager
- System Health Dashboard