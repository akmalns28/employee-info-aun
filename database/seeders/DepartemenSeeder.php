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
        $data = [['kode_departemen' => 'IT', 'departemen' => 'IT'], ['kode_departemen' => 'CM', 'departemen' => 'Creative Marketing'], ['kode_departemen' => 'HRD', 'departemen' => 'HRD'], ['kode_departemen' => 'ADM', 'departemen' => 'Administrasi'], ['kode_departemen' => 'KEU', 'departemen' => 'Keuangan'], ['kode_departemen' => 'OPS', 'departemen' => 'Operasional'], ['kode_departemen' => 'LOG', 'departemen' => 'Logistik']];

        foreach ($data as $item) {
            $departemen = Departemen::where('kode_departemen', $item['kode_departemen'])->first();

            if ($departemen) {
                $departemen->update([
                    'departemen' => $item['departemen'],
                ]);
                continue;
            }

            Departemen::create([
                'uuid' => (string) Str::uuid(),
                'kode_departemen' => $item['kode_departemen'],
                'departemen' => $item['departemen'],
            ]);
        }
    }
}
