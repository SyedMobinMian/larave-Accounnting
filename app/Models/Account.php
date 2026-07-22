<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = []; // <-- Yeh add karein
    public function journalItems()
    {
        return $this->hasMany(JournalItem::class);
    }
}