<?php

namespace App\Observers;

use App\Models\Expense;
use App\Models\JournalEntry;
use App\Models\JournalItem;

class ExpenseObserver
{
    public function created(Expense $expense): void
    {
        $entry = JournalEntry::create([
            'date' => $expense->expense_date,
            'reference_no' => 'EXP-' . $expense->id, // Fixed: 'reference' -> 'reference_no'
            'description' => 'Expense: ' . ($expense->description ?? 'General Expense'),
        ]);

        // Debit: Expense Account
        JournalItem::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $expense->expense_account_id,
            'debit' => $expense->amount,
            'credit' => 0,
        ]);

        // Credit: Payment Account (Bank/Cash)
        JournalItem::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $expense->payment_account_id,
            'debit' => 0,
            'credit' => $expense->amount,
        ]);
    }
}