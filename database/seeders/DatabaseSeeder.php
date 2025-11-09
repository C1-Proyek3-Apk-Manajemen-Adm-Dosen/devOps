<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // contoh akun TU
        User::create([
            'nama_lengkap' => 'Lia Rahmawati',
            'email'        => 'tu@gmail.com',
            'password'     => Hash::make('123456'), // otomatis bcrypt
            'role'         => 'tu',
            'status'       => true,
        ]);

        // contoh akun Dosen
        User::create([
            'nama_lengkap' => 'Dosen A',
            'email'        => 'dosen@gmail.com',
            'password'     => Hash::make('123456'),
            'role'         => 'dosen',
            'status'       => true,
        ]);

        // contoh akun Kaprodi
        User::create([
            'nama_lengkap' => 'Kaprodi B',
            'email'        => 'kaprodi@gmail.com',
            'password'     => Hash::make('123456'),
            'role'         => 'koordinator',
            'status'       => true,
        ]);
    }
}
