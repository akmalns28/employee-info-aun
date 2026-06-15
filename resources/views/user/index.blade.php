@extends('layouts.app')
@section('title')
    User
@endsection
@section('header')
    User
@endsection
@section('sub header')
    User
@endsection

@section('content')
    <div class="card">
        <div class="card-body pt-2">
            <div class="table-responsive">
                <table class="table" id="datatable" style="width:100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
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
                    url: "{{ route('user.getAllUser') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        width: "5%",
                    },
                    {
                        data: 'nama_depan',
                        name: 'nama_depan',
                        render: function(data, type, row) {
                            let namaBelakang = row.nama_belakang ? row.nama_belakang : '';
                            return data + ' ' + namaBelakang;
                        }
                    },
                    {
                        data: 'email',
                        name: 'email',
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

            //delete
            $('#datatable').on('click', '.delete-button', function() {

                let table = $('#datatable').DataTable();
                let data = table.row($(this).closest('tr')).data();

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data user akan dihapus permanen",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {

                    if (result.isConfirmed) {

                        $.ajax({
                            url: '/user/' + data.uuid,
                            type: 'DELETE',
                            beforeSend: () => {
                                $('.delete-button').prop('disabled', true);
                            },
                            success: function(res) {

                                refreshData(datatable);

                                showSuccessToast(
                                    res.message ?? 'User berhasil dihapus',
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


            //edit
            $(document).ready(function() {
                let table = $('#datatable').DataTable();

                $('#datatable').on('click', '.edit-button', function() {
                    let data = table.row($(this).closest('tr')).data();

                    $.get("{{ url('/user') }}/" + data.uuid + "/edit", function(user) {
                        openEditModal(user);
                    });
                });

                $('#edit_avatar').on('change', function() {
                    const file = this.files[0];

                    if (!file) {
                        return;
                    }

                    const reader = new FileReader();

                    reader.onload = function(e) {
                        $('#edit_avatar_preview').attr('src', e.target.result);
                    };

                    reader.readAsDataURL(file);
                });

                $('#editCheckPermissionAll').on('change', function() {
                    $('.permission-checkbox:not(.role-permission)')
                        .prop('checked', this.checked);
                    $('.group-check').prop('checked', this.checked);
                });

                $(document).on('change', '.group-check', function() {
                    let groupClass = $(this).data('group');

                    $('.' + groupClass + ' .permission-checkbox:not(.role-permission)')
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

                const rolesData = @json($roles->load('permissions'));

                function applyRolePermissions(roleName) {
                    $('.permission-checkbox')
                        .prop('checked', false)
                        .prop('disabled', false)
                        .removeClass('role-permission');

                    let role = rolesData.find(r => r.name === roleName);

                    if (role && role.permissions) {
                        role.permissions.forEach(function(permission) {
                            $('.permission-checkbox[value="' + permission.name + '"]')
                                .prop('checked', true)
                                .prop('disabled', true)
                                .addClass('role-permission');
                        });
                    }
                }

                $('#edit_role').on('change', function() {
                    applyRolePermissions($(this).val());
                    updateEditGroupChecks();
                    updateEditAllCheck();
                });

                function openEditModal(user) {
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').text('');

                    $('#edit_uuid').val(user.uuid);
                    $('#edit_nama_depan').val(user.nama_depan);
                    $('#edit_nama_belakang').val(user.nama_belakang);
                    $('#edit_email').val(user.email);

                    $('#edit_avatar_preview').attr(
                        'src',
                        user.avatar ?
                        (user.avatar.includes('http') ? user.avatar : '/storage/' + user.avatar) :
                        'https://ui-avatars.com/api/?name=' +
                        encodeURIComponent((user.nama_depan ?? '') + ' ' + (user.nama_belakang ?? '')) +
                        '&background=1e40af&color=fff&size=256&rounded=true&bold=true'
                    );

                    $('#edit_avatar').val('');

                    let roleName = user.roles && user.roles.length > 0 ?
                        user.roles[0].name :
                        '';

                    $('#edit_role').val(roleName);

                    applyRolePermissions(roleName);

                    if (user.permissions && user.permissions.length > 0) {
                        user.permissions.forEach(function(permission) {
                            let value = typeof permission === 'object' ?
                                permission.name :
                                permission;

                            $('.permission-checkbox[value="' + value + '"]:not(.role-permission)')
                                .prop('checked', true);
                        });
                    }

                    updateEditGroupChecks();
                    updateEditAllCheck();

                    $('#modal-edit').modal('show');
                }

                // Submit Form
                $('#editForm').on('submit', function(e) {
                    e.preventDefault();
                    let uuid = $('#edit_uuid').val();
                    let formData = new FormData(this);

                    $.ajax({
                        url: '/user/' + uuid,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            refreshData(datatable);
                            $('#modal-edit').modal('hide');
                            showSuccessToast(
                                "Data Berhasil Diubah",
                                `${window.location.origin}/assets/static/icon/success.svg`
                            );
                        },
                        error: function(err) {
                            if (err.status === 422) {
                                let errors = err.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    $(`#edit_${key}`).addClass('is-invalid');
                                    $(`#error_${key}`).text(value[0]);
                                });
                            }
                            showErrorToast(
                                "Data Gagal Diubah",
                                `${window.location.origin}/assets/static/icon/error.svg`
                            );
                        }
                    });
                });
            });

        });
    </script>
@endpush


@push('modal')
    <div class="modal modal-blur fade" id="modal-edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="editForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_uuid" name="uuid">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="col-md-12">
                            <div class="card-header mb-4">
                                <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a href="#tabs-dataDiri-5" class="nav-link active" data-bs-toggle="tab"
                                            aria-selected="true" role="tab">Data Diri</a>
                                    </li>
                                    @haspermission('permission.edit')
                                        <li class="nav-item" role="presentation">
                                            <a href="#tabs-permission-5" class="nav-link" data-bs-toggle="tab"
                                                aria-selected="false" role="tab" tabindex="-1">Permission</a>
                                        </li>
                                    @endhaspermission
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane active show" id="tabs-dataDiri-5" role="tabpanel">
                                        <div>
                                            <div class="mb-3">
                                                <div class="mt-2 text-center">
                                                    <img id="edit_avatar_preview"
                                                        class="avatar avatar-xl rounded-circle">
                                                </div>
                                                <label class="form-label">Avatar</label>
                                                <input type="file" id="edit_avatar" name="avatar" class="form-control"
                                                    accept="image/*">
                                                <div class="invalid-feedback" id="error_avatar"></div>

                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Nama Depan</label>
                                                <input type="text" id="edit_nama_depan" name="nama_depan"
                                                    class="form-control">
                                                <div class="invalid-feedback" id="error_nama_depan"></div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Nama Belakang</label>
                                                <input type="text" id="edit_nama_belakang" name="nama_belakang"
                                                    class="form-control">
                                                <div class="invalid-feedback" id="error_nama_belakang"></div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" id="edit_email" name="email" class="form-control">
                                                <div class="invalid-feedback" id="error_email"></div>
                                            </div>
                                        </div>
                                    </div>

                                    @haspermission('permission.edit')
                                        <div class="tab-pane" id="tabs-permission-5" role="tabpanel">
                                            <div>
                                                {{-- role --}}
                                                <div class="col-12 mb-3">
                                                    <div class="form-group">
                                                        <label for="edit_role">Role</label>
                                                        <select id="edit_role" name="role" class="form-select">
                                                            <option value="">-- Pilih Role --</option>
                                                            @foreach ($roles as $role)
                                                                <option value="{{ $role->name }}">
                                                                    {{ Str::title($role->name) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <small class="text-danger" id="error_role"></small>
                                                    </div>
                                                </div>
                                                <!-- Permissions -->
                                                <div class="col-12">
                                                    <!-- CHECK ALL -->
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input"
                                                            id="editCheckPermissionAll">
                                                        <label class="form-check-label">All</label>
                                                    </div>

                                                    <hr>

                                                    @php $i = 1; @endphp
                                                    @foreach ($permission_groups as $group)
                                                        <div class="d-flex mb-3">

                                                            <!-- GROUP -->
                                                            <div class="col-3">
                                                                <div class="form-check">
                                                                    <input type="checkbox"
                                                                        class="form-check-input group-check"
                                                                        data-group="group-{{ $i }}">
                                                                    <label class="form-check-label">
                                                                        {{ Str::title($group->name) }}
                                                                    </label>
                                                                </div>
                                                            </div>

                                                            <!-- PERMISSIONS -->
                                                            <div
                                                                class="d-flex flex-wrap gap-3 col-9 group-{{ $i }}">
                                                                @php
                                                                    $permissions = App\Http\Controllers\UserController::getpermissionsByGroupName(
                                                                        $group->name,
                                                                    );
                                                                @endphp

                                                                @foreach ($permissions as $permission)
                                                                    <div class="form-check form-switch" style="width:100px;">
                                                                        <input type="checkbox"
                                                                            class="form-check-input permission-checkbox"
                                                                            name="permissions[]"
                                                                            value="{{ $permission->name }}">
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
                                        </div>
                                    @endhaspermission

                                </div>
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
@endpush
