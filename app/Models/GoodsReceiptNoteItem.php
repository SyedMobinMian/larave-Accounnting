<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceiptNoteItem extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}