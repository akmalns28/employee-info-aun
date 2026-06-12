@extends('layouts.app')
@section('title')
    Role
@endsection
@section('header')
    Role
@endsection
@section('sub header')
    Role
@endsection

@section('content')
    <div class="card">
        <div class="card-body pt-2">
            <div class="table-responsive">
                <table class="table" id="datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
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
                    url: "{{ route('role.getAllRole') }}",
                    type: "POST"
                },
                order: [
                    [0, 'DESC']
                ],
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: "5%"
                    },
                    {
                        data: 'name',
                        name: 'name'
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
                await datatable.ajax.reload();
                $('#refreshData').attr('disabled', false);
            });

            $('#datatable').on('click', '.delete-button', function() {

                let table = $('#datatable').DataTable();
                let data = table.row($(this).closest('tr')).data();

                const dataId = data.id; // 🔥 ambil dari row
                const btn = $(this);

                if (!dataId) {
                    console.error('ID tidak ditemukan dari row DataTable');
                    return;
                }

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

                    if (!result.isConfirmed) return;

                    const url = "{{ route('role.destroy', ':id') }}"
                        .replace(':id', dataId);

                    $.ajax({
                        url: url,
                        type: "DELETE",
                        success: () => {
                            table.ajax.reload(null, false);
                            showSuccessToast(
                                "Data Berhasil Dihapus",
                                `${window.location.origin}/assets/static/icon/success.svg`
                            );
                        },
                        error: () => {
                            showErrorToast(
                                "Data Gagal Dihapus",
                                `${window.location.origin}/assets/static/icon/error.svg`
                            );
                        }
                    });

                });
            });

            $('#datatable').on('click', '.edit-button', function() {

                let table = $('#datatable').DataTable();
                let data = table.row($(this).closest('tr')).data();

                $.get("{{ url('/role') }}/" + data.id + "/edit", function(res) {
                    openEditModal(res);
                });
            });

            $('#editForm').on('submit', function(e) {
                e.preventDefault();

                let id = $('#edit_id').val();
                let url = "{{ route('role.update', ':id') }}".replace(':id', id);

                $.ajax({
                    url: url,
                    type: 'POST', // spoof PUT
                    data: $(this).serialize(),
                    success: function(res) {

                        $('#modal-edit').modal('hide');
                        $('#datatable').DataTable().ajax.reload(null, false);

                        showSuccessToast(
                            res.message ?? 'Data berhasil diperbarui',
                            `${window.location.origin}/assets/static/icon/success.svg`
                        );
                    },
                    error: function() {
                        showErrorToast(
                            "Update gagal",
                            `${window.location.origin}/assets/static/icon/error.svg`
                        );
                    }
                });
            });

        });

        function openEditModal(role) {

            $('#edit_id').val(role.id);
            $('#edit_name').val(role.name);

            // reset semua checkbox
            $('.permission-checkbox').prop('checked', false);
            $('.group-check').prop('checked', false);
            $('#editCheckPermissionAll').prop('checked', false);

            // centang dari server
            if (role.permissions && role.permissions.length > 0) {
                role.permissions.forEach(function(perm) {

                    let value = typeof perm === 'object' ?
                        perm.name :
                        perm;

                    $('.permission-checkbox[value="' + value + '"]')
                        .prop('checked', true);
                });
            }

            updateEditGroupChecks();
            $('#modal-edit').modal('show');
        }

        $('#editCheckPermissionAll').on('change', function() {
            $('.permission-checkbox').prop('checked', this.checked);
            $('.group-check').prop('checked', this.checked);
        });

        $(document).on('change', '.group-check', function() {
            let groupClass = $(this).data('group');

            $('.' + groupClass + ' .permission-checkbox')
                .prop('checked', this.checked);

            updateEditAllCheck();
        });

        $(document).on('change', '.permission-checkbox', function() {
            updateEditGroupChecks();
            updateEditAllCheck();
        });

        function updateEditGroupChecks() {
            $('.group-check').each(function() {

                let groupClass = $(this).data('group');
                let total = $('.' + groupClass + ' .permission-checkbox').length;
                let checked = $('.' + groupClass + ' .permission-checkbox:checked').length;

                $(this).prop('checked', total === checked);
            });
        }

        function updateEditAllCheck() {
            let total = $('.permission-checkbox').length;
            let checked = $('.permission-checkbox:checked').length;

            $('#editCheckPermissionAll').prop('checked', total === checked);
        }
    </script>
    <script>
        $("#checkPermissionAll").click(function() {
            if ($(this).is(':checked')) {
                $('input[type=checkbox]').prop('checked', true);
            } else {
                $('input[type=checkbox]').prop('checked', false);
            }
        });

        function checkPermissionByGroup(groupClass, groupCheckbox) {
            const isChecked = groupCheckbox.checked;
            document.querySelectorAll(`.${groupClass} input[type="checkbox"]`).forEach(function(checkbox) {
                checkbox.checked = isChecked;
            });
        }


        function checkSinglePermission(groupClassName, groupID, countTotalPermission) {
            const classCheckbox = $('.' + groupClassName + ' input');
            const groupIDCheckBox = $("#" + groupID);

            if ($('.' + groupClassName + ' input:checked').length == countTotalPermission) {
                groupIDCheckBox.prop('checked', true);
            } else {
                groupIDCheckBox.prop('checked', false);
            }
            implementAllChecked();
        }

        function implementAllChecked() {
            const countPermissions = {{ count($all_permissions) }};
            const countPermissionGroups = {{ count($permission_groups) }};

            if ($('input[type="checkbox"]:checked').length >= (countPermissions + countPermissionGroups)) {
                $("#checkPermissionAll").prop('checked', true);
            } else {
                $("#checkPermissionAll").prop('checked', false);
            }
        }
    </script>
