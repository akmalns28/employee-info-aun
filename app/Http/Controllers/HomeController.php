<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use App\Models\Karyawan;

class HomeController extends Controller
{
    public function dashboard()
    {
        $this->checkPermission('dashboard.view');

        $totalKaryawan = Karyawan::count();
        $totalDepartemen = Departemen::count();
        $karyawanLaki = Karyawan::where('jenis_kelamin', 'laki-laki')->count();
        $karyawanPerempuan = Karyawan::where('jenis_kelamin', 'perempuan')->count();
        $karyawanBulanIni = Karyawan::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
        $karyawanHariIni = Karyawan::whereDate('created_at', today())->count();
        $karyawanTerbaru = Karyawan::with('departemen')->latest()->take(5)->get();

        return view('dashboard', compact('totalKaryawan', 'totalDepartemen', 'karyawanLaki', 'karyawanPerempuan', 'karyawanBulanIni', 'karyawanHariIni', 'karyawanTerbaru'));
    }
}
