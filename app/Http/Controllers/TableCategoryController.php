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
        $query = TableCategory::with(['branch','creator'])->where('restaurant_id', app('restaurant')->id);
        if ($user->role == 'owner') {
            $branchIds = Branch::where('owner_id',$user->id)->pluck('id');
            $query->whereIn('branch_id', $branchIds);
        }
        if ($user->role == 'branch_manager') {
            $branch = Branch::where('branch_manager_id',$user->id)->first();
            if ($branch) {
                $query->where('branch_id', $branch->id);
            }
        }
        $categories = $query->latest()->paginate(20);
        return view('admin.table_categories.index',compact('categories'));
    }

    public function create()
    {
        $user = Auth::user();
        if ($user->role == 'owner') {
            $branches = Branch::where('owner_id', $user->id)->where('is_active', 1)->get();
            return view('admin.table_categories.create', compact('branches'));
        }

        if ($user->role == 'branch_manager') {
            $branch = Branch::where('branch_manager_id', $user->id)->first();
            return view('admin.table_categories.create', compact('branch'));
        }
        abort(403);
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required',
            'name' => 'required|max:255',
        ]);
        TableCategory::create([
            'restaurant_id' => app('restaurant')->id,
            'branch_id' => $request->branch_id,
            'name' => $request->name,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
        return redirect()
            ->route('restaurant.table-categories.index', request()->route('restaurant'))
            ->with('success', 'Table Category created successfully.');
    }

    public function edit($restaurant, TableCategory $tableCategory)
    {
        $user = Auth::user();
        if ($user->role == 'owner') {
            $branches = Branch::where('owner_id', $user->id)->where('is_active', 1)->get();
            return view('admin.table_categories.edit',compact('tableCategory', 'branches'));
        }
        if ($user->role == 'branch_manager') {
            $branch = Branch::where('branch_manager_id', $user->id)
                ->firstOrFail();

            if ($tableCategory->branch_id != $branch->id) {
                abort(403, 'Unauthorized');
            }
            return view('admin.table_categories.edit',compact('tableCategory', 'branch'));
        }
        abort(403);
    }
    public function update(Request $request, $restaurant, TableCategory $tableCategory)
    {
        $request->validate([
            'branch_id' => 'required',
            'name'      => 'required|max:255',
        ]);
        $tableCategory->update([
            'branch_id' => $request->branch_id,
            'name'      => $request->name,
            'updated_by'=> Auth::id(),
        ]);
        return redirect()
            ->route('restaurant.table-categories.index', $restaurant)
            ->with('success', 'Updated successfully.');
    }

    public function destroy($restaurant, TableCategory $tableCategory)
    {
        $user = Auth::user();
        if ($user->role == 'branch_manager') {
            $branch = Branch::where('branch_manager_id',$user->id)->first();
            if ($tableCategory->branch_id != $branch->id) {
                abort(403);
            }
        }
        $tableCategory->delete();
        return back()->with(
            'success',
            'Deleted successfully.'
        );
    }
}