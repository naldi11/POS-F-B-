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
        User::create([
            'name' => 'Kasir Rumpo Coffe',
            'email' => 'kasir@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}
