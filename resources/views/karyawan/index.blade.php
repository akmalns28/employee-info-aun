@extends('layouts.app')
@section('title')
    Karyawan
@endsection
@section('header')
    Karyawan
@endsection
@section('sub header')
    Karyawan
@endsection

@section('content')
    <form id="filterForm">
        <div class="row">

            <div class="col-md-4">
                <div class="mb-3">
                    <label class="form-label">Cari</label>
                    <div class="input-icon">
                        <input type="text" id="search" class="form-control" placeholder="Cari NIP, Nama, Email">

                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                <path d="M21 21l-6 -6" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">Departemen</label>

                    <select id="departemen_uuid" class="form-select">

                        <option value="all">Semua</option>

                        @foreach ($departemen as $dept)
                            <option value="{{ $dept->uuid }}">
                                {{ $dept->departemen }}
                            </option>
                        @endforeach

                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">Status</label>

                    <select id="status" class="form-select">

                        <option value="all">Semua</option>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>

                    </select>
                </div>
            </div>


        </div>
    </form>

    <div id="karyawan-container">

        <div class="row row-cards">
            @forelse ($karyawans as $karyawan)
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <div class="card-body p-3 text-center">

                            <img src="{{ $karyawan->avatar
                                ? asset('storage/' . $karyawan->avatar)
                                : 'https://ui-avatars.com/api/?name=' .
                                    urlencode($karyawan->nama_depan . ' ' . $karyawan->nama_belakang) .
                                    '&background=1e3a8a&color=ffffff&size=256&bold=true' }}"
                                class="avatar avatar-xl rounded-circle mb-2">

                            <h3 class="m-0 ">
                                {{ $karyawan->nip }}
                            </h3>

                            <h3 class="mb-1">
                                {{ $karyawan->nama_depan }} {{ $karyawan->nama_belakang }}
                            </h3>

                            <div class="text-secondary">
                                {{ $karyawan->departemen->departemen ?? '-' }}
                            </div>

                            <div class="text-secondary">
                                {{ $karyawan->jabatan ?? '-' }}
                            </div>

                            <div class="mt-2">
                                @if ($karyawan->status == 1)
                                    <span class="badge bg-success-lt">Aktif</span>
                                @else
                                    <span class="badge bg-danger-lt">Nonaktif</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex">
                            <!-- ID Card -->
                            @haspermission('karyawan.id card')
                                <a href="{{ route('karyawan.idCard', $karyawan->nip) }}"
                                    class="card-btn d-flex align-items-center text-decoration-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-id">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M3 7a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v10a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3l0 -10" />
                                        <path d="M7 10a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M15 8l2 0" />
                                        <path d="M15 12l2 0" />
                                        <path d="M7 16l10 0" />
                                    </svg>
                                    <span class="ms-1">ID Card</span>
                                </a>
                            @endhaspermission


                            <!-- Dropdown -->
                            <div class="dropdown d-flex">
                                <a href="#"
                                    class="card-btn d-flex align-items-center justify-content-center text-decoration-none"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="d-flex align-items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                d="M6 10.5a1.5 1.5 0 1 0 0 3a1.5 1.5 0 0 0 0-3m4.5 1.5a1.5 1.5 0 1 1 3 0a1.5 1.5 0 0 1-3 0m6 0a1.5 1.5 0 1 1 3 0a1.5 1.5 0 0 1-3 0" />
                                        </svg>
                                        <div class="fw-medium ms-1">Lainnya</div>
                                    </div>
                                </a>

                                @haspermission('karyawan.detail')
                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                        <button type="button" class="dropdown-item detail-button"
                                            data-nip="{{ $karyawan->nip }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24">
                                                <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5"
                                                    d="M12 8h.008M12 16v-5m10 1c0 5.523-4.477 10-10 10S2 17.523 2 12S6.477 2 12 2s10 4.477 10 10" />
                                            </svg>
                                            Detail
                                        </button>
                                    @endhaspermission

                                    @haspermission('karyawan.edit')
                                        <button type="button" class="dropdown-item edit-button"
                                            data-uuid="{{ $karyawan->uuid }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24">
                                                <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2"
                                                    d="M4 20h4L18.5 9.5a2.828 2.828 0 1 0-4-4L4 16zm9.5-13.5l4 4" />
                                            </svg>
                                            Edit
                                        </button>
                                    @endhaspermission

                                    @haspermission('karyawan.delete')
                                        <button type="button" class="dropdown-item delete-button"
                                            data-uuid="{{ $karyawan->uuid }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24">
                                                <path fill="none" stroke="currentColor" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="1.5"
                                                    d="M14 11v6m-4-6v6M6 7v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7M4 7h16M7 7l2-4h6l2 4" />
                                            </svg>
                                            Delete
                                        </button>
                                    @endhaspermission

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty">
                        <div class="empty-header">0</div>
                        <p class="empty-title">Belum ada data karyawan</p>
                        <p class="empty-subtitle text-secondary">
                            Silakan tambahkan data karyawan terlebih dahulu.
                        </p>
                    </div>
                </div>
            @endforelse

            <div class="d-flex justify-content-end mt-4">
                {{ $karyawans->links() }}
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @if ($errors->has('file'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const importModal = new bootstrap.Modal(document.getElementById('modal-import'));
                importModal.show();
            });
        </script>
    @endif

    <script>
        $(document).ready(function() {

            let typingTimer;

            function loadData() {
                $.ajax({
                    url: "{{ route('karyawan.index') }}",
                    type: "GET",
                    data: {
                        search: $('#search').val(),
                        departemen_uuid: $('#departemen_uuid').val(),
                        status: $('#status').val()
                    },
                    success: function(response) {

                        let html = $(response)
                            .find('#karyawan-container')
                            .html();

                        $('#karyawan-container').html(html);

                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }

            $('#search').on('keyup', function() {

                clearTimeout(typingTimer);

                typingTimer = setTimeout(function() {
                    loadData();
                }, 500);

            });

            $('#departemen_uuid').on('change', function() {
                loadData();
            });

            $('#status').on('change', function() {
                loadData();
            });

        });
    </script>

    <script>
        const showUrlTemplate = @json(route('karyawan.show', ':nip'));
        const editUrlTemplate = @json(route('karyawan.edit', ':uuid'));
        const updateUrlTemplate = @json(route('karyawan.update', ':uuid'));
        const destroyUrlTemplate = @json(route('karyawan.destroy', ':uuid'));

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function() {

            $(document).on('click', '.detail-button', function() {
                let nip = $(this).data('nip');
                let url = showUrlTemplate.replace(':nip', nip);

                $.ajax({
                    url: url,
                    type: 'GET',
                    beforeSend: function() {
                        $('#detail_nama_lengkap').text('Loading...');
                        $('#modal-detail').modal('show');
                    },
                    success: function(res) {
                        let namaLengkap = `${res.nama_depan ?? ''} ${res.nama_belakang ?? ''}`
                            .trim();

                        $('#detail_avatar').css('background-image', `url('${res.avatar}')`);
                        $('#detail_qr_code').attr('src', res.qr_code ||
                            '{{ asset('assets/static/avatars/default.jpg') }}');

                        $('#detail_nama_lengkap').text(namaLengkap || '-');
                        $('#detail_departemen').text(res.departemen || '-');

                        if (res.status == 1) {
                            $('#detail_status_badge').html(
                                '<span class="badge bg-success-lt">Aktif</span>');
                        } else {
                            $('#detail_status_badge').html(
                                '<span class="badge bg-danger-lt">Nonaktif</span>');
                        }

                        let tglLahir = res.tgl_lahir || '-';
                        if (tglLahir !== '-') {
                            let date = new Date(tglLahir);
                            tglLahir = date.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'long',
                                year: 'numeric'
                            });
                        }

                        $('#detail_nip').text(res.nip || '-');
                        $('#detail_email').text(res.email || '-');
                        $('#detail_no_hp').text(res.no_hp || '-');
                        $('#detail_jenis_kelamin').text(res.jenis_kelamin || '-');
                        $('#detail_tempat_lahir').text(res.tempat_lahir || '-');
                        $('#detail_tgl_lahir').text(tglLahir);
                        $('#detail_alamat').text(res.alamat || '-');
                        $('#detail_jabatan').text(res.jabatan || '-');
                    },
                    error: function() {
                        $('#modal-detail').modal('hide');
                        showErrorToast(
                            "Gagal mengambil detail karyawan",
                            `${window.location.origin}/assets/static/icon/error.svg`
                        );
                    }
                });
            });

            $(document).on('click', '.edit-button', function() {
                let uuid = $(this).data('uuid');
                let url = editUrlTemplate.replace(':uuid', uuid);

                // reset validation
                $('.is-invalid').removeClass('is-invalid');
                $('.text-danger').text('');

                // reset file input biar ga kebawa file lama
                $('#edit_avatar').val('');

                $.ajax({
                    url: url,
                    type: 'GET',
                    beforeSend: function() {
                        // optional: bisa tambahin loading kalau mau
                    },
                    success: function(res) {
                        $('#edit_uuid').val(res.uuid ?? '');
                        $('#edit_nama_depan').val(res.nama_depan ?? '');
                        $('#edit_nama_belakang').val(res.nama_belakang ?? '');
                        $('#edit_email').val(res.email ?? '');
                        $('#edit_nip').val(res.nip ?? '');
                        $('#edit_jabatan').val(res.jabatan ?? '');
                        $('#edit_no_hp').val(res.no_hp ?? '');
                        $('#edit_jenis_kelamin').val(res.jenis_kelamin ?? '');
                        $('#edit_tempat_lahir').val(res.tempat_lahir ?? '');
                        $('#edit_tgl_lahir').val(res.tgl_lahir ?? '');
                        $('#edit_alamat').val(res.alamat ?? '');
                        $('#edit_status').val(res.status ?? '1');
                        $('#edit_departemen_uuid').val(res.departemen_uuid ?? '');

                        $('#modal-edit').modal('show');
                    },
                    error: function(xhr) {
                        let message = "Gagal mengambil data edit";

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        showErrorToast(
                            message,
                            `${window.location.origin}/assets/static/icon/error.svg`
                        );
                    }
                });
            });

            $('#editForm').on('submit', function(e) {
                e.preventDefault();

                let uuid = $('#edit_uuid').val();
                let url = updateUrlTemplate.replace(':uuid', uuid);
                let formData = new FormData(this);
                let submitBtn = $('#editForm button[type="submit"]');

                // reset validation
                $('.is-invalid').removeClass('is-invalid');
                $('.text-danger').text('');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        submitBtn.prop('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                    Menyimpan...
                `);
                    },
                    success: function(res) {
                        $('#modal-edit').modal('hide');

                        showSuccessToast(
                            res.message ?? "Data berhasil diperbarui",
                            `${window.location.origin}/assets/static/icon/success.svg`
                        );

                        setTimeout(() => {
                            location.reload();
                        }, 800);
                    },
                    error: function(err) {
                        if (err.status === 422) {
                            let errors = err.responseJSON.errors;

                            $.each(errors, function(key, value) {
                                $(`#edit_${key}`).addClass('is-invalid');
                                $(`#edit_${key}_error`).text(value[0]);
                            });
                        } else {
                            let message = "Terjadi kesalahan saat memperbarui data";

                            if (err.responseJSON && err.responseJSON.message) {
                                message = err.responseJSON.message;
                            }

                            showErrorToast(
                                message,
                                `${window.location.origin}/assets/static/icon/error.svg`
                            );
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html(`Simpan`);
                    }
                });
            });

            $(document).on('click', '.delete-button', function() {
                let uuid = $(this).data('uuid');
                let url = destroyUrlTemplate.replace(':uuid', uuid);

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data karyawan akan dihapus permanen",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            beforeSend: () => {
                                $('.delete-button').prop('disabled', true);
                            },
                            success: function(res) {
                                showSuccessToast(
                                    res.message ?? 'Data berhasil dihapus',
                                    `${window.location.origin}/assets/static/icon/success.svg`
                                );

                                setTimeout(() => {
                                    location.reload();
                                }, 800);
                            },
                            error: function() {
                                showErrorToast(
                                    "Data Gagal Dihapus",
                                    `${window.location.origin}/assets/static/icon/error.svg`
                                );
                            },
                            complete: () => {
                                $('.delete-button').prop('disabled', false);
                            }
                        });
                    }
                });
            });

        });
    </script>
