<?php

namespace App\Observers;

use App\Models\Account;
use App\Models\InvoicePayment;
use App\Models\JournalEntry;
use App\Models\JournalItem;

class InvoicePaymentObserver
{
    public function created(InvoicePayment $payment): void
    {
        $invoice = $payment->invoice;

        // 1. Create Journal Entry
        $entry = JournalEntry::create([
            'date' => $payment->payment_date,
            'reference_no' => 'PAY-' . $payment->id . ' (Inv #' . $invoice->invoice_number . ')',
            'description' => 'Payment received for Invoice ' . $invoice->invoice_number,
        ]);

        // Debit: Cash or Bank Account
        JournalItem::create([
            'journal_entry_id' => $entry->id,
            'account_id' => $payment->account_id,
            'debit' => $payment->amount,
            'credit' => 0,
        ]);

        // Credit: Accounts Receivable (1200)
        $arAccount = Account::where('code', '1200')->first();

        if ($arAccount) {
            JournalItem::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $arAccount->id,
                'debit' => 0,
                'credit' => $payment->amount,
            ]);
        }

        // 2. Update Invoice Status
        $totalPaid = $invoice->payments()->sum('amount');

        if ($totalPaid >= $invoice->total_amount) {
            $invoice->update(['status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 'partially_paid']);
        }
    }
}
