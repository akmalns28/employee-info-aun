<?php

namespace App\Imports;

use App\Models\Departemen;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class KaryawanImportValidator implements ToCollection, WithHeadingRow
{
    public array $errors = [];
    public array $warnings = [];
    public array $preview = [];

    public int $totalRows = 0;
    public int $validRows = 0;
    public int $insertCount = 0;
    public int $updateCount = 0;

    private array $seenNips = [];
    private array $seenEmails = [];
    private array $seenSlugs = [];

    public function collection(Collection $rows)
    {
        $this->totalRows = $rows->count();

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            /*
            |--------------------------------------------------------------------------
            | Skip Baris Kosong
            |--------------------------------------------------------------------------
            */

            if ($this->isEmptyRow($row)) {
                continue;
            }

            $rowErrors = [];
            $rowWarnings = [];

            /*
            |--------------------------------------------------------------------------
            | Tanggal Lahir
            |--------------------------------------------------------------------------
            */

            $tglLahir = null;

            if (!empty($row['tgl_lahir'])) {
                try {
                    if (is_numeric($row['tgl_lahir'])) {
                        $tglLahir = Date::excelToDateTimeObject($row['tgl_lahir'])->format('Y-m-d');
                    } else {
                        $tglLahir = Carbon::parse($row['tgl_lahir'])->format('Y-m-d');
                    }
                } catch (\Throwable $th) {
                    $rowErrors[] = 'Format tanggal lahir tidak valid.';
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Normalisasi Data
            |--------------------------------------------------------------------------
            */

            $data = [
                'nip' => trim((string) ($row['nip'] ?? '')),

                'nama_depan' => trim((string) ($row['nama_depan'] ?? '')),

                'nama_belakang' => trim((string) ($row['nama_belakang'] ?? '')),

                'email' => strtolower(trim((string) ($row['email'] ?? ''))),

                'jabatan' => trim((string) ($row['jabatan'] ?? '')),

                'no_hp' => trim((string) ($row['no_hp'] ?? '')),

                'jenis_kelamin' => strtolower(trim((string) ($row['jenis_kelamin'] ?? ''))),

                'tempat_lahir' => trim((string) ($row['tempat_lahir'] ?? '')),

                'tgl_lahir' => $tglLahir,

                'alamat' => trim((string) ($row['alamat'] ?? '')),

                'status' => trim((string) ($row['status'] ?? '')),

                'divisi' => trim((string) ($row['divisi'] ?? '')),

                'departemen' => trim((string) ($row['departemen'] ?? '')),
            ];

            /*
            |--------------------------------------------------------------------------
            | Validasi Field
            |--------------------------------------------------------------------------
            */

            $validator = Validator::make(
                $data,
                [
                    'nip' => 'required|string|max:50',

                    'nama_depan' => 'required|string|max:100',

                    'nama_belakang' => 'nullable|string|max:100',

                    'email' => 'required|email|max:100',

                    'jabatan' => 'nullable|string|max:100',

                    'no_hp' => 'nullable|string|max:20',

                    'jenis_kelamin' => 'nullable|in:laki-laki,perempuan',

                    'tempat_lahir' => 'nullable|string|max:100',

                    'tgl_lahir' => 'nullable|date',

                    'alamat' => 'nullable|string',

                    'status' => 'nullable',

                    'divisi' => 'nullable|string|max:100',

                    'departemen' => 'nullable|string|max:100',
                ],
                [
                    'nip.required' => 'NIP wajib diisi.',

                    'nama_depan.required' => 'Nama depan wajib diisi.',

                    'email.required' => 'Email wajib diisi.',

                    'email.email' => 'Format email tidak valid.',

                    'jenis_kelamin.in' => 'Jenis kelamin harus laki-laki atau perempuan.',
                ],
            );

            if ($validator->fails()) {
                $rowErrors = array_merge($rowErrors, $validator->errors()->all());
            }

            /*
            |--------------------------------------------------------------------------
            | Nama Lengkap & Slug
            |--------------------------------------------------------------------------
            */

            $namaLengkap = trim($data['nama_depan'] . ' ' . $data['nama_belakang']);

            $slugNama = Str::slug($namaLengkap);

            /*
            |--------------------------------------------------------------------------
            | Cek Duplicate NIP Dalam File Excel
            |--------------------------------------------------------------------------
            */

            if ($data['nip'] !== '') {
                if (isset($this->seenNips[$data['nip']])) {
                    $rowErrors[] = 'NIP ' . $data['nip'] . ' duplikat dengan baris ' . $this->seenNips[$data['nip']] . '.';
                } else {
                    $this->seenNips[$data['nip']] = $rowNumber;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Cek Duplicate Email Dalam Excel
            |--------------------------------------------------------------------------
            */

            if ($data['email'] !== '') {
                if (isset($this->seenEmails[$data['email']])) {
                    $rowErrors[] = 'Email ' . $data['email'] . ' duplikat dengan baris ' . $this->seenEmails[$data['email']] . '.';
                } else {
                    $this->seenEmails[$data['email']] = $rowNumber;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Cek Duplicate Nama / Slug Dalam Excel
            |--------------------------------------------------------------------------
            |
            | Karena URL ID Card sekarang:
            |
            | /id-card/nama-lengkap
            |
            */

            if ($slugNama !== '') {
                if (isset($this->seenSlugs[$slugNama])) {
                    $rowErrors[] = 'Nama lengkap "' . $namaLengkap . '" menghasilkan URL yang sama dengan baris ' . $this->seenSlugs[$slugNama] . '.';
                } else {
                    $this->seenSlugs[$slugNama] = $rowNumber;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Cek Karyawan Existing Berdasarkan NIP
            |--------------------------------------------------------------------------
            */

            $existingKaryawan = null;

            if ($data['nip'] !== '') {
                $existingKaryawan = Karyawan::where('nip', $data['nip'])->first();
            }

            /*
            |--------------------------------------------------------------------------
            | Cek Email Sudah Digunakan Karyawan Lain
            |--------------------------------------------------------------------------
            */

            if ($data['email'] !== '') {
                $emailOwner = Karyawan::where('email', $data['email'])->first();

                if ($emailOwner && $emailOwner->nip !== $data['nip']) {
                    $rowErrors[] = 'Email ' . $data['email'] . ' sudah digunakan oleh karyawan dengan NIP ' . $emailOwner->nip . '.';
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Cek Departemen
            |--------------------------------------------------------------------------
            */

            $departemen = null;

            if (!empty($data['departemen'])) {
                $departemen = Departemen::whereRaw('LOWER(departemen) = ?', [strtolower($data['departemen'])])->first();

                if (!$departemen) {
                    $rowErrors[] = 'Departemen "' . $data['departemen'] . '" tidak ditemukan di database.';
                }
            } else {
                $rowWarnings[] = 'Departemen kosong.';
            }

            /*
            |--------------------------------------------------------------------------
            | Cek Status
            |--------------------------------------------------------------------------
            */

            if ($data['status'] !== '') {
                $statusValue = strtolower($data['status']);

                $allowedStatus = ['0', '1', 'aktif', 'active', 'nonaktif', 'inactive'];

                if (!in_array($statusValue, $allowedStatus, true)) {
                    $rowErrors[] = 'Status tidak valid. Gunakan aktif/nonaktif atau 1/0.';
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Cek Slug Bentrok Dengan Karyawan Existing
            |--------------------------------------------------------------------------
            */

            if ($slugNama !== '') {
                $slugConflict = Karyawan::query()
                    ->get()
                    ->first(function ($item) use ($slugNama, $data) {
                        /*
                         * Karyawan dengan NIP yang sama
                         * boleh mempunyai slug tersebut
                         * karena merupakan update.
                         */
                        if ($item->nip === $data['nip']) {
                            return false;
                        }

                        $existingName = trim(($item->nama_depan ?? '') . ' ' . ($item->nama_belakang ?? ''));

                        return Str::slug($existingName) === $slugNama;
                    });

                if ($slugConflict) {
                    $rowErrors[] = 'Nama lengkap menghasilkan URL ID Card yang sudah digunakan oleh NIP ' . $slugConflict->nip . '.';
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Tentukan Insert / Update
            |--------------------------------------------------------------------------
            */

            $action = $existingKaryawan ? 'UPDATE' : 'INSERT';

            /*
            |--------------------------------------------------------------------------
            | Simpan Error
            |--------------------------------------------------------------------------
            */

            if (count($rowErrors) > 0) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'nip' => $data['nip'],
                    'nama' => $namaLengkap,
                    'messages' => $rowErrors,
                ];
            } else {
                $this->validRows++;

                if ($action === 'UPDATE') {
                    $this->updateCount++;
                } else {
                    $this->insertCount++;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Simpan Warning
            |--------------------------------------------------------------------------
            */

            if (count($rowWarnings) > 0) {
                $this->warnings[] = [
                    'row' => $rowNumber,
                    'nip' => $data['nip'],
                    'nama' => $namaLengkap,
                    'messages' => $rowWarnings,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | Preview
            |--------------------------------------------------------------------------
            */

            $this->preview[] = [
                'row' => $rowNumber,
                'nip' => $data['nip'],
                'nama_lengkap' => $namaLengkap,
                'email' => $data['email'],
                'departemen' => $data['departemen'],
                'slug' => $slugNama,
                'action' => $action,

                'valid' => count($rowErrors) === 0,

                'errors' => $rowErrors,

                'warnings' => $rowWarnings,
            ];
        }
    }

    private function isEmptyRow(Collection $row): bool
    {
        return collect($row)->filter(fn($value) => $value !== null && trim((string) $value) !== '')->isEmpty();
    }

    public function isValid(): bool
    {
        return count($this->errors) === 0;
    }
}
