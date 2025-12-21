<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin WiFi',
            'username' => 'admin',
            'email' => 'admin@wifi.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Default Payment Settings
        \App\Models\PaymentSetting::create([
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_owner' => 'Kadek Sinduarta',
        ]);
    }
}
