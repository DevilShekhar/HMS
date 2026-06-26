<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'description',
        'monthly_price',
        'quarterly_price',
        'half_yearly_price',
        'yearly_price',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(BranchSubscription::class);
    }
}
