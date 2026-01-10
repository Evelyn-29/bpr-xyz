<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RolesAndUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Buat role dasar
        $roles = ['Admin', 'Manager', 'Direktur', 'Nasabah'];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Buat user dummy untuk tiap role
        $users = [
            ['name' => 'Admin BPR', 'email' => 'admin@gmail.com', 'password' => 'passwd123', 'role' => 'Admin'],
            ['name' => 'Manager BPR', 'email' => 'manager@gmail.com', 'password' => 'passwd123', 'role' => 'Manager'],
            ['name' => 'Direktur BPR', 'email' => 'direktur@gmail.com', 'password' => 'passwd123', 'role' => 'Direktur'],
            ['name' => 'Nasabah Dummy', 'email' => 'nasabah@gmail.com', 'password' => 'psswd123', 'role' => 'Nasabah'],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                ]
            );
            $user->assignRole($data['role']);
        }
    }
}
