@extends('layouts.app')

@section('title')
    Posisi
@endsection

@section('header')
    Posisi
@endsection

@section('sub header')
    Posisi
@endsection

@section('content')
    <div class="card">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table w-100" id="datatable">

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Posisi</th>
                            <th>Posisi</th>
                            <th>Jumlah Karyawan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody></tbody>

                </table>

            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {

            let datatable = $('#datatable').DataTable({
                processing: true,
                serverSide: true,

                ajax: {
                    url: "{{ route('posisi.getAllPosisi') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },

                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: "5%"
                    },
                    {
                        data: 'kode_posisi',
                        name: 'kode_posisi'
                    },
                    {
                        data: 'nama_posisi',
                        name: 'nama_posisi'
                    },
                    {
                        data: 'karyawans_count',
                        name: 'karyawans_count',
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: "20%"
                    }
                ]
            });

            $('#refreshData').click(async () => {
                $('#refreshData').attr('disabled', true);

                await refreshData(datatable);

                $('#refreshData').attr('disabled', false);
            });

            async function refreshData(table) {
                await new Promise((resolve) => {
                    table.ajax.reload(resolve);
                });
            }

            $('#datatable').on('click', '.delete-button', function() {

                let table = $('#datatable').DataTable();

                let row = table.row($(this).closest('tr'));

                let data = row.data();

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: 'Data posisi akan dihapus permanen',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {

                    if (result.isConfirmed) {

                        $.ajax({
                            url: `/posisi/${data.uuid}`,
                            type: "DELETE",

                            beforeSend: () => {
                                $('.delete-button').prop('disabled', true);
                            },

                            success: function() {

                                refreshData(datatable);

                                showSuccessToast(
                                    "Data Berhasil Dihapus",
                                    `${window.location.origin}/assets/static/icon/success.svg`
                                );
                            },

                            error: function(xhr) {

                                showErrorToast(
                                    xhr.responseJSON?.message ??
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

            $('#datatable').on('click', '.edit-button', function() {

                let table = $('#datatable').DataTable();

                let row = table.row($(this).closest('tr'));

                let data = row.data();

                $('#edit_uuid').val(data.uuid);

                $('#edit_kode_posisi').val(data.kode_posisi);

                $('#edit_nama_posisi').val(data.nama_posisi);

                $('#modal-edit').modal('show');

            });

            $('#editForm').on('submit', function(e) {

                e.preventDefault();

                let uuid = $('#edit_uuid').val();

                let formData = $(this).serialize();

                $.ajax({
                    url: `/posisi/${uuid}`,
                    method: 'PUT',
                    data: formData,

                    success: function(res) {

                        refreshData(datatable);

                        showSuccessToast(
                            res.message ?? 'Posisi berhasil diperbarui',
                            `${window.location.origin}/assets/static/icon/success.svg`
                        );

                        $('#modal-edit').modal('hide');
                    },

                    error: function(err) {

                        if (err.status === 422) {

                            let errors = err.responseJSON.errors;

                            $.each(errors, function(key, value) {

                                $(`#edit_${key}`).addClass('is-invalid');

                                $(`#edit_${key}_error`).text(value[0]);

                            });

                            return;
                        }

                        showErrorToast(
                            err.responseJSON?.message ?? 'Terjadi kesalahan.',
                            `${window.location.origin}/assets/static/icon/error.svg`
                        );
                    }
                });

            });

        });
    </script>
@endpush

@push('modal')

    <div class="modal modal-blur fade" id="modal-tambah" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg" role="document">

            <div class="modal-content">

                <form action="{{ route('posisi.store') }}" method="POST">

                    @csrf

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Posisi Baru
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-2">

                            <label class="form-label">
                                Kode Posisi
                            </label>

                            <input type="text" class="form-control" name="kode_posisi" value="{{ old('kode_posisi') }}"
                                placeholder="Masukan kode posisi" required>

                            @error('kode_posisi')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror

                        </div>


                        <div class="mb-2">

                            <label class="form-label">
                                Posisi
                            </label>

                            <input type="text" class="form-control" name="nama_posisi" value="{{ old('nama_posisi') }}"
                                placeholder="Masukan posisi" required>

                            @error('nama_posisi')
                                <small class="text-danger">
                                    {{ $message }}
                                </small>
                            @enderror

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


    <div class="modal modal-blur fade" id="modal-edit" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg" role="document">

            <form id="editForm" method="POST">

                @csrf
                @method('PUT')

                <input type="hidden" id="edit_uuid" name="uuid">

                <div class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Edit Posisi
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                    </div>

                    <div class="modal-body">

                        <div class="mb-3">

                            <label class="form-label">
                                Kode Posisi
                            </label>

                            <input type="text" id="edit_kode_posisi" name="kode_posisi" class="form-control" required>

                            <small class="text-danger" id="edit_kode_posisi_error"></small>

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Posisi
                            </label>

                            <input type="text" id="edit_nama_posisi" name="nama_posisi" class="form-control" required>

                            <small class="text-danger" id="edit_nama_posisi_error"></small>

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

@endpush
