<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = 'web';

        // Shield-style permission names — must match the Policies and the Roles panel.
        // Note: FilamentShield derives the resource slug from the model class name, so
        // TrackingCheckpoint becomes "tracking::checkpoint" (namespace separator style).
        $permissions = [
            // Branches
            'view_any_branch',
            'view_branch',
            'create_branch',
            'update_branch',
            'delete_branch',
            'delete_any_branch',
            'force_delete_branch',
            'force_delete_any_branch',
            'restore_branch',
            'restore_any_branch',

            // Tariffs
            'view_any_tariff',
            'view_tariff',
            'create_tariff',
            'update_tariff',
            'delete_tariff',
            'delete_any_tariff',
            'force_delete_tariff',
            'force_delete_any_tariff',
            'restore_tariff',
            'restore_any_tariff',

            // Shipments
            'view_any_shipment',
            'view_shipment',
            'create_shipment',
            'update_shipment',
            'delete_shipment',
            'delete_any_shipment',
            'force_delete_shipment',
            'force_delete_any_shipment',
            'restore_shipment',
            'restore_any_shipment',

            // Tracking checkpoints (Shield uses "tracking::checkpoint" as the resource slug)
            'view_any_tracking::checkpoint',
            'view_tracking::checkpoint',
            'create_tracking::checkpoint',
            'update_tracking::checkpoint',
            'delete_tracking::checkpoint',
            'delete_any_tracking::checkpoint',
            'force_delete_tracking::checkpoint',
            'force_delete_any_tracking::checkpoint',
            'restore_tracking::checkpoint',
            'restore_any_tracking::checkpoint',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guard,
            ]);
        }

        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => $guard,
        ]);

        $adminOperasional = Role::firstOrCreate([
            'name' => 'admin_operasional',
            'guard_name' => $guard,
        ]);

        $dispatcher = Role::firstOrCreate([
            'name' => 'dispatcher',
            'guard_name' => $guard,
        ]);

        $petugas = Role::firstOrCreate([
            'name' => 'petugas',
            'guard_name' => $guard,
        ]);

        // Super admin receives every defined permission.
        $superAdmin->syncPermissions($permissions);

        // Operational admin: manage shipments and tracking, view branches and tariffs.
        $adminOperasional->syncPermissions([
            'view_any_branch',
            'view_branch',

            'view_any_tariff',
            'view_tariff',

            'view_any_shipment',
            'view_shipment',
            'create_shipment',
            'update_shipment',
            'delete_shipment',
            'delete_any_shipment',

            'view_any_tracking::checkpoint',
            'view_tracking::checkpoint',
            'create_tracking::checkpoint',
            'update_tracking::checkpoint',
            'delete_tracking::checkpoint',
            'delete_any_tracking::checkpoint',
        ]);

        // Dispatcher: create and manage shipments, create tracking updates.
        $dispatcher->syncPermissions([
            'view_any_shipment',
            'view_shipment',
            'create_shipment',
            'update_shipment',

            'view_any_tracking::checkpoint',
            'view_tracking::checkpoint',
            'create_tracking::checkpoint',
            'update_tracking::checkpoint',
        ]);

        // Petugas: view shipments and add tracking updates.
        $petugas->syncPermissions([
            'view_any_shipment',
            'view_shipment',

            'view_any_tracking::checkpoint',
            'view_tracking::checkpoint',
            'create_tracking::checkpoint',
            'update_tracking::checkpoint',
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
