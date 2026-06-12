@extends('layouts.app')
@section('title')
    Departemen
@endsection
@section('header')
    Departemen
@endsection
@section('sub header')
    Departemen
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table w-100" id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Departemen</th>
                            <th>Departemen</th>
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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Bootstrap 5 styling -->
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
                    url: "{{ route('departemen.getAllDepartemen') }}",
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
                        data: 'kode_departemen',
                        name: 'kode_departemen'
                    },
                    {
                        data: 'departemen',
                        name: 'departemen'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: "20%"
                    },
                ]
            });

            $('#refreshData').click(async () => {
                $('#refreshData').attr('disabled', true)
                await refreshData(datatable)
                $('#refreshData').attr('disabled', false)
            })

            async function refreshData(table) {
                await new Promise((resolve) => {
                    table.ajax.reload(resolve)
                })
            }

            $('#datatable').on('click', '.delete-button', function() {

                let table = $('#datatable').DataTable();
                let row = table.row($(this).closest('tr'));
                let data = row.data();

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Semua data yang berkaitan akan ikut terhapus",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {

                    if (result.isConfirmed) {

                        $.ajax({
                            url: `/departemen/${data.uuid}`,
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
                            error: function(xhr, status, errors) {
                                var errors = xhr.responseJSON.errors;
                                btn.removeAttr('disabled').text('Hapus')
                                showErrorToast("Data Gagal Dihapus",
                                    `${window.location.origin}/assets/static/icon/error.svg`
                                )
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

                console.log(data); // debug jika perlu

                $('#edit_uuid').val(data.uuid);
                $('#edit_kode_departemen').val(data.kode_departemen);
                $('#edit_departemen').val(data.departemen);

                $('#modal-edit').modal('show');
            });

            $('#editForm').on('submit', function(e) {
                e.preventDefault();

                let uuid = $('#edit_uuid').val();
                let formData = $(this).serialize();

                $.ajax({
                    url: `/departemen/${uuid}`,
                    method: 'PUT',
                    data: formData,
                    success: function(res) {

                        refreshData(datatable);

                        showSuccessToast(
                            res.message ?? 'Departemen berhasil diperbarui',
                            `${window.location.origin}/assets/static/icon/success.svg`
                        );

                        $('#modal-edit').modal('hide');
                    },
                    error: function(err) {
                        console.log(err);
                        alert('Terjadi kesalahan.');
                    }
                });
            });

        });
    </script>
@endpush


@push('modal')
    {{-- tambah --}}
    <div class="modal modal-blur fade" id="modal-tambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('departemen.store') }}" method="POST">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Departemen Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <div class="mb-2">
                            <label class="form-label">Kode Departemen</label>
                            <input type="text" class="form-control" name="kode_departemen"
                                value="{{ old('kode_departemen') }}" placeholder="Masukan kode departemen" required>
                            @error('kode_departemen')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Departemen</label>
                            <input type="text" class="form-control" name="departemen" value="{{ old('departemen') }}"
                                placeholder="Masukan departemen" required>
                            @error('departemen')
                                <small class="text-danger">{{ $message }}</small>
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

    {{-- edit --}}
    <div class="modal modal-blur fade" id="modal-edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit_uuid" name="uuid">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Departemen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <!-- Kode Departemen -->
                        <div class="mb-3">
                            <label class="form-label">Kode Departemen</label>
                            <input type="text" id="edit_kode_departemen" name="kode_departemen" class="form-control"
                                required>
                            <small class="text-danger" id="edit_kode_departemen_error"></small>
                        </div>

                        <!-- Nama Departemen -->
                        <div class="mb-3">
                            <label class="form-label">Departemen</label>
                            <input type="text" id="edit_departemen" name="departemen" class="form-control" required>
                            <small class="text-danger" id="edit_departemen_error"></small>
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
