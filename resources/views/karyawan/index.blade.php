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

@push('styles')
    <style>
        .export-check-wrapper {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 20;
            border-radius: 8px;
            padding: 6px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, .15)
        }

        .export-check-wrapper .form-check-input {
            cursor: pointer;
            margin: 0
        }
    </style>
@endpush

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
                    <label class="form-label">Divisi</label>
                    <select id="divisi_uuid" class="form-select">
                        <option value="all">Semua</option>
                        @foreach ($divisi as $item)
                            <option value="{{ $item->uuid }}">{{ $item->departemen?->departemen ?? '-' }} -
                                {{ $item->nama_divisi }}</option>
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

            <div class="col-md-2">
                <div class="mb-3">
                    <label class="form-label">Tampilkan</label>
                    <select id="per_page" class="form-select">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>
        </div>
    </form>

    @php
        $permissions = auth()->user()->getAllPermissions()->pluck('name');
        $hasExportQrCodePermission = $permissions->contains(fn($name) => str_contains($name, 'export qr code'));
        $hasDeletePermission = auth()->user()->can('karyawan.delete');
        $hasSelectionPermission = $hasExportQrCodePermission || $hasDeletePermission;
    @endphp

    @if ($hasExportQrCodePermission)
        <form action="{{ route('karyawan.exportQrCode') }}" method="POST" target="_blank" id="exportQrCodeForm">
            @csrf
            <input type="hidden" name="selected_nips" id="selected_nips">
        </form>
    @endif

    @if ($hasSelectionPermission)
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div class="fw-bold">Terpilih: <span id="selected-count">0</span></div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-primary" id="select-all-visible">
                    <i class="ti ti-checks me-1"></i>Pilih Semua yang Tampil
                </button>
                <button type="button" class="btn btn-outline-secondary" id="clear-selected">
                    <i class="ti ti-x me-1"></i>Hapus Pilihan
                </button>
                @if ($hasExportQrCodePermission)
                    <button type="submit" form="exportQrCodeForm" class="btn btn-primary" id="export-selected">
                        <i class="ti ti-qrcode me-1"></i>Export QR
                    </button>
                @endif
                @if ($hasDeletePermission)
                    <button type="button" class="btn btn-danger" id="delete-selected" disabled>
                        <i class="ti ti-trash me-1"></i>Hapus Terpilih
                    </button>
                @endif
            </div>
        </div>
    @endif

    <div id="karyawan-container">
        <div class="row row-cards">
            @forelse ($karyawans as $karyawan)
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 position-relative">
                        @if ($hasSelectionPermission)
                            <label class="form-check export-check-wrapper top-0 start-0 m-2">
                                <input class="form-check-input karyawan-checkbox" type="checkbox"
                                    value="{{ $karyawan->nip }}" data-uuid="{{ $karyawan->uuid }}"
                                    data-nama="{{ trim($karyawan->nama_depan . ' ' . $karyawan->nama_belakang) }}">
                            </label>
                        @endif

                        <div class="card-body p-3 text-center">
                            <img src="{{ $karyawan->avatar ? asset('storage/' . $karyawan->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($karyawan->nama_depan . ' ' . $karyawan->nama_belakang) . '&background=1e3a8a&color=ffffff&size=256&bold=true' }}"
                                class="avatar avatar-xl rounded-circle mb-2">
                            <h3 class="m-0">{{ $karyawan->nip }}</h3>
                            <h3 class="mb-1">{{ $karyawan->nama_depan }} {{ $karyawan->nama_belakang }}</h3>
                            <div class="text-secondary">{{ $karyawan->divisi?->departemen?->departemen ?? '-' }}</div>
                            <div class="text-secondary">{{ $karyawan->divisi?->nama_divisi ?? '-' }}</div>
                            <div class="text-secondary">{{ $karyawan->posisi?->nama_posisi ?? '-' }}</div>
                            <div class="mt-2">
                                @if ($karyawan->status == 1)
                                    <span class="badge bg-success-lt">Aktif</span>
                                @else
                                    <span class="badge bg-danger-lt">Nonaktif</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex">
                            @haspermission('karyawan.id card')
                                <a href="{{ route('karyawan.idCard', $karyawan->slug_nama) }}" target="_blank"
                                    class="card-btn d-flex align-items-center text-decoration-none">
                                    <i class="ti ti-id me-1"></i>ID Card
                                </a>
                            @endhaspermission

                            <div class="dropdown d-flex">
                                <a href="#"
                                    class="card-btn d-flex align-items-center justify-content-center text-decoration-none"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots"></i>
                                    <div class="fw-medium ms-1">Lainnya</div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    @haspermission('karyawan.detail')
                                        <button type="button" class="dropdown-item detail-button"
                                            data-nip="{{ $karyawan->nip }}">
                                            <i class="ti ti-info-circle me-2"></i>Detail
                                        </button>
                                    @endhaspermission
                                    @haspermission('karyawan.edit')
                                        <button type="button" class="dropdown-item edit-button"
                                            data-uuid="{{ $karyawan->uuid }}">
                                            <i class="ti ti-pencil me-2"></i>Edit
                                        </button>
                                    @endhaspermission
                                    @haspermission('karyawan.delete')
                                        <button type="button" class="dropdown-item delete-button"
                                            data-uuid="{{ $karyawan->uuid }}">
                                            <i class="ti ti-trash me-2"></i>Delete
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
                        <p class="empty-subtitle text-secondary">Silakan tambahkan data karyawan terlebih dahulu.</p>
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
                new bootstrap.Modal(document.getElementById('modal-import')).show();
            });
        </script>
    @endif

    <script>
        $(document).ready(function() {
            let selectedNips = [],
                selectedUuids = [],
                typingTimer = null;
            const bulkDeleteUrl = @json(route('karyawan.bulkDestroy'));

            function syncSelectedInput() {
                $('#selected_nips').val(JSON.stringify(selectedNips));
                $('#selected-count').text(selectedUuids.length);
                $('#delete-selected').prop('disabled', selectedUuids.length === 0);
            }

            function refreshCheckedState() {
                $('.karyawan-checkbox').each(function() {
                    const uuid = String($(this).data('uuid'));
                    $(this).prop('checked', selectedUuids.includes(uuid));
                });
                syncSelectedInput();
            }

            function loadData() {
                $.ajax({
                    url: "{{ route('karyawan.index') }}",
                    type: "GET",
                    data: {
                        search: $('#search').val().trim(),
                        divisi_uuid: $('#divisi_uuid').val(),
                        status: $('#status').val(),
                        per_page: $('#per_page').val()
                    },
                    success: function(response) {
                        const html = $(response).find('#karyawan-container').html();
                        $('#karyawan-container').html(html);
                        refreshCheckedState();
                    },
                    error: function(xhr) {
                        console.error('Filter karyawan gagal:', xhr.responseText);
                    }
                });
            }

            $(document).on('click', '.karyawan-checkbox', function(e) {
                e.stopPropagation();
            });

            $(document).on('change', '.karyawan-checkbox', function() {
                const nip = String($(this).val());
                const uuid = String($(this).data('uuid'));
                if ($(this).is(':checked')) {
                    if (!selectedNips.includes(nip)) selectedNips.push(nip);
                    if (!selectedUuids.includes(uuid)) selectedUuids.push(uuid);
                } else {
                    selectedNips = selectedNips.filter(item => item !== nip);
                    selectedUuids = selectedUuids.filter(item => item !== uuid);
                }
                syncSelectedInput();
            });

            $('#select-all-visible').on('click', function() {
                $('.karyawan-checkbox').each(function() {
                    const nip = String($(this).val());
                    const uuid = String($(this).data('uuid'));
                    if (!selectedNips.includes(nip)) selectedNips.push(nip);
                    if (!selectedUuids.includes(uuid)) selectedUuids.push(uuid);
                    $(this).prop('checked', true);
                });
                syncSelectedInput();
            });

            $('#clear-selected').on('click', function() {
                selectedNips = [];
                selectedUuids = [];
                $('.karyawan-checkbox').prop('checked', false);
                syncSelectedInput();
            });

            $(document).on('submit', '#exportQrCodeForm', function(e) {
                syncSelectedInput();
                if (selectedNips.length === 0) {
                    e.preventDefault();
                    showErrorToast('Pilih minimal satu karyawan untuk export',
                        `${window.location.origin}/assets/static/icon/error.svg`);
                }
            });

            $('#delete-selected').on('click', function() {
                if (selectedUuids.length === 0) return;
                const total = selectedUuids.length;
                Swal.fire({
                    title: `Hapus ${total} karyawan?`,
                    text: 'Semua data karyawan yang dipilih akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then(function(result) {
                    if (!result.isConfirmed) return;
                    const button = $('#delete-selected');
                    $.ajax({
                        url: bulkDeleteUrl,
                        type: 'DELETE',
                        data: {
                            uuids: selectedUuids
                        },
                        beforeSend: function() {
                            button.prop('disabled', true).html(
                                '<span class="spinner-border spinner-border-sm me-1"></span>Menghapus...'
                                );
                        },
                        success: function(res) {
                            selectedNips = [];
                            selectedUuids = [];
                            syncSelectedInput();
                            showSuccessToast(res.message ?? 'Data berhasil dihapus',
                                `${window.location.origin}/assets/static/icon/success.svg`
                                );
                            loadData();
                        },
                        error: function(xhr) {
                            showErrorToast(xhr.responseJSON?.message ??
                                'Gagal menghapus data karyawan',
                                `${window.location.origin}/assets/static/icon/error.svg`
                                );
                        },
                        complete: function() {
                            button.prop('disabled', selectedUuids.length === 0).html(
                                '<i class="ti ti-trash me-1"></i>Hapus Terpilih');
                        }
                    });
                });
            });

            $('#search').on('input', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(loadData, 500);
            });

            $('#divisi_uuid,#status,#per_page').on('change', loadData);
            refreshCheckedState();
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
                let nip = $(this).data('nip'),
                    url = showUrlTemplate.replace(':nip', nip);
                $.ajax({
                    url: url,
                    type: 'GET',
                    beforeSend: function() {
                        $('#detail_nama_lengkap').text('Loading...');
                        $('#modal-detail').modal('show');
                    },
                    success: function(res) {
                        let namaLengkap = `${res.nama_depan??''} ${res.nama_belakang??''}`
                        .trim();
                        $('#detail_avatar').css('background-image', `url('${res.avatar}')`);
                        $('#detail_qr_code').attr('src', res.qr_code ||
                            '{{ asset('assets/static/avatars/default.jpg') }}');
                        $('#detail_nama_lengkap').text(namaLengkap || '-');
                        $('#detail_departemen').text(res.departemen || '-');
                        $('#detail_divisi').text(res.divisi || '-');
                        $('#detail_posisi').text(res.posisi || '-');
                        $('#detail_status_badge').html(res.status == 1 ?
                            '<span class="badge bg-success-lt">Aktif</span>' :
                            '<span class="badge bg-danger-lt">Nonaktif</span>');
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
                    },
                    error: function() {
                        $('#modal-detail').modal('hide');
                        showErrorToast("Gagal mengambil detail karyawan",
                            `${window.location.origin}/assets/static/icon/error.svg`);
                    }
                });
            });

            $(document).on('click', '.edit-button', function() {
                let uuid = $(this).data('uuid'),
                    url = editUrlTemplate.replace(':uuid', uuid);
                $('.is-invalid').removeClass('is-invalid');
                $('.text-danger').text('');
                $('#edit_avatar').val('');
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(res) {
                        $('#edit_uuid').val(res.uuid ?? '');
                        $('#edit_nama_depan').val(res.nama_depan ?? '');
                        $('#edit_nama_belakang').val(res.nama_belakang ?? '');
                        $('#edit_email').val(res.email ?? '');
                        $('#edit_nip').val(res.nip ?? '');
                        $('#edit_no_hp').val(res.no_hp ?? '');
                        $('#edit_jenis_kelamin').val(res.jenis_kelamin ?? '');
                        $('#edit_tempat_lahir').val(res.tempat_lahir ?? '');
                        $('#edit_tgl_lahir').val(res.tgl_lahir ?? '');
                        $('#edit_alamat').val(res.alamat ?? '');
                        $('#edit_status').val(res.status ?? '1');
                        $('#edit_divisi_uuid').val(res.divisi_uuid ?? '');
                        $('#edit_uuid_posisi').val(res.uuid_posisi ?? '');
                        $('#modal-edit').modal('show');
                    },
                    error: function(xhr) {
                        showErrorToast(xhr.responseJSON?.message ?? "Gagal mengambil data edit",
                            `${window.location.origin}/assets/static/icon/error.svg`);
                    }
                });
            });

            $('#editForm').on('submit', function(e) {
                e.preventDefault();
                let uuid = $('#edit_uuid').val(),
                    url = updateUrlTemplate.replace(':uuid', uuid),
                    formData = new FormData(this),
                    submitBtn = $('#editForm button[type="submit"]');
                $('.is-invalid').removeClass('is-invalid');
                $('.text-danger').text('');
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        submitBtn.prop('disabled', true).html(
                            '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...'
                            );
                    },
                    success: function(res) {
                        $('#modal-edit').modal('hide');
                        showSuccessToast(res.message ?? "Data berhasil diperbarui",
                            `${window.location.origin}/assets/static/icon/success.svg`);
                        setTimeout(() => location.reload(), 800);
                    },
                    error: function(err) {
                        if (err.status === 422) {
                            $.each(err.responseJSON.errors, function(key, value) {
                                $(`#edit_${key}`).addClass('is-invalid');
                                $(`#edit_${key}_error`).text(value[0]);
                            });
                        } else {
                            showErrorToast(err.responseJSON?.message ??
                                "Terjadi kesalahan saat memperbarui data",
                                `${window.location.origin}/assets/static/icon/error.svg`);
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html('Simpan');
                    }
                });
            });

            $(document).on('click', '.delete-button', function() {
                let uuid = $(this).data('uuid'),
                    url = destroyUrlTemplate.replace(':uuid', uuid);
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: 'Data karyawan akan dihapus permanen',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            beforeSend: function() {
                                $('.delete-button').prop('disabled', true);
                            },
                            success: function(res) {
                                showSuccessToast(res.message ?? 'Data berhasil dihapus',
                                    `${window.location.origin}/assets/static/icon/success.svg`
                                    );
                                setTimeout(() => location.reload(), 800);
                            },
                            error: function(xhr) {
                                showErrorToast(xhr.responseJSON?.message ??
                                    'Data Gagal Dihapus',
                                    `${window.location.origin}/assets/static/icon/error.svg`
                                    );
                            },
                            complete: function() {
                                $('.delete-button').prop('disabled', false);
                            }
                        });
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formImport = document.getElementById('form-import-karyawan');
            const fileInput = document.getElementById('import-file');
            const btnTest = document.getElementById('btn-test-import');
            const btnImport = document.getElementById('btn-import-karyawan');
            const validationResult = document.getElementById('import-validation-result');
            const previewWrapper = document.getElementById('import-preview-wrapper');
            const previewBody = document.getElementById('import-preview-body');
            const testImportUrl = @json(route('karyawan.import.test'));
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fileInput.addEventListener('change', function() {
                btnImport.disabled = true;
                validationResult.innerHTML = '';
                validationResult.style.display = 'none';
                previewBody.innerHTML = '';
                previewWrapper.style.display = 'none';
            });

            btnTest.addEventListener('click', async function() {
                if (!fileInput.files || fileInput.files.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'File belum dipilih',
                        text: 'Silakan pilih file Excel terlebih dahulu.'
                    });
                    return;
                }

                const file = fileInput.files[0];
                const allowedExtensions = ['xlsx', 'xls', 'csv'];
                const extension = file.name.split('.').pop().toLowerCase();

                if (!allowedExtensions.includes(extension)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Format file tidak valid',
                        text: 'Gunakan file XLSX, XLS, atau CSV.'
                    });
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File terlalu besar',
                        text: 'Ukuran maksimal file adalah 5 MB.'
                    });
                    return;
                }

                const formData = new FormData();
                formData.append('file', file);
                btnTest.disabled = true;
                btnImport.disabled = true;
                btnTest.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-1"></span>Memvalidasi...';
                validationResult.style.display = 'none';
                previewWrapper.style.display = 'none';

                try {
                    const response = await fetch(testImportUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    });

                    const result = await response.json();
                    if (!response.ok) throw result;

                    renderValidationSummary(result);
                    renderPreview(result.preview || []);

                    if (result.success) {
                        btnImport.disabled = false;
                        Swal.fire({
                            icon: 'success',
                            title: 'Validasi Berhasil',
                            html: `<div class="text-start"><strong>${result.summary.valid}</strong> data valid.<br><strong>${result.summary.insert}</strong> data akan ditambahkan.<br><strong>${result.summary.update}</strong> data akan diperbarui.</div>`
                        });
                    } else {
                        btnImport.disabled = true;
                        Swal.fire({
                            icon: 'error',
                            title: 'Validasi Gagal',
                            html: `Ditemukan <strong>${result.summary.error}</strong> baris yang bermasalah.`
                        });
                    }
                } catch (error) {
                    btnImport.disabled = true;
                    let message = error.message || 'Terjadi kesalahan saat melakukan validasi.';
                    if (error.errors) message = Object.values(error.errors).flat().join('<br>');
                    validationResult.innerHTML =
                        `<div class="alert alert-danger"><strong>Validasi gagal.</strong><br>${message}</div>`;
                    validationResult.style.display = 'block';
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        html: message
                    });
                } finally {
                    btnTest.disabled = false;
                    btnTest.innerHTML = '<i class="ti ti-check me-1"></i>Tes / Validasi';
                }
            });

            function renderValidationSummary(result) {
                const summary = result.summary;
                const alertClass = result.success ? 'alert-success' : 'alert-danger';
                let html =
                    `<div class="alert ${alertClass}"><div class="fw-bold mb-3">${escapeHtml(result.message)}</div><div class="row g-2 text-center"><div class="col-4 col-md-2"><small class="text-muted">Total</small><div class="fs-3 fw-bold">${summary.total}</div></div><div class="col-4 col-md-2"><small class="text-muted">Valid</small><div class="fs-3 fw-bold text-success">${summary.valid}</div></div><div class="col-4 col-md-2"><small class="text-muted">Insert</small><div class="fs-3 fw-bold text-primary">${summary.insert}</div></div><div class="col-4 col-md-2"><small class="text-muted">Update</small><div class="fs-3 fw-bold text-warning">${summary.update}</div></div><div class="col-4 col-md-2"><small class="text-muted">Error</small><div class="fs-3 fw-bold text-danger">${summary.error}</div></div><div class="col-4 col-md-2"><small class="text-muted">Warning</small><div class="fs-3 fw-bold">${summary.warning}</div></div></div></div>`;

                if (result.errors?.length) {
                    html +=
                        '<div class="alert alert-danger"><strong>Data yang harus diperbaiki:</strong><hr class="my-2"><ul class="mb-0">';
                    result.errors.forEach(error => {
                        html +=
                            `<li class="mb-2"><strong>Baris ${error.row}</strong>${error.nip?' - NIP: '+escapeHtml(error.nip):''}${error.nama?' - '+escapeHtml(error.nama):''}<ul>`;
                        error.messages.forEach(message => html += `<li>${escapeHtml(message)}</li>`);
                        html += '</ul></li>';
                    });
                    html += '</ul></div>';
                }

                if (result.warnings?.length) {
                    html +=
                        '<div class="alert alert-warning"><strong>Peringatan:</strong><hr class="my-2"><ul class="mb-0">';
                    result.warnings.forEach(warning => html +=
                        `<li>Baris ${warning.row} - ${warning.messages.map(escapeHtml).join(', ')}</li>`);
                    html += '</ul></div>';
                }

                validationResult.innerHTML = html;
                validationResult.style.display = 'block';
            }

            function renderPreview(data) {
                previewBody.innerHTML = '';
                if (!data.length) {
                    previewWrapper.style.display = 'none';
                    return;
                }

                data.forEach(function(item) {
                    const tr = document.createElement('tr');
                    if (!item.valid) tr.classList.add('table-danger');

                    const actionBadge = item.action === 'INSERT' ?
                        '<span class="badge bg-success">INSERT</span>' :
                        '<span class="badge bg-warning text-dark">UPDATE</span>';

                    const statusBadge = item.valid ?
                        '<span class="badge bg-success">Valid</span>' :
                        '<span class="badge bg-danger">Error</span>';

                    tr.innerHTML =
                        `<td>${item.row}</td><td>${escapeHtml(item.nip)}</td><td>${escapeHtml(item.nama_lengkap)}</td><td>${escapeHtml(item.email)}</td><td>${escapeHtml(item.divisi)}</td><td>${escapeHtml(item.posisi)}</td><td>${actionBadge}</td><td>${statusBadge}</td>`;
                    previewBody.appendChild(tr);
                });

                previewWrapper.style.display = 'block';
            }

            formImport.addEventListener('submit', function(event) {
                event.preventDefault();

                if (btnImport.disabled) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Validasi diperlukan',
                        text: 'Klik Tes / Validasi terlebih dahulu.'
                    });
                    return;
                }

                Swal.fire({
                    icon: 'question',
                    title: 'Import data karyawan?',
                    text: 'Data akan ditambahkan atau diperbarui berdasarkan NIP.',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Import',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) formImport.submit();
                });
            });

            document.getElementById('modal-import').addEventListener('hidden.bs.modal', function() {
                formImport.reset();
                btnImport.disabled = true;
                validationResult.innerHTML = '';
                validationResult.style.display = 'none';
                previewBody.innerHTML = '';
                previewWrapper.style.display = 'none';
            });

            function escapeHtml(value) {
                if (value === null || value === undefined) return '';
                const div = document.createElement('div');
                div.textContent = String(value);
                return div.innerHTML;
            }
        });
    </script>
