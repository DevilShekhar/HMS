<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model
{
    protected $table = 'tables';
    protected $fillable = [
        'cat_id',
        'restaurant_id',
        'branch_id',
        'table_number',
        'capacity',
        'status',
        'created_by',
        'updated_by'
    ];

    public function category()
    {
        return $this->belongsTo(TableCategory::class,'cat_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
