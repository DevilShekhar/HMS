<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRating extends Model
{
    protected $fillable = [
        'restaurant_id',
        'branch_id',
        'order_id',
        'rating',
        'remark',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function customer()
    {
        return $this->belongsTo(User::class,'customer_id');
    }
}
