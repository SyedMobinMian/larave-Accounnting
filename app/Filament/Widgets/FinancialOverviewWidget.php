<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue = Invoice::sum('total');
        
        // Status 'unpaid' ya 'sent' ya 'pending'
        $unpaidInvoices = Invoice::whereIn('status', ['unpaid', 'sent', 'draft'])->sum('total');
        
        $totalExpenses = Expense::sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;

        return [
            Stat::make(__('Total Revenue'), '₹' . number_format($totalRevenue, 2))
                ->description(__('Total Invoiced Amount'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make(__('Unpaid Receivables'), '₹' . number_format($unpaidInvoices, 2))
                ->description(__('Pending Client Payments'))
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color('warning'),

            Stat::make(__('Total Expenses'), '₹' . number_format($totalExpenses, 2))
                ->description(__('Total Business Spending'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make(__('Net Profit / (Loss)'), '₹' . number_format($netProfit, 2))
                ->description($netProfit >= 0 ? __('Profitable') : __('Loss'))
                ->color($netProfit >= 0 ? 'success' : 'danger'),
        ];
    }
}