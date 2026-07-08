<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Category;
use App\Models\InventoryItem;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $restaurant = Restaurant::query()->where('slug', request()->route('restaurant'))->firstOrFail();

        if (Auth::user()->role == 'owner') {

            $menuItems = MenuItem::with(['branch', 'category'])
                ->where('restaurant_id', $restaurant->id)
                ->latest()
                ->get();

        } else {

            $branch = Branch::query()->where('slug', request()->route('branch'))->firstOrFail();

            $menuItems = MenuItem::with(['branch', 'category'])
                ->where('restaurant_id', $restaurant->id)
                ->where('branch_id', $branch->id)
                ->where('is_active', 1)
                ->latest()
                ->get();
        }

        // For Recipe Modal
        $branches = Branch::query()->where('restaurant_id', $restaurant->id)->get();

        $inventoryItems = InventoryItem::query()->where('restaurant_id', $restaurant->id)->get();

        return view('admin.menu_items.index', compact('menuItems', 'branches', 'inventoryItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $restaurant = Restaurant::query()->where(
            'slug',
            request()->route('restaurant')
        )->firstOrFail();

        if (Auth::user()->hasRole('owner')) {

            $branches = Branch::query()->where(
                'restaurant_id',
                $restaurant->id
            )->get();

            $categories = Category::query()->where(
                'restaurant_id',
                $restaurant->id
            )->where('is_active', 1)->get();
        } else {

            $branch = Branch::query()->where(
                'branch_manager_id',
                Auth::id()
            )->firstOrFail();

            $branches = collect([$branch]);

            $categories = Category::query()->where(
                'restaurant_id',
                $restaurant->id
            )
                ->where('branch_id', $branch->id)
                ->where('is_active', 1)
                ->get();
        }

        return view(
            'admin.menu_items.create',
            compact(
                'branches',
                'categories'
            )
        );
    }

    public function categoriesByBranch($restaurant, $branch)
    {
        $categories = Category::query()->where('branch_id', $branch)
            ->where('is_active', 1)
            ->select('id', 'name')
            ->get();

        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('menu_items')->where(function ($query) use ($request) {
                    return $query->where('category_id', $request->category_id);
                }),
            ],
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.unique' => 'This menu item already exists in the selected category.',
        ]);

        $restaurant = Restaurant::query()->where(
            'slug',
            request()->route('restaurant')
        )->firstOrFail();

        if (Auth::user()->hasRole('owner')) {

            $branchId = $request->branch_id;
        } else {

            $branch = Branch::query()->where(
                'branch_manager_id',
                Auth::id()
            )->first();

            if (! $branch) {
                return back()->withErrors([
                    'branch' => 'Branch not assigned.',
                ]);
            }

            $branchId = $branch->id;
        }

        $image = null;

        if ($request->hasFile('image')) {

            $file = $request->file('image');

            $filename = time().'_'.$file->getClientOriginalName();

            if (! file_exists(public_path('uploads/menu-items'))) {
                mkdir(public_path('uploads/menu-items'), 0777, true);
            }

            $file->move(
                public_path('uploads/menu-items'),
                $filename
            );

            $image = 'uploads/menu-items/'.$filename;
        }

        MenuItem::create([
            'restaurant_id' => $restaurant->id,
            'owner_id' => $restaurant->users()
                ->where('role', 'owner')
                ->value('id'),

            'branch_id' => $branchId,
            'category_id' => $request->category_id,
            'created_by' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'food_type' => $request->food_type,
            'image' => $image,
            'is_available' => $request->is_available ?? 1,
            'is_active' => 1,
        ]);
        if (Auth::user()->role === 'super_admin') {

            return redirect()
                ->route('menu-items.index')
                ->with(
                    'success',
                    'Menu Item Added Successfully'
                );
        }
        if (Auth::user()->branch_id) {

            return redirect()
                ->route('branch.menu-items.index', [
                    'restaurant' => $restaurant->slug,
                    'branch' => Auth::user()->branch->slug,
                ])
                ->with(
                    'success',
                    'Menu Item Added Successfully'
                );
        }

        // Owner
        return redirect()
            ->route('restaurant.menu-items.index', [
                'restaurant' => $restaurant->slug,
            ])
            ->with(
                'success',
                'Menu Item Added Successfully'
            );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($restaurant, $branch = null, $menu_item = null)
    {
        // Restaurant level route
        if ($menu_item === null) {
            $menu_item = $branch;
            $branch = null;
        }

        $menuItem = MenuItem::findOrFail($menu_item);

        $restaurantModel = Restaurant::query()->where(
            'slug',
            $restaurant
        )->firstOrFail();

        if (Auth::user()->hasRole('owner')) {

            $branches = Branch::query()->where(
                'restaurant_id',
                $restaurantModel->id
            )->get();

            $categories = Category::query()->where(
                'restaurant_id',
                $restaurantModel->id
            )
                ->where('is_active', 1)
                ->get();
        } elseif (Auth::user()->hasRole('branch_manager')) {

            $branchModel = Branch::query()->where(
                'branch_manager_id',
                Auth::id()
            )
                ->firstOrFail();

            if ($menuItem->branch_id != $branchModel->id) {
                abort(403);
            }

            $branches = collect([$branchModel]);

            $categories = Category::query()->where(
                'restaurant_id',
                $restaurantModel->id
            )
                ->where('branch_id', $branchModel->id)
                ->where('is_active', 1)
                ->get();
        }

        return view(
            'admin.menu_items.edit',
            compact(
                'menuItem',
                'branches',
                'categories'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $restaurant, $menu_item)
    {
        $menuItem = MenuItem::findOrFail($menu_item);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|max:255',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if (Auth::user()->hasRole('owner')) {

            $branchId = $request->branch_id;
        } else {

            $branch = Branch::query()->where(
                'branch_manager_id',
                Auth::id()
            )->firstOrFail();

            if ($menuItem->branch_id != $branch->id) {
                abort(403);
            }

            $branchId = $branch->id;
        }

        $data = [
            'branch_id' => $branchId,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'food_type' => $request->food_type,
            'is_available' => $request->is_available ?? 1,
            'is_active' => $request->is_active ?? 1,
        ];

        if ($request->hasFile('image')) {

            // Delete old image
            if (
                $menuItem->image &&
                file_exists(public_path($menuItem->image))
            ) {
                unlink(public_path($menuItem->image));
            }

            $file = $request->file('image');

            $filename = time().'_'.$file->getClientOriginalName();

            if (! file_exists(public_path('uploads/menu-items'))) {
                mkdir(public_path('uploads/menu-items'), 0777, true);
            }

            $file->move(
                public_path('uploads/menu-items'),
                $filename
            );

            $data['image'] = 'uploads/menu-items/'.$filename;
        }

        $menuItem->update($data);

        if (Auth::user()->role === 'super_admin') {

            return redirect()
                ->route('menu-items.index')
                ->with(
                    'success',
                    'Menu Item Added Successfully'
                );
        }
        if (Auth::user()->branch_id) {

            return redirect()
                ->route('branch.menu-items.index', [
                    'restaurant' => $restaurant,
                    'branch' => Auth::user()->branch->slug,
                ])
                ->with(
                    'success',
                    'Menu Item Updated Successfully'
                );
        }

        // Owner

        return redirect()
            ->route('restaurant.menu-items.index', [
                'restaurant' => $restaurant,
            ])
            ->with(
                'success',
                'Menu Item Updated Successfully'
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($restaurant, $branch = null, $menu_item = null)
    {
        // Restaurant level route
        if ($menu_item === null) {
            $menu_item = $branch;
            $branch = null;
        }

        $menuItem = MenuItem::findOrFail($menu_item);

        $menuItem->update([
            'is_active' => 0,
        ]);

        if ($branch) {
            return redirect()
                ->route('branch.menu-items.index', [
                    'restaurant' => $restaurant,
                    'branch' => $branch,
                ])
                ->with('success', 'Menu Item Deactivated Successfully');
        }

        return redirect()
            ->route('restaurant.menu-items.index', [
                'restaurant' => $restaurant,
            ])
            ->with('success', 'Menu Item Deactivated Successfully');
    }
}
