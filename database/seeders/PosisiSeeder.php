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
        $data = [['kode_posisi' => 'STAFF', 'nama_posisi' => 'Staff'], ['kode_posisi' => 'MGR', 'nama_posisi' => 'Manager'], ['kode_posisi' => 'AST', 'nama_posisi' => 'Assisten'], ['kode_posisi' => 'LEAD', 'nama_posisi' => 'Leader'], ['kode_posisi' => 'DIR', 'nama_posisi' => 'Direktur'], ['kode_posisi' => 'SPV', 'nama_posisi' => 'SPV']];

        foreach ($data as $item) {
            $posisi = Posisi::where('kode_posisi', $item['kode_posisi'])->first();

            if ($posisi) {
                $posisi->update([
                    'nama_posisi' => $item['nama_posisi'],
                ]);
                continue;
            }

            Posisi::create([
                'uuid' => (string) Str::uuid(),
                'kode_posisi' => $item['kode_posisi'],
                'nama_posisi' => $item['nama_posisi'],
            ]);
        }
    }
}
