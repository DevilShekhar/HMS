<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\InventoryItem;
use App\Models\MenuItem;
use App\Models\Recipe;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RecipeController extends Controller
{
    public function index($restaurant)
    {
        $restaurant = Restaurant::query()->where('slug', $restaurant)->firstOrFail();

        $user = Auth::user();

        $recipes = Recipe::with([
            'menuItem',
            'branch',
        ])
            ->where('restaurant_id', $restaurant->id)
            ->when($user->branch_id, function ($query) use ($user) {
                $query->where('branch_id', $user->branch_id);
            })
            ->selectRaw('MIN(id) as id, menu_item_id, branch_id, status')
            ->groupBy('menu_item_id', 'branch_id', 'status')
            ->get();

        return view('admin.mgnt_recipe.index', compact(
            'recipes',
            'restaurant'
        ));
    }

    public function create($restaurant)
{
    $restaurant = Restaurant::query()->where('slug', $restaurant)->firstOrFail();

    $user = Auth::user();

    if ($user->branch_id) {

        $branches = Branch::query()->where('id', $user->branch_id)->get();

        $menuItems = MenuItem::query()->where('restaurant_id', $restaurant->id)
            ->where('branch_id', $user->branch_id)
            ->orderBy('name')
            ->get();

        $inventoryItems = InventoryItem::query()->where('restaurant_id', $restaurant->id)
            ->where('branch_id', $user->branch_id)
            ->orderBy('name')
            ->get();

    } else {

        // Owner / Super Admin

        $branches = Branch::query()->where('restaurant_id', $restaurant->id)
            ->orderBy('name')
            ->get();

        // Keep empty until branch selected
        $menuItems = collect();

        $inventoryItems = collect();
    }

    $recipes = Recipe::with([
        'menuItem',
        'inventory',
    ])
    ->where('restaurant_id', $restaurant->id)
    ->latest()
    ->get();

    return view('admin.mgnt_recipe.create', compact(
        'restaurant',
        'branches',
        'menuItems',
        'inventoryItems',
        'recipes'
    ));
}

    public function store(Request $request, $restaurant)
    {
        $restaurant = Restaurant::query()->where('slug', $restaurant)->firstOrFail();

        $request->validate([
            'branch_id' => 'nullable',
            'menu_item_id' => 'required',
            'inventory_id' => [
                'required',
                'array',
                'min:1',
            ],
            'inventory_id.*' => [
                'required',
                'exists:inventory_items,id',
                Rule::unique('mgnt_recipes', 'inventory_id')->where(function ($query) use ($request) {
                    return $query->where('menu_item_id', $request->menu_item_id);
                }),
            ],
            'quantity_required' => 'required|array|min:1',
            'quantity_required.*' => 'required|numeric|min:0.001',
            'recipe_unit' => 'required|array|min:1',
            'recipe_unit.*' => 'required',
        ], [
            'inventory_id.*.unique' => 'This inventory item is already added to the selected menu item.',
        ]);

        foreach ($request->inventory_id as $key => $inventoryId) {

            Recipe::create([

                'restaurant_id' => $request->restaurant_id,
                'branch_id' => $request->branch_id,
                'menu_item_id' => $request->menu_item_id,
                'inventory_id' => $inventoryId,
                'quantity_required' => $request->quantity_required[$key],
                'recipe_unit' => $request->recipe_unit[$key],
                'remarks' => $request->remarks[$key],
                'status' => 'active',
                'created_by' => Auth::id(),
            ]);
        }

        $user = Auth::user();

        if ($user->branch_id) {

            return redirect()
                ->route('branch.recipe.index', [
                    'restaurant' => $restaurant->slug,
                    'branch' => $user->branch?->slug,
                ])
                ->with('success', 'Order created successfully.');
        }

        if ($user->restaurant_id) {

            return redirect()
                ->route('restaurant.recipe.index', [
                    'restaurant' => $restaurant->slug,
                ])
                ->with('success', 'Order created successfully.');
        }

        // Super admin
        return redirect()
            ->route('orders.index')
            ->with('success', 'Order created success    fully.');
    }

    public function edit(Request $request)
    {
        $restaurantSlug = $request->route('restaurant');
        $branchSlug = $request->route('branch'); // null for restaurant route
        $menuItemId = $request->route('recipe');

        $restaurant = Restaurant::query()->where('slug', $restaurantSlug)->firstOrFail();

        if ($branchSlug) {

            $branch = Branch::query()->where('slug', $branchSlug)
                ->where('restaurant_id', $restaurant->id)
                ->firstOrFail();

            $recipes = Recipe::query()->where('menu_item_id', $menuItemId)
                ->where('branch_id', $branch->id)
                ->get();

            $inventoryItems = InventoryItem::query()->where('restaurant_id', $restaurant->id)
                ->where('branch_id', $branch->id)
                ->get();

        } else {

            $branch = null;

            $recipes = Recipe::query()->where('menu_item_id', $menuItemId)->get();

            $inventoryItems = InventoryItem::query()->where('restaurant_id', $restaurant->id)->get();
        }

        if ($recipes->isEmpty()) {
            abort(404);
        }

        $branches = Branch::query()->where('restaurant_id', $restaurant->id)->get();
        $menuItems = MenuItem::query()->where('restaurant_id', $restaurant->id)->get();

        return view('admin.mgnt_recipe.edit', compact(
            'recipes',
            'restaurant',
            'branch',
            'branches',
            'menuItems',
            'inventoryItems'
        ));
    }

    public function update(Request $request)
    {
        $restaurantSlug = $request->route('restaurant');
        $branchSlug = $request->route('branch');
        $menuItemId = $request->route('recipe');

        $restaurant = Restaurant::query()->where('slug', $restaurantSlug)->firstOrFail();

        $branch = null;

        if ($branchSlug) {
            $branch = Branch::query()->where('slug', $branchSlug)
                ->where('restaurant_id', $restaurant->id)
                ->firstOrFail();
        }

        $request->validate([
            'branch_id' => 'required',
            'menu_item_id' => 'required',

            'inventory_id' => 'required|array|min:1',
            'inventory_id.*' => [
                'required',
                'exists:inventory_items,id',
                'distinct',
            ],

            'quantity_required' => 'required|array|min:1',
            'quantity_required.*' => 'required|numeric|min:0.001',

            'recipe_unit' => 'required|array|min:1',
            'recipe_unit.*' => 'required',
        ], [
            'inventory_id.*.distinct' => 'The same inventory item cannot be added more than once.',
        ]);

        // Delete old recipe ingredients
        $query = Recipe::query()->where('menu_item_id', $request->menu_item_id);

        if ($branch) {
            $query->where('branch_id', $branch->id);
        }

        $query->delete();

        foreach ($request->inventory_id as $key => $inventoryId) {

            Recipe::create([
                'restaurant_id' => $restaurant->id,
                'branch_id' => $request->branch_id,
                'menu_item_id' => $request->menu_item_id,
                'inventory_id' => $inventoryId,
                'quantity_required' => $request->quantity_required[$key],
                'recipe_unit' => $request->recipe_unit[$key],
                'remarks' => $request->remarks[$key] ?? null,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        if ($branch) {
            return redirect()->route('branch.recipe.index', [
                'restaurant' => $restaurant->slug,
                'branch' => $branch->slug,
            ])->with('success', 'Recipe updated successfully.');
        }

        return redirect()->route('restaurant.recipe.index', [
            'restaurant' => $restaurant->slug,
        ])->with('success', 'Recipe updated successfully.');
    }

    public function show($restaurant, $branchOrMenuItem, $menuItemId = null)
    {
        $restaurant = Restaurant::query()->where('slug', $restaurant)
            ->firstOrFail();

        if ($menuItemId) {

            // branch request
            $branch = Branch::query()->where('slug', $branchOrMenuItem)
                ->where('restaurant_id', $restaurant->id)
                ->firstOrFail();

            $id = $menuItemId;

            $recipes = Recipe::with([
                'inventory',
                'menuItem',
                'branch',
            ])
                ->where('branch_id', $branch->id)
                ->where('menu_item_id', $id)
                ->get();

        } else {

            // restaurant request
            $id = $branchOrMenuItem;

            $recipes = Recipe::with([
                'inventory',
                'menuItem',
                'branch',
            ])
                ->where('menu_item_id', $id)
                ->get();

        }

        if ($recipes->isEmpty()) {
            abort(404);
        }

        return view(
            'admin.mgnt_recipe.show',
            compact('recipes')
        );
    }

    public function destroy(Request $request)
    {
        $restaurantSlug = $request->route('restaurant');
        $branchSlug = $request->route('branch');
        $menuItemId = $request->route('recipe');

        $restaurant = Restaurant::query()->where('slug', $restaurantSlug)->firstOrFail();

        $query = Recipe::query()->where('menu_item_id', $menuItemId);

        if ($branchSlug) {

            $branch = Branch::query()->where('slug', $branchSlug)
                ->where('restaurant_id', $restaurant->id)
                ->firstOrFail();

            $query->where('branch_id', $branch->id);
        }

        // Soft delete by changing status
        $query->update([
            'status' => 'inactive',
            'updated_by' => Auth::id(),
        ]);

        if ($branchSlug) {
            return redirect()->route('branch.recipe.index', [
                'restaurant' => $restaurant->slug,
                'branch' => $branch->slug,
            ])->with('success', 'Recipe deactivated successfully.');
        }

        return redirect()->route('restaurant.recipe.index', [
            'restaurant' => $restaurant->slug,
        ])->with('success', 'Recipe deactivated successfully.');
    }
}
