@extends('layouts.app')

@section('title')
    Departemen
@endsection

@section('header')
    Departemen
@endsection

@section('sub header')
    Departemen & Divisi
@endsection

@section('content')

    <div class="card">

        <div class="card-body">

            <div class="d-flex justify-content-end mb-3">

            </div>

            <div class="table-responsive">

                <table
                    class="table w-100"
                    id="datatable"
                >

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Departemen</th>
                            <th>Departemen</th>
                            <th>Divisi</th>
                            <th>Jumlah Divisi</th>
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
                'X-CSRF-TOKEN':
                    $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {

            const editUrlTemplate = @json(
                route('departemen.edit', ':uuid')
            );

            const updateUrlTemplate = @json(
                route('departemen.update', ':uuid')
            );

            const destroyUrlTemplate = @json(
                route('departemen.destroy', ':uuid')
            );


            const datatable =
                $('#datatable').DataTable({

                    processing: true,

                    serverSide: true,

                    ajax: {
                        url:
                            "{{ route('departemen.getAllDepartemen') }}",

                        type:
                            "POST",

                        error: function(xhr) {

                            console.error(
                                'DataTables Status:',
                                xhr.status
                            );

                            console.error(
                                'DataTables Response:',
                                xhr.responseText
                            );

                            showErrorToast(
                                'Gagal mengambil data departemen.',
                                `${window.location.origin}/assets/static/icon/error.svg`
                            );
                        }
                    },

                    columns: [
                        {
                            data:
                                'DT_RowIndex',

                            name:
                                'DT_RowIndex',

                            orderable:
                                false,

                            searchable:
                                false,

                            width:
                                '5%'
                        },

                        {
                            data:
                                'kode_departemen',

                            name:
                                'kode_departemen'
                        },

                        {
                            data:
                                'departemen',

                            name:
                                'departemen'
                        },

                        {
                            data:
                                'divisi',

                            name:
                                'divisi',

                            orderable:
                                false,

                            searchable:
                                false
                        },

                        {
                            data:
                                'jumlah_divisi',

                            name:
                                'jumlah_divisi',

                            orderable:
                                false,

                            searchable:
                                false
                        },

                        {
                            data:
                                'action',

                            name:
                                'action',

                            orderable:
                                false,

                            searchable:
                                false,

                            width:
                                '15%'
                        }
                    ]
                });


            function refreshData() {

                datatable
                    .ajax
                    .reload(
                        null,
                        false
                    );

            }


            function escapeHtml(value) {

                if (
                    value === null ||
                    value === undefined
                ) {
                    return '';
                }

                return String(value)
                    .replace(
                        /&/g,
                        '&amp;'
                    )
                    .replace(
                        /</g,
                        '&lt;'
                    )
                    .replace(
                        />/g,
                        '&gt;'
                    )
                    .replace(
                        /"/g,
                        '&quot;'
                    )
                    .replace(
                        /'/g,
                        '&#039;'
                    );
            }


            $('#datatable').on(
                'click',
                '.delete-button',
                function() {

                    const row =
                        datatable.row(
                            $(this).closest('tr')
                        );

                    const data =
                        row.data();


                    Swal.fire({
                        title:
                            'Yakin ingin menghapus?',

                        text:
                            'Departemen beserta seluruh divisinya akan dihapus.',

                        icon:
                            'question',

                        showCancelButton:
                            true,

                        confirmButtonText:
                            'Hapus',

                        cancelButtonText:
                            'Batal'

                    }).then(
                        function(result) {

                            if (
                                !result.isConfirmed
                            ) {
                                return;
                            }


                            $.ajax({
                                url:
                                    destroyUrlTemplate.replace(
                                        ':uuid',
                                        data.uuid
                                    ),

                                type:
                                    'DELETE',

                                beforeSend:
                                    function() {

                                        $('.delete-button')
                                            .prop(
                                                'disabled',
                                                true
                                            );
                                    },

                                success:
                                    function(res) {

                                        refreshData();

                                        showSuccessToast(
                                            res.message ??
                                            'Data berhasil dihapus.',
                                            `${window.location.origin}/assets/static/icon/success.svg`
                                        );
                                    },

                                error:
                                    function(xhr) {

                                        showErrorToast(
                                            xhr.responseJSON?.message ??
                                            'Data gagal dihapus.',
                                            `${window.location.origin}/assets/static/icon/error.svg`
                                        );
                                    },

                                complete:
                                    function() {

                                        $('.delete-button')
                                            .prop(
                                                'disabled',
                                                false
                                            );
                                    }
                            });

                        }
                    );

                }
            );


            function addEditDivisiRow(
                uuid = '',
                namaDivisi = ''
            ) {

                const index =
                    $('#edit-divisi-wrapper .divisi-row')
                        .length;


                const html = `
                    <div class="input-group mb-2 divisi-row">

                        <input
                            type="hidden"
                            name="divisi[${index}][uuid]"
                            value="${escapeHtml(uuid)}"
                        >

                        <input
                            type="text"
                            name="divisi[${index}][nama_divisi]"
                            class="form-control"
                            value="${escapeHtml(namaDivisi)}"
                            placeholder="Nama divisi"
                            required
                        >

                        <button
                            type="button"
                            class="btn btn-outline-danger remove-edit-divisi"
                        >
                            <i class="ti ti-trash"></i>
                        </button>

                    </div>
                `;


                $('#edit-divisi-wrapper')
                    .append(
                        html
                    );
            }


            function reindexEditDivisi() {

                $('#edit-divisi-wrapper .divisi-row')
                    .each(
                        function(index) {

                            $(this)
                                .find(
                                    'input[type="hidden"]'
                                )
                                .attr(
                                    'name',
                                    `divisi[${index}][uuid]`
                                );


                            $(this)
                                .find(
                                    'input[type="text"]'
                                )
                                .attr(
                                    'name',
                                    `divisi[${index}][nama_divisi]`
                                );

                        }
                    );
            }


            $('#datatable').on(
                'click',
                '.edit-button',
                function() {

                    const row =
                        datatable.row(
                            $(this).closest('tr')
                        );

                    const data =
                        row.data();


                    $('.is-invalid')
                        .removeClass(
                            'is-invalid'
                        );


                    $('#edit_kode_departemen_error')
                        .text('');

                    $('#edit_departemen_error')
                        .text('');

                    $('#edit_divisi_error')
                        .text('');


                    $('#edit-divisi-wrapper')
                        .empty();


                    $.ajax({
                        url:
                            editUrlTemplate.replace(
                                ':uuid',
                                data.uuid
                            ),

                        type:
                            'GET',

                        success:
                            function(res) {

                                $('#edit_uuid')
                                    .val(
                                        res.uuid
                                    );


                                $('#edit_kode_departemen')
                                    .val(
                                        res.kode_departemen
                                    );


                                $('#edit_departemen')
                                    .val(
                                        res.departemen
                                    );


                                if (
                                    res.divisis &&
                                    res.divisis.length > 0
                                ) {

                                    res.divisis
                                        .forEach(
                                            function(divisi) {

                                                addEditDivisiRow(
                                                    divisi.uuid,
                                                    divisi.nama_divisi
                                                );

                                            }
                                        );

                                } else {

                                    addEditDivisiRow();

                                }


                                $('#modal-edit')
                                    .modal(
                                        'show'
                                    );
                            },

                        error:
                            function(xhr) {

                                showErrorToast(
                                    xhr.responseJSON?.message ??
                                    'Gagal mengambil data.',
                                    `${window.location.origin}/assets/static/icon/error.svg`
                                );
                            }
                    });

                }
            );


            $('#edit-add-divisi')
                .on(
                    'click',
                    function() {

                        addEditDivisiRow();

                    }
                );


            $(document).on(
                'click',
                '.remove-edit-divisi',
                function() {

                    const rows =
                        $('#edit-divisi-wrapper .divisi-row');


                    if (
                        rows.length <= 1
                    ) {

                        const row =
                            $(this)
                                .closest(
                                    '.divisi-row'
                                );


                        row.find(
                            'input[type="text"]'
                        ).val('');


                        row.find(
                            'input[type="hidden"]'
                        ).val('');


                        return;
                    }


                    $(this)
                        .closest(
                            '.divisi-row'
                        )
                        .remove();


                    reindexEditDivisi();

                }
            );


            $('#editForm')
                .on(
                    'submit',
                    function(e) {

                        e.preventDefault();


                        const uuid =
                            $('#edit_uuid')
                                .val();


                        reindexEditDivisi();


                        $('.is-invalid')
                            .removeClass(
                                'is-invalid'
                            );


                        $('#edit_kode_departemen_error')
                            .text('');

                        $('#edit_departemen_error')
                            .text('');

                        $('#edit_divisi_error')
                            .text('');


                        const submitButton =
                            $('#editForm button[type="submit"]');


                        $.ajax({
                            url:
                                updateUrlTemplate.replace(
                                    ':uuid',
                                    uuid
                                ),

                            type:
                                'POST',

                            data:
                                $(this)
                                    .serialize(),

                            beforeSend:
                                function() {

                                    submitButton
                                        .prop(
                                            'disabled',
                                            true
                                        )
                                        .html(`
                                            <span
                                                class="spinner-border spinner-border-sm me-2"
                                            ></span>

                                            Menyimpan...
                                        `);
                                },

                            success:
                                function(res) {

                                    $('#modal-edit')
                                        .modal(
                                            'hide'
                                        );


                                    showSuccessToast(
                                        res.message ??
                                        'Data berhasil diperbarui.',
                                        `${window.location.origin}/assets/static/icon/success.svg`
                                    );


                                    refreshData();

                                },

                            error:
                                function(xhr) {

                                    if (
                                        xhr.status === 422
                                    ) {

                                        const errors =
                                            xhr.responseJSON
                                                .errors ?? {};


                                        $.each(
                                            errors,

                                            function(
                                                key,
                                                value
                                            ) {

                                                if (
                                                    key ===
                                                    'kode_departemen'
                                                ) {

                                                    $('#edit_kode_departemen')
                                                        .addClass(
                                                            'is-invalid'
                                                        );


                                                    $('#edit_kode_departemen_error')
                                                        .text(
                                                            value[0]
                                                        );

                                                }


                                                if (
                                                    key ===
                                                    'departemen'
                                                ) {

                                                    $('#edit_departemen')
                                                        .addClass(
                                                            'is-invalid'
                                                        );


                                                    $('#edit_departemen_error')
                                                        .text(
                                                            value[0]
                                                        );

                                                }


                                                if (
                                                    key.startsWith(
                                                        'divisi'
                                                    )
                                                ) {

                                                    $('#edit_divisi_error')
                                                        .text(
                                                            value[0]
                                                        );

                                                }

                                            }
                                        );


                                        return;
                                    }


                                    showErrorToast(
                                        xhr.responseJSON?.message ??
                                        'Terjadi kesalahan.',
                                        `${window.location.origin}/assets/static/icon/error.svg`
                                    );
                                },

                            complete:
                                function() {

                                    submitButton
                                        .prop(
                                            'disabled',
                                            false
                                        )
                                        .html(
                                            'Simpan'
                                        );
                                }
                        });

                    }
                );

        });
    </script>


    <script>
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const wrapper =
                    document.getElementById(
                        'divisi-wrapper'
                    );


                const addButton =
                    document.getElementById(
                        'add-divisi'
                    );


                if (
                    !wrapper ||
                    !addButton
                ) {
                    return;
                }


                addButton.addEventListener(
                    'click',
                    function() {

                        const row =
                            document.createElement(
                                'div'
                            );


                        row.className =
                            'input-group mb-2 divisi-row';


                        row.innerHTML = `
                            <input
                                type="text"
                                name="divisi[]"
                                class="form-control"
                                placeholder="Nama divisi"
                                required
                            >

                            <button
                                type="button"
                                class="btn btn-outline-danger remove-divisi"
                            >
                                <i class="ti ti-trash"></i>
                            </button>
                        `;


                        wrapper.appendChild(
                            row
                        );

                    }
                );


                wrapper.addEventListener(
                    'click',
                    function(event) {

                        const button =
                            event.target.closest(
                                '.remove-divisi'
                            );


                        if (!button) {
                            return;
                        }


                        const rows =
                            wrapper.querySelectorAll(
                                '.divisi-row'
                            );


                        if (
                            rows.length <= 1
                        ) {

                            rows[0]
                                .querySelector(
                                    'input'
                                )
                                .value = '';

                            return;
                        }


                        button
                            .closest(
                                '.divisi-row'
                            )
                            .remove();

                    }
                );

            }
        );
    </script>

