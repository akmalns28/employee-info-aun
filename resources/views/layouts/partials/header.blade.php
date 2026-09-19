<div class="page-header d-print-none" aria-label="Page header">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">@yield('sub header')</div>
                <h2 class="page-title">@yield('header')</h2>
            </div>

            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">

                    <button
                        class="btn ms-1 btn-sm btn-secondary {{ Route::is('karyawan.*', 'dashboard') ? 'd-none' : 'd-block' }}"
                        id="refreshData">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                            <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                        </svg>
                        Refresh
                    </button>

                    @php
                        $hasImportPermission = auth()
                            ->user()
                            ->getAllPermissions()
                            ->pluck('name')
                            ->contains(fn($name) => str_contains($name, 'import'));
                    @endphp

                    @if ($hasImportPermission)
                        <button type="button"
                            class="btn btn-success {{ Route::is('karyawan.*') ? 'd-block' : 'd-none' }}"
                            data-bs-toggle="modal" data-bs-target="#modal-import">
                            <i class="ti ti-file-import me-1"></i> Import Excel
                        </button>
                    @endif

                    @php
                        $hasCreatePermission = auth()
                            ->user()
                            ->getAllPermissions()
                            ->pluck('name')
                            ->contains(fn($name) => str_contains($name, 'create'));
                    @endphp

                    @if ($hasCreatePermission)
                        <a href="#"
                            class="btn btn-primary btn-5 {{ Route::is('user.*', 'dashboard') ? 'd-none' : 'd-block' }}"
                            data-bs-toggle="modal" data-bs-target="#modal-tambah">

                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-2">
                                <path d="M12 5l0 14"></path>
                                <path d="M5 12l14 0"></path>
                            </svg>
                            Buat
                        </a>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
