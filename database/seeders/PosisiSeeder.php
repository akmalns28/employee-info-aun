<?php

namespace Database\Seeders;

use App\Models\Posisi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PosisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posisis = [
            [
                'nama_posisi' => 'Staff',
                'kode_posisi' => 'STAFF',
            ],
            [
                'nama_posisi' => 'Admin',
                'kode_posisi' => 'ADMIN',
            ],
            [
                'nama_posisi' => 'Supervisor',
                'kode_posisi' => 'SPV',
            ],
            [
                'nama_posisi' => 'Coordinator',
                'kode_posisi' => 'COORD',
            ],
            [
                'nama_posisi' => 'Manager',
                'kode_posisi' => 'MGR',
            ],
            [
                'nama_posisi' => 'Head',
                'kode_posisi' => 'HEAD',
            ],
            [
                'nama_posisi' => 'Assistant Manager',
                'kode_posisi' => 'AST-MGR',
            ],
            [
                'nama_posisi' => 'Personal Assistant',
                'kode_posisi' => 'PA',
            ],
        ];

        foreach ($posisis as $posisi) {
            Posisi::updateOrCreate(
                [
                    'kode_posisi' => $posisi['kode_posisi'],
                ],
                [
                    'uuid' => (string) Str::uuid(),
                    'nama_posisi' => $posisi['nama_posisi'],
                ],
            );
        }
    }
}
