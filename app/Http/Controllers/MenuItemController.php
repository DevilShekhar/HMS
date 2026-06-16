<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                ->where('is_active', 1)
                ->latest()
                ->get();
        } else {
            $branch = Auth::user()->managedBranch;

            $menuItems = MenuItem::with(['branch', 'category'])
                ->where('restaurant_id', $restaurant->id)
                ->where('branch_id', $branch->id)
                ->where('is_active', 1)
                ->latest()
                ->get();
        }

        return view(
            'admin.menu_items.index',
            compact('menuItems')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $restaurant = Restaurant::where(
            'slug',
            request()->route('restaurant')
        )->firstOrFail();

        if (auth()->user()->hasRole('owner')) {

            $branches = Branch::where(
                'restaurant_id',
                $restaurant->id
            )->get();

            $categories = Category::where(
                'restaurant_id',
                $restaurant->id
            )->where('is_active', 1)->get();

        } else {

            $branch = Branch::where(
                'branch_manager_id',
                auth()->id()
            )->firstOrFail();

            $branches = collect([$branch]);

            $categories = Category::where(
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
    $categories = Category::where('branch_id', $branch)
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
            'name' => 'required|max:255',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $restaurant = Restaurant::where(
            'slug',
            request()->route('restaurant')
        )->firstOrFail();

        if (auth()->user()->hasRole('owner')) {

            $branchId = $request->branch_id;

        } else {

            $branch = Branch::where(
                'branch_manager_id',
                auth()->id()
            )->first();

            if (!$branch) {
                return back()->withErrors([
                    'branch' => 'Branch not assigned.'
                ]);
            }

            $branchId = $branch->id;
        }

        $image = null;

        if ($request->hasFile('image')) {

            $file = $request->file('image');

            $filename = time() . '_' . $file->getClientOriginalName();

            if (!file_exists(public_path('uploads/menu-items'))) {
                mkdir(public_path('uploads/menu-items'), 0777, true);
            }

            $file->move(
                public_path('uploads/menu-items'),
                $filename
            );

            $image = 'uploads/menu-items/' . $filename;
        }

        MenuItem::create([
            'restaurant_id' => $restaurant->id,
            'owner_id' => $restaurant->users()
                ->where('role', 'owner')
                ->value('id'),

            'branch_id' => $branchId,
            'category_id' => $request->category_id,
            'created_by' => auth()->id(),

            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'food_type' => $request->food_type,
            'image' => $image,
            'is_available' => $request->is_available ?? 1,
            'is_active' => 1,
        ]);

        return redirect()
            ->route('restaurant.menu-items.index', [
                'restaurant' => $restaurant->slug
            ])
            ->with(
                'success',
                'Menu Item Added Successfully'
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($restaurant, $menu_item)
    {
        $menuItem = MenuItem::findOrFail($menu_item);

        $restaurantModel = Restaurant::where(
            'slug',
            request()->route('restaurant')
        )->firstOrFail();

        if (auth()->user()->hasRole('owner')) {

            $branches = Branch::where(
                'restaurant_id',
                $restaurantModel->id
            )->get();

            $categories = Category::where(
                'restaurant_id',
                $restaurantModel->id
            )
            ->where('is_active', 1)
            ->get();

        } else {

            $branch = Branch::where(
                'branch_manager_id',
                auth()->id()
            )->firstOrFail();

            if ($menuItem->branch_id != $branch->id) {
                abort(403);
            }

            $branches = collect([$branch]);

            $categories = Category::where(
                'restaurant_id',
                $restaurantModel->id
            )
            ->where('branch_id', $branch->id)
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

        if (auth()->user()->hasRole('owner')) {

            $branchId = $request->branch_id;

        } else {

            $branch = Branch::where(
                'branch_manager_id',
                auth()->id()
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

            $filename = time() . '_' . $file->getClientOriginalName();

            if (!file_exists(public_path('uploads/menu-items'))) {
                mkdir(public_path('uploads/menu-items'), 0777, true);
            }

            $file->move(
                public_path('uploads/menu-items'),
                $filename
            );

            $data['image'] = 'uploads/menu-items/' . $filename;
        }

        $menuItem->update($data);

        return redirect()
            ->route('restaurant.menu-items.index', [
                'restaurant' => $restaurant
            ])
            ->with(
                'success',
                'Menu Item Updated Successfully'
            );
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($restaurant, $menu_item)
    {
        $menuItem = MenuItem::findOrFail($menu_item);

        $menuItem->update([
            'is_active' => 0
        ]);

        return redirect()
            ->route('restaurant.menu-items.index', [
                'restaurant' => $restaurant
            ])
            ->with(
                'success',
                'Menu Item Deleted Successfully'
            );
    }
}
