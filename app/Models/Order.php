<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $casts = [
        'bill_generated_at' => 'datetime',
    ];

    protected $fillable = [
        'restaurant_id',
        'branch_id',
        'chef_id',
        'created_by',
        'customer_id',
        'table_id',
        'table_no',
        'customer_name',
        'kitchen_status',
        'mobile_number',
        'token_no',
        'order_type',
        'status',
        'subtotal',
        'tax',
        'total',
        'restaurant_id',
        'branch_id',
        'payment_method',
        'birth_date',
        'anniversary_date', 'email', 'payment_status', 'bill_no', 'bill_generated_at','order_datetime'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function chef()
    {
        return $this->belongsTo(
            User::class,
            'chef_id'
        );
    }

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_no', 'table_number');
    }
}
