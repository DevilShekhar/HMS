<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
            'manager'
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

        return view(
            'admin.branches.create',
            compact(
                'restaurants',
                'owners'
            )
        );
    }

    /**
     * Store Branch
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id'     => 'required|exists:restaurants,id',
            'owner_id'          => 'required|exists:users,id',
            'name'              => 'required|max:255',
            'code'              => 'nullable|max:50',
            'phone'             => 'nullable|max:20',
            'email'             => 'nullable|email',
            'address'           => 'nullable',
            'city'              => 'nullable|max:100',
            'state'             => 'nullable|max:100',
            'country'           => 'nullable|max:100',
            'postal_code'       => 'nullable|max:20',
            'latitude'          => 'nullable',
            'longitude'         => 'nullable',
            'gst_number'        => 'nullable|max:100',
            'fssai_license'     => 'nullable|max:100',
            'opening_time'      => 'nullable',
            'closing_time'      => 'nullable',
            'branch_manager_id' => 'nullable|exists:users,id',
        ]);

        $validated['is_active'] = 1;
        $validated['slug'] = Str::slug($validated['name']);
        Branch::create($validated);

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
            'manager'
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

        return view(
            'admin.branches.edit',
            compact(
                'branch',
                'restaurants',
                'owners',
                'managers'
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
            'restaurant_id'     => 'required|exists:restaurants,id',
            'owner_id'          => 'required|exists:users,id',
            'branch_manager_id' => 'nullable|exists:users,id',
            'name'              => 'required|max:255',
            'code'              => 'nullable|max:50',
            'phone'             => 'nullable|max:20',
            'email'             => 'nullable|email',
            'address'           => 'nullable',
            'city'              => 'nullable|max:100',
            'state'             => 'nullable|max:100',
            'country'           => 'nullable|max:100',
            'postal_code'       => 'nullable|max:20',
            'latitude'          => 'nullable',
            'longitude'         => 'nullable',
            'gst_number'        => 'nullable|max:100',
            'fssai_license'     => 'nullable|max:100',
            'opening_time'      => 'nullable',
            'closing_time'      => 'nullable',
            'is_active'         => 'required'
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $branch->update($validated);

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
            'is_active' => 0
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
                'restaurant' => $request->route('restaurant')
            ])
            ->with(
                'success',
                'Branch manager assigned successfully.'
            );
    }
}
