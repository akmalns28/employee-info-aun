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
                                    <div class="font-weight-medium">{{ $totalKaryawan ?? 0 }} Karyawan</div>
                                    <div class="text-secondary">Total data karyawan</div>
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
                                        <i class="ti ti-user"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">{{ $karyawanLaki ?? 0 }} Laki-laki</div>
                                    <div class="text-secondary">Karyawan laki-laki</div>
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
                                    <span class="bg-pink text-white avatar">
                                        <i class="ti ti-user-heart"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-medium">{{ $karyawanPerempuan ?? 0 }} Perempuan</div>
                                    <div class="text-secondary">Karyawan perempuan</div>
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
                                    <div class="font-weight-medium">{{ $totalDepartemen ?? 0 }} Departemen</div>
                                    <div class="text-secondary">Unit kerja tersedia</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Statistik Karyawan</h3>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <div class="text-secondary">Total Karyawan</div>
                            <div class="h1">{{ $totalKaryawan ?? 0 }}</div>
                        </div>

                        <div class="col-6 mb-3">
                            <div class="text-secondary">Total Departemen</div>
                            <div class="h1">{{ $totalDepartemen ?? 0 }}</div>
                        </div>

                        <div class="col-6">
                            <div class="text-secondary">Karyawan Bulan Ini</div>
                            <div class="h2 text-success">{{ $karyawanBulanIni ?? 0 }}</div>
                        </div>

                        <div class="col-6">
                            <div class="text-secondary">Input Hari Ini</div>
                            <div class="h2 text-primary">{{ $karyawanHariIni ?? 0 }}</div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Komposisi Karyawan</h3>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <span>Laki-laki</span>
                            <strong>{{ $karyawanLaki ?? 0 }}</strong>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-blue"
                                style="width: {{ $totalKaryawan ? ($karyawanLaki / $totalKaryawan) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <span>Perempuan</span>
                            <strong>{{ $karyawanPerempuan ?? 0 }}</strong>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-pink"
                                style="width: {{ $totalKaryawan ? ($karyawanPerempuan / $totalKaryawan) * 100 : 0 }}%">
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="d-flex justify-content-between">
                            <span>Data Karyawan</span>
                            <strong>{{ $totalKaryawan ?? 0 }}</strong>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-green" style="width: 100%"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card card-md sticky-top">
                <div class="card-stamp card-stamp-lg">
                    <div class="card-stamp-icon bg-primary">
                        <i class="ti ti-users"></i>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <h3 class="h1">Data Karyawan Terbaru</h3>

                            <div class="table-responsive">
                                <table class="table table-vcenter">
                                    <thead>
                                        <tr>
                                            <th>NIK</th>
                                            <th>Nama</th>
                                            <th>Departemen</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Tanggal Input</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($karyawanTerbaru as $item)
                                            <tr>
                                                <td>{{ $item->nip ?? '-' }}</td>
                                                <td>{{ $item->nama_depan . ' ' . $item->nama_belakang ?? '-' }}</td>
                                                <td>{{ $item->departemen->departemen ?? '-' }}</td>
                                                <td>
                                                    @if ($item->jenis_kelamin == 'laki-laki')
                                                        Laki-laki
                                                    @elseif ($item->jenis_kelamin == 'perempuan')
                                                        Perempuan
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') : '-' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-secondary">
                                                    Belum ada data karyawan
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route('karyawan.index') }}" class="btn btn-primary">
                                    <i class="ti ti-users"></i>
                                    Lihat Semua Karyawan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
