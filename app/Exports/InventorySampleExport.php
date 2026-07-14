<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class InventorySampleExport implements FromArray
{
    public function array(): array
    {
        return [
            [
                'name',
                'unit',
                'total_stock',
                'minimum_stock',
            ],
            [
                'Rice',
                'kg',
                100,
                10,
            ],
            [
                'Cooking Oil',
                'liter',
                50,
                5,
            ],
        ];
    }
}
