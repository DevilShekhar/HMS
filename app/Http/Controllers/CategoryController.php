<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role == 'owner') {

            $categories = Category::with([
                    'branch',
                    'creator'
                ])
                ->where('restaurant_id', $user->restaurant_id)
                ->latest()
                ->paginate(10);

        } else {

            $branchId = Branch::where(
                'branch_manager_id',
                $user->id
            )->value('id');

            $categories = Category::with([
                    'branch',
                    'creator'
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
        $user = auth()->user();

        $branches = collect();

        if ($user->role == 'owner') {

            $branches = Branch::where(
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
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'nullable'
        ]);

        $user = auth()->user();

        if ($user->role == 'owner') {

            $request->validate([
                'branch_id' => 'required|exists:branches,id'
            ]);

            $branchId = $request->branch_id;

        } else {

            $branchId = Branch::where(
                'branch_manager_id',
                $user->id
            )->value('id');
        }

        $image = null;

        if ($request->hasFile('image')) {

            $image = $request->file('image')
                ->store('categories', 'public');
        }

        Category::create([
            'restaurant_id' => $user->restaurant_id,
            'branch_id'     => $branchId,
            'created_by'    => $user->id,
            'name'          => $request->name,
            'description'   => $request->description,
            'image'         => $image,
            'is_active'     => $request->is_active ?? 1,
        ]);

        return redirect()
            ->route(
                'restaurant.categories.index',
                [
                    'restaurant' => request()->route('restaurant')
                ]
            )
            ->with(
                'success',
                'Category created successfully.'
            );
    }

    public function show(Category $category)
    {
        return view(
            'admin.categories.show',
            compact('category')
        );
    }
    public function edit($restaurant, $category)
    {
        $category = Category::findOrFail($category);

        $branches = Branch::where(
            'restaurant_id',
            auth()->user()->restaurant_id
        )->get();

        return view(
            'admin.categories.edit',
            compact('category', 'branches')
        );
    }

    public function update(Request $request,$restaurant,$category)
    {
        $category = Category::findOrFail($category);
        $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);
        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? 1,
        ];
        if (
            auth()->user()->role == 'owner' &&
            $request->filled('branch_id')
        ) {
            $data['branch_id'] = $request->branch_id;
        }
        if ($request->hasFile('image')) {
            if (
                $category->image &&
                Storage::disk('public')->exists($category->image)
            ) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')
                ->store('categories', 'public');
        }
        $category->update($data);
        return redirect()
            ->route('restaurant.categories.index', [
                'restaurant' => $restaurant
            ])
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if (
            $category->image &&
            Storage::disk('public')->exists(
                $category->image
            )
        ) {

            Storage::disk('public')
                ->delete($category->image);
        }

        $category->delete();

        return back()->with(
            'success',
            'Category deleted successfully.'
        );
    }
}