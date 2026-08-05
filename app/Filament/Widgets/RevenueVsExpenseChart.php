<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Invoice;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RevenueVsExpenseChart extends ChartWidget
{
    protected static ?string $heading = 'Revenue vs Expenses (Last 6 Months)';

    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = null;

    protected function getData(): array
    {
        // Cache chart data for 10 minutes to avoid repeated aggregate queries on every Livewire request
        return cache()->remember('dashboard.revenue_vs_expense', now()->addMinutes(10), function () {
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

            // Single grouped aggregate query for revenue (12 queries → 1)
            $revenueRows = Invoice::query()
                ->select(DB::raw("YEAR($invoiceDateColumn) as yr, MONTH($invoiceDateColumn) as mo, SUM(total_amount) as total"))
                ->where($invoiceDateColumn, '>=', $months->first()->startOfMonth())
                ->groupBy('yr', 'mo')
                ->get()
                ->keyBy(fn ($row) => $row->yr . '-' . $row->mo);

            // Single grouped aggregate query for expenses (12 queries → 1)
            $expenseRows = Expense::query()
                ->select(DB::raw("YEAR($expenseDateColumn) as yr, MONTH($expenseDateColumn) as mo, SUM(amount) as total"))
                ->where($expenseDateColumn, '>=', $months->first()->startOfMonth())
                ->groupBy('yr', 'mo')
                ->get()
                ->keyBy(fn ($row) => $row->yr . '-' . $row->mo);

            $revenueData = [];
            $expenseData = [];
            $labels = [];

            foreach ($months as $month) {
                $labels[] = $month->format('M Y');
                $key = $month->year . '-' . $month->month;
                $revenueData[] = (float) ($revenueRows[$key]->total ?? 0);
                $expenseData[] = (float) ($expenseRows[$key]->total ?? 0);
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
        });
    }

    protected function getType(): string
    {
        return 'line';
    }
}

