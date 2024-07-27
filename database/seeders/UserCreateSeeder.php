<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserCreateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = "Password124@";
        $superadmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt($password),
        ]);

        $marketing = User::create([
            'name' => 'Marketing',
            'email' => 'marketing@gmail.com',
            'password' => bcrypt($password),
        ]);

        $finance = User::create([
            'name' => 'Finance',
            'email' => 'finance@gmail.com',
            'password' => bcrypt($password),
        ]);

        $superadmin->assignRole('superadmin');
    }
}
