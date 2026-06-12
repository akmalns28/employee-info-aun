<?php

namespace Database\Seeders;

use App\Models\Departemen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DepartemenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departemens = [['departemen' => 'Direksi', 'kode_departemen' => 'DIR'], ['departemen' => 'Administrasi', 'kode_departemen' => 'ADM'], ['departemen' => 'Keuangan', 'kode_departemen' => 'KEU'], ['departemen' => 'Komersial', 'kode_departemen' => 'KOM'], ['departemen' => 'Operasional Sales', 'kode_departemen' => 'OPS'], ['departemen' => 'Operasional Produksi', 'kode_departemen' => 'OPP']];

        foreach ($departemens as $departemen) {
            Departemen::create([
                'uuid' => Str::uuid(),
                'departemen' => $departemen['departemen'],
                'kode_departemen' => $departemen['kode_departemen'],
            ]);
        }
    }
}
