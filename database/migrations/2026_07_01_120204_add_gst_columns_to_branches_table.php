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
        Schema::table('branches', function (Blueprint $table) {

            $table->boolean('gst_enabled')->default(false)->after('address');
            $table->decimal('gst', 5, 2)
                ->default(0)
                ->after('gst_enabled');
            $table->decimal('cgst', 5, 2)
                ->default(0)
                ->after('gst');
            $table->decimal('sgst', 5, 2)
                ->default(0)
                ->after('cgst');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {

            $table->dropColumn([
                'gst_enabled',
                'gst',
                'cgst',
                'sgst',
            ]);

        });
    }
};
