<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        // $user = Auth::user();
        $user = Auth::user();

        if ($user->role == 'owner') {

            $categories = Category::with([
                'branch',
                'creator',
            ])
                ->where('restaurant_id', $user->restaurant_id)
                ->latest()
                ->paginate(10);
        } else {

            $branchId = Branch::query()
                ->where('branch_manager_id', $user->id)
                ->value('id');

            $categories = Category::with([
                'branch',
                'creator',
            ])
                ->where('branch_id', $branchId)
                ->latest()
                ->paginate(10);
        }

        return view(
            'admin.categories.index',
            compact('categories')
        );
    }

    public function create()
    {
        $user = Auth::user();

        $branches = collect();

        if ($user->role == 'owner') {

            $branches = Branch::query()->where(
                'restaurant_id',
                $user->restaurant_id
            )->where(
                'is_active',
                1
            )->get();
        }

        return view(
            'admin.categories.create',
            compact('branches')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'is_active' => 'nullable',
        ]);

        $user = Auth::user();

        if ($user->role == 'owner') {

            $request->validate([
                'branch_id' => 'required|exists:branches,id',
            ]);

            $branchId = $request->branch_id;

        } else {

            $branch = Branch::query()->where('branch_manager_id', $user->id)->first();

            if (! $branch) {
                return back()->with('error', 'No branch assigned to this branch manager.');
            }

            $branchId = $branch->id;
        }

        // Check duplicate category in the same branch
        $exists = Category::query()->where('branch_id', $branchId)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'name' => 'This category already exists for this branch.',
                ])
                ->withInput();
        }

        Category::create([
            'restaurant_id' => $user->restaurant_id,
            'branch_id' => $branchId,
            'created_by' => $user->id,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? 1,
        ]);

        if ($user->role === 'super_admin') {
            return redirect()
                ->route('categories.index')
                ->with('success', 'Category created successfully.');
        }

        if ($user->role === 'branch_manager') {
            return redirect()
                ->route('branch.categories.index', [
                    'restaurant' => $user->restaurant?->slug,
                    'branch' => $user->branch?->slug,
                ])
                ->with('success', 'Category created successfully.');
        }

        return redirect()
            ->route('restaurant.categories.index', [
                'restaurant' => $user->restaurant?->slug,
            ])
            ->with('success', 'Category created successfully.');
    }

    public function edit($restaurant, $branch = null, $category = null)
    {
        if ($category === null) {
            $category = $branch;
            $branch = null;
        }

        $category = Category::findOrFail($category);

        $branches = Branch::query()
            ->where('restaurant_id', Auth::user()->restaurant_id)
            ->get();

        return view(
            'admin.categories.edit',
            compact(
                'category',
                'branches',
                'restaurant',
                'branch'
            )
        );
    }

    public function update(Request $request, $restaurant, $category)
    {
        $category = Category::findOrFail($category);
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
        ]);
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? 1,
        ];
        if (
            Auth::user()->role == 'owner' &&
            $request->filled('branch_id')
        ) {
            $data['branch_id'] = $request->branch_id;
        }
        $category->update($data);
        if (Auth::user()->role === 'super_admin') {

            return redirect()
                ->route('categories.index')
                ->with('success', 'Category created successfully.');
        }

        // Branch Manager
        if (Auth::user()->role === 'branch_manager') {

            return redirect()
                ->route('branch.categories.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                    'branch' => Auth::user()->branch?->slug,
                ])
                ->with('success', 'Category created successfully.');
        }

        // Owner
        if (Auth::user()->role === 'owner') {
            return redirect()
                ->route('restaurant.categories.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                ])
                ->with('success', 'Category created successfully.');
        }
    }

    public function destroy($restaurant, Category $category)
    {
        $category->update([
            'is_active' => 0,
        ]);

        // SUPER ADMIN
        if (Auth::user()->role === 'super_admin') {

            return redirect()
                ->route('categories.index')
                ->with('success', 'Category deavtivated successfully.');
        }

        $restaurantSlug = request()->route('restaurant');
        $branchSlug = request()->route('branch');

        // BRANCH LEVEL
        if (! empty($restaurantSlug) && ! empty($branchSlug)) {

            return redirect()
                ->route('branch.categories.index', [
                    'restaurant' => $restaurantSlug,
                    'branch' => $branchSlug,
                ])
                ->with('success', 'Category deavtivated successfully.');
        }

        // RESTAURANT LEVEL
        return redirect()
            ->route('restaurant.categories.index', [
                'restaurant' => $restaurantSlug,
            ])
            ->with('success', 'Category deavtivated successfully.');
    }
}
