<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class FinancialOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        // Cache dashboard aggregates for 5 minutes
        $stats = Cache::remember('dashboard.financial_overview', now()->addMinutes(5), function () {
            $totalRevenue = (float) Invoice::sum('total_amount');
            $unpaidInvoices = (float) Invoice::whereIn('status', ['unpaid', 'partially_paid'])->sum('total_amount');
            $totalExpenses = (float) Expense::sum('amount');
            $invoiceCount = Invoice::count();
            $clientCount = Client::count();
            $productCount = Product::count();
            $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'min_stock_alert')->count();

            return [
                'totalRevenue' => $totalRevenue,
                'unpaidInvoices' => $unpaidInvoices,
                'totalExpenses' => $totalExpenses,
                'netProfit' => $totalRevenue - $totalExpenses,
                'invoiceCount' => $invoiceCount,
                'clientCount' => $clientCount,
                'productCount' => $productCount,
                'lowStockCount' => $lowStockCount,
            ];
        });

        return [
            Stat::make(__('Total Revenue'), '₹' . number_format($stats['totalRevenue'], 2))
                ->description(__('Total Invoiced Amount') . ' (' . $stats['invoiceCount'] . ' ' . __('invoices') . ')')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 10, 5, 15, 10, 20]),

            Stat::make(__('Outstanding'), '₹' . number_format($stats['unpaidInvoices'], 2))
                ->description(__('Pending Payments from Clients'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('Total Expenses'), '₹' . number_format($stats['totalExpenses'], 2))
                ->description(__('Total Outgoing Payments'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make(__('Net Profit'), '₹' . number_format($stats['netProfit'], 2))
                ->description($stats['netProfit'] >= 0 ? __('Profitable') : __('Loss'))
                ->descriptionIcon($stats['netProfit'] >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($stats['netProfit'] >= 0 ? 'success' : 'danger'),

            Stat::make(__('Clients'), (string) $stats['clientCount'])
                ->description(__('Registered Clients'))
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),

            Stat::make(__('Low Stock Products'), (string) $stats['lowStockCount'])
                ->description(__('out of') . ' ' . $stats['productCount'] . ' ' . __('products'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($stats['lowStockCount'] > 0 ? 'danger' : 'success'),
        ];
    }
}

