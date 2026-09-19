<?php

namespace App\Imports;

use App\Models\Departemen;
use App\Models\Karyawan;
use Carbon\Carbon;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class KaryawanImport implements ToCollection, WithHeadingRow
{
    public array $errors = [];

    public int $successCount = 0;
    public int $insertCount = 0;
    public int $updateCount = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            try {
                $tglLahir = null;

                if (!empty($row['tgl_lahir'])) {
                    if (is_numeric($row['tgl_lahir'])) {
                        $tglLahir = Date::excelToDateTimeObject($row['tgl_lahir'])->format('Y-m-d');
                    } else {
                        $tglLahir = Carbon::parse($row['tgl_lahir'])->format('Y-m-d');
                    }
                }

                $data = [
                    'nip' => trim((string) ($row['nip'] ?? '')),
                    'nama_depan' => trim((string) ($row['nama_depan'] ?? '')),
                    'nama_belakang' => trim((string) ($row['nama_belakang'] ?? '')),
                    'email' => trim((string) ($row['email'] ?? '')),
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

                $validator = Validator::make(
                    $data,
                    [
                        'nip' => 'required|string|max:50',
                        'nama_depan' => 'required|string|max:100',
                        'email' => 'required|email|max:100',
                        'nama_belakang' => 'nullable|string|max:100',
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
                    $this->errors[] = [
                        'row' => $rowNumber,
                        'messages' => $validator->errors()->all(),
                    ];
                    continue;
                }

                $departemen = null;
                if (!empty($data['departemen'])) {
                    $departemen = Departemen::whereRaw('LOWER(departemen) = ?', [strtolower($data['departemen'])])->first();
                }

                $status = 1;

                if ($data['status'] !== '') {
                    $statusValue = strtolower((string) $data['status']);

                    if (in_array($statusValue, ['0', 'nonaktif', 'inactive'])) {
                        $status = 0;
                    }
                }

                $payload = [
                    'departemen_uuid' => $departemen?->uuid,
                    'nip' => $data['nip'],
                    'nama_depan' => $data['nama_depan'],
                    'nama_belakang' => $data['nama_belakang'] ?: null,
                    'email' => $data['email'],
                    'jabatan' => $data['jabatan'] ?: null,
                    'no_hp' => $data['no_hp'] ?: null,
                    'jenis_kelamin' => $data['jenis_kelamin'] ?: null,
                    'tempat_lahir' => $data['tempat_lahir'] ?: null,
                    'tgl_lahir' => $data['tgl_lahir'],
                    'alamat' => $data['alamat'] ?: null,
                    'status' => $status,
                ];

                /*
|--------------------------------------------------------------------------
| Cari Berdasarkan NIP
|--------------------------------------------------------------------------
|
| NIP tetap digunakan sebagai identifier untuk menentukan apakah
| data import adalah INSERT atau UPDATE.
|
| Tetapi URL QR tidak lagi menggunakan NIP.
|
*/

                $karyawan = Karyawan::where('nip', $data['nip'])->first();

                if ($karyawan) {
                    /*
    |--------------------------------------------------------------------------
    | Simpan Slug Nama Lama
    |--------------------------------------------------------------------------
    */

                    $oldNamaLengkap = trim(($karyawan->nama_depan ?? '') . ' ' . ($karyawan->nama_belakang ?? ''));

                    $oldSlug = Str::slug($oldNamaLengkap);

                    $oldQrPath = $karyawan->qr_code;

                    /*
    |--------------------------------------------------------------------------
    | Update Data
    |--------------------------------------------------------------------------
    */

                    $karyawan->update($payload);

                    /*
    |--------------------------------------------------------------------------
    | Refresh
    |--------------------------------------------------------------------------
    */

                    $karyawan->refresh();

                    $newNamaLengkap = trim(($karyawan->nama_depan ?? '') . ' ' . ($karyawan->nama_belakang ?? ''));

                    $newSlug = Str::slug($newNamaLengkap);

                    /*
    |--------------------------------------------------------------------------
    | Regenerate QR Jika Nama Berubah
    |--------------------------------------------------------------------------
    */

                    if ($oldSlug !== $newSlug) {
                        if ($oldQrPath && Storage::disk('public')->exists($oldQrPath)) {
                            Storage::disk('public')->delete($oldQrPath);
                        }

                        $qrPath = $this->generateQrImage($newNamaLengkap);

                        $karyawan->update([
                            'qr_code' => $qrPath,
                        ]);
                    }

                    /*
    |--------------------------------------------------------------------------
    | Jika QR Belum Ada
    |--------------------------------------------------------------------------
    */

                    if (!$karyawan->qr_code) {
                        $qrPath = $this->generateQrImage($newNamaLengkap);

                        $karyawan->update([
                            'qr_code' => $qrPath,
                        ]);
                    }

                    $this->updateCount++;
                } else {
                    /*
    |--------------------------------------------------------------------------
    | Insert Data Baru
    |--------------------------------------------------------------------------
    */

                    $newKaryawan = Karyawan::create([
                        'uuid' => (string) Str::uuid(),
                        ...$payload,
                    ]);

                    /*
    |--------------------------------------------------------------------------
    | Nama Lengkap
    |--------------------------------------------------------------------------
    */

                    $namaLengkap = trim(($newKaryawan->nama_depan ?? '') . ' ' . ($newKaryawan->nama_belakang ?? ''));

                    /*
    |--------------------------------------------------------------------------
    | Generate QR Berdasarkan Nama
    |--------------------------------------------------------------------------
    */

                    $qrPath = $this->generateQrImage($namaLengkap);

                    /*
    |--------------------------------------------------------------------------
    | Update QR
    |--------------------------------------------------------------------------
    */

                    $newKaryawan->update([
                        'qr_code' => $qrPath,
                    ]);

                    $this->insertCount++;
                }

                $this->successCount++;
            } catch (\Throwable $th) {
                $this->errors[] = [
                    'row' => $rowNumber,
                    'messages' => ['Gagal diproses: ' . $th->getMessage()],
                ];
            }
        }
    }

    private function generateQrImage(string $namaLengkap): string
    {
        /*
    |--------------------------------------------------------------------------
    | Folder QR
    |--------------------------------------------------------------------------
    */

        $folder = 'qrcode/karyawan';

        /*
    |--------------------------------------------------------------------------
    | Slug Nama Lengkap
    |--------------------------------------------------------------------------
    |
    | Contoh:
    |
    | Devia Nur Julianthy
    |
    | menjadi:
    |
    | devia-nur-julianthy
    |
    */

        $slugNama = Str::slug($namaLengkap);

        /*
    |--------------------------------------------------------------------------
    | Nama File
    |--------------------------------------------------------------------------
    */

        $fileName = $slugNama . '.png';

        $filePath = $folder . '/' . $fileName;

        /*
    |--------------------------------------------------------------------------
    | Buat Folder Jika Belum Ada
    |--------------------------------------------------------------------------
    */

        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }

        /*
    |--------------------------------------------------------------------------
    | URL ID Card
    |--------------------------------------------------------------------------
    */

        $idCardUrl = route('karyawan.idCard', [
            'nama_lengkap' => $slugNama,
        ]);

        /*
    |--------------------------------------------------------------------------
    | Generate QR
    |--------------------------------------------------------------------------
    */

        $qrCode = new QrCode(data: $idCardUrl, size: 300, margin: 10);

        $writer = new PngWriter();

        $result = $writer->write($qrCode);

        /*
    |--------------------------------------------------------------------------
    | Simpan QR
    |--------------------------------------------------------------------------
    */

        Storage::disk('public')->put($filePath, $result->getString());

        return $filePath;
    }
}
