<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'bank_name',
        'account_number',
        'currency',
        'opening_balance',
        'is_active',
    ];

    protected function accountNumber(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Crypt::decryptString($value),
            set: fn ($value) => Crypt::encryptString($value),
        );
    }
}