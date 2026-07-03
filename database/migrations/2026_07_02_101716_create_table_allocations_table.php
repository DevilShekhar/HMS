<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('table_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('restaurants')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('table_id')->constrained('restaurant_tables')->cascadeOnDelete();
            $table->foreignId('waiter_id')->constrained('users')->cascadeOnDelete();

            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('table_allocations');
    }
};
