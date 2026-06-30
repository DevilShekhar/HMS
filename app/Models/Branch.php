<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'restaurant_id',
        'owner_id',
        'branch_manager_id',
        'name',
        'code',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'gst_number',
        'fssai_license',
        'qrcode',
        'opening_time',
        'closing_time',
        'is_active',
        'slug','registration_qrcode'
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'branch_manager_id');
    }

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function subscriptions()
    {
        return $this->hasMany(BranchSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(BranchSubscription::class)
            ->where('status', 'active');
            // ->latestOfMany();
    }
}
