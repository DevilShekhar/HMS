<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\TableCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TableCategoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = TableCategory::query()->with(['branch', 'creator'])->where('restaurant_id', app('restaurant')->id);
        if ($user->role == 'owner') {
            $branchIds = Branch::query()->where('owner_id', $user->id)->pluck('id');
            $query->whereIn('branch_id', $branchIds);
        }
        if ($user->role == 'branch_manager') {
            $branch = Branch::query()->where('branch_manager_id', $user->id)->first();
            if ($branch) {
                $query->where('branch_id', $branch->id);
            }
        }
        $categories = $query->latest()->paginate(20);

        return view('admin.table_categories.index', compact('categories'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->branch_id) {

            $branch = Branch::findOrFail($user->branch_id);

            return view('admin.table_categories.create', compact('branch'));
        }

        // Restaurant owner
        $branches = Branch::query()->where('restaurant_id', app('restaurant')->id)
            ->where('is_active', 1)
            ->get();

        return view('admin.table_categories.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => [
                'required',
                'max:255',
                Rule::unique('table_categories')->where(function ($query) use ($request) {
                    return $query->where('branch_id', $request->branch_id);
                }),
            ],
            'branch_id' => 'required',
        ], [
    'name.unique' => 'This name already exists in the selected branch.',
]);

        TableCategory::create([
            'restaurant_id' => app('restaurant')->id,
            'branch_id' => $request->branch_id,
            'name' => $request->name,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        if ($user->branch_id) {

            return redirect()
                ->route('branch.table-categories.index', [
                    'restaurant' => request()->route('restaurant'),
                    'branch' => $user->branch->slug,
                ])
                ->with('success', 'Table Category created successfully.');
        }

        return redirect()
            ->route('restaurant.table-categories.index', [
                'restaurant' => request()->route('restaurant'),
            ])
            ->with('success', 'Table Category created successfully.');
    }

    public function edit($restaurant, $branch = null, $tableCategory = null)
    {
        if ($tableCategory === null && $branch !== null) {
            $tableCategory = $branch;
        }

        if (! $tableCategory instanceof TableCategory) {

            $tableCategory = TableCategory::findOrFail($tableCategory);
        }

        $branchModel = Branch::query()->find($tableCategory->branch_id);

        return view('admin.table_categories.edit', [
            'tableCategory' => $tableCategory,
            'branches' => collect([$branchModel]),
            'branch' => $branchModel,
        ]);
    }

    public function update(Request $request, $restaurant, $branch = null, $tableCategory = null)
    {

        if ($tableCategory === null) {
            $tableCategory = $branch;
        }

        if (! $tableCategory instanceof TableCategory) {
            $tableCategory = TableCategory::findOrFail($tableCategory);
        }

        $request->validate([
            'branch_id' => 'required',
            'name' => 'required|max:255',
        ]);

        $tableCategory->update([
            'branch_id' => $request->branch_id,
            'name' => $request->name,
            'updated_by' => Auth::id(),
        ]);

        if (Auth::user()->branch_id) {

            return redirect()
                ->route('branch.table-categories.index', [
                    'restaurant' => $restaurant,
                    'branch' => Auth::user()->branch->slug,
                ])
                ->with('success', 'Updated successfully.');
        }

        // Restaurant owner
        return redirect()
            ->route('restaurant.table-categories.index', [
                'restaurant' => $restaurant,
            ])
            ->with('success', 'Updated successfully.');
    }

    public function destroy($restaurant, $branch = null, ?TableCategory $tableCategory = null)
    {
        // Handle route model binding issue
        if ($tableCategory === null && $branch instanceof TableCategory) {
            $tableCategory = $branch;
            $branch = null;
        }

        $user = Auth::user();

        // Branch manager permission check
        if ($user->role == 'branch_manager') {

            $branchModel = Branch::findOrFail($tableCategory->branch_id);

            if ($branchModel->branch_manager_id != $user->id) {
                abort(403);
            }
        }

        // Soft delete
        $tableCategory->update([
            'status' => 0,
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Table Category Deactivated successfully.');
    }
}
