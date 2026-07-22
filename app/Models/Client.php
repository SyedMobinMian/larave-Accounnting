<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    // Sirf inhi fields ko mass-assign hone ki permission milegi
    protected $fillable = [
        'company',
        'vat_number',
        'phone',
        'website',
        'address',
        'city',
        'state',
        'zip',
        'country',
    ];
}