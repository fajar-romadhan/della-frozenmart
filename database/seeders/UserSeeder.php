<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder - Membuat 3 akun pengguna default.
 *
 * Admin: pengelola data harian
 * Manager: manajemen stok & analisis
 * Owner: pemilik toko (laporan)
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@della.test',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status_aktif' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Manager',
                'email' => 'manager@della.test',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'status_aktif' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Owner',
                'email' => 'owner@della.test',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'status_aktif' => true,
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
