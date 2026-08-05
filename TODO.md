# i18n Task TODO List

## Phase 1: Language files
- [ ] Add all missing keys to `lang/en.json`
- [ ] Add all missing keys to `lang/hi.json`
- [ ] Add all missing keys to `lang/de.json`
- [ ] Add all missing keys to `lang/es.json`

## Phase 2: PHP Resources/Pages/Widgets
- [ ] `InvoiceResource.php` — translate status options
- [ ] `UserResource.php` — translate sections/labels/helperText
- [ ] `Pages/Settings/SettingsWorkspace.php` — translate all labels/sections/notifications
- [ ] `Admin/Pages/ManageSettings.php` — translate all
- [ ] `Admin/Pages/ManageSystemSettings.php` — translate all
- [ ] `Pages/ManageSystemSettings.php` (root) — translate all
- [ ] `Admin/Pages/FinancialReport.php` — translate navigationGroup
- [ ] `Widgets/RevenueVsExpenseChart.php` — translate heading
- [ ] `Client/Resources/ClientInvoiceResource.php` — translate labels/section/notifications
- [ ] `Providers/Filament/AdminPanelProvider.php` — translate brandName

## Phase 3: Blade Views
- [ ] `settings-workspace.blade.php` — translate "Settings", "Save Settings", group names
- [ ] `manage-settings.blade.php` — translate "Save Preferences"
- [ ] `financial-report.blade.php` — translate all labels
- [ ] `landing.blade.php` — translate entire page
- [ ] `welcome.blade.php` — translate title/nav/description
- [ ] `pdf/document.blade.php` — translate all PDF labels
- [ ] `pdf/invoice.blade.php` — translate all PDF labels

## Final
- [ ] Run `php artisan view:clear` and verify app loads
