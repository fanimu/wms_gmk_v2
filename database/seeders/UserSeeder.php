<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmk.co.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Staff Gudang',
            'email' => 'staff@gmk.co.id',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Viewer',
            'email' => 'viewer@gmk.co.id',
            'password' => Hash::make('password'),
            'role' => 'viewer',
            'is_active' => true,
        ]);
    }
}
