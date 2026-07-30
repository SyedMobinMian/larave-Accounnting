I want a centralized ERP Settings Workspace, similar to how modern ERP and accounting systems organize their configuration.
Instead of showing every configuration item in the main application sidebar, the sidebar should contain only business modules (Accounting, Sales, Purchases, Inventory, CRM, Reports, etc.).
All configuration and administration pages should be accessible from a single Settings workspace.

Navigation Behaviour-
Clicking Settings should open a dedicated Settings workspace (not a single form).
The workspace should have:
A left navigation panel containing setting categories.
A top horizontal tab navigation for the selected category.
A main content area displaying forms, tables, or wizards.


Settings

├── General
├── Company
├── Users
├── Roles
├── Permissions
├── Localization
├── Accounting
├── Inventory
├── Procurement
├── Security
├── Email
├── Payment
├── AI
└── Customization
When the user clicks Inventory, the top tabs should become:
Warehouses | Unit of Measure | Categories | Brands | Barcode | Stock Rules

When the user clicks Localization:
Currency | Language | Country | Timezone | Tax Rules | Date Format

The main application sidebar must remain clean.
Configuration entities such as Users, Roles, Currency, Languages, Units of Measure, Company, Themes, Layouts, Taxes, Payment Gateways, Email Settings, etc. should not appear individually in the main sidebar.
They should all be organized inside the centralized Settings workspace.
Use the navigation philosophy commonly found in enterprise ERP systems, where all administrative configuration is centralized into a dedicated Settings workspace with category navigation on the left and contextual tabs across the top.
The experience should feel like a professional ERP Control Center rather than a collection of unrelated settings pages.