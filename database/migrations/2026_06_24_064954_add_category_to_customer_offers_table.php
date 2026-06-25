<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('customer_offers', function (Blueprint $table) {
            $table->enum('category', [
                'birthday',
                'anniversary',
                'other',
            ])->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_offers', function (Blueprint $table) {
            //
        });
    }
};
