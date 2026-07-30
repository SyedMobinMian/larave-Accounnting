<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceiptNote extends Model
{
    protected $guarded = [];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items()
    {
        return $this->hasMany(GoodsReceiptNoteItem::class);
    }
}