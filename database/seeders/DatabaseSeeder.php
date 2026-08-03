<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Users
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Rumpo Coffe',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir@gmail.com'],
            [
                'name' => 'Kasir Rumpo Coffe',
                'password' => Hash::make('password'),
                'role' => 'cashier',
            ]
        );
    }
}
