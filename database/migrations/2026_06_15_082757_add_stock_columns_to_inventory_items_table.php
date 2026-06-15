<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {

            $table->decimal('total_stock', 10, 2)
                  ->default(0)
                  ->after('unit');

            $table->decimal('remaining_stock', 10, 2)
                  ->default(0)
                  ->after('total_stock');

        });
    }

    public function down(): void
    {
        Schema::table('inventory_items', function (Blueprint $table) {

            $table->dropColumn([
                'total_stock',
                'remaining_stock'
            ]);

        });
    }
};