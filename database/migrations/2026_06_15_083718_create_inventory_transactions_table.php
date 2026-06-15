<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('restaurant_id');
            $table->unsignedBigInteger('branch_id');

            $table->unsignedBigInteger('inventory_item_id');

            $table->enum('type', [
                'in',
                'out'
            ]);

            $table->decimal(
                'quantity',
                10,
                2
            );

            $table->text('remarks')
                ->nullable();

            $table->unsignedBigInteger('created_by')
                ->nullable();

            $table->timestamps();

            $table->foreign('restaurant_id')
                ->references('id')
                ->on('restaurants')
                ->cascadeOnDelete();

            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->cascadeOnDelete();

            $table->foreign('inventory_item_id')
                ->references('id')
                ->on('inventory_items')
                ->cascadeOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};