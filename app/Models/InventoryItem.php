<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'restaurant_id',
        'branch_id',
        'name',
        'unit',
        'total_stock',
        'remaining_stock',
        'minimum_stock',
        'is_active',
        'created_by',
        'updated_by'
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

    public function updater()
    {
        return $this->belongsTo(User::class,'updated_by');
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class,'inventory_item_id');
    }
}