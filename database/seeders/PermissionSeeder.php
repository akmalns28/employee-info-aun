<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'dashboard.view',
            // User Permissions
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',

            // Role Permissions
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',

            // Permission Permissions
            'permission.view',
            'permission.create',
            'permission.edit',
            'permission.delete',

            // Departemen Permissions
            'departemen.view',
            'departemen.create',
            'departemen.edit',
            'departemen.delete',

            'karyawan.view',
            'karyawan.create',
            'karyawan.edit',
            'karyawan.delete',
            'karyawan.import',
            'karyawan.detail',
            'karyawan.id card',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::create(['name' => $permission, 'group_name' => explode('.', $permission)[0]]);
        }
    }
}
