<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Divisi;
use App\Models\Karyawan;
use App\Models\Posisi;

class HomeController extends Controller
{
    public function dashboard()
    {
        $this->checkPermission('dashboard.view');

        $totalKaryawan = Karyawan::count();
        $totalDepartemen = Departemen::count();
        $totalDivisi = Divisi::count();
        $totalPosisi = Posisi::count();

        $karyawanAktif = Karyawan::where('status', 1)->count();
        $karyawanNonaktif = Karyawan::where('status', 0)->count();

        $karyawanLaki = Karyawan::where('jenis_kelamin', 'laki-laki')->count();
        $karyawanPerempuan = Karyawan::where('jenis_kelamin', 'perempuan')->count();

        $karyawanBulanIni = Karyawan::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();

        $karyawanHariIni = Karyawan::whereDate('created_at', today())->count();

        $karyawanTerbaru = Karyawan::with(['divisi.departemen', 'posisi'])
            ->latest()
            ->take(5)
            ->get();

        $karyawanPerDepartemen = Departemen::withCount([
            'divisis as total_karyawan' => function ($query) {
                $query->join('karyawans', 'karyawans.divisi_uuid', '=', 'divisis.uuid');
            },
        ])
            ->orderBy('departemen')
            ->get();

        $karyawanPerDivisi = Divisi::withCount('karyawans')->with('departemen')->orderBy('nama_divisi')->get();

        return view('dashboard', compact('totalKaryawan', 'totalDepartemen', 'totalDivisi', 'totalPosisi', 'karyawanAktif', 'karyawanNonaktif', 'karyawanLaki', 'karyawanPerempuan', 'karyawanBulanIni', 'karyawanHariIni', 'karyawanTerbaru', 'karyawanPerDepartemen', 'karyawanPerDivisi'));
    }
}
