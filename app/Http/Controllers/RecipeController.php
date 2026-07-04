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

        $recipes = Recipe::with([
            'menuItem',
            'branch',
        ])
            ->selectRaw('MIN(id) as id, menu_item_id, branch_id')
            ->groupBy('menu_item_id', 'branch_id')
            ->get();

        return view('admin.mgnt_recipe.index', compact(
            'recipes',
            'restaurant'
        ));
    }

    public function create($restaurant)
    {
        $restaurant = Restaurant::query()->where('slug', $restaurant)->firstOrFail();

        $branches = Branch::query()->where(
            'restaurant_id',
            $restaurant->id
        )->get();

        $menuItems = MenuItem::query()->where(
            'restaurant_id',
            $restaurant->id
        )->get();

        $inventoryItems = InventoryItem::query()->where(
            'restaurant_id',
            $restaurant->id
        )->get();

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

        // $request->validate([
        //     'branch_id' => 'nullable',
        //     'menu_item_id' => 'required',
        //     'inventory_id' => 'required|array|min:1',
        //     'inventory_id.*' => 'required|exists:inventory_items,id',
        //     'quantity_required' => 'required|array|min:1',
        //     'quantity_required.*' => 'required|numeric|min:0.001',
        //     'recipe_unit' => 'required|array|min:1',
        //     'recipe_unit.*' => 'required',

        // ]);
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

    public function edit($restaurant, $recipe, $branch = null)
    {
        $restaurant = Restaurant::query()->where('slug', $restaurant)
            ->firstOrFail();

        if ($branch) {

            $branch = Branch::query()->where('slug', $branch)
                ->where('restaurant_id', $restaurant->id)
                ->firstOrFail();

            $recipes = Recipe::query()->where('menu_item_id', $recipe)
                ->where('branch_id', $branch->id)
                ->get();

            $inventoryItems = InventoryItem::query()->where('restaurant_id', $restaurant->id)
                ->where('branch_id', $branch->id)
                ->get();

        } else {

            $recipes = Recipe::query()->where('menu_item_id', $recipe)
                ->get();

            $inventoryItems = InventoryItem::query()->where('restaurant_id', $restaurant->id)
                ->get();
        }

        if ($recipes->isEmpty()) {
            abort(404);
        }

        $branches = Branch::query()->where('restaurant_id', $restaurant->id)->get();

        $menuItems = MenuItem::query()->where('restaurant_id', $restaurant->id)->get();

        return view(
            'admin.mgnt_recipe.edit',
            compact(
                'recipes',
                'restaurant',
                'branch',
                'branches',
                'menuItems',
                'inventoryItems'
            )
        );
    }

    public function update(Request $request, $restaurant, $recipe, $branch = null)
    {
        $restaurant = Restaurant::query()->where('slug', $restaurant)
            ->firstOrFail();

        $request->validate([
            'branch_id' => 'required',
            'menu_item_id' => 'required',
            'inventory_id' => 'required|array',
            'inventory_id.*' => 'required',
            'quantity_required' => 'required|array',
            'quantity_required.*' => 'required|numeric|min:0.001',
            'recipe_unit' => 'required|array',
            'recipe_unit.*' => 'required',
        ]);

        // delete old ingredients
        Recipe::query()->where('menu_item_id', $recipe)
            ->delete();

        // create updated ingredients
        foreach ($request->inventory_id as $key => $inventoryId) {

            Recipe::create([
                'restaurant_id' => $restaurant->id,
                'branch_id' => $request->branch_id,
                'menu_item_id' => $request->menu_item_id,
                'inventory_id' => $inventoryId,
                'quantity_required' => $request->quantity_required[$key],
                'recipe_unit' => $request->recipe_unit[$key],
                'remarks' => $request->remarks[$key] ?? null,
                'updated_by' => Auth::id(),
                'created_by' => Auth::id(),
            ]);

        }

        if ($branch) {

            return redirect()
                ->route('branch.recipe.index', [
                    'restaurant' => $restaurant->slug,
                    'branch' => $branch,
                ])
                ->with('success', 'Recipe updated successfully');

        }

        return redirect()
            ->route('restaurant.recipe.index', [
                'restaurant' => $restaurant->slug,
            ])
            ->with('success', 'Recipe updated successfully');
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

    public function destroy($restaurant, $recipe)
    {
        $restaurant = Restaurant::query()->where('slug', $restaurant)->firstOrFail();

        $recipe = Recipe::findOrFail($recipe);

        $recipe->delete();

        return redirect()
            ->route('recipe.index', [
                'restaurant' => $restaurant->slug,
            ])
            ->with('success', 'Recipe deleted successfully');
    }
}