@endpush


@push('modal')

    <div
        class="modal modal-blur fade"
        id="modal-tambah"
        tabindex="-1"
        aria-hidden="true"
    >

        <div
            class="modal-dialog modal-lg"
            role="document"
        >

            <div class="modal-content">

                <form
                    action="{{ route('departemen.store') }}"
                    method="POST"
                    id="departemenForm"
                >

                    @csrf


                    <div class="modal-header">

                        <h5 class="modal-title">
                            Departemen Baru
                        </h5>


                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                        ></button>

                    </div>


                    <div class="modal-body">


                        <div class="mb-3">

                            <label class="form-label">
                                Kode Departemen
                            </label>


                            <input
                                type="text"
                                name="kode_departemen"
                                class="form-control"
                                value="{{ old('kode_departemen') }}"
                                placeholder="Masukan kode departemen"
                                required
                            >


                            @error('kode_departemen')

                                <small class="text-danger">
                                    {{ $message }}
                                </small>

                            @enderror

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Nama Departemen
                            </label>


                            <input
                                type="text"
                                name="departemen"
                                class="form-control"
                                value="{{ old('departemen') }}"
                                placeholder="Masukan nama departemen"
                                required
                            >


                            @error('departemen')

                                <small class="text-danger">
                                    {{ $message }}
                                </small>

                            @enderror

                        </div>


                        <div class="mb-3">

                            <div
                                class="d-flex justify-content-between align-items-center mb-2"
                            >

                                <label
                                    class="form-label mb-0"
                                >
                                    Divisi
                                </label>


                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary"
                                    id="add-divisi"
                                >
                                    <i class="ti ti-plus me-1"></i>

                                    Tambah Divisi
                                </button>

                            </div>


                            <div id="divisi-wrapper">


                                @if (old('divisi'))


                                    @foreach (old('divisi') as $divisi)


                                        <div
                                            class="input-group mb-2 divisi-row"
                                        >

                                            <input
                                                type="text"
                                                name="divisi[]"
                                                class="form-control"
                                                value="{{ $divisi }}"
                                                placeholder="Nama divisi"
                                                required
                                            >


                                            <button
                                                type="button"
                                                class="btn btn-outline-danger remove-divisi"
                                            >
                                                <i class="ti ti-trash"></i>
                                            </button>

                                        </div>


                                    @endforeach


                                @else


                                    <div
                                        class="input-group mb-2 divisi-row"
                                    >

                                        <input
                                            type="text"
                                            name="divisi[]"
                                            class="form-control"
                                            placeholder="Nama divisi"
                                            required
                                        >


                                        <button
                                            type="button"
                                            class="btn btn-outline-danger remove-divisi"
                                        >
                                            <i class="ti ti-trash"></i>
                                        </button>

                                    </div>


                                @endif

                            </div>


                            @error('divisi')

                                <small class="text-danger">
                                    {{ $message }}
                                </small>

                            @enderror


                            @error('divisi.*')

                                <small class="text-danger">
                                    {{ $message }}
                                </small>

                            @enderror

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-outline-danger"
                            data-bs-dismiss="modal"
                        >
                            Batal
                        </button>


                        <button
                            type="submit"
                            class="btn btn-primary ms-auto"
                        >
                            Simpan
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    <div
        class="modal modal-blur fade"
        id="modal-edit"
        tabindex="-1"
        aria-hidden="true"
    >

        <div
            class="modal-dialog modal-lg"
            role="document"
        >

            <form id="editForm">

                @csrf
                @method('PUT')


                <input
                    type="hidden"
                    id="edit_uuid"
                    name="uuid"
                >


                <div class="modal-content">


                    <div class="modal-header">

                        <h5 class="modal-title">
                            Edit Departemen & Divisi
                        </h5>


                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                        ></button>

                    </div>


                    <div class="modal-body">


                        <div class="mb-3">

                            <label class="form-label">
                                Kode Departemen
                            </label>


                            <input
                                type="text"
                                id="edit_kode_departemen"
                                name="kode_departemen"
                                class="form-control"
                                required
                            >


                            <small
                                class="text-danger"
                                id="edit_kode_departemen_error"
                            ></small>

                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Nama Departemen
                            </label>


                            <input
                                type="text"
                                id="edit_departemen"
                                name="departemen"
                                class="form-control"
                                required
                            >


                            <small
                                class="text-danger"
                                id="edit_departemen_error"
                            ></small>

                        </div>


                        <div class="mb-3">


                            <div
                                class="d-flex justify-content-between align-items-center mb-2"
                            >

                                <label
                                    class="form-label mb-0"
                                >
                                    Divisi
                                </label>


                                <button
                                    type="button"
                                    id="edit-add-divisi"
                                    class="btn btn-sm btn-primary"
                                >
                                    <i class="ti ti-plus me-1"></i>

                                    Tambah Divisi
                                </button>

                            </div>


                            <div
                                id="edit-divisi-wrapper"
                            ></div>


                            <small
                                class="text-danger"
                                id="edit_divisi_error"
                            ></small>

                        </div>


                    </div>


                    <div class="modal-footer">


                        <button
                            type="button"
                            class="btn btn-outline-danger"
                            data-bs-dismiss="modal"
                        >
                            Batal
                        </button>


                        <button
                            type="submit"
                            class="btn btn-primary ms-auto"
                        >
                            Simpan
                        </button>


                    </div>


                </div>

            </form>

        </div>

    </div>

@endpush