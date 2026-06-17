<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        // $user = Auth::user();
        $user = Auth::user();

        if ($user->role == 'owner') {

            $categories = Category::with([
                'branch',
                'creator'
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
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active' => 'nullable'
        ]);

        $user = Auth::user();

        if ($user->role == 'owner') {

            $request->validate([
                'branch_id' => 'required|exists:branches,id'
            ]);

            $branchId = $request->branch_id;
        } else {

            $branchId = Branch::query()
                ->where('branch_manager_id', $user->id)
                ->value('id');
        }

        $image = null;

        if ($request->hasFile('image')) {

            $file = $request->file('image');

            $filename = time() . '_' . $file->getClientOriginalName();

            if (!file_exists(public_path('uploads/categories'))) {
                mkdir(public_path('uploads/categories'), 0777, true);
            }

            $file->move(
                public_path('uploads/categories'),
                $filename
            );

            $image = 'uploads/categories/' . $filename;
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
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
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
        if ($request->hasFile('image')) {
            // Delete old image
            if (
                $category->image &&
                file_exists(public_path($category->image))
            ) {
                unlink(public_path($category->image));
            }
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            if (!file_exists(public_path('uploads/categories'))) {
                mkdir(public_path('uploads/categories'), 0777, true);
            }
            $file->move(
                public_path('uploads/categories'),
                $filename
            );
            $data['image'] = 'uploads/categories/' . $filename;
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

    public function destroy($restaurant, $branch, Category $category)

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

        $category->delete($category);

        // SUPER ADMIN
        if (auth()->user()->role === 'super_admin') {

            return redirect()
                ->route('categories.index')
                ->with('success', 'Category deleted successfully.');
        }


        $restaurantSlug = request()->route('restaurant');
        $branchSlug = request()->route('branch');


        // BRANCH LEVEL
        if (!empty($restaurantSlug) && !empty($branchSlug)) {

            return redirect()
                ->route('branch.categories.index', [
                    'restaurant' => $restaurantSlug,
                    'branch' => $branchSlug,
                ])
                ->with('success', 'Category deleted successfully.');
        }


        // RESTAURANT LEVEL
        return redirect()
            ->route('restaurant.categories.index', [
                'restaurant' => $restaurantSlug,
            ])
            ->with('success', 'Category deleted successfully.');
    }
}
