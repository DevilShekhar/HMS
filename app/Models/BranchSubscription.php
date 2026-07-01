<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchSubscription extends Model
{
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    protected $fillable = [
        'branch_id',
        'subscription_plan_id',
        'billing_cycle',
        'amount',
        'start_date',
        'end_date',
        'status',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }
}
