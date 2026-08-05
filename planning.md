# i18n Internationalization Plan for Laravel Accounting

## 1. Information Gathered

### Current State:
- **Language files exist**: `lang/en.json`, `lang/hi.json`, `lang/de.json`, `lang/es.json`
- **Filament Language Switch plugin** is already configured in `AppServiceProvider`
- **Many resources already use `__()` helper** for labels (good progress)
- **Code has mixed i18n usage**: Some files use `__('String')`, others have raw strings

### Files with Hardcoded User-Facing Strings (Needing i18n):

#### A. Blade Views (5 files):
1. `resources/views/landing.blade.php` - Full landing page with hardcoded text
2. `resources/views/filament/pages/settings-workspace.blade.php` - "Settings", "Save Settings", group labels
3. `resources/views/filament/pages/financial-report.blade.php` - "Total Revenue", "Total Expenses", "Net Profit / (Loss)", "Trial Balance Overview", table headers, "No transaction entries found"
4. `resources/views/pdf/document.blade.php` - PDF template with "Billed To:", "Issue Date:", "Due/Expiry Date:", "Description", "Qty", "Unit Price", "Amount", "Subtotal:", "Tax Total:", "Total:", "Notes:", "Terms & Conditions:", footer text
5. `resources/views/pdf/invoice.blade.php` - Legacy invoice PDF

#### B. PHP Resource Files with Hardcoded Labels (that don't use `__()`):
1. `app/Filament/Admin/Resources/InvoiceResource.php` - Status options ('Unpaid', 'Paid', 'Cancelled')
2. `app/Filament/Admin/Resources/EstimateResource.php` - Status options in form ('Draft', 'Sent', 'Accepted', 'Declined')
3. `app/Filament/Admin/Resources/ClientResource.php` - `$navigationGroup`, `$navigationLabel`, `$modelLabel`, `$pluralModelLabel`
4. `app/Filament/Admin/Resources/AccountResource.php` - `$navigationGroup`
5. `app/Filament/Admin/Resources/ContactResource.php` - labels
6. `app/Filament/Admin/Resources/UserResource.php` - "User Account Details", "Associated Client Company...", "Client Account", "Internal Staff / Admin"
7. `app/Filament/Admin/Resources/VendorResource.php` - Uses `__()` via getNavigationGroup()
8. `app/Filament/Admin/Resources/BankAccountResource.php` - Uses `__()` via getNavigationGroup()
9. `app/Filament/Admin/Resources/BillResource.php` - Status options not using `__()`
10. `app/Filament/Admin/Resources/PurchaseOrderResource.php` - Status options
11. `app/Filament/Admin/Resources/ProductResource.php` - navigation labels
12. `app/Filament/Admin/Resources/LeadResource.php` - labels
13. `app/Filament/Admin/Resources/WarehouseResource.php` - labels
14. `app/Filament/Admin/Resources/StockMovementResource.php` - labels
15. `app/Filament/Admin/Resources/CategoryResource.php` - labels
16. `app/Filament/Admin/Resources/UnitResource.php` - labels

#### C. PHP Pages with Hardcoded Strings:
1. `app/Filament/Admin/Pages/Settings/SettingsWorkspace.php` - Categories, tabs, sections, labels, helper texts, notification messages - MASSIVE file
2. `app/Filament/Admin/Pages/ManageSettings.php` - Section titles, descriptions, labels, notification
3. `app/Filament/Admin/Pages/ManageSystemSettings.php` - Labels, descriptions, options
4. `app/Filament/Pages/ManageSystemSettings.php` - Labels, options
5. `app/Filament/Admin/Pages/FinancialReport.php` - `$navigationGroup`
6. `app/Filament/Admin/Resources/InvoiceResource/RelationManagers/ItemsRelationManager.php` - "Product" label

#### D. PHP Widgets:
1. `app/Filament/Widgets/FinancialOverviewWidget.php` - Most already use `__()` ✓
2. `app/Filament/Widgets/RevenueVsExpenseChart.php` - `$heading` hardcoded

#### E. Exports:
1. `app/Exports/ProductsExport.php` - `headings()` has hardcoded column names
2. `app/Exports/InvoicesExport.php` - `headings()` has hardcoded column names
3. `app/Exports/ClientsExport.php` - `headings()` has hardcoded column names
4. `app/Exports/ExpensesExport.php` - (needs checking)
5. `app/Exports/VendorsExport.php` - (needs checking)

#### F. Imports:
1. `app/Imports/ClientsImport.php` - Column mapping literals
2. `app/Imports/ProductsImport.php` - Column mapping literals

#### G. Settings Classes:
1. `app/Settings/CompanySettings.php` - (needs checking)
2. `app/Settings/EmailSettings.php` - (needs checking)
3. `app/Settings/InventorySettings.php` - (needs checking)
4. `app/Settings/PaymentSettings.php` - (needs checking)
5. `app/Settings/SecuritySettings.php` - (needs checking)

#### H. AdminPanelProvider:
1. `app/Providers/Filament/AdminPanelProvider.php` - Navigation group labels use `__()` ✓

## 2. Plan

### Phase 1: Add missing strings to language files
- Collect all hardcoded strings and add them to `lang/en.json`
- Ensure translations match for hi, de, es

### Phase 2: Fix Blade Views
- Update all blade views to use `__()` or `@lang()` / `{{ __('...') }}`

### Phase 3: Fix PHP Resources/Pages/Widgets
- Convert all hardcoded labels, titles, descriptions, helper texts, options to use `__()`

### Phase 4: Fix Exports/Imports
- Convert export headings to translatable strings

### Phase 5: Fix Settings Classes & PDF templates
- Ensure all user-facing strings are in language files

## 3. Dependent Files to Edit
1. `lang/en.json` - Add missing translations
2. `lang/hi.json` - Add missing translations  
3. `lang/de.json` - Add missing translations
4. `lang/es.json` - Add missing translations
5. Each Blade view file
6. Each PHP Resource/Page/Widget file with hardcoded strings

## 4. Followup Steps
- Verify the application loads without errors
- Test language switching works
- Confirm all user-facing text is translatable

