<?php

namespace Database\Seeders;

use App\Models\Divisi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisis = [
            [
                'nama_divisi' => 'Support',
                'kode_divisi' => 'SUP',
            ],
            [
                'nama_divisi' => 'Operation',
                'kode_divisi' => 'OPS',
            ],
            [
                'nama_divisi' => 'Commercial',
                'kode_divisi' => 'COM',
            ],
        ];

        foreach ($divisis as $divisi) {
            Divisi::updateOrCreate(
                [
                    'kode_divisi' => $divisi['kode_divisi'],
                ],
                [
                    'uuid' => (string) Str::uuid(),
                    'nama_divisi' => $divisi['nama_divisi'],
                ],
            );
        }
    }
}
