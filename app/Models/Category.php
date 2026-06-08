<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'restaurant_id',
        'branch_id',
        'created_by',
        'name',
        'description',
        'image',
        'is_active',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}