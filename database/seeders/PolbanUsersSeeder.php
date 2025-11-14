<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PolbanUsersSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $users = [
            // existing dummy (boleh kamu hapus kalau tak perlu)
            [
                'nama_lengkap' => 'Azkha Nazzala',
                'email'        => 'azkha.nazzala.tif24@polban.ac.id',
                'password'     => Hash::make('Password123!'),
                'role'         => 'dosen',
                'status'       => true,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'nama_lengkap' => 'Zahra Aldila',
                'email'        => 'zahra.aldila.tif24@polban.ac.id',
                'password'     => Hash::make('Password123!'),
                'role'         => 'tu',
                'status'       => true,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'nama_lengkap' => 'Rahma Attaya',
                'email'        => 'rahma.attaya.tif24@polban.ac.id',
                'password'     => Hash::make('Password123!'),
                'role'         => 'koordinator',
                'status'       => true,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],

            // 👉 tambahan baru (role: dosen) — sesuai permintaanmu
            [
                'nama_lengkap' => 'Dzakir Tsabit',
                'email'        => 'dzakir.tsabit.tif24@polban.ac.id',
                'password'     => Hash::make('Password123!'),
                'role'         => 'dosen',
                'status'       => true,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'nama_lengkap' => 'Ibnu Hilmi',
                'email'        => 'ibnu.hilmi.tif24@polban.ac.id',
                'password'     => Hash::make('Password123!'),
                'role'         => 'dosen',
                'status'       => true,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        // upsert by email agar tidak duplicate jika dijalankan berulang
        DB::table('users')->upsert(
            $users,
            ['email'],
            ['nama_lengkap','password','role','status','updated_at']
        );
    }
}
