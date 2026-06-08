<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('restaurant_id');
            $table->foreignId('branch_id');

            $table->foreignId('customer_id')
                ->nullable();

            $table->string('customer_name');
            $table->string('mobile_number');

            $table->foreignId('table_id')
                ->nullable();

            $table->string('token_no')->unique();

            $table->enum('order_type', [
                'normal',
                'vip'
            ])->default('normal');

            $table->enum('status', [
                'pending',
                'preparing',
                'ready',
                'delivered',
                'billing',
                'completed',
                'cancelled'
            ])->default('pending');

            $table->decimal('subtotal',10,2)
                ->default(0);

            $table->decimal('tax',10,2)
                ->default(0);

            $table->decimal('total',10,2)
                ->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
