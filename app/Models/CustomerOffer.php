<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerOffer extends Model
{
    //
    protected $fillable = [
        'title',
        'description',
        'status',
        'category', 'restaurant_id',
        'branch_id',
    ];
}
