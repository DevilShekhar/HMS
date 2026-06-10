<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();

        // Super Admin
        Role::findByName('super_admin')
            ->syncPermissions(Permission::all());

        // Owner
        Role::findByName('owner')->syncPermissions([
            'view-owner-dashboard',
            'manage-user',
            'manage-role',
            'manage-permission',

            'create-branch',
            'view-branch',
            'edit-branch',
            'delete-branch',

            'create-staff',
            'view-staff',
            'edit-staff',
            'delete-staff',

            'create-customer',
            'view-customer',
            'edit-customer',
            'delete-customer',
            'manage-vip-customer',
            'view-vip-customer',

            'create-table',
            'view-table',
            'edit-table',
            'delete-table',

            'create-order',
            'view-order',
            'edit-order',
            'delete-order',
            'prepare-order',
            'ready-order',
            'deliver-order',
            'complete-order',

            'create-token',
            'view-token',

            'view-kitchen',
            'manage-kitchen',

            'create-inventory',
            'view-inventory',
            'edit-inventory',
            'delete-inventory',
            'request-stock',
            'approve-stock-request',

            'generate-bill',
            'view-bill',
            'edit-bill',
            'delete-bill',
            'process-payment',
            'view-payment',

            'view-sales-report',
            'view-profit-report',
            'view-inventory-report',
            'view-staff-report',

            'purchase-subscription',
            'renew-subscription',
            'cancel-subscription',
            'view-subscription',
        ]);

        // Branch Manager
        Role::findByName('branch_manager')->syncPermissions([
            'view-dashboard',
            'view-staff',
            'create-staff',
            'edit-staff',

            'view-customer',
            'create-customer',
            'edit-customer',

            'view-table',
            'edit-table',

            'create-order',
            'view-order',
            'edit-order',
            'prepare-order',
            'ready-order',
            'deliver-order',
            'complete-order',

            'view-token',

            'view-kitchen',
            'manage-kitchen',

            'view-inventory',
            'request-stock',

            'generate-bill',
            'view-bill',
            'process-payment',

            'view-sales-report',
            'view-inventory-report',
            'view-staff-report',
        ]);

        // Waiter Head
        Role::findByName('waiter_head')->syncPermissions([
            'view-dashboard',
            'view-table',
            'create-order',
            'view-order',
            'edit-order',
            'view-token',
            'deliver-order',
            'complete-order',
            'view-customer',
            'create-customer',
            'generate-bill',
            'view-bill',
        ]);

        // Waiter
        Role::findByName('waiter')->syncPermissions([
            'view-dashboard',
            'view-table',
            'create-order',
            'view-order',
            'view-token',
            'view-customer',
            'create-customer',
        ]);

        // Chef
        Role::findByName('chef')->syncPermissions([
            'view-dashboard',
            'view-kitchen',
            'view-order',
            'prepare-order',
            'ready-order',
            'view-inventory',
            'request-stock',
        ]);

        // Cashier
        Role::findByName('cashier')->syncPermissions([
            'view-dashboard',
            'view-order',
            'generate-bill',
            'view-bill',
            'edit-bill',
            'process-payment',
            'view-payment',
            'view-sales-report',
        ]);

        // Customer
        Role::findByName('customer')->syncPermissions([
            'view-order',
            'view-subscription',
        ]);


    }
}
