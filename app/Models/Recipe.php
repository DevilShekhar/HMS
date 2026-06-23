<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $table = 'mgnt_recipes';

    protected $fillable = [
        'restaurant_id',
        'branch_id',
        'menu_item_id',
        'inventory_id',
        'quantity_required',
        'recipe_unit',
        'remarks',
        'status',
        'created_by',
        'updated_by',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function inventory()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
