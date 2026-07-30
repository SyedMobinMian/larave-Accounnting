<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Invoice;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class RevenueVsExpenseChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue vs Expenses (Last 6 Months)';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $months = collect(range(5, 0))->map(function ($i) {
            return Carbon::now()->subMonths($i);
        });

        // Auto-detect date column in Invoices table
        $invoiceDateColumn = Schema::hasColumn('invoices', 'date') 
            ? 'date' 
            : (Schema::hasColumn('invoices', 'issue_date') ? 'issue_date' : 'created_at');

        // Auto-detect date column in Expenses table
        $expenseDateColumn = Schema::hasColumn('expenses', 'expense_date') 
            ? 'expense_date' 
            : (Schema::hasColumn('expenses', 'date') ? 'date' : 'created_at');

        $revenueData = [];
        $expenseData = [];
        $labels = [];

        foreach ($months as $month) {
            $labels[] = $month->format('M Y');

            $revenueData[] = Invoice::whereYear($invoiceDateColumn, $month->year)
                ->whereMonth($invoiceDateColumn, $month->month)
                ->sum('total_amount');

            $expenseData[] = Expense::whereYear($expenseDateColumn, $month->year)
                ->whereMonth($expenseDateColumn, $month->month)
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => __('Revenue'),
                    'data' => $revenueData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => __('Expenses'),
                    'data' => $expenseData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
