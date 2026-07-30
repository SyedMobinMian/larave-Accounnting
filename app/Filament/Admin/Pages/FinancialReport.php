<?php

namespace App\Filament\Admin\Pages; // <-- Admin sub-folder attach karein


use Filament\Pages\Page;
use App\Models\Account;
use App\Models\JournalItem;

class FinancialReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Financials';

    protected static string $view = 'filament.pages.financial-report';

    public function getViewData(): array
{
    // Fetch accounts with pre-calculated totals (N+1 Query fixed)
    $accounts = Account::query()
        ->withSum('journalItems as total_debit', 'debit')
        ->withSum('journalItems as total_credit', 'credit')
        ->get();

    $trialBalance = $accounts->map(function ($account) {
        $debit = (float) ($account->total_debit ?? 0);
        $credit = (float) ($account->total_credit ?? 0);

        return [
            'code' => $account->code,
            'name' => $account->name,
            'type' => ucfirst($account->type),
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $debit - $credit,
        ];
    });

    // Query builder through explicit query() call fixes IDE type errors
    $totalRevenue = (float) JournalItem::query()
        ->whereHas('account', fn ($q) => $q->where('type', 'revenue'))
        ->sum('credit');

    $totalExpense = (float) JournalItem::query()
        ->whereHas('account', fn ($q) => $q->where('type', 'expense'))
        ->sum('debit');

    $netProfit = $totalRevenue - $totalExpense;

    return [
        'trialBalance' => $trialBalance,
        'totalRevenue' => $totalRevenue,
        'totalExpense' => $totalExpense,
        'netProfit' => $netProfit,
    ];
}
}