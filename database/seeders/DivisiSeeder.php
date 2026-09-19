<?php

namespace Database\Seeders;

use App\Models\Departemen;
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
        $data = [
            'IT' => ['IT Support'],
            'Creative Marketing' => ['Sosial Media Specialist', 'Videografher & Editor', 'Creative Leader', 'Content Creator'],
            'HRD' => ['Human Resource', 'People Development', 'Personal Admin'],
            'Administrasi' => ['Sekertaris'],
            'Keuangan' => ['Collector', 'Admin Finance', 'Purchasing', 'Finance & Accouting', 'Admin Coordinator', 'Finance & Accounting'],
            'Operasional' => ['Maintenance', 'Operasional', 'Resource And Development', 'GA Operasional', 'Teknisi', 'Sales Respsentative'],
            'Logistik' => ['Inventory'],
        ];

        foreach ($data as $namaDepartemen => $divisis) {
            $departemen = Departemen::where('departemen', $namaDepartemen)->firstOrFail();

            foreach ($divisis as $namaDivisi) {
                Divisi::firstOrCreate(
                    [
                        'uuid_departemen' => $departemen->uuid,
                        'nama_divisi' => $namaDivisi,
                    ],
                    [
                        'uuid' => (string) Str::uuid(),
                    ],
                );
            }
        }
    }
}
