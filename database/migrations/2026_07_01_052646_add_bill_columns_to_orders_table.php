<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->string('bill_no')
                ->nullable()
                ->unique()
                ->after('token_no');

            $table->timestamp('bill_generated_at')
                ->nullable()
                ->after('bill_no');

        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropColumn([
                'bill_no',
                'bill_generated_at',
            ]);

        });
    }
};
