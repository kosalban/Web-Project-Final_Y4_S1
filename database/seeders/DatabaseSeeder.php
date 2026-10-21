<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Technician;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'Admin',
        ]);

        User::create([
            'name' => 'Demo User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'role' => 'Staff',
        ]);

        Category::insert([
            ['name' => 'Hardware', 'description' => 'Computer and hardware problems.', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Network', 'description' => 'Internet, Wi-Fi and network problems.', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Software', 'description' => 'Application and operating system problems.', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Technician::insert([
            ['name' => 'Dara Technician', 'email' => 'dara@example.com', 'phone' => '012345678', 'specialization' => 'Network', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Vanna Technician', 'email' => 'vanna@example.com', 'phone' => '098765432', 'specialization' => 'Hardware', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