@endpush


@push('modal')

    {{-- import  --}}
    <div class="modal modal-blur fade" id="modal-import" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('karyawan.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data Karyawan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Upload File Excel</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted">
                                Format file: .xlsx, .xls, .csv
                            </small>
                        </div>

                        <div class="alert alert-info mb-0">
                            <strong>Catatan:</strong><br>
                            Pastikan format header file sesuai template:
                            <br>
                            <code>nip, nama_depan, nama_belakang, email, jabatan, no_hp, jenis_kelamin, tempat_lahir,
                                tgl_lahir, alamat, status, departemen</code>
                        </div>

                        <p class="mt-2">Belum mempunyai template import?<a
                                href="{{ asset('assets/static/template/template-import-karyawan.xlsx') }}" download>
                                Download template</a></p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-success ms-auto">
                            <i class="ti ti-upload me-1"></i> Import Sekarang
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- tambah --}}
    <div class="modal modal-blur fade" id="modal-tambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <form action="{{ route('karyawan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Karyawan Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">

                            {{-- Departemen --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Departemen</label>
                                <select class="form-select" name="departemen_uuid">
                                    <option value="">-- Pilih Departemen --</option>
                                    @foreach ($departemen as $dept)
                                        <option value="{{ $dept->uuid }}"
                                            {{ old('departemen_uuid') == $dept->uuid ? 'selected' : '' }}>
                                            {{ $dept->departemen }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('departemen_uuid')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- NIP --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP</label>
                                <input type="text" class="form-control" name="nip" value="{{ old('nip') }}"
                                    placeholder="Masukkan NIP">
                                @error('nip')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Jabatan --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan</label>
                                <input type="text" class="form-control" name="jabatan" value="{{ old('jabatan') }}"
                                    placeholder="Masukkan jabatan">
                                @error('jabatan')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                                    placeholder="Masukkan email" required>
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Nama Depan --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Depan</label>
                                <input type="text" class="form-control" name="nama_depan"
                                    value="{{ old('nama_depan') }}" placeholder="Masukkan nama depan" required>
                                @error('nama_depan')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Nama Belakang --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Belakang</label>
                                <input type="text" class="form-control" name="nama_belakang"
                                    value="{{ old('nama_belakang') }}" placeholder="Masukkan nama belakang">
                                @error('nama_belakang')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Jenis Kelamin --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select class="form-select" name="jenis_kelamin">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="laki-laki" {{ old('jenis_kelamin') == 'laki-laki' ? 'selected' : '' }}>
                                        Laki-laki
                                    </option>
                                    <option value="perempuan" {{ old('jenis_kelamin') == 'perempuan' ? 'selected' : '' }}>
                                        Perempuan
                                    </option>
                                </select>
                                @error('jenis_kelamin')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- No HP --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No HP</label>
                                <input type="text" class="form-control" name="no_hp" value="{{ old('no_hp') }}"
                                    placeholder="Masukkan nomor HP">
                                @error('no_hp')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Tempat Lahir --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control" name="tempat_lahir"
                                    value="{{ old('tempat_lahir') }}" placeholder="Masukkan tempat lahir">
                                @error('tempat_lahir')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Tanggal Lahir --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" name="tgl_lahir"
                                    value="{{ old('tgl_lahir') }}">
                                @error('tgl_lahir')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Aktif
                                    </option>
                                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Nonaktif
                                    </option>
                                </select>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Alamat --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="alamat" rows="3" placeholder="Masukkan alamat">{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-primary ms-auto">
                            Simpan
                        </button>

                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- edit --}}
    <div class="modal modal-blur fade" id="modal-edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <form id="editForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                <input type="hidden" id="edit_uuid" name="uuid">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Karyawan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            {{-- Avatar --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Foto / Avatar</label>
                                <input type="file" id="edit_avatar" class="form-control" name="avatar"
                                    accept="image/*">
                                <small class="text-danger" id="edit_avatar_error"></small>
                            </div>

                            {{-- Departemen --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Departemen</label>
                                <select id="edit_departemen_uuid" name="departemen_uuid" class="form-select">
                                    <option value="">-- Pilih Departemen --</option>
                                    @foreach ($departemen as $dept)
                                        <option value="{{ $dept->uuid }}">{{ $dept->departemen }}</option>
                                    @endforeach
                                </select>
                                <small class="text-danger" id="edit_departemen_uuid_error"></small>
                            </div>

                            {{-- NIP --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP</label>
                                <input type="text" id="edit_nip" name="nip" class="form-control">
                                <small class="text-danger" id="edit_nip_error"></small>
                            </div>

                            {{-- jabatan --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jabatan</label>
                                <input type="text" id="edit_jabatan" name="jabatan" class="form-control">
                                <small class="text-danger" id="edit_jabatan_error"></small>
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" id="edit_email" name="email" class="form-control" required>
                                <small class="text-danger" id="edit_email_error"></small>
                            </div>

                            {{-- Nama Depan --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Depan</label>
                                <input type="text" id="edit_nama_depan" name="nama_depan" class="form-control"
                                    required>
                                <small class="text-danger" id="edit_nama_depan_error"></small>
                            </div>

                            {{-- Nama Belakang --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Belakang</label>
                                <input type="text" id="edit_nama_belakang" name="nama_belakang" class="form-control">
                                <small class="text-danger" id="edit_nama_belakang_error"></small>
                            </div>

                            {{-- Jenis Kelamin --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select id="edit_jenis_kelamin" name="jenis_kelamin" class="form-select">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="laki-laki">Laki-laki</option>
                                    <option value="perempuan">Perempuan</option>
                                </select>
                                <small class="text-danger" id="edit_jenis_kelamin_error"></small>
                            </div>

                            {{-- No HP --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">No HP</label>
                                <input type="text" id="edit_no_hp" name="no_hp" class="form-control">
                                <small class="text-danger" id="edit_no_hp_error"></small>
                            </div>

                            {{-- Tempat Lahir --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" id="edit_tempat_lahir" name="tempat_lahir" class="form-control">
                                <small class="text-danger" id="edit_tempat_lahir_error"></small>
                            </div>

                            {{-- Tanggal Lahir --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" id="edit_tgl_lahir" name="tgl_lahir" class="form-control">
                                <small class="text-danger" id="edit_tgl_lahir_error"></small>
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select id="edit_status" name="status" class="form-select">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                                <small class="text-danger" id="edit_status_error"></small>
                            </div>

                            {{-- Alamat --}}
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea id="edit_alamat" name="alamat" class="form-control" rows="3"></textarea>
                                <small class="text-danger" id="edit_alamat_error"></small>
                            </div>


                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary ms-auto">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- detail --}}
    <div class="modal modal-blur fade" id="modal-detail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-3 text-center">
                            <span id="detail_avatar" class="avatar avatar-2xl rounded-circle"
                                style="background-image: url('{{ asset('assets/static/avatars/default.jpg') }}'); width: 120px; height: 120px;">
                            </span>
                        </div>

                        <div class="col-md-3 text-center">
                            <img id="detail_qr_code" src="{{ asset('assets/static/avatars/default.jpg') }}"
                                alt="QR Code" style="width: 120px; height: 120px; object-fit: contain;">
                        </div>

                        <div class="col-md-4">
                            <h2 class="mb-1" id="detail_nama_lengkap">-</h2>
                            <div class="text-secondary mb-2" id="detail_departemen">-</div>
                            <div class="text-secondary mb-2" id="detail_jabatan">-</div>

                        </div>
                    </div>

                    <div class="hr-text">Informasi Karyawan</div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-label">NIP</div>
                            <div class="form-control-plaintext" id="detail_nip">-</div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-label">Email</div>
                            <div class="form-control-plaintext" id="detail_email">-</div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-label">No HP</div>
                            <div class="form-control-plaintext" id="detail_no_hp">-</div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-label">Jenis Kelamin</div>
                            <div class="form-control-plaintext" id="detail_jenis_kelamin">-</div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-label">Tempat Lahir</div>
                            <div class="form-control-plaintext" id="detail_tempat_lahir">-</div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-label">Tanggal Lahir</div>
                            <div class="form-control-plaintext" id="detail_tgl_lahir">-</div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-label">Alamat</div>
                            <div class="form-control-plaintext" id="detail_alamat">-</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush
