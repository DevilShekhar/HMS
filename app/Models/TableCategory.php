<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TableCategory extends Model
{
    protected $fillable = [
        'restaurant_id',
        'branch_id',
        'name',
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
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
