<?php

namespace App\Http\Controllers;

use App\Imports\KaryawanImport;
use App\Models\Departemen;
use App\Models\Karyawan;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class KaryawanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $departemen = Departemen::orderBy('departemen')->get();

        $query = Karyawan::with('departemen');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nip', 'like', '%' . $search . '%')
                    ->orWhere('nama_depan', 'like', '%' . $search . '%')
                    ->orWhere('nama_belakang', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('departemen_uuid') && $request->departemen_uuid !== 'all') {
            $query->where('departemen_uuid', $request->departemen_uuid);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $perPage = (int) $request->get('per_page', 10);

        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 10;
        }

        $karyawans = $query->latest()->paginate($perPage)->withQueryString();

        return view('karyawan.index', compact('karyawans', 'departemen'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('karyawan.create');

        DB::beginTransaction();

        try {
            $request->validate([
                'departemen_uuid' => 'nullable|exists:departemens,uuid',
                'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                'nip' => 'required|string|max:50|unique:karyawans,nip',
                'nama_depan' => 'required|string|max:100',
                'nama_belakang' => 'nullable|string|max:100',
                'jenis_kelamin' => 'nullable|in:laki-laki,perempuan',
                'tempat_lahir' => 'nullable|string|max:100',
                'tgl_lahir' => 'nullable|date',
                'alamat' => 'nullable|string',
                'email' => 'required|email|max:100|unique:karyawans,email',
                'no_hp' => 'nullable|string|max:20|unique:karyawans,no_hp',
                'status' => 'required|boolean',
            ]);

            $uuid = (string) Str::uuid();

            // Upload avatar jika ada
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('avatar/karyawan', 'public');
            }

            // Simpan data karyawan dulu
            $karyawan = Karyawan::create([
                'uuid' => $uuid,
                'departemen_uuid' => $request->departemen_uuid,
                'avatar' => $avatarPath,
                'nip' => $request->nip,
                'jabatan' => $request->jabatan,
                'nama_depan' => $request->nama_depan,
                'nama_belakang' => $request->nama_belakang,
                'jenis_kelamin' => $request->jenis_kelamin,
                'tempat_lahir' => $request->tempat_lahir,
                'tgl_lahir' => $request->tgl_lahir,
                'alamat' => $request->alamat,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'status' => $request->status,
            ]);

            // URL tujuan QR Code (halaman ID Card)
            $idCardUrl = route('karyawan.idCard', $karyawan->nip);

            // Generate QR Code
            $qrCode = new QrCode(data: $idCardUrl, size: 300, margin: 10);

            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            // Nama file QR
            $qrFileName = 'qrcode/karyawan/' . $karyawan->nip . '.png';

            // Simpan QR ke storage/public
            Storage::disk('public')->put($qrFileName, $result->getString());

            // Update field qr_code
            $karyawan->update([
                'qr_code' => $qrFileName,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $nip)
    {
        $this->checkPermission('karyawan.detail');

        $karyawan = Karyawan::with(['departemen'])
            ->where('nip', $nip)
            ->firstOrFail();

        return response()->json([
            'uuid' => $karyawan->uuid,
            'nama_depan' => $karyawan->nama_depan,
            'nama_belakang' => $karyawan->nama_belakang,
            'nip' => $karyawan->nip,
            'jabatan' => $karyawan->jabatan,
            'email' => $karyawan->email,
            'no_hp' => $karyawan->no_hp,
            'jenis_kelamin' => $karyawan->jenis_kelamin,
            'tempat_lahir' => $karyawan->tempat_lahir,
            'tgl_lahir' => $karyawan->tgl_lahir,
            'alamat' => $karyawan->alamat,
            'status' => $karyawan->status,
            'departemen' => $karyawan->departemen->departemen ?? '-',
            'avatar' => $karyawan->avatar ? asset('storage/' . $karyawan->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($karyawan->nama_depan . ' ' . $karyawan->nama_belakang) . '&background=1e3a8a&color=ffffff&size=256&bold=true',
            'qr_code' => $karyawan->qr_code ? asset('storage/' . $karyawan->qr_code) : asset('assets/static/avatars/default.jpg'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        $this->checkPermission('karyawan.edit');

        $karyawan = Karyawan::where('uuid', $uuid)->firstOrFail();

        return response()->json([
            'uuid' => $karyawan->uuid,
            'nama_depan' => $karyawan->nama_depan,
            'nama_belakang' => $karyawan->nama_belakang,
            'email' => $karyawan->email,
            'nip' => $karyawan->nip,
            'jabatan' => $karyawan->jabatan,
            'no_hp' => $karyawan->no_hp,
            'jenis_kelamin' => $karyawan->jenis_kelamin,
            'tempat_lahir' => $karyawan->tempat_lahir,
            'tgl_lahir' => $karyawan->tgl_lahir,
            'alamat' => $karyawan->alamat,
            'status' => $karyawan->status,
            'departemen_uuid' => $karyawan->departemen_uuid,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $uuid)
    {
        $this->checkPermission('karyawan.edit');
        $karyawan = Karyawan::where('uuid', $uuid)->first();

        if (!$karyawan) {
            return response()->json(
                [
                    'message' => 'Data karyawan tidak ditemukan.',
                ],
                404,
            );
        }

        $validated = $request->validate([
            'departemen_uuid' => 'nullable|exists:departemens,uuid',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'nip' => ['nullable', 'string', 'max:50', Rule::unique('karyawans', 'nip')->ignore($karyawan->id)],
            'nama_depan' => 'required|string|max:100',
            'nama_belakang' => 'nullable|string|max:100',
            'email' => ['required', 'email', 'max:100', Rule::unique('karyawans', 'email')->ignore($karyawan->id)],
            'jabatan' => 'nullable|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:laki-laki,perempuan',
            'tempat_lahir' => 'nullable|string|max:100',
            'tgl_lahir' => 'nullable|date',
            'alamat' => 'nullable|string',
            'status' => 'required|in:0,1',
        ]);

        DB::beginTransaction();

        try {
            // upload avatar baru jika ada
            if ($request->hasFile('avatar')) {
                // hapus avatar lama jika ada
                if ($karyawan->avatar && Storage::disk('public')->exists($karyawan->avatar)) {
                    Storage::disk('public')->delete($karyawan->avatar);
                }

                $validated['avatar'] = $request->file('avatar')->store('karyawan/avatar', 'public');
            }

            $karyawan->update([
                'departemen_uuid' => $validated['departemen_uuid'] ?? null,
                'avatar' => $validated['avatar'] ?? $karyawan->avatar,
                'nip' => $validated['nip'] ?? null,
                'nama_depan' => $validated['nama_depan'],
                'nama_belakang' => $validated['nama_belakang'] ?? null,
                'email' => $validated['email'],
                'jabatan' => $validated['jabatan'] ?? null,
                'no_hp' => $validated['no_hp'] ?? null,
                'jenis_kelamin' => $validated['jenis_kelamin'] ?? null,
                'tempat_lahir' => $validated['tempat_lahir'] ?? null,
                'tgl_lahir' => $validated['tgl_lahir'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
                'status' => $validated['status'],
            ]);

            if ($validated['status'] == 0) {
                $karyawan->user()?->delete();
            }

            DB::commit();

            return response()->json([
                'message' => 'Data karyawan berhasil diperbarui.',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(
                [
                    'message' => 'Terjadi kesalahan saat memperbarui data.',
                    'error' => $th->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        $this->checkPermission('karyawan.delete');

        try {
            $karyawan = Karyawan::where('uuid', $uuid)->firstOrFail();
            $karyawan->delete();
            $karyawan->user()?->delete();

            return response()->json(['success' => 'Data Berhasil Dihapus'], 200);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function idCard(Request $request, $nip = null)
    {
        if ($request->ajax() || $request->expectsJson()) {
            $request->validate([
                'nip' => ['required', 'string'],
            ]);

            $karyawan = Karyawan::with('departemen')->where('nip', $request->nip)->first();

            if (!$karyawan) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Data karyawan tidak ditemukan.',
                    ],
                    404,
                );
            }

            return response()->json([
                'success' => true,
                'data' => $this->formatIdCardData($karyawan),
            ]);
        }

        $karyawan = null;

        if ($nip) {
            $karyawan = Karyawan::with('departemen')->where('nip', $nip)->firstOrFail();
        } elseif (auth()->check()) {
            $karyawan = auth()->user()?->karyawan;

            if ($karyawan) {
                $karyawan->load('departemen');
            }
        }

        $dataKaryawan = $karyawan ? $this->formatIdCardData($karyawan) : null;

        return view('karyawan.id-card', [
            'karyawan' => $karyawan,
            'dataKaryawan' => $dataKaryawan,
        ]);
    }

    private function formatIdCardData($karyawan)
    {
        $user = auth()->user();

        $bolehLihatAlamat = auth()->check() && ($user?->departemen?->departemen === 'Administrasi' || $user?->karyawan?->nip === $karyawan->nip);

        return [
            'nip' => $karyawan->nip ?? '-',
            'namaLengkap' => trim(($karyawan->nama_depan ?? '') . ' ' . ($karyawan->nama_belakang ?? '')),
            'jabatan' => $karyawan->jabatan ?? '-',
            'foto' => $karyawan->avatar ? asset('storage/' . $karyawan->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(($karyawan->nama_depan ?? '') . ' ' . ($karyawan->nama_belakang ?? '')) . '&background=1e3a8a&color=ffffff&size=256&bold=true',
            'email' => $karyawan->email ?? '-',
            'departemen' => $karyawan->departemen->departemen ?? '-',
            'jenis_kelamin' => $karyawan->jenis_kelamin ? Str::title(str_replace('-', ' ', $karyawan->jenis_kelamin)) : '-',
            'tempat_lahir' => $karyawan->tempat_lahir ?? '-',
            'tgl_lahir' => $karyawan->tgl_lahir ? Carbon::parse($karyawan->tgl_lahir)->translatedFormat('d F Y') : '-',
            'no_hp' => $karyawan->no_hp ?? '-',
            'alamat' => $bolehLihatAlamat ? $karyawan->alamat ?? '-' : '-',
            'boleh_lihat_alamat' => $bolehLihatAlamat,
            'status' => $karyawan->status == 1 ? 'Aktif' : 'Nonaktif',
            'status_class' => $karyawan->status == 1 ? 'status-active' : 'status-nonactive',
        ];
    }

    public function import(Request $request)
    {
        $this->checkPermission('karyawan.import');

        $request->validate(
            [
                'file' => 'required|mimes:xlsx,xls,csv|max:5120',
            ],
            [
                'file.required' => 'File wajib diupload.',
                'file.mimes' => 'File harus berformat xlsx, xls, atau csv.',
                'file.max' => 'Ukuran file maksimal 5MB.',
            ],
        );

        try {
            $import = new KaryawanImport();
            Excel::import($import, $request->file('file'));

            $messages = [];

            if ($import->insertCount > 0) {
                $messages[] = "{$import->insertCount} data baru ditambahkan";
            }

            if ($import->updateCount > 0) {
                $messages[] = "{$import->updateCount} data berhasil diperbarui";
            }

            if (count($import->errors) > 0) {
                $errorText = collect($import->errors)
                    ->map(function ($error) {
                        return 'Baris ' . $error['row'] . ': ' . implode(', ', $error['messages']);
                    })
                    ->implode(' | ');

                return redirect()
                    ->back()
                    ->with('warning', implode(', ', $messages) . '. Sebagian data gagal diimport: ' . $errorText);
            }

            return redirect()
                ->back()
                ->with('success', implode(', ', $messages) ?: 'Data karyawan berhasil diimport.');
        } catch (\Throwable $th) {
            return redirect()
                ->back()
                ->with('error', 'Gagal import data: ' . $th->getMessage());
        }
    }

    public function exportQrCode(Request $request)
    {
        $request->validate([
            'selected_nips' => ['required', 'string'],
        ]);

        $nips = json_decode($request->selected_nips, true);

        if (!is_array($nips) || count($nips) === 0) {
            return back()->with('error', 'Pilih minimal satu karyawan untuk export.');
        }

        $karyawans = Karyawan::with('departemen')
            ->whereIn('nip', $nips)
            ->get()
            ->sortBy(function ($karyawan) use ($nips) {
                return array_search($karyawan->nip, $nips);
            })
            ->values();

        return view('karyawan.export-qrcode', [
            'karyawans' => $karyawans,
            'selectedNips' => $nips,
        ]);
    }

    public function exportQrCodeZip(Request $request)
    {
        $request->validate([
            'selected_nips' => ['required', 'string'],
        ]);

        $nips = json_decode($request->selected_nips, true);

        if (!is_array($nips) || count($nips) === 0) {
            return back()->with('error', 'Pilih minimal satu karyawan untuk export.');
        }

        $nips = array_values(array_unique($nips));

        $karyawans = Karyawan::whereIn('nip', $nips)
            ->get()
            ->sortBy(function ($karyawan) use ($nips) {
                return array_search($karyawan->nip, $nips);
            })
            ->values();

        $tempDir = storage_path('app/temp');

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $zipFileName = 'qrcode-karyawan-' . now()->format('YmdHis') . '.zip';
        $zipPath = $zipFileName;

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file ZIP.');
        }

        foreach ($karyawans as $karyawan) {
            if (!$karyawan->qr_code) {
                continue;
            }

            $qrPath = storage_path('app/public/' . $karyawan->qr_code);

            if (!file_exists($qrPath)) {
                continue;
            }

            $imageContent = file_get_contents($qrPath);
            $image = imagecreatefromstring($imageContent);

            if (!$image) {
                continue;
            }

            $width = imagesx($image);
            $height = imagesy($image);

            $jpgImage = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($jpgImage, 255, 255, 255);

            imagefill($jpgImage, 0, 0, $white);
            imagecopy($jpgImage, $image, 0, 0, 0, 0, $width, $height);

            ob_start();
            imagejpeg($jpgImage, null, 95);
            $jpgContent = ob_get_clean();

            imagedestroy($image);
            imagedestroy($jpgImage);

            $namaLengkap = trim(($karyawan->nama_depan ?? '') . ' ' . ($karyawan->nama_belakang ?? ''));
            $fileName = $this->cleanFileName($karyawan->nip . '-' . $namaLengkap) . '.jpg';

            $zip->addFromString($fileName, $jpgContent);
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    private function cleanFileName($name)
    {
        $name = preg_replace('/[^A-Za-z0-9\- ]/', '', $name);
        $name = preg_replace('/\s+/', '-', $name);
        $name = preg_replace('/-+/', '-', $name);

        return trim($name, '-');
    }
}
