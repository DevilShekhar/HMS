<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
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
    public function create()
    {
        $user = Auth::user();
        $restaurant = app('restaurant');
        if ($user->hasRole('owner')) {
            $branches = Branch::query()->where('restaurant_id', $restaurant->id)->get();
            return view('admin.inventory.create', compact('branches'));
        }
        $branch = Branch::query()->where('branch_manager_id', $user->id)->firstOrFail();
        return view('admin.inventory.create', compact('branch'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('owner')) {
            $branchId = $request->branch_id;
        } else {
            $branchId = Branch::query()->where('branch_manager_id', $user->id)->value('id');
        }
        $request->validate([
            'name' => [
                'required',
                Rule::unique('inventory_items')
                    ->where(fn($q) => $q->where(
                        'branch_id',
                        $branchId
                    )),
            ],
            'unit' => 'required',
            'total_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
        ]);
        InventoryItem::create([
            'restaurant_id'   => app('restaurant')->id,
            'branch_id'       => $branchId,
            'name'            => $request->name,
            'unit'            => $request->unit,
            'total_stock'     => $request->total_stock,
            'remaining_stock' => $request->total_stock,
            'minimum_stock'   => $request->minimum_stock,
            'is_active'       => 1,
            'created_by'      => Auth::id(),
            'updated_by'      => Auth::id(),
        ]);
        return redirect()
            ->route(
                'restaurant.inventory.index',
                ['restaurant' => app('restaurant')->slug]
            )
            ->with(
                'success',
                'Inventory Item Added Successfully'
            );
    }
    public function edit($restaurant, $inventory)
    {
        $inventory = InventoryItem::findOrFail(
            $inventory
        );

        $branches = Branch::query()->where(
            'restaurant_id',
            app('restaurant')->id
        )->get();
        return view(
            'admin.inventory.edit',
            compact(
                'inventory',
                'branches'
            )
        );
    }
    public function update(Request $request, $restaurant, $inventory)
    {
        $inventory = InventoryItem::findOrFail($inventory);
        $branchId = Auth::user()->hasRole('owner') ? $request->branch_id : $inventory->branch_id;
        $request->validate([
            'name' => [
                'required',
                Rule::unique('inventory_items')
                    ->where(fn($q) => $q->where(
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
            'branch_id'       => $branchId,
            'name'            => $request->name,
            'unit'            => $request->unit,
            'total_stock'     => $request->total_stock,
            'remaining_stock' => $request->remaining_stock,
            'minimum_stock'   => $request->minimum_stock,
            'updated_by'      => Auth::id(),
        ]);
        return redirect()
            ->route(
                'restaurant.inventory.index',
                ['restaurant' => $restaurant]
            )
            ->with(
                'success',
                'Inventory Updated Successfully'
            );
    }
    public function destroy($restaurant, $inventory)
    {
        $inventory = InventoryItem::findOrFail($inventory);
        $inventory->delete();
        return back()->with(
            'success',
            'Inventory Deleted Successfully'
        );
    }
    public function stockInForm($restaurant, $inventory)
    {
        $inventory = InventoryItem::findOrFail($inventory);
        return view(
            'admin.inventory.stock_in',
            compact('inventory')
        );
    }
    public function stockInStore(Request $request, $restaurant, $inventory)
    {
        $inventory = InventoryItem::findOrFail($inventory);
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
            'remarks' => 'nullable|string|max:500'
        ]);
        InventoryTransaction::create([
            'restaurant_id'     => $inventory->restaurant_id,
            'branch_id'         => $inventory->branch_id,
            'inventory_item_id' => $inventory->id,
            'type'              => 'in',
            'quantity'          => $request->quantity,
            'remarks'           => $request->remarks,
            'created_by'        => Auth::id(),
        ]);
        $inventory->increment('total_stock', $request->quantity);
        $inventory->increment(
            'remaining_stock',
            $request->quantity
        );
        $inventory->update([
            'updated_by' => Auth::id()
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
            'remarks' => 'nullable|string|max:500'
        ]);
        if (
            $request->quantity >
            $inventory->remaining_stock
        ) {
            return back()->withErrors([
                'quantity' =>
                'Insufficient stock available.'
            ]);
        }
        InventoryTransaction::create([
            'restaurant_id'     => $inventory->restaurant_id,
            'branch_id'         => $inventory->branch_id,
            'inventory_item_id' => $inventory->id,
            'type'              => 'out',
            'quantity'          => $request->quantity,
            'remarks'           => $request->remarks,
            'created_by'        => Auth::id(),
        ]);
        $inventory->decrement(
            'remaining_stock',
            $request->quantity
        );
        $inventory->update([
            'updated_by' => Auth::id()
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

    public function transactions($restaurant, $inventory)
    {
        $inventory = InventoryItem::findOrFail($inventory);
        $transactions = InventoryTransaction::with('creator')
            ->where(
                'inventory_item_id',
                $inventory->id
            )
            ->latest()
            ->paginate(20);
        return view(
            'admin.inventory.transactions',
            compact(
                'inventory',
                'transactions'
            )
        );
    }
}
