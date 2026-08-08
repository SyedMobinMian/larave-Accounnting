<?php

namespace App\Exports;

use App\Models\Account;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Bill;
use App\Models\Category;
use App\Models\Client;
use App\Models\Contact;
use App\Models\Estimate;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\Lead;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\Vendor;
use App\Models\Warehouse;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Full company backup export. Produces a single XLSX workbook with one
 * sheet per module, so an admin can back up (or migrate) the entire
 * company / organisation data in one file.
 */
class CompanyBackupExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new InvoicesExport(),
            new ClientsExport(),
            new ProductsExport(),
            new ExpensesExport(),
            new VendorsExport(),
            new EstimatesExport(),
            new BillsExport(),
            new LeadsExport(),
            new PurchaseOrdersExport(),
            new StockMovementsExport(),
            new UnitsExport(),
            new WarehousesExport(),
            new BankAccountsExport(),
            new BankTransactionsExport(),
            new JournalEntriesExport(),
            new AccountsExport(),
            new ContactsExport(),
            new CategoriesExport(),
        ];
    }

    /**
     * Optional: expose a list of the sheets that will be included so the
     * UI can display a summary before downloading.
     */
    public static function sheetTitles(): array
    {
        return [
            'Invoices',
            'Clients',
            'Products',
            'Expenses',
            'Vendors',
            'Estimates',
            'Bills',
            'Leads',
            'Purchase Orders',
            'Stock Movements',
            'Units',
            'Warehouses',
            'Bank Accounts',
            'Bank Transactions',
            'Journal Entries',
            'Accounts',
            'Contacts',
            'Categories',
        ];
    }
}
