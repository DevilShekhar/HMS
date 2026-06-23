<?php

namespace App\Services;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;

class InventoryService
{
    public function deductRecipeStock($order)
    {
        foreach ($order->items as $orderItem) {

            $recipes = Recipe::query()
                ->where('menu_item_id', $orderItem->menu_item_id)
                ->get();

            foreach ($recipes as $recipe) {

                $requiredQty =
                    $recipe->quantity_required * $orderItem->quantity;

                $inventory = InventoryItem::findOrFail(
                    $recipe->inventory_id
                );

                $deductQty = $this->convertUnit(
                    $requiredQty,
                    $recipe->recipe_unit,
                    $inventory->unit
                );

                if ($inventory->remaining_stock < $deductQty) {

                    throw new \Exception(
                        'Insufficient stock for '.$inventory->name
                    );
                }

                InventoryTransaction::create([
                    'restaurant_id' => $inventory->restaurant_id,
                    'branch_id' => $inventory->branch_id,
                    'inventory_item_id' => $inventory->id,
                    'type' => 'out',
                    'quantity' => $deductQty,
                    'remarks' => 'Used for Order #'.$order->id,
                    'created_by' => Auth::id(),
                ]);

                $inventory->decrement(
                    'remaining_stock',
                    $deductQty
                );

                $inventory->update([
                    'updated_by' => Auth::id(),
                ]);
            }
        }
    }

    private function convertUnit($qty, $from, $to)
    {
        $from = strtolower(trim($from));
        $to = strtolower(trim($to));

        if ($from == $to) {
            return $qty;
        }

        if ($from == 'ml' && $to == 'liter') {
            return $qty / 1000;
        }

        if ($from == 'liter' && $to == 'ml') {
            return $qty * 1000;
        }

        if ($from == 'gram' && $to == 'kg') {
            return $qty / 1000;
        }

        if ($from == 'kg' && $to == 'gram') {
            return $qty * 1000;
        }

        return $qty;
    }
}
