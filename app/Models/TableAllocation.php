<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TableAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'branch_id',
        'table_id',
        'waiter_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'allocation_date' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id');
    }

    public function waiter()
    {
        return $this->belongsTo(User::class, 'waiter_id')
                    ->where('role', 'waiter');
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
