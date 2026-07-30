<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $guarded = [];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Relationship to payment transactions
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Total amount paid across all successful payments
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->where('status', 'success')->sum('amount');
    }

    // Remaining balance left to pay
    public function getRemainingBalanceAttribute(): float
    {
        return max(0, (float) $this->total_amount - $this->totalPaid);
    }

    // Auto-update status based on payments
    public function updatePaymentStatus(): void
    {
        $totalPaid = $this->totalPaid;

        if ($totalPaid >= $this->total_amount) {
            $this->update(['status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $this->update(['status' => 'partially_paid']);
        } else {
            $this->update(['status' => 'unpaid']);
        }
    }
}
