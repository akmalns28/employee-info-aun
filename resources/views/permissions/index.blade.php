@extends('layouts.app')
@section('title')
    Permissions
@endsection
@section('header')
    Permissions
@endsection
@section('sub header')
    Permissions
@endsection

@section('content')
    <div class="card">
        <div class="card-body pt-2">
            <div class="table-responsive">
                <table class="table" id="datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Menu</th>
                            <th>Permission</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
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
        // Tambahkan event listener untuk tombol hapus bawaan
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove-permission')) {
                const wrapper = document.getElementById('permissions-wrapper');
                const inputGroup = e.target.parentElement;
                wrapper.removeChild(inputGroup);
            }
        });
    </script>

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
                    url: "{{ route('permissions.getAllPermissions') }}",
                    type: "POST"
                },
                order: ['1', 'DESC'],
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: "5%"
                    },
                    {
                        data: 'group_name',
                        name: 'group_name',
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        width: "20%",
                    }
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
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Semua data yang berkaitan akan ikut terhapus",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#233446',
                    cancelButtonColor: '#8592a3',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.value) {
                        const dataId = $(this).data('id'); // Mengambil data-id dari elemen
                        const urlTemplate =
                            "{{ route('permissions.destroy', ':data') }}"; // Template URL dengan placeholder
                        const bindUrl = urlTemplate.replace(':data',
                            dataId); // Menggantikan placeholder dengan dataId
                        const btn = $(this); // Menyimpan tombol yang diklik

                        $.ajax({
                            url: bindUrl,
                            type: "DELETE",
                            dataType: "JSON",
                            proccessData: false,
                            contentType: "application/json",
                            beforeSend: () => {
                                btn.attr('disabled', true).html(
                                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'
                                )
                            },
                            success: (response) => {
                                refreshData(datatable)
                                showSuccessToast("Data Berhasil Dihapus",
                                    `${window.location.origin}/assets/static/icon/success.svg`
                                )
                            },
                            error: function(xhr, status, errors) {
                                var errors = xhr.responseJSON.errors;
                                btn.removeAttr('disabled').text('Hapus')
                                showErrorToast("Data Gagal Dihapus",
                                    `${window.location.origin}/assets/static/icon/error.svg`
                                )
                            }
                        })
                    }
                })
            });

            $(document).on('click', '#add-permission', function() {

                $('#permissions-wrapper').append(`
        <div class="input-group mb-2 permission-item">
            <input type="text" name="permissions[]" 
                class="form-control"
                placeholder="contoh: blog.create">

            <button type="button" class="btn btn-danger remove-permission">
                ✕
            </button>
        </div>
    `);

            });

            // hapus field
            $(document).on('click', '.remove-permission', function() {
                $(this).closest('.permission-item').remove();
            });

            $('#datatable').on('click', '.edit-button', function() {

                let table = $('#datatable').DataTable();
                let data = table.row($(this).closest('tr')).data();

                console.log(data); // debug

                $('#edit_id').val(data.id);
                $('#modal-edit input[name="group_name"]').val(data.group_name);
                $('#modal-edit input[name="name"]').val(data.name);

                $('#modal-edit').modal('show');
            });

            $('#editForm').on('submit', function(e) {
                e.preventDefault();

                let id = $('#edit_id').val();

                $.ajax({
                    url: '/permissions/' + id,
                    type: 'POST', // Laravel spoofing PUT
                    data: $(this).serialize(),
                    success: function(res) {

                        $('#modal-edit').modal('hide');

                        $('#datatable').DataTable().ajax.reload(null, false);

                        showSuccessToast(
                            res.message ?? 'Data berhasil diperbarui',
                            `${window.location.origin}/assets/static/icon/success.svg`
                        );
                    },
                    error: function(xhr) {
                        showErrorToast("Data Gagal Dihapus",
                            `${window.location.origin}/assets/static/icon/error.svg`
                        )
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
            <form action="{{ route('permissions.store') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Permission Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="group_name">Group</label>
                                <input type="text" id="group_name" name="group_name" autofocus
                                    value="{{ old('group_name') }}" placeholder="Cnt: nama permission(Blog)"
                                    class="form-control @error('group_name')
                                    is-invalid
                                @enderror">
                                @error('group_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="permissions">Permissions</label>
                                <div id="permissions-wrapper">
                                    <div class="input-group mb-2">
                                        <input type="text"
                                            class="form-control @error('permissions.*') is-invalid @enderror"
                                            name="permissions[]" placeholder="Cnt: permission(blog.create)">
                                        <button type="button"
                                            class="btn btn-outline-danger remove-permission d-none">Hapus</button>
                                    </div>
                                    @error('permissions.*')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="add-permission">Tambah
                                    Field</button>
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

    {{-- edit --}}
    <div class="modal modal-blur fade" id="modal-edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="editForm">
                @csrf
                @method('PUT')

                <input type="hidden" id="edit_id" name="id">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Edit Permission Group</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="group_name">Group</label>
                                <input type="text" id="group_name" value="" name="group_name"
                                    placeholder="Cnt: nama permission(Blog)"
                                    class="form-control @error('group_name')
                                        is-invalid
                                    @enderror">
                                @error('group_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label for="permissions">Permissions</label>
                                <div id="permissions-wrapper">
                                    <input type="text" id="permissions" value="" name="name"
                                        placeholder="Cnt: permission(blog.create)"
                                        class="form-control mb-2 @error('name')
                                            is-invalid
                                        @enderror">
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endpush
