<?php

namespace App\Http\Controllers;

use App\Models\TableCategory;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        if (!Auth::user()) {
            abort(403);
        }

        $user = Auth::user();

        // Branch users
        if ($user->branch_id) {

            $branch = Branch::query()->find($user->branch_id);

            return view(
                'admin.table_categories.create',
                compact('branch')
            );
        }

        // Restaurant users
        $branches = Branch::query()->where(
            'restaurant_id',
            app('restaurant')->id
        )->where('is_active', 1)->get();

        return view(
            'admin.table_categories.create',
            compact('branches')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'branch_id' => Auth::user()->branch_id ? 'required' : 'nullable',
        ]);

        TableCategory::create([
            'restaurant_id' => app('restaurant')->id,
            'branch_id' => $request->branch_id ?? null,
            'name' => $request->name,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        if (Auth::user()->branch_id) {

            return redirect()
                ->route('branch.table-categories.index', [
                    'restaurant' => request()->route('restaurant'),
                    'branch'     => Auth::user()->branch->slug,
                ])
                ->with('success', 'Table Category created successfully.');
        } else {

            return redirect()
                ->route('restaurant.table-categories.index', [
                    'restaurant' => request()->route('restaurant'),
                ])
                ->with('success', 'Table Category created successfully.');
        }
    }

    public function edit($restaurant, $branch, TableCategory $tableCategory)
    {
        $user = Auth::user();

        $branchModel = Branch::query()->where('slug', $branch)
            ->firstOrFail();


        if ($user->role == 'branch_manager') {

            if ($branchModel->branch_manager_id != $user->id) {
                abort(403);
            }
        }


        if ($tableCategory->branch_id != $branchModel->id) {
            abort(403);
        }


        return view('admin.table_categories.edit', [
            'tableCategory' => $tableCategory,
            'branch' => $branchModel
        ]);
    }
    public function update(Request $request, $restaurant, TableCategory $tableCategory)
    {
        $request->validate([
            'branch_id' => 'required',
            'name'      => 'required|max:255',
        ]);

        $tableCategory->update([
            'branch_id'  => $request->branch_id,
            'name'       => $request->name,
            'updated_by' => Auth::id(),
        ]);


        // Branch Manager redirect
        if (Auth::user()->branch_id) {

            return redirect()
                ->route('branch.table-categories.index', [
                    'restaurant' => $restaurant,
                    'branch'     => Auth::user()->branch->slug,
                ])
                ->with('success', 'Updated successfully.');
        }


        // Owner redirect
        return redirect()
            ->route('restaurant.table-categories.index', [
                'restaurant' => $restaurant,
            ])
            ->with('success', 'Updated successfully.');
    }

    public function destroy($restaurant, $branch, TableCategory $tableCategory,$id)
    {
        $user = Auth::user();

        if ($user->role == 'branch_manager') {

            $branchModel = Branch::query()->where('slug', $branch)
                ->where('branch_manager_id', $user->id)
                ->firstOrFail();


            if ($tableCategory->branch_id != $branchModel->id) {
                abort(403);
            }
        }


        $tableCategory->delete($id);


        return redirect()
            ->route('branch.table-categories.index', [
                'restaurant' => $restaurant,
                'branch'     => $branch,
            ])
            ->with('success', 'Deleted successfully.');
    }
}
