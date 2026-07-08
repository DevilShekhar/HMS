<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'restaurant_id',
        'owner_id',
        'branch_id',
        'category_id',
        'created_by',
        'name',
        'description',
        'price',
        'food_type',
        'image',
        'is_available',
        'is_active',
        'menu_items_datetime',
        'menu_items_timezone'
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
