<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropUnique('orders_token_no_unique');

            $table->unique([
                    'restaurant_id',
                    'branch_id',
                    'order_type',
                    'token_no'
                ],
                'orders_token_scope_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropUnique('orders_token_scope_unique');

            $table->unique('token_no');
        });
    }
};
