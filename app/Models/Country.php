<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $casts = [
        'status' => 'boolean',
    ];
    protected $fillable = [
        'name',
        'iso_code',
        'currency_code',
        'currency_symbol',
        'timezone', 'status',
    ];
}
