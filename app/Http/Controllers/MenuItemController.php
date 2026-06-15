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
        $restaurant = Restaurant::query()->where('slug', request()->route('restaurant'))->firstOrFail();

        if (Auth::user()->role == 'owner') {
            $branches = Branch::query()->where(
                'restaurant_id',
                $restaurant->id
            )->get();

            $categories = Category::query()->where(
                'restaurant_id',
                $restaurant->id
            )->get();
        } else {
            $branches = collect([
                Auth::user()->branch
            ]);

            $categories = Category::query()->where(
                'branch_id',
                Auth::user()->branch_id
            )->get();
        }

        return view(
            'admin.menu_items.create',
            compact('branches', 'categories')
        );
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required',
            'price' => 'required|numeric'
        ]);

        $restaurant = Restaurant::query()->where('slug', request()->route('restaurant'))->firstOrFail();

        if (Auth::user()->role == 'owner') {
            $branchId = $request->branch_id;
        } else {
            $branchId = Auth::user()->branch_id;
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
            'is_available' => 1,
            'is_active' => 1,
        ]);

        return redirect()
            ->route(
                'restaurant.menu-items.index',
                $restaurant->slug
            )
            ->with('success', 'Menu Item Added');
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


        $restaurantModel = Restaurant::query()->where('slug', request()->route('restaurant'))->firstOrFail();
        if (Auth::user()->role == 'owner') {
            $branches = Branch::query()->where(
                'restaurant_id',
                $restaurantModel->id
            )->get();

            $categories = Category::query()->where(
                'restaurant_id',
                $restaurantModel->id
            )->get();
        } else {
            $branch = Auth::user()->managedBranch;

            if (!$branch) {
                abort(403, 'No branch assigned');
            }

            if ($menuItem->branch_id != $branch->id) {
                abort(403);
            }

            $branches = collect([$branch]);

            $categories = Category::query()->where(
                'branch_id',
                $branch->id
            )->get();
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
            'category_id' => 'required',
            'name'        => 'required',
            'price'       => 'required|numeric'
        ]);

        if (Auth::user()->role == 'owner') {
            $branchId = $request->branch_id;
        } else {
            $branch = Auth::user()->managedBranch;

            if (!$branch) {
                abort(403, 'No branch assigned');
            }

            if ($menuItem->branch_id != $branch->id) {
                abort(403);
            }

            $branchId = $branch->id;
        }

        $data = [
            'branch_id'     => $branchId,
            'category_id'   => $request->category_id,
            'name'          => $request->name,
            'description'   => $request->description,
            'price'         => $request->price,
            'food_type'     => $request->food_type,
            'is_available'  => $request->is_available ?? $menuItem->is_available,
            'is_active'     => $request->is_active ?? $menuItem->is_active,
        ];

        if ($request->hasFile('image')) {
            $image = $request->file('image')
                ->store('menu-items', 'public');

            $data['image'] = $image;
        }

        $menuItem->update($data);

        return redirect()
            ->route(
                'restaurant.menu-items.index',
                ['restaurant' => $restaurant]
            )
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
