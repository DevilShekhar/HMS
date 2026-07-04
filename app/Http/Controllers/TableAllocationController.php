<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\RestaurantTable;
use App\Models\TableAllocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TableAllocationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = TableAllocation::with(['table', 'waiter', 'branch'])
            ->where('restaurant_id', app('restaurant')->id);

        if ($user->role === 'owner') {
            $branchIds = Branch::query()->where('owner_id', $user->id)->pluck('id');
            $query->whereIn('branch_id', $branchIds);
        } elseif ($user->role === 'branch_manager' || $user->branch_id) {
            $query->where('branch_id', $user->branch_id ??
                Branch::query()->where('branch_manager_id', $user->id)->value('id'));
        } else {
            abort(403);
        }

        $allocations = $query->latest()->paginate(20);

        return view('admin.table-allocations.index', compact('allocations'));
    }

    public function create()
    {
        $user = Auth::user();
        $restaurantId = app('restaurant')->id;

        if ($user->role === 'owner') {
            $branches = Branch::query()->where('owner_id', $user->id)
                ->where('is_active', 1)
                ->get();
        } elseif ($user->branch_id) {
            $branches = Branch::query()->where('id', $user->branch_id)->get();
        } else {
            abort(403);
        }

        $tables = RestaurantTable::query()->where('restaurant_id', $restaurantId)
            ->where('status', 1)
            ->get();

        $waiters = User::query()->where('restaurant_id', $restaurantId)
            ->where('role', 'waiter')
            ->when($user->branch_id, fn ($q) => $q->where('branch_id', $user->branch_id))
            ->get();

        return view('admin.table-allocations.create', compact('branches', 'tables', 'waiters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'table_id' => 'required|exists:restaurant_tables,id',
            'waiter_id' => 'required|exists:users,id',
            'allocation_date' => 'nullable|date',
            'shift' => 'nullable|string|max:50',
        ]);

        // Security: Only allow waiters
        $waiter = User::query()->where('id', $request->waiter_id)
            ->where('role', 'waiter')
            ->firstOrFail();

        TableAllocation::create([
            'restaurant_id' => app('restaurant')->id,
            'branch_id' => $request->branch_id,
            'table_id' => $request->table_id,
            'waiter_id' => $request->waiter_id,
            'is_active' => true,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return $this->redirectBasedOnRole('Table allocation created successfully.');
    }

    public function edit($restaurant, $branch = null, $table_allocation = null)
    {
        if ($table_allocation === null) {
            $table_allocation = $branch;
            $branch = null;
        }

        $allocation = TableAllocation::findOrFail($table_allocation);

        $this->authorizeAccess($allocation);

        $branches = Branch::query()->where('owner_id', Auth::id())
            ->orWhere('id', $allocation->branch_id)
            ->get();

        $tables = RestaurantTable::query()->where('restaurant_id', app('restaurant')->id)->get();

        $waiters = User::query()->where('role', 'waiter')->get();

        return view('admin.table-allocations.edit', compact(
            'allocation',
            'branches',
            'tables',
            'waiters'
        ));
    }

    public function update(Request $request, TableAllocation $allocation)
    {
        $this->authorizeAccess($allocation);

        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'table_id' => 'required|exists:restaurant_tables,id',
            'waiter_id' => 'required|exists:users,id',

        ]);

        $allocation->update([
            'branch_id' => $request->branch_id,
            'table_id' => $request->table_id,
            'waiter_id' => $request->waiter_id,

            'updated_by' => Auth::id(),
        ]);

        return $this->redirectBasedOnRole('Table allocation updated successfully.');
    }

    public function destroy(TableAllocation $allocation)
    {
        $this->authorizeAccess($allocation);
        $allocation->update(['is_active' => false, 'updated_by' => Auth::id()]);

        return $this->redirectBasedOnRole('Table allocation deactivated successfully.');
    }

    private function authorizeAccess($allocation)
    {
        $user = Auth::user();
        if ($user->role === 'owner') {
            return;
        }
        if ($user->branch_id && $allocation->branch_id === $user->branch_id) {
            return;
        }
        abort(403);
    }

    private function redirectBasedOnRole($message)
    {
        $user = Auth::user();
        if ($user->role === 'owner') {
            return redirect()->route('restaurant.table-allocations.index', [
                'restaurant' => $user->restaurant?->slug,
            ])->with('success', $message);
        }
        if ($user->branch_id) {
            return redirect()->route('branch.table-allocations.index', [
                'restaurant' => $user->restaurant?->slug,
                'branch' => $user->branch?->slug,
            ])->with('success', $message);
        }

        return redirect()->route('table-allocations.index')->with('success', $message);
    }
}
