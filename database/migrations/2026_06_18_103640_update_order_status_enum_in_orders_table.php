<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, allow both old and new values
        DB::statement("
            ALTER TABLE orders
            MODIFY COLUMN status ENUM(
                'pending',
                'preparing',
                'prepared',
                'ready',
                'delivered',
                'billing',
                'completed',
                'cancelled'
            ) NOT NULL DEFAULT 'pending'
        ");

        // Update existing data
        DB::table('orders')
            ->where('status', 'preparing')
            ->update(['status' => 'prepared']);

        DB::table('orders')
            ->where('status', 'billing')
            ->update(['status' => 'completed']);

        // Remove old values from ENUM
        DB::statement("
            ALTER TABLE orders
            MODIFY COLUMN status ENUM(
                'pending',
                'prepared',
                'ready',
                'delivered',
                'completed',
                'cancelled'
            ) NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // Allow both old and new values
        DB::statement("
            ALTER TABLE orders
            MODIFY COLUMN status ENUM(
                'pending',
                'preparing',
                'prepared',
                'ready',
                'delivered',
                'billing',
                'completed',
                'cancelled'
            ) NOT NULL DEFAULT 'pending'
        ");

        // Restore old values
        DB::table('orders')
            ->where('status', 'prepared')
            ->update(['status' => 'preparing']);

        DB::table('orders')
            ->where('status', 'completed')
            ->update(['status' => 'billing']);

        // Restore original ENUM
        DB::statement("
            ALTER TABLE orders
            MODIFY COLUMN status ENUM(
                'pending',
                'preparing',
                'ready',
                'delivered',
                'billing',
                'completed',
                'cancelled'
            ) NOT NULL DEFAULT 'pending'
        ");
    }
};