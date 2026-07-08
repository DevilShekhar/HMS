<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\BranchSubscription;
use App\Models\Country;
use App\Models\Restaurant;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BranchController extends Controller
{
    /**
     * Display Branches
     */
    public function index()
    {
        $user = Auth::user();

        $query = Branch::with([
            'restaurant',
            'owner',
            'manager','country'
        ]);

        if ($user->role === 'owner') {

            $query->where('owner_id', $user->id);

            // Only branch managers created under this owner
            $managers = User::query()->where('restaurant_id', $user->restaurant_id)
                ->where('role', 'branch_manager')
                ->where('created_by', $user->id)
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        } elseif ($user->role === 'super_admin') {

            $managers = User::query()->where('role', 'branch_manager')
                ->where('status', 'active')
                ->orderBy('name')
                ->get();
        } else {

            abort(403);
        }

        $branches = $query
            ->latest()
            ->paginate(20);

        return view(
            'admin.branches.index',
            compact(
                'branches',
                'managers'
            )
        );
    }

    /**
     * Create Form
     */
    public function create()
    {
        $restaurants = Restaurant::query()->where('status', 1)
            ->orderBy('name')
            ->get();

        $owners = User::query()->where('role', 'owner')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        $plans = SubscriptionPlan::query()->get();

        $existingCombinations = Branch::select('restaurant_id', 'owner_id')->get();
        $countries = Country::query()->where('status', 1)->get();

        return view(
            'admin.branches.create',
            compact(
                'restaurants',
                'owners', 'plans', 'existingCombinations', 'countries'
            )
        );
    }

    /**
     * Store Branch
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'owner_id' => 'required|exists:users,id',
            'name' => 'required|max:255',
            'code' => 'required|max:50',
            'phone' => 'required|max:20',
            'email' => 'required|email',
            'address' => 'required',
            'country_id' => 'required|exists:countries,id',
            'city' => 'required|max:100',
            'state' => 'required|max:100',
            'postal_code' => 'nullable|max:20',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'gst_number' => 'required|max:100',
            'fssai_license' => 'required|max:100',
            'opening_time' => 'required',
            'closing_time' => 'required',
            'branch_manager_id' => 'nullable|exists:users,id',
            'subscription_plan_id' => 'required|exists:subscription_plans,id',
            'billing_cycle' => 'required|in:monthly,quarterly,half_yearly,yearly',
            'gst_enabled' => 'nullable|boolean',
            'gst' => 'nullable|numeric|min:0|max:100',
        ]);

        // $exists = Branch::query()->where('restaurant_id', $validated['restaurant_id'])
        //     ->where('owner_id', $validated['owner_id'])
        //     ->exists();

        // if ($exists) {
        //     return back()
        //         ->withErrors([
        //             'owner_id' => 'This owner is already assigned to the selected restaurant.',
        //         ])
        //         ->withInput();
        // }

        $validated['is_active'] = 1;
        $validated['slug'] = Str::slug($validated['name']);
        $validated['gst_enabled'] = $request->boolean('gst_enabled');

        if ($validated['gst_enabled']) {

            $validated['gst'] = (float) $request->gst;
            $validated['cgst'] = $validated['gst'] / 2;
            $validated['sgst'] = $validated['gst'] / 2;

        } else {

            $validated['gst'] = 0;
            $validated['cgst'] = 0;
            $validated['sgst'] = 0;

        }
        $branch = Branch::create($validated);

        $registrationUrl = config('app.url').'/'.
        $branch->restaurant->slug.'/'.
        $branch->slug.'/register';

        $qrFolder = public_path('uploads/registration-qrcodes');

        if (! file_exists($qrFolder)) {
            mkdir($qrFolder, 0755, true);
        }

        $fileName = 'branch_'.$branch->id.'_registration.svg';
        $filePath = $qrFolder.'/'.$fileName;

        QrCode::format('svg')
            ->size(400)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($registrationUrl, $filePath);

        // Force refresh in case of caching
        clearstatcache();

        $branch->update([
            'registration_qrcode' => 'uploads/registration-qrcodes/'.$fileName,
        ]);
        $plan = SubscriptionPlan::findOrFail(
            $request->subscription_plan_id
        );

        $amount = match ($request->billing_cycle) {

            'monthly' => $plan->monthly_price,

            'quarterly' => $plan->quarterly_price,

            'half_yearly' => $plan->half_yearly_price,

            'yearly' => $plan->yearly_price,
        };

        $startDate = now();

        $endDate = match ($request->billing_cycle) {

            'monthly' => now()->addMonth(),

            'quarterly' => now()->addMonths(3),

            'half_yearly' => now()->addMonths(6),

            'yearly' => now()->addYear(),
        };

        BranchSubscription::create([
            'branch_id' => $branch->id,
            'subscription_plan_id' => $plan->id,
            'billing_cycle' => $request->billing_cycle,
            'amount' => $amount,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active',
        ]);

        return redirect()
            ->route('branches.index')
            ->with(
                'success',
                'Branch created successfully.'
            );
    }

    /**
     * Show Branch
     */
    public function show(Request $request)
    {
        $branchId = $request->route('branch');

        $branch = Branch::with([
            'restaurant',
            'owner',
            'manager',
            'activeSubscription.plan',
        ])->findOrFail($branchId);

        return view('admin.branches.show', compact('branch'));
    }

    /**
     * Edit Form
     */
    public function edit($id)
    {
        $branch = Branch::findOrFail($id);

        $restaurants = Restaurant::query()->where('status', 1)
            ->orderBy('name')
            ->get();

        $owners = User::query()->where('role', 'owner')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $managers = User::query()->where('role', 'branch_manager')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        $plans = SubscriptionPlan::query()->where('status', 1)->get();

        $currentSubscription = $branch->activeSubscription;
        $countries = Country::query()->where('status', 1)->get();

        return view(
            'admin.branches.edit',
            compact(
                'branch',
                'restaurants',
                'owners',
                'managers', 'plans', 'currentSubscription','countries'
            )
        );
    }

    /**
     * Update Branch
     */
    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'owner_id' => 'required|exists:users,id',
            'branch_manager_id' => 'nullable|exists:users,id',
            'name' => 'required|max:255',
            'code' => 'nullable|max:50',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable',
            'country_id' => 'required|exists:countries,id',
            'city' => 'nullable|max:100',
            'state' => 'nullable|max:100',
            'postal_code' => 'nullable|max:20',
            'latitude' => 'nullable',
            'longitude' => 'nullable',
            'gst_number' => 'nullable|max:100',
            'fssai_license' => 'nullable|max:100',
            'opening_time' => 'nullable',
            'closing_time' => 'nullable',
            'is_active' => 'nullable',
            'subscription_plan_id' => 'nullable|exists:subscription_plans,id',
            'billing_cycle' => 'nullable|in:monthly,quarterly,half_yearly,yearly',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $branch->update($validated);

        $registrationUrl = config('app.url').'/'.
            $branch->restaurant->slug.'/'.
            $branch->slug.'/register';

        $qrFolder = public_path('uploads/registration-qrcodes');

        if (! file_exists($qrFolder)) {
            mkdir($qrFolder, 0755, true);
        }

        $fileName = 'branch_'.$branch->id.'_registration.svg';
        $filePath = $qrFolder.'/'.$fileName;

        QrCode::format('svg')
            ->size(400)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($registrationUrl, $filePath);

        // Force refresh in case of caching
        clearstatcache();

        $branch->update([
            'registration_qrcode' => 'uploads/registration-qrcodes/'.$fileName,
        ]);
        $currentSubscription = $branch->activeSubscription;

        if (! $currentSubscription) {

            // First subscription for this branch

            $plan = SubscriptionPlan::findOrFail(
                $request->subscription_plan_id
            );

            $amount = match ($request->billing_cycle) {
                'monthly' => $plan->monthly_price,
                'quarterly' => $plan->quarterly_price,
                'half_yearly' => $plan->half_yearly_price,
                'yearly' => $plan->yearly_price,
            };

            $endDate = match ($request->billing_cycle) {
                'monthly' => now()->addMonth(),
                'quarterly' => now()->addMonths(3),
                'half_yearly' => now()->addMonths(6),
                'yearly' => now()->addYear(),
            };

            BranchSubscription::create([
                'branch_id' => $branch->id,
                'subscription_plan_id' => $plan->id,
                'billing_cycle' => $request->billing_cycle,
                'amount' => $amount,
                'start_date' => now(),
                'end_date' => $endDate,
                'status' => 'active',
            ]);

        } elseif (
            $currentSubscription->subscription_plan_id != $request->subscription_plan_id ||
            $currentSubscription->billing_cycle != $request->billing_cycle
        ) {

            // Plan changed

            $currentSubscription->update([
                'status' => 'cancelled',
            ]);

            $plan = SubscriptionPlan::findOrFail(
                $request->subscription_plan_id
            );

            $amount = match ($request->billing_cycle) {
                'monthly' => $plan->monthly_price,
                'quarterly' => $plan->quarterly_price,
                'half_yearly' => $plan->half_yearly_price,
                'yearly' => $plan->yearly_price,
            };

            $endDate = match ($request->billing_cycle) {
                'monthly' => now()->addMonth(),
                'quarterly' => now()->addMonths(3),
                'half_yearly' => now()->addMonths(6),
                'yearly' => now()->addYear(),
            };

            BranchSubscription::create([
                'branch_id' => $branch->id,
                'subscription_plan_id' => $plan->id,
                'billing_cycle' => $request->billing_cycle,
                'amount' => $amount,
                'start_date' => now(),
                'end_date' => $endDate,
                'status' => 'active',
            ]);
        }

        return redirect()
            ->route('branches.index')
            ->with(
                'success',
                'Branch updated successfully.'
            );
    }

    /**
     * Delete Branch
     */
    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);

        $branch->update([
            'is_active' => 0,
        ]);

        return redirect()
            ->route('branches.index')
            ->with(
                'success',
                'Branch deactivated successfully.'
            );
    }

    /**
     * AJAX Owners By Restaurant
     */
    public function getOwners($restaurantId)
    {
        $owners = User::query()->where('restaurant_id', $restaurantId)
            ->where('role', 'owner')
            ->where('status', 'active')
            ->select('id', 'name')
            ->get();

        return response()->json($owners);
    }

    /**
     * AJAX Managers By Owner
     */
    public function getManagers($ownerId)
    {
        $owner = User::findOrFail($ownerId);

        $managers = User::query()->where(
            'restaurant_id',
            $owner->restaurant_id
        )
            ->where('role', 'branch_manager')
            ->where('status', 'active')
            ->select('id', 'name')
            ->get();

        return response()->json($managers);
    }

    public function assignManager(Request $request)
    {
        $branch = Branch::findOrFail($request->route('branch'));
        $branch->branch_manager_id = $request->branch_manager_id;
        $branch->save();

        return redirect()
            ->route('restaurant.branches.index', [
                'restaurant' => $request->route('restaurant'),
            ])
            ->with(
                'success',
                'Branch manager assigned successfully.'
            );
    }

    public function uploadQrCode(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'qrcode' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $branch = Branch::findOrFail($request->branch_id);

        if ($request->hasFile('qrcode')) {

            $file = $request->file('qrcode');

            $filename = time().'_'.$file->getClientOriginalName();

            $file->move(
                public_path('uploads/qrcodes'),
                $filename
            );

            $branch->update([
                'qrcode' => 'uploads/qrcodes/'.$filename,
            ]);
        }

        return back()->with('success', 'QR uploaded successfully.');
    }

    public function regenerateQr($id)
    {
        $branch = Branch::with('restaurant')->findOrFail($id);

        $url = route('branch.register', [
            'restaurant' => $branch->restaurant->slug,
            'branch' => $branch->slug,
        ]);

        $fileName = 'branch_'.$branch->id.'_registration.svg';

        $path = public_path(
            'uploads/registration-qrcodes/'.$fileName
        );

        if (! file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        QrCode::format('svg')
            ->size(400)
            ->generate(
                $url,
                $path
            );

        $branch->update([
            'registration_qrcode' => 'uploads/registration-qrcodes/'.$fileName,
        ]);

        return 'QR regenerated: '.$url;
    }

    public function updateGst(Request $request, Restaurant $restaurant, Branch $branch)
    {
        // Better permission check
        $user = Auth::user();

        if ($user->role === 'super_admin') {
        } elseif ($user->role === 'owner' && $user->restaurant_id === $restaurant->id) {
        } elseif ($user->role === 'branch_manager' && $user->branch_id === $branch->id) {
        } else {
            abort(403, 'You do not have permission to update GST details.');
        }

        $validated = $request->validate([
            'gst_enabled' => 'boolean|nullable',
            'gst_number' => 'nullable|string|max:255',
            'gst' => 'nullable|numeric|min:0|max:100',
        ]);

        // If GST is enabled, GST % is required
        if ($request->boolean('gst_enabled') && empty($validated['gst'])) {
            return back()->withErrors(['gst' => 'GST percentage is required when GST is enabled.']);
        }

        $branch->update([
            'gst_enabled' => $request->boolean('gst_enabled'),
            'gst_number' => $validated['gst_number'] ?? null,
            'gst' => $validated['gst'] ?? null,
            'cgst' => $request->boolean('gst_enabled') && $validated['gst']
                            ? round($validated['gst'] / 2, 2)
                            : null,
            'sgst' => $request->boolean('gst_enabled') && $validated['gst']
                            ? round($validated['gst'] / 2, 2)
                            : null,
        ]);

        return redirect()
            ->route('restaurant.branches.index', [
                'restaurant' => $restaurant->slug,
            ])
            ->with('success', 'GST details updated successfully for branch: '.$branch->name);
    }
}