@endpush

@push('modal')

    <div class="modal modal-blur fade" id="modal-import" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form-import-karyawan" action="{{ route('karyawan.import') }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data Karyawan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Upload File Excel</label>
                            <input type="file" name="file" id="import-file" class="form-control"
                                accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted">Format file: .xlsx, .xls, .csv</small>
                        </div>
                        <div class="alert alert-info">
                            <strong>Catatan:</strong> Pastikan format header file sesuai template.
                        </div>
                        <p class="mb-3">
                            Belum mempunyai template import?
                            <a href="{{ asset('assets/static/template/template-import-karyawan.xlsx') }}"
                                download>Download template</a>
                        </p>
                        <div id="import-validation-result" style="display:none"></div>
                        <div id="import-preview-wrapper" class="table-responsive mt-3" style="display:none">
                            <h5 class="mb-2">Preview Import</h5>
                            <table class="table table-sm table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Baris</th>
                                        <th>NIP</th>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Divisi</th>
                                        <th>Posisi</th>
                                        <th>Action</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="import-preview-body"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Batal</button>
                        <button type="button" id="btn-test-import" class="btn btn-outline-primary">
                            <i class="ti ti-check me-1"></i>Tes / Validasi
                        </button>
                        <button type="submit" id="btn-import-karyawan" class="btn btn-success" disabled>
                            <i class="ti ti-upload me-1"></i>Import Sekarang
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Divisi</label>
                                <select class="form-select" name="divisi_uuid">
                                    <option value="">-- Pilih Divisi --</option>
                                    @foreach ($divisi as $item)
                                        <option value="{{ $item->uuid }}"
                                            {{ old('divisi_uuid') == $item->uuid ? 'selected' : '' }}>
                                            {{ $item->departemen?->departemen ?? '-' }} - {{ $item->nama_divisi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('divisi_uuid')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Posisi</label>
                                <select class="form-select" name="uuid_posisi">
                                    <option value="">-- Pilih Posisi --</option>
                                    @foreach ($posisi as $pos)
                                        <option value="{{ $pos->uuid }}"
                                            {{ old('uuid_posisi') == $pos->uuid ? 'selected' : '' }}>
                                            {{ $pos->nama_posisi }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('uuid_posisi')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP</label>
                                <input type="text" class="form-control" name="nip" value="{{ old('nip') }}"
                                    placeholder="Masukkan NIP">
                                @error('nip')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}"
                                    placeholder="Masukkan email" required>
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Depan</label>
                                <input type="text" class="form-control" name="nama_depan"
                                    value="{{ old('nama_depan') }}" placeholder="Masukkan nama depan" required>
                                @error('nama_depan')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Belakang</label>
                                <input type="text" class="form-control" name="nama_belakang"
                                    value="{{ old('nama_belakang') }}" placeholder="Masukkan nama belakang">
                                @error('nama_belakang')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select class="form-select" name="jenis_kelamin">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="laki-laki" {{ old('jenis_kelamin') == 'laki-laki' ? 'selected' : '' }}>
                                        Laki-laki</option>
                                    <option value="perempuan" {{ old('jenis_kelamin') == 'perempuan' ? 'selected' : '' }}>
                                        Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">No HP</label>
                                <input type="text" class="form-control" name="no_hp" value="{{ old('no_hp') }}"
                                    placeholder="Masukkan nomor HP">
                                @error('no_hp')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control" name="tempat_lahir"
                                    value="{{ old('tempat_lahir') }}" placeholder="Masukkan tempat lahir">
                                @error('tempat_lahir')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" class="form-control" name="tgl_lahir"
                                    value="{{ old('tgl_lahir') }}">
                                @error('tgl_lahir')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" name="status">
                                    <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

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
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary ms-auto">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Foto / Avatar</label>
                                <input type="file" id="edit_avatar" class="form-control" name="avatar"
                                    accept="image/*">
                                <small class="text-danger" id="edit_avatar_error"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Divisi</label>
                                <select id="edit_divisi_uuid" name="divisi_uuid" class="form-select">
                                    <option value="">-- Pilih Divisi --</option>
                                    @foreach ($divisi as $item)
                                        <option value="{{ $item->uuid }}">{{ $item->departemen?->departemen ?? '-' }} -
                                            {{ $item->nama_divisi }}</option>
                                    @endforeach
                                </select>
                                <small class="text-danger" id="edit_divisi_uuid_error"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Posisi</label>
                                <select id="edit_uuid_posisi" name="uuid_posisi" class="form-select">
                                    <option value="">-- Pilih Posisi --</option>
                                    @foreach ($posisi as $pos)
                                        <option value="{{ $pos->uuid }}">{{ $pos->nama_posisi }}</option>
                                    @endforeach
                                </select>
                                <small class="text-danger" id="edit_uuid_posisi_error"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIP</label>
                                <input type="text" id="edit_nip" name="nip" class="form-control">
                                <small class="text-danger" id="edit_nip_error"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" id="edit_email" name="email" class="form-control" required>
                                <small class="text-danger" id="edit_email_error"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Depan</label>
                                <input type="text" id="edit_nama_depan" name="nama_depan" class="form-control"
                                    required>
                                <small class="text-danger" id="edit_nama_depan_error"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Belakang</label>
                                <input type="text" id="edit_nama_belakang" name="nama_belakang" class="form-control">
                                <small class="text-danger" id="edit_nama_belakang_error"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select id="edit_jenis_kelamin" name="jenis_kelamin" class="form-select">
                                    <option value="">-- Pilih Jenis Kelamin --</option>
                                    <option value="laki-laki">Laki-laki</option>
                                    <option value="perempuan">Perempuan</option>
                                </select>
                                <small class="text-danger" id="edit_jenis_kelamin_error"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">No HP</label>
                                <input type="text" id="edit_no_hp" name="no_hp" class="form-control">
                                <small class="text-danger" id="edit_no_hp_error"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" id="edit_tempat_lahir" name="tempat_lahir" class="form-control">
                                <small class="text-danger" id="edit_tempat_lahir_error"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" id="edit_tgl_lahir" name="tgl_lahir" class="form-control">
                                <small class="text-danger" id="edit_tgl_lahir_error"></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select id="edit_status" name="status" class="form-select">
                                    <option value="1">Aktif</option>
                                    <option value="0">Nonaktif</option>
                                </select>
                                <small class="text-danger" id="edit_status_error"></small>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea id="edit_alamat" name="alamat" class="form-control" rows="3"></textarea>
                                <small class="text-danger" id="edit_alamat_error"></small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary ms-auto">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
                                style="background-image:url('{{ asset('assets/static/avatars/default.jpg') }}');width:120px;height:120px"></span>
                        </div>

                        <div class="col-md-3 text-center">
                            <img id="detail_qr_code" src="{{ asset('assets/static/avatars/default.jpg') }}"
                                alt="QR Code" style="width:120px;height:120px;object-fit:contain">
                        </div>

                        <div class="col-md-4">
                            <h2 class="mb-1" id="detail_nama_lengkap">-</h2>
                            <div class="text-secondary mb-2" id="detail_departemen">-</div>
                            <div class="text-secondary mb-2" id="detail_divisi">-</div>
                            <div class="text-secondary mb-2" id="detail_posisi">-</div>
                            <div id="detail_status_badge"></div>
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
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>

            </div>
        </div>
    </div>

@endpush
