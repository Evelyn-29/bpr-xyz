<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan Role 'Superadmin' sudah ada (dibuat via PermissionSeeder)
        // Kita gunakan firstOrCreate agar tidak error jika seeder dijalankan 2x
        $user = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'], // Email Login
            [
                'name'              => 'Super Administrator',
                'password'          => Hash::make('passwd123'),
                'email_verified_at' => now(), // Auto verified biar bisa langsung masuk
            ]
        );

        // Assign Role Superadmin ke user ini
        // Pastikan User model kamu sudah pakai trait HasRoles dari Spatie
        $user->assignRole('Superadmin');
    }
}
