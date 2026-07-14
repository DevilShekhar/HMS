<?php

namespace App\Exports;

use App\Models\InventoryItem;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromArray;

class InventoryExport implements FromArray
{
    public function array(): array
    {
        $query = InventoryItem::query()->where('restaurant_id',app('restaurant')->id);

        // Branch Manager -> only own branch
        if (Auth::user()->branch_id) {
            $query->where('branch_id', Auth::user()->branch_id);
        }

        $rows = [
            [
                'name',
                'unit',
                'total_stock',
                'minimum_stock',
            ]
        ];

        foreach ($query->get() as $item) {
            $rows[] = [
                $item->name,
                $item->unit,
                $item->total_stock,
                $item->minimum_stock,
            ];
        }

        return $rows;
    }
}
