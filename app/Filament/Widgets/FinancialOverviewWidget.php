<?php

namespace App\Filament\Widgets; // (ya App\Filament\Admin\Widgets)

use App\Models\Expense;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinancialOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Change 'total' to 'total_amount'
        $totalRevenue = Invoice::sum('total_amount');
        
        // Status checks with 'total_amount'
        $unpaidInvoices = Invoice::whereIn('status', ['unpaid', 'sent', 'draft'])->sum('total_amount');
        
        $totalExpenses = Expense::sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;

        return [
            Stat::make(__('Total Revenue'), '₹' . number_format($totalRevenue, 2))
                ->description(__('Total Invoiced Amount'))
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            
            Stat::make(__('Unpaid Invoices'), '₹' . number_format($unpaidInvoices, 2))
                ->description(__('Pending Payments'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make(__('Total Expenses'), '₹' . number_format($totalExpenses, 2))
                ->description(__('Total Outgoing'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),

            Stat::make(__('Net Profit'), '₹' . number_format($netProfit, 2))
                ->description(__('Revenue minus Expenses'))
                ->color($netProfit >= 0 ? 'success' : 'danger'),
        ];
    }
}