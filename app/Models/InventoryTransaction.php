<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InventoryTransaction extends Model
{
    protected $fillable = [
        'restaurant_id',
        'branch_id',
        'inventory_item_id',
        'type',
        'quantity',
        'remarks',
        'created_by',
    ];
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class,'created_by');
    }
}