<?php

namespace App\Imports;

use App\Models\InventoryItem;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class InventoryImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $branchId = Auth::user()->branch_id;

        $inventory = InventoryItem::query()->where('restaurant_id', app('restaurant')->id)
            ->where('branch_id', $branchId)
            ->whereRaw('LOWER(name) = ?', [strtolower(trim($row['name']))])
            ->first();

        if ($inventory) {

            // Existing item -> Add stock
            $inventory->update([
                'unit' => $row['unit'],
                'total_stock' => $inventory->total_stock + (float) $row['total_stock'],
                'remaining_stock' => $inventory->remaining_stock + (float) $row['total_stock'],
                'minimum_stock' => $row['minimum_stock'],
                'updated_by' => Auth::id(),
            ]);

            return null;
        }

        // New item -> Create record
        return new InventoryItem([
            'restaurant_id' => app('restaurant')->id,
            'branch_id' => $branchId,
            'name' => trim($row['name']),
            'unit' => $row['unit'],
            'total_stock' => $row['total_stock'],
            'remaining_stock' => $row['total_stock'],
            'minimum_stock' => $row['minimum_stock'],
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
            'is_active' => 1,
        ]);
    }
}
