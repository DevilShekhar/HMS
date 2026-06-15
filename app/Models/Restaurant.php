<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'name',
        'slug'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
