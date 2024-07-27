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

        Permission::create(['name' => 'tambah-materi']);
        Permission::create(['name' => 'edit-materi']);
        Permission::create(['name' => 'hapus-materi']);
        Permission::create(['name' => 'lihat-materi']);

        Role::create(['name' => 'superadmin']);
        Role::create(['name' => 'customer']);

        $roleAdmin = Role::findByName('superadmin');

        //? (?) Memasukan Permission ke Role admin
        $roleAdmin->givePermissionTo([
            'tambah-customer',
            'edit-customer',
            'hapus-customer',
            'lihat-customer',
            'tambah-user',
            'edit-user',
            'hapus-user',
            'lihat-user',
            'tambah-order',
            'edit-order',
            'hapus-order',
            'lihat-order',
            'approve-order',
            'cancel-order',
        ]);

        $roleMarketing = Role::findByName('marketing');
        $roleMarketing->givePermissionTo([
            'tambah-order',
            'edit-order',
            'hapus-order',
            'lihat-order',
            'tambah-customer',
            'edit-customer',
            'hapus-customer',
            'lihat-customer',
        ]);

        $roleFinance = Role::findByName('finance');
        $roleFinance->givePermissionTo([
            'lihat-order',
            'approve-order',
            'cancel-order',
        ]);

    }
}
