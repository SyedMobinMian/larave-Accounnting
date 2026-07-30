<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue = Invoice::sum('total_amount');
        $unpaidInvoices = Invoice::whereIn('status', ['unpaid', 'partially_paid'])->sum('total_amount');
        $totalExpenses = Expense::sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;
        $invoiceCount = Invoice::count();
        $clientCount = Client::count();
        $productCount = Product::count();
        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'min_stock_alert')->count();

        return [
            Stat::make(__('Total Revenue'), '₹' . number_format($totalRevenue, 2))
                ->description(__('Total Invoiced Amount') . ' (' . $invoiceCount . ' ' . __('invoices') . ')')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 10, 5, 15, 10, 20]),

            Stat::make(__('Outstanding'), '₹' . number_format($unpaidInvoices, 2))
                ->description(__('Pending Payments from Clients'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('Total Expenses'), '₹' . number_format($totalExpenses, 2))
                ->description(__('Total Outgoing Payments'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make(__('Net Profit'), '₹' . number_format($netProfit, 2))
                ->description($netProfit >= 0 ? __('Profitable') : __('Loss'))
                ->descriptionIcon($netProfit >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($netProfit >= 0 ? 'success' : 'danger'),

            Stat::make(__('Clients'), (string) $clientCount)
                ->description(__('Registered Clients'))
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),

            Stat::make(__('Low Stock Products'), (string) $lowStockCount)
                ->description(__('out of') . ' ' . $productCount . ' ' . __('products'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'danger' : 'success'),
        ];
    }
}
