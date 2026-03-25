<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ALL PERMISSIONS (YOUR MODULES)
        $permissions = [

            // Vendor
            'vendor-list','vendor-create','vendor-edit','vendor-delete',

            // Job Worker
            'jobworker-list','jobworker-create','jobworker-edit','jobworker-delete',

            // Customer
            'customer-list','customer-create','customer-edit','customer-delete',

            // Item + Stock
            'item-list','item-create','item-edit','item-delete',
            'stock-view','stock-manage',

            // Purchase
            'purchase-list','purchase-create','purchase-edit','purchase-delete','purchase-approve',

            // Job Work Assign
            'jobassign-list','jobassign-create','jobassign-edit','jobassign-delete','jobassign-send',

            // Job Worker Inward
            'inward-list','inward-create','inward-edit','inward-delete','inward-approve',

            // Dispatch
            'dispatch-list','dispatch-create','dispatch-edit','dispatch-delete','dispatch-complete',
        ];

        // Create Permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $operator = Role::firstOrCreate(['name' => 'Operator']);
        $account = Role::firstOrCreate(['name' => 'Account']);
        $store = Role::firstOrCreate(['name' => 'Store Manager']);

        // Assign Permissions

        // Admin → All
        $admin->syncPermissions(Permission::all());

        // Manager
        $manager->syncPermissions([
            'vendor-list','vendor-create','vendor-edit',
            'jobworker-list','jobworker-create','jobworker-edit',
            'customer-list','customer-create','customer-edit',
            'item-list','item-create','item-edit','stock-view','stock-manage',
            'purchase-list','purchase-create','purchase-edit',
            'jobassign-list','jobassign-create','jobassign-send',
            'inward-list','inward-create','inward-approve',
            'dispatch-list','dispatch-create','dispatch-complete',
        ]);

        // Operator
        $operator->syncPermissions([
            'customer-list',
            'item-list','stock-view',
            'jobassign-list','jobassign-create',
            'inward-list','inward-create',
            'dispatch-list','dispatch-create',
        ]);

        // Accountant
        $account->syncPermissions([
            'purchase-list','purchase-create','purchase-approve',
            'dispatch-list',
            'inward-list',
            'stock-view',
        ]);

        // Store Manager
        $store->syncPermissions([
            'item-list','item-create','item-edit',
            'stock-manage',
            'purchase-list','purchase-create',
            'inward-list','inward-create',
        ]);
    }
}