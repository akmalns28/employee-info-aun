@extends('layouts.app')

@section('title')
    Dashboard
@endsection

@section('header')
    Dashboard
@endsection

@section('sub header')
    Dashboard
@endsection

@section('content')
    <div class="row row-deck row-cards">

        <div class="col-12">
            <div class="row row-cards">

                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-primary text-white avatar">
                                        <i class="ti ti-users"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ $totalKaryawan ?? 0 }}
                                    </div>
                                    <div class="text-secondary">
                                        Total Karyawan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-green text-white avatar">
                                        <i class="ti ti-building"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ $totalDepartemen ?? 0 }}
                                    </div>
                                    <div class="text-secondary">
                                        Total Departemen
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-blue text-white avatar">
                                        <i class="ti ti-category"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ $totalDivisi ?? 0 }}
                                    </div>
                                    <div class="text-secondary">
                                        Total Divisi
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card card-sm">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span class="bg-orange text-white avatar">
                                        <i class="ti ti-briefcase"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">
                                        {{ $totalPosisi ?? 0 }}
                                    </div>
                                    <div class="text-secondary">
                                        Total Posisi
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">Karyawan Aktif</div>
                    <div class="h1 mb-0 text-success">
                        {{ $karyawanAktif ?? 0 }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">Karyawan Nonaktif</div>
                    <div class="h1 mb-0 text-danger">
                        {{ $karyawanNonaktif ?? 0 }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">Karyawan Bulan Ini</div>
                    <div class="h1 mb-0 text-primary">
                        {{ $karyawanBulanIni ?? 0 }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-body">
                    <div class="subheader">Input Hari Ini</div>
                    <div class="h1 mb-0">
                        {{ $karyawanHariIni ?? 0 }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">

                    <h3 class="card-title">
                        Komposisi Gender
                    </h3>

                    <div class="mb-4">

                        <div class="d-flex justify-content-between mb-1">
                            <span>Laki-laki</span>
                            <strong>
                                {{ $karyawanLaki ?? 0 }}
                            </strong>
                        </div>

                        <div class="progress progress-sm">
                            <div class="progress-bar bg-blue"
                                style="width: {{ $totalKaryawan > 0 ? ($karyawanLaki / $totalKaryawan) * 100 : 0 }}%"></div>
                        </div>

                    </div>

                    <div>

                        <div class="d-flex justify-content-between mb-1">
                            <span>Perempuan</span>
                            <strong>
                                {{ $karyawanPerempuan ?? 0 }}
                            </strong>
                        </div>

                        <div class="progress progress-sm">
                            <div class="progress-bar bg-pink"
                                style="width: {{ $totalKaryawan > 0 ? ($karyawanPerempuan / $totalKaryawan) * 100 : 0 }}%">
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">

                <div class="card-header">
                    <h3 class="card-title">
                        Karyawan per Departemen
                    </h3>
                </div>

                <div class="card-body">

                    @forelse ($karyawanPerDepartemen as $item)
                        <div class="mb-3">

                            <div class="d-flex justify-content-between">
                                <span>
                                    {{ $item->departemen }}
                                </span>

                                <strong>
                                    {{ $item->total_karyawan }}
                                </strong>
                            </div>

                            <div class="progress progress-sm">

                                <div class="progress-bar"
                                    style="width: {{ $totalKaryawan > 0 ? ($item->total_karyawan / $totalKaryawan) * 100 : 0 }}%">
                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="text-secondary text-center">
                            Belum ada data departemen
                        </div>
                    @endforelse

                </div>

            </div>
        </div>

        <div class="col-12">
            <div class="card">

                <div class="card-header">

                    <h3 class="card-title">
                        Karyawan per Divisi
                    </h3>

                </div>

                <div class="table-responsive">

                    <table class="table table-vcenter card-table">

                        <thead>
                            <tr>
                                <th>Departemen</th>
                                <th>Divisi</th>
                                <th class="text-end">
                                    Jumlah Karyawan
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($karyawanPerDivisi as $item)
                                <tr>

                                    <td>
                                        {{ $item->departemen?->departemen ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $item->nama_divisi }}
                                    </td>

                                    <td class="text-end">
                                        <span class="badge bg-blue-lt">
                                            {{ $item->karyawans_count }}
                                        </span>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="3" class="text-center text-secondary">
                                        Belum ada data divisi
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>
        </div>

        <div class="col-12">

            <div class="card card-md">

                <div class="card-stamp card-stamp-lg">

                    <div class="card-stamp-icon bg-primary">
                        <i class="ti ti-users"></i>
                    </div>

                </div>

                <div class="card-body">

                    <h3 class="h1 mb-3">
                        Data Karyawan Terbaru
                    </h3>

                    <div class="table-responsive">

                        <table class="table table-vcenter">

                            <thead>
                                <tr>
                                    <th>NIP</th>
                                    <th>Nama</th>
                                    <th>Departemen</th>
                                    <th>Divisi</th>
                                    <th>Posisi</th>
                                    <th>Status</th>
                                    <th>Tanggal Input</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse ($karyawanTerbaru as $item)
                                    <tr>

                                        <td>
                                            {{ $item->nip ?? '-' }}
                                        </td>

                                        <td>
                                            {{ trim(($item->nama_depan ?? '') . ' ' . ($item->nama_belakang ?? '')) ?: '-' }}
                                        </td>

                                        <td>
                                            {{ $item->divisi?->departemen?->departemen ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $item->divisi?->nama_divisi ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $item->posisi?->nama_posisi ?? '-' }}
                                        </td>

                                        <td>

                                            @if ($item->status == 1)
                                                <span class="badge bg-success-lt">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="badge bg-danger-lt">
                                                    Nonaktif
                                                </span>
                                            @endif

                                        </td>

                                        <td>
                                            {{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') : '-' }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="7" class="text-center text-secondary">
                                            Belum ada data karyawan
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>

                    <div class="mt-3">

                        <a href="{{ route('karyawan.index') }}" class="btn btn-primary">
                            <i class="ti ti-users me-1"></i>
                            Lihat Semua Karyawan
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