@endpush

@push('modal')
    {{-- tambah --}}
    <div class="modal modal-blur fade" id="modal-tambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form action="{{ route('role.store') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Role Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label for="name">Role</label>
                                <input type="text" id="name" name="name" autofocus placeholder="Masukan role"
                                    value="{{ old('name') }}"
                                    class="form-control @error('name')
                                    is-invalid
                                @enderror">
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="name">Permissions</label>

                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="checkPermissionAll" value="1">
                                    <label class="form-check-label" for="checkPermissionAll">All</label>
                                </div>
                                <hr>
                                @php $i = 1; @endphp
                                @foreach ($permission_groups as $group)
                                    <div class="d-flex mb-3">
                                        <div class="col-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                    id="group-{{ $i }}-management"
                                                    onclick="checkPermissionByGroup('role-{{ $i }}-management-checkbox', this)">
                                                <label class="form-check-label" for="group-{{ $i }}-management">
                                                    {{ Str::title($group->name) }}
                                                </label>
                                            </div>

                                        </div>

                                        <div
                                            class="d-flex flex-wrap gap-3 col-9 role-{{ $i }}-management-checkbox">
                                            @php
                                                $permissions = App\Http\Controllers\RoleController::getpermissionsByGroupName(
                                                    $group->name,
                                                );
                                                $j = 1;
                                            @endphp
                                            @foreach ($permissions as $permission)
                                                <div class="form-check form-switch" style="width: 100px;">
                                                    <input type="checkbox" class="form-check-input" name="permissions[]"
                                                        id="checkPermission{{ $permission->id }}"
                                                        value="{{ $permission->name }}">
                                                    <label class="form-check-label"
                                                        for="checkPermission{{ $permission->id }}">
                                                        {{ Str::headline(Str::after($permission->name, '.')) }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @php  $i++; @endphp
                                @endforeach

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
                        <h5>Edit Role</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <!-- Role Name -->
                        <div class="col-12 mb-3">
                            <div class="form-group">
                                <label for="edit_name">Role</label>
                                <input type="text" id="edit_name" name="name" class="form-control"
                                    placeholder="Masukan role">
                            </div>
                        </div>

                        <!-- Permissions -->
                        <div class="col-12">
                            <label class="form-label">Permissions</label>

                            <!-- CHECK ALL -->
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="editCheckPermissionAll">
                                <label class="form-check-label">All</label>
                            </div>

                            <hr>

                            @php $i = 1; @endphp
                            @foreach ($permission_groups as $group)
                                <div class="d-flex mb-3">

                                    <!-- GROUP -->
                                    <div class="col-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input group-check"
                                                data-group="group-{{ $i }}">
                                            <label class="form-check-label">
                                                {{ Str::title($group->name) }}
                                            </label>
                                        </div>
                                    </div>

                                    <!-- PERMISSIONS -->
                                    <div class="d-flex flex-wrap gap-3 col-9 group-{{ $i }}">
                                        @php
                                            $permissions = App\Http\Controllers\RoleController::getpermissionsByGroupName(
                                                $group->name,
                                            );
                                        @endphp

                                        @foreach ($permissions as $permission)
                                            <div class="form-check form-switch" style="width:100px;">
                                                <input type="checkbox" class="form-check-input permission-checkbox"
                                                    name="permissions[]" value="{{ $permission->name }}">
                                                <label class="form-check-label">
                                                        {{ Str::headline(Str::after($permission->name, '.')) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                                @php $i++; @endphp
                            @endforeach

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
