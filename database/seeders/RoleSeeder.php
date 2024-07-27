<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'tambah-customer']);
        Permission::create(['name' => 'edit-customer']);
        Permission::create(['name' => 'hapus-customer']);
        Permission::create(['name' => 'lihat-customer']);

        Permission::create(['name' => 'tambah-video']);
        Permission::create(['name' => 'edit-video']);
        Permission::create(['name' => 'hapus-video']);
        Permission::create(['name' => 'lihat-video']);
        Permission::create(['name' => 'approve-video']);
        Permission::create(['name' => 'cancel-video']);
        Permission::create(['name' => 'request-video']);

        Permission::create(['name' => 'tambah-materi']);
        Permission::create(['name' => 'edit-materi']);
        Permission::create(['name' => 'hapus-materi']);
        Permission::create(['name' => 'lihat-materi']);

        Role::create(['name' => 'superadmin']);
        Role::create(['name' => 'customer']);

        $roleAdmin = Role::findByName('superadmin');

        $roleAdmin->givePermissionTo([
            'tambah-customer',
            'edit-customer',
            'hapus-customer',
            'lihat-customer',
            'tambah-video',
            'edit-video',
            'hapus-video',
            'lihat-video',
            'approve-video',
            'cancel-video',
        ]);

        $roleCustommer = Role::findByName('customer');
        $roleCustommer->givePermissionTo([
            'request-video',
        ]);
    }
}
