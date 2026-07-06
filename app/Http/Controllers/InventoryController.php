<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    public function index()
    {
        $restaurant = app('restaurant');
        $items = InventoryItem::with(['branch', 'creator', 'updater'])->where('restaurant_id', $restaurant->id)->latest()->paginate(20);

        return view('admin.inventory.index', compact('items'));
    }

    public function create($restaurant, $branch = null)
    {
        $restaurant = app('restaurant');

        // If branch URL exists
        if ($branch) {

            $branchModel = Branch::query()->where('slug', $branch)
                ->where('restaurant_id', $restaurant->id)
                ->firstOrFail();

            return view('admin.inventory.create', [
                'branch' => $branchModel,
            ]);
        }

        $branches = Branch::query()->where('restaurant_id', $restaurant->id)
            ->get();

        return view('admin.inventory.create', [
            'branches' => $branches,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $user = Auth::user();

        if ($user->hasRole('owner')) {
            $branchId = $request->branch_id;
        } elseif ($user->branch_id) {
            $branchId = $user->branch_id;
        } else {
            $branchId = null;
        }
        $request->validate([
            'name' => [
                'required',
                Rule::unique('inventory_items')
                    ->where(fn ($q) => $q->where(
                        'branch_id',
                        $branchId
                    )),
            ],
            'unit' => 'required',
            'total_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
        ]);
        InventoryItem::create([
            'restaurant_id' => app('restaurant')->id,
            'branch_id' => $branchId,
            'name' => $request->name,
            'unit' => $request->unit,
            'total_stock' => $request->total_stock,
            'remaining_stock' => $request->total_stock,
            'minimum_stock' => $request->minimum_stock,
            'is_active' => 1,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
        $user = Auth::user();

        if ($user->role === 'super_admin') {

            return redirect()
                ->route('inventory.index')
                ->with('success', 'Inventory Item created successfully.');
        }

        // Any branch user
        if ($user->branch_id) {

            return redirect()
                ->route('branch.inventory.index', [
                    'restaurant' => $user->restaurant?->slug,
                    'branch' => $user->branch?->slug,
                ])
                ->with('success', 'Inventory Item created successfully.');
        }

        // Restaurant level user
        if ($user->restaurant_id) {

            return redirect()
                ->route('restaurant.inventory.index', [
                    'restaurant' => $user->restaurant?->slug,
                ])
                ->with('success', 'Inventory Item created successfully.');
        }
    }

    public function edit($restaurant, $branch = null, $inventory = null)
    {

        if ($inventory === null) {
            $inventory = $branch;
            $branch = null;
        }

        $inventory = InventoryItem::findOrFail($inventory);

        if ($branch) {

            if ($inventory->branch_id != Auth::user()->branch_id) {
                abort(403);
            }
        }

        $branches = Branch::query()->where(
            'restaurant_id',
            app('restaurant')->id
        )->get();

        return view('admin.inventory.edit', compact('inventory', 'branches'));
    }

    public function update(Request $request, $restaurant, $inventory)
    {
        $inventory = InventoryItem::findOrFail($inventory);
        $branchId = Auth::user()->hasRole('owner') ? $request->branch_id : $inventory->branch_id;
        $request->validate([
            'name' => [
                'required',
                Rule::unique('inventory_items')
                    ->where(fn ($q) => $q->where(
                        'branch_id',
                        $branchId
                    ))
                    ->ignore($inventory->id),
            ],
            'unit' => 'required',
            'total_stock' => 'required|numeric|min:0',
            'remaining_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
        ]);
        $inventory->update([
            'branch_id' => $branchId,
            'name' => $request->name,
            'unit' => $request->unit,
            'total_stock' => $request->total_stock,
            'remaining_stock' => $request->remaining_stock,
            'minimum_stock' => $request->minimum_stock,
            'updated_by' => Auth::id(),
        ]);
        if (Auth::user()->role === 'super_admin') {

            return redirect()
                ->route('inventory.index')
                ->with('success', 'Table Category Updated successfully.');
        }

        // Branch Manager
        if (Auth::user()->role === 'branch_manager') {

            return redirect()
                ->route('branch.inventory.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                    'branch' => Auth::user()->branch?->slug,
                ])
                ->with('success', 'Table Category Updated successfully.');
        }

        // Owner
        if (Auth::user()->role === 'owner') {
            return redirect()
                ->route('restaurant.inventory.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                ])
                ->with('success', 'Table Category Updated successfully.');
        }
    }

    public function destroy($restaurant, $inventory)
    {
        $inventory = InventoryItem::findOrFail($inventory);

        $inventory->update([
            'is_active' => 0,
        ]);

        return back()->with(
            'success',
            'Inventory Deactivated Successfully'
        );
    }

    public function stockInForm(Restaurant $restaurant, Branch $branch, InventoryItem $inventory)
    {
        return view('admin.inventory.stock_in', [
            'inventory' => $inventory,
        ]);
    }

    public function stockInStore(Request $request, $restaurant, $inventory)
    {
        $inventory = InventoryItem::findOrFail($inventory);
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:500',
        ]);
        InventoryTransaction::create([
            'restaurant_id' => $inventory->restaurant_id,
            'branch_id' => $inventory->branch_id,
            'inventory_item_id' => $inventory->id,
            'type' => 'in',
            'quantity' => $request->quantity,
            'remarks' => $request->remarks,
            'created_by' => Auth::id(),
        ]);
        $inventory->increment('total_stock', $request->quantity);
        $inventory->increment(
            'remaining_stock',
            $request->quantity
        );
        $inventory->update([
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route(
                'restaurant.inventory.index',
                ['restaurant' => $restaurant]
            )
            ->with(
                'success',
                'Stock Added Successfully'
            );
    }

    public function stockOutForm($restaurant, $inventory)
    {
        $inventory = InventoryItem::findOrFail($inventory);

        return view(
            'admin.inventory.stock_out',
            compact('inventory')
        );
    }

    public function stockOutStore(Request $request, $restaurant, $inventory)
    {
        $inventory = InventoryItem::findOrFail($inventory);
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:500',
        ]);
        if (
            $request->quantity >
            $inventory->remaining_stock
        ) {
            return back()->withErrors([
                'quantity' => 'Insufficient stock available.',
            ]);
        }
        InventoryTransaction::create([
            'restaurant_id' => $inventory->restaurant_id,
            'branch_id' => $inventory->branch_id,
            'inventory_item_id' => $inventory->id,
            'type' => 'out',
            'quantity' => $request->quantity,
            'remarks' => $request->remarks,
            'created_by' => Auth::id(),
        ]);
        $inventory->decrement(
            'remaining_stock',
            $request->quantity
        );
        $inventory->update([
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route(
                'restaurant.inventory.index',
                ['restaurant' => $restaurant]
            )
            ->with(
                'success',
                'Stock Consumed Successfully'
            );
    }

    public function transactions(Restaurant $restaurant, Branch $branch, InventoryItem $inventory)
    {
        $transactions = InventoryTransaction::with('creator')
            ->where('inventory_item_id', $inventory->id)
            ->latest()
            ->paginate(20);

        return view(
            'admin.inventory.transactions',
            compact('inventory', 'transactions')
        );
    }
}
