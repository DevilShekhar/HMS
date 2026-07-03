<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class SubscriptionPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        $plans = SubscriptionPlan::all();
        return view('admin.subscription_plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.subscription_plans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'monthly_price' => 'required|numeric|min:0',
            'quarterly_price' => 'required|numeric|min:0',
            'half_yearly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
        ]);

        $data = $request->all();
        $data['status'] = 1;


        SubscriptionPlan::create($data);

        return redirect()->route('subscription-plans.index')
            ->with('success', 'Plan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SubscriptionPlan $subscriptionPlan)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
     public function edit(SubscriptionPlan $subscriptionPlan)
    {
        return view('admin.subscription_plans.edit', compact('subscriptionPlan'));
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'monthly_price' => 'required|numeric|min:0',
            'quarterly_price' => 'nullable|numeric|min:0',
            'half_yearly_price' => 'nullable|numeric|min:0',
            'yearly_price' => 'nullable|numeric|min:0',
        ]);

        $data = $request->all();
        $data['status'] = $request->has('status');

        $subscriptionPlan->update($data);

        return redirect()->route('subscription-plans.index')
            ->with('success', 'Plan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan = SubscriptionPlan::findOrFail($subscriptionPlan);

        $subscriptionPlan->update([
            'status' => 0
        ]);

        return redirect()
            ->route('subscription-plans.index')
            ->with('success', 'SubscriptionPlan deactivated successfully');
    }
}
