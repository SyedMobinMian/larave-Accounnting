<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Add performance indexes across frequently queried columns.
     * Fully idempotent: skips indexes that already exist.
     */
    public function up(): void
    {
        // Invoices - status filters, date sorting, client lookup
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasIndex('invoices', ['status'])) {
                $table->index(['status']);
            }
            if (!Schema::hasIndex('invoices', ['issue_date'])) {
                $table->index(['issue_date']);
            }
            if (!Schema::hasIndex('invoices', ['client_id'])) {
                $table->index(['client_id']);
            }
        });

        // Invoice items - parent lookup for relation manager
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasIndex('invoice_items', ['invoice_id'])) {
                $table->index(['invoice_id']);
            }
        });

        // Expenses - date sorting, vendor, payment method
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasIndex('expenses', ['expense_date'])) {
                $table->index(['expense_date']);
            }
            if (!Schema::hasIndex('expenses', ['vendor_id'])) {
                $table->index(['vendor_id']);
            }
            if (!Schema::hasIndex('expenses', ['payment_method'])) {
                $table->index(['payment_method']);
            }
        });

        // Products - stock alerts, category, warehouse
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'category_id') && !Schema::hasIndex('products', ['category_id'])) {
                $table->index(['category_id']);
            }
            if (Schema::hasColumn('products', 'warehouse_id') && !Schema::hasIndex('products', ['warehouse_id'])) {
                $table->index(['warehouse_id']);
            }
            if (Schema::hasColumn('products', 'stock_quantity') && !Schema::hasIndex('products', ['stock_quantity'])) {
                $table->index(['stock_quantity']);
            }
        });

        // Journal entries / items - account aggregation for reports
        Schema::table('journal_items', function (Blueprint $table) {
            if (!Schema::hasIndex('journal_items', ['journal_entry_id'])) {
                $table->index(['journal_entry_id']);
            }
            if (!Schema::hasIndex('journal_items', ['account_id'])) {
                $table->index(['account_id']);
            }
        });

        // Leads - status/source filters
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasIndex('leads', ['status'])) {
                $table->index(['status']);
            }
            if (!Schema::hasIndex('leads', ['source'])) {
                $table->index(['source']);
            }
        });

        // Bills - status filters
        Schema::table('bills', function (Blueprint $table) {
            if (!Schema::hasIndex('bills', ['status'])) {
                $table->index(['status']);
            }
            if (!Schema::hasIndex('bills', ['vendor_id'])) {
                $table->index(['vendor_id']);
            }
        });

        // Bank transactions - account lookup + date sorting
        Schema::table('bank_transactions', function (Blueprint $table) {
            if (!Schema::hasIndex('bank_transactions', ['bank_account_id'])) {
                $table->index(['bank_account_id']);
            }
            if (!Schema::hasIndex('bank_transactions', ['transaction_date'])) {
                $table->index(['transaction_date']);
            }
        });

        // Stock movements - product/warehouse lookups
        Schema::table('stock_movements', function (Blueprint $table) {
            if (!Schema::hasIndex('stock_movements', ['product_id'])) {
                $table->index(['product_id']);
            }
            if (!Schema::hasIndex('stock_movements', ['warehouse_id'])) {
                $table->index(['warehouse_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasIndex('invoices', ['status'])) {
                $table->dropIndex(['status']);
            }
            if (Schema::hasIndex('invoices', ['issue_date'])) {
                $table->dropIndex(['issue_date']);
            }
            if (Schema::hasIndex('invoices', ['client_id'])) {
                $table->dropIndex(['client_id']);
            }
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            if (Schema::hasIndex('invoice_items', ['invoice_id'])) {
                $table->dropIndex(['invoice_id']);
            }
        });

        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasIndex('expenses', ['expense_date'])) {
                $table->dropIndex(['expense_date']);
            }
            if (Schema::hasIndex('expenses', ['vendor_id'])) {
                $table->dropIndex(['vendor_id']);
            }
            if (Schema::hasIndex('expenses', ['payment_method'])) {
                $table->dropIndex(['payment_method']);
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasIndex('products', ['category_id'])) {
                $table->dropIndex(['category_id']);
            }
            if (Schema::hasIndex('products', ['warehouse_id'])) {
                $table->dropIndex(['warehouse_id']);
            }
            if (Schema::hasIndex('products', ['stock_quantity'])) {
                $table->dropIndex(['stock_quantity']);
            }
        });

        Schema::table('journal_items', function (Blueprint $table) {
            if (Schema::hasIndex('journal_items', ['journal_entry_id'])) {
                $table->dropIndex(['journal_entry_id']);
            }
            if (Schema::hasIndex('journal_items', ['account_id'])) {
                $table->dropIndex(['account_id']);
            }
        });

        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasIndex('leads', ['status'])) {
                $table->dropIndex(['status']);
            }
            if (Schema::hasIndex('leads', ['source'])) {
                $table->dropIndex(['source']);
            }
        });

        Schema::table('bills', function (Blueprint $table) {
            if (Schema::hasIndex('bills', ['status'])) {
                $table->dropIndex(['status']);
            }
            if (Schema::hasIndex('bills', ['vendor_id'])) {
                $table->dropIndex(['vendor_id']);
            }
        });

        Schema::table('bank_transactions', function (Blueprint $table) {
            if (Schema::hasIndex('bank_transactions', ['bank_account_id'])) {
                $table->dropIndex(['bank_account_id']);
            }
            if (Schema::hasIndex('bank_transactions', ['transaction_date'])) {
                $table->dropIndex(['transaction_date']);
            }
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            if (Schema::hasIndex('stock_movements', ['product_id'])) {
                $table->dropIndex(['product_id']);
            }
            if (Schema::hasIndex('stock_movements', ['warehouse_id'])) {
                $table->dropIndex(['warehouse_id']);
            }
        });
    }
};

