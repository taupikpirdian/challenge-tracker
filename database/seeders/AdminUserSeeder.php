<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Assign admin role
        $admin->assignRole('admin');

        $this->command->info('Admin user created successfully.');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: password');
    }
}
