<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // pastikan path model benar

class DummyPolbanPolbanSeeder extends Seeder
{
    public function run()
    {
        // Jika ingin menghindari duplicate email, kita cek dulu
        $users = [
            [
                'nama_lengkap' => 'Azkha Nazzala',
                'email' => 'azkha.nazzala.tif24@polban.ac.id',
                'password' => Hash::make('Password123!'), // bisa ganti
                'role' => 'dosen',
                'status' => true,
            ],
            [
                'nama_lengkap' => 'Zahra Aldila',
                'email' => 'zahra.aldila.tif24@polban.ac.id',
                'password' => Hash::make('Password123!'),
                'role' => 'tu',
                'status' => true,
            ],
            [
                'nama_lengkap' => 'Rahma Attaya',
                'email' => 'rahma.attaya.tif24@polban.ac.id',
                'password' => Hash::make('Password123!'),
                'role' => 'koordinator', // sesuai nilai role di DB-mu
                'status' => true,
            ],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                $u
            );
        }
    }
}
