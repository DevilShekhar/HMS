<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\RestaurantTable;
use App\Models\TableCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RestaurantTableController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = RestaurantTable::with(['category', 'branch'])->where('restaurant_id', app('restaurant')->id);
        if ($user->role == 'owner') {
            $branchIds = Branch::query()->where(
                'owner_id',
                $user->id
            )->pluck('id');
            $query->whereIn(
                'branch_id',
                $branchIds
            );
        }
        if ($user->role == 'branch_manager') {
            $branch = Branch::query()->where(
                'branch_manager_id',
                $user->id
            )->first();
            if ($branch) {
                $query->where(
                    'branch_id',
                    $branch->id
                );
            }
        }
        $tables = $query->latest()->paginate(20);

        return view('admin.tables.index', compact('tables'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role == 'owner') {
            $branches = Branch::query()->where('owner_id', $user->id)->where('is_active', 1)->get();
            $categories = TableCategory::query()->where('restaurant_id', app('restaurant')->id)->get();

            return view('admin.tables.create', compact('branches', 'categories'));
        }
        if ($user->branch_id) {

            $branch = Branch::findOrFail($user->branch_id);

            $categories = TableCategory::query()
                ->where('branch_id', $branch->id)
                ->get();

            return view(
                'admin.tables.create',
                compact('branch', 'categories')
            );
        }

        abort(403);
    }

    public function store(Request $request)
    {
        $request->validate([
            'cat_id' => 'required',
            'branch_id' => 'required',
            'table_number' => [
                'required',
                'max:50',
                Rule::unique('tables')->where(function ($query) use ($request) {
                    return $query->where('branch_id', $request->branch_id);
                }),
            ],
            'capacity' => 'required|integer|min:1',
        ], [
            'table_number.unique' => 'This table number already exists in the selected branch.',
        ]);
        RestaurantTable::create([
            'cat_id' => $request->cat_id,
            'restaurant_id' => app('restaurant')->id,
            'branch_id' => $request->branch_id,
            'table_number' => $request->table_number,
            'capacity' => $request->capacity,
            'status' => 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
        if (Auth::user()->role === 'super_admin') {

            return redirect()
                ->route('tables.index')
                ->with('success', 'Tables created successfully.');
        }

        // Branch Manager
        if (Auth::user()->branch_id) {

            return redirect()
                ->route('branch.tables.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                    'branch' => Auth::user()->branch?->slug,
                ])
                ->with('success', 'Tables created successfully.');
        }

        // Owner
        if (Auth::user()->role === 'owner') {
            return redirect()
                ->route('restaurant.tables.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                ])
                ->with('success', 'Tables created successfully.');
        }
    }

    public function show($restaurant, RestaurantTable $table)
    {
        return view('admin.tables.show', compact('table'));
    }

    public function edit($restaurant, $branch = null, $table = null)
    {
        if ($table === null) {
            $table = $branch;
            $branch = null;
        }

        $table = RestaurantTable::findOrFail($table);

        $user = Auth::user();
        if ($user->role == 'owner') {
            $branches = Branch::query()->where('owner_id', $user->id)->where('is_active', 1)->get();
            $categories = TableCategory::query()->where('restaurant_id', app('restaurant')->id)->get();

            return view('admin.tables.edit', compact('table', 'branches', 'categories'));
        }
        if ($user->role == 'branch_manager') {
            $branch = Branch::query()->where('branch_manager_id', $user->id)->first();
            $categories = TableCategory::query()->where('branch_id', $branch->id)->get();

            return view('admin.tables.edit', compact('table', 'branch', 'categories'));
        }
        abort(403);
    }

    public function update(Request $request, $restaurant, RestaurantTable $table)
    {
        $request->validate([
            'cat_id' => 'required',
            'branch_id' => 'required',
            'table_number' => 'required|max:50',
            'capacity' => 'required|integer|min:1',
        ]);
        $table->update([
            'cat_id' => $request->cat_id,
            'branch_id' => $request->branch_id,
            'table_number' => $request->table_number,
            'capacity' => $request->capacity,
            'updated_by' => Auth::id(),
        ]);
        if (Auth::user()->role === 'super_admin') {

            return redirect()
                ->route('tables.index')
                ->with('success', 'Tables created successfully.');
        }

        // Branch Manager
        if (Auth::user()->role === 'branch_manager') {

            return redirect()
                ->route('branch.tables.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                    'branch' => Auth::user()->branch?->slug,
                ])
                ->with('success', 'Tables created successfully.');
        }

        // Owner
        if (Auth::user()->role === 'owner') {
            return redirect()
                ->route('restaurant.tables.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                ])
                ->with('success', 'Tables created successfully.');
        }
    }

    public function destroy($restaurant, RestaurantTable $table)
    {
        $table->update([
            'status' => 0,
            'updated_by' => Auth::id(),
        ]);

        if (Auth::user()->role === 'super_admin') {

            return redirect()
                ->route('tables.index')
                ->with('success', 'Table deactivated successfully.');
        }

        // Any branch user (branch_manager, waiter_head, waiter etc.)
        if (Auth::user()->branch_id) {

            return redirect()
                ->route('branch.tables.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                    'branch' => Auth::user()->branch?->slug,
                ])
                ->with('success', 'Table deactivated successfully.');
        }

        // Restaurant user (owner)
        return redirect()
            ->route('restaurant.tables.index', [
                'restaurant' => Auth::user()->restaurant?->slug,
            ])
            ->with('success', 'Table deactivated successfully.');
    }

    public function categoriesByBranch($restaurant, Branch $branch)
    {
        return response()->json(
            TableCategory::query()->where('branch_id', $branch->id)
                ->select('id', 'name')
                ->get()
        );
    }
}
