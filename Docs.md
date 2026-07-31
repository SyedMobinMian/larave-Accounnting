# Task: Refactor Project Navigation & Implement Enterprise Settings Architecture

## Objective

Refactor the application navigation and Settings module into a scalable, enterprise-grade architecture.

This is an architectural refactoring task.

The goal is NOT to change business logic.
The goal is to reorganize the UI, navigation, namespaces and module structure while preserving existing functionality.

------------------------------------------------------------
PHASE 1
Main Sidebar Refactoring
------------------------------------------------------------

Rebuild the application sidebar using the following hierarchy.

Each menu item must have an appropriate Filament Heroicon.

Dashboard

Sales & CRM
    Clients
    Contacts
    Leads
    Estimates
    Invoices

Procurement
    Vendors
    Purchase Orders
    Bills

Inventory
    Products
    Categories
    Warehouses
    Stock

Banking
    Bank Accounts
    Transactions

Financials
    Chart of Accounts
    Journal Entries
    Expenses

Reports

Settings

Requirements

• Rename navigation groups where necessary.
• Move resources into their correct navigation groups.
• Remove duplicated navigation items.
• Preserve all existing permissions.
• Preserve all routes.
• Preserve all URLs if possible.
• Update namespaces where required.
• Update Filament navigation registration.
• Update clusters if required.
• Update menu ordering.
• Keep the sidebar clean and consistent.

Do NOT remove any existing functionality.

------------------------------------------------------------
PHASE 2
Project Audit
------------------------------------------------------------

After restructuring the sidebar:

• Scan the project for broken namespaces.
• Scan for broken imports.
• Scan for broken routes.
• Scan for incorrect navigation groups.
• Scan for duplicate Resources.
• Scan for duplicate Pages.
• Scan for dead code related to old navigation.
• Fix every issue found.

The application must compile without errors.

------------------------------------------------------------
PHASE 3
Settings Architecture
------------------------------------------------------------

The existing Settings implementation must be completely redesigned.

Settings should become a dedicated Configuration Center.

Clicking Settings should NOT simply open a single settings page.

Instead it should open a dedicated Settings workspace.

The Settings workspace must contain:

Level 1
Application Sidebar

↓

Level 2
Settings Sidebar

↓

Level 3
Top Navigation Tabs

↓

Content Area

This architecture must be modular and easily extendable.

------------------------------------------------------------
Settings Sidebar
------------------------------------------------------------

Settings

Core
Business
Appearance
Administration
Platform

------------------------------------------------------------
Core
------------------------------------------------------------

General

Company

Localization

------------------------------------------------------------
Business
------------------------------------------------------------

Finance

Sales

Procurement

Inventory

------------------------------------------------------------
Appearance
------------------------------------------------------------

Appearance & Branding

------------------------------------------------------------
Administration
------------------------------------------------------------

Access Management

Notifications

Security

------------------------------------------------------------
Platform
------------------------------------------------------------

Integrations

AI

System

------------------------------------------------------------
Every module should support nested tabs exactly as defined in the architecture document.

The hierarchy must match the supplied specification.

Do not remove or rename items unless absolutely necessary.

------------------------------------------------------------
Future Requirements
------------------------------------------------------------

The architecture must support:

• unlimited future modules

• plugin architecture

• multi-tenancy

• localization

• RBAC

• SaaS

• enterprise scalability

No hardcoded assumptions.

Everything should be modular.

------------------------------------------------------------
Implementation Rules
------------------------------------------------------------

Use Laravel 13 best practices.

Use Filament v4 best practices.

Use SOLID principles.

Use clean architecture.

Keep Controllers thin.

Business logic belongs in Services.

Resources should remain focused.

Avoid duplicate code.

Keep components reusable.

Follow PSR standards.

------------------------------------------------------------
IMPORTANT
------------------------------------------------------------

Do NOT implement everything in one huge commit.

Work in phases.

After completing each phase:

1. verify the application compiles

2. verify navigation

3. verify permissions

4. verify no broken imports

Only then continue to the next phase.

If a requirement conflicts with the existing implementation,
explain the conflict before changing it.