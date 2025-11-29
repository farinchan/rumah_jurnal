@extends('back.app')
@section('styles')
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
@endsection
@section('content')
    @php
        [$before, $after] = explode(' - ', $event->datetime);
        $date_before = \Carbon\Carbon::parse($before)->toDateTimeString();
        $date_after = \Carbon\Carbon::parse($after)->toDateTimeString();
        // dd($date_before, $date_after);
    @endphp
    <div id="kt_content_container" class=" container-xxl ">
        @include('back.pages.event.detail.header')
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <input type="text" data-kt-user-table-filter="search"
                            class="form-control form-control-solid w-250px ps-13" placeholder="Cari Pengguna" />
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">


                        <button class="btn btn-primary me-3" data-bs-toggle="modal" data-bs-target="#kt_modal_add_user">
                            <i class="ki-duotone ki-plus fs-2"></i>Tambah Peserta</button>

                        <div class="btn-group">
                            <button class="btn btn-secondary" data-bs-toggle="modal"
                                data-bs-target="#kt_modal_import_reviewer">
                                <i class="ki-duotone ki-file-down fs-2"></i>Import Reviewer</button>

                            <button class="btn btn-secondary" data-bs-toggle="modal"
                                data-bs-target="#kt_modal_import_editor">
                                <i class="ki-duotone ki-file-down fs-2"></i>Import Editor</button>
                        </div>

                        <div class="modal fade" tabindex="-1" id="kt_modal_add_user">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h3 class="modal-title">Tambah Peserta</h3>

                                        <!--begin::Close-->
                                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                                            data-bs-dismiss="modal" aria-label="Close">
                                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                                    class="path2"></span></i>
                                        </div>
                                        <!--end::Close-->
                                    </div>


                                    <form action="{{ route('back.event.detail.participant.store', $event->id) }}"
                                        method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="alert alert-info d-flex align-items-center p-5 mb-5">
                                                <i class="ki-duotone ki-information-2 fs-1 text-info me-3">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                                <div class="d-flex flex-column">
                                                    <h5 class="mb-1">Informasi Tambah Peserta</h5>
                                                    <span>
                                                        Jika Peserta sudah memiliki akun di sistem dengan email yang
                                                        sama, maka data peserta akan terhubung dengan akun tersebut.
                                                        <br />
                                                        Jika belum memiliki akun, maka sistem akan membuatkan akun baru
                                                        secara otomatis dengan password default
                                                        <strong>rumahjurnal123</strong>.
                                                        <br />
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mb-10">
                                                <label for="email" class="form-label">Nama Peserta</label>
                                                <input type="text" class="form-control form-control-solid" name="name"
                                                    placeholder="Masukkan nama peserta" required />
                                            </div>

                                            <div class="mb-10">
                                                <label for="email" class="form-label">Email Peserta</label>
                                                <input type="email" class="form-control form-control-solid" name="email"
                                                    placeholder="Masukkan email peserta" required />
                                            </div>
                                            <div class="mb-10">
                                                <label for="phone" class="form-label">No. Telepon Peserta</label>
                                                <input type="text" class="form-control form-control-solid" name="phone"
                                                    placeholder="Masukkan no. telepon peserta" required />

                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Import Reviewer -->
                        <div class="modal fade" tabindex="-1" id="kt_modal_import_reviewer">
                            <div class="modal-dialog mw-1000px">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h3 class="modal-title">Import Reviewer ke Peserta Event</h3>
                                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                                            data-bs-dismiss="modal" aria-label="Close">
                                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                                    class="path2"></span></i>
                                        </div>
                                    </div>

                                    <form action="{{ route('back.event.detail.participant.import-reviewer', $event->id) }}"
                                        method="POST" id="import_reviewer_form">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-10">
                                                <div class="d-flex justify-content-between align-items-center mb-5">
                                                    <label class="form-label">Pilih Reviewer yang akan diimport:</label>
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="select_all_reviewers" />
                                                        <label class="form-check-label" for="select_all_reviewers">
                                                            <strong>Pilih Semua</strong>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Search Box -->
                                                <div class="position-relative mb-5">
                                                    <div class="d-flex align-items-center position-relative">
                                                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5 text-muted">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        <input type="text" id="reviewer_search"
                                                               class="form-control form-control-solid w-100 ps-13 pe-10"
                                                               placeholder="Cari reviewer berdasarkan nama, email, atau afiliasi..." />
                                                        <button type="button" id="clear_reviewer_search"
                                                                class="btn btn-sm btn-icon btn-active-light-primary position-absolute end-0 me-2"
                                                                style="display: none;" title="Clear search">
                                                            <i class="ki-duotone ki-cross fs-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </button>
                                                    </div>
                                                    <small class="text-muted mt-1">Gunakan pencarian untuk memfilter data reviewer</small>
                                                </div>

                                                <div class="alert alert-info d-flex align-items-center p-5 mb-5">
                                                    <i class="ki-duotone ki-information-2 fs-1 text-info me-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                    <div class="d-flex flex-column">
                                                        <h5 class="mb-1">Informasi Import</h5>
                                                        <span>Data reviewer yang sudah terdaftar sebagai peserta akan
                                                            dilewati untuk menghindari duplikasi.</span>
                                                    </div>
                                                </div>

                                                <div class="card card-bordered">
                                                    <div class="card-body p-0"
                                                        style="max-height: 400px; overflow-y: auto;">
                                                        <div class="table-responsive">
                                                            <table
                                                                class="table table-striped table-row-bordered gy-5 gs-7">
                                                                <thead class="sticky-top bg-light">
                                                                    <tr class="fw-bold fs-6 text-gray-800">
                                                                        <th class="w-25px">
                                                                            <div
                                                                                class="form-check form-check-sm form-check-custom form-check-solid">
                                                                                <input class="form-check-input"
                                                                                    type="checkbox"
                                                                                    id="check_all_header" />
                                                                            </div>
                                                                        </th>
                                                                        <th>Nama</th>
                                                                        <th>Email</th>
                                                                        <th>Afiliasi</th>
                                                                        <th>No. Telepon</th>
                                                                        <th>Journal yang Dikelola</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="reviewer_list">
                                                                    <tr>
                                                                        <td colspan="6" class="text-center">
                                                                            <div class="spinner-border spinner-border-sm"
                                                                                role="status">
                                                                                <span
                                                                                    class="visually-hidden">Loading...</span>
                                                                            </div>
                                                                            Loading reviewers...
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light"
                                                data-bs-dismiss="modal">Tutup</button>
                                            <button type="submit" class="btn btn-success" id="submit_import">
                                                <i class="ki-duotone ki-file-down fs-2"></i>Import Reviewer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Import Editor -->
                        <div class="modal fade" tabindex="-1" id="kt_modal_import_editor">
                            <div class="modal-dialog mw-1000px">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h3 class="modal-title">Import Editor ke Peserta Event</h3>
                                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                                            data-bs-dismiss="modal" aria-label="Close">
                                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                                    class="path2"></span></i>
                                        </div>
                                    </div>

                                    <form action="{{ route('back.event.detail.participant.import-editor', $event->id) }}"
                                        method="POST" id="import_editor_form">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="mb-10">
                                                <div class="d-flex justify-content-between align-items-center mb-5">
                                                    <label class="form-label">Pilih Editor yang akan diimport:</label>
                                                    <div class="form-check form-check-custom form-check-solid">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="select_all_editors" />
                                                        <label class="form-check-label" for="select_all_editors">
                                                            <strong>Pilih Semua</strong>
                                                        </label>
                                                    </div>
                                                </div>

                                                <!-- Search Box -->
                                                <div class="position-relative mb-5">
                                                    <div class="d-flex align-items-center position-relative">
                                                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5 text-muted">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        <input type="text" id="editor_search"
                                                               class="form-control form-control-solid w-100 ps-13 pe-10"
                                                               placeholder="Cari editor berdasarkan nama, email, atau afiliasi..." />
                                                        <button type="button" id="clear_editor_search"
                                                                class="btn btn-sm btn-icon btn-active-light-primary position-absolute end-0 me-2"
                                                                style="display: none;" title="Clear search">
                                                            <i class="ki-duotone ki-cross fs-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                        </button>
                                                    </div>
                                                    <small class="text-muted mt-1">Gunakan pencarian untuk memfilter data editor</small>
                                                </div>

                                                <div class="alert alert-info d-flex align-items-center p-5 mb-5">
                                                    <i class="ki-duotone ki-information-2 fs-1 text-info me-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                    <div class="d-flex flex-column">
                                                        <h5 class="mb-1">Informasi Import</h5>
                                                        <span>Data editor yang sudah terdaftar sebagai peserta akan dilewati
                                                            untuk menghindari duplikasi.</span>
                                                    </div>
                                                </div>

                                                <div class="card card-bordered">
                                                    <div class="card-body p-0"
                                                        style="max-height: 400px; overflow-y: auto;">
                                                        <div class="table-responsive">
                                                            <table
                                                                class="table table-striped table-row-bordered gy-5 gs-7">
                                                                <thead class="sticky-top bg-light">
                                                                    <tr class="fw-bold fs-6 text-gray-800">
                                                                        <th class="w-25px">
                                                                            <div
                                                                                class="form-check form-check-sm form-check-custom form-check-solid">
                                                                                <input class="form-check-input"
                                                                                    type="checkbox"
                                                                                    id="check_all_editors_header" />
                                                                            </div>
                                                                        </th>
                                                                        <th>Nama</th>
                                                                        <th>Email</th>
                                                                        <th>Afiliasi</th>
                                                                        <th>No. Telepon</th>
                                                                        <th>Journal yang Dikelola</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="editor_list">
                                                                    <tr>
                                                                        <td colspan="6" class="text-center">
                                                                            <div class="spinner-border spinner-border-sm"
                                                                                role="status">
                                                                                <span
                                                                                    class="visually-hidden">Loading...</span>
                                                                            </div>
                                                                            Loading editors...
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light"
                                                data-bs-dismiss="modal">Tutup</button>
                                            <button type="submit" class="btn btn-warning" id="submit_import_editor">
                                                <i class="ki-duotone ki-file-down fs-2"></i>Import Editor
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="btn-group">
                            {{-- <a class="btn btn-secondary" href="#">
                                <i class="ki-duotone ki-file-up fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Export
                            </a> --}}
                        </div>
                    </div>
                    <div class="d-flex justify-content-end align-items-center d-none" {{-- data-kt-user-table-toolbar="selected" --}}>
                        <div class="fw-bold me-5">
                            <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected
                        </div>
                        <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete
                            Selected</button>
                    </div>

                </div>
            </div>
            <div class="card-body py-4">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">
                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                                        data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                                </div>
                            </th>
                            <th class="min-w-125px">Pengguna</th>
                            <th class="min-w-125px">Nama Terdaftar</th>
                            <th class="min-w-125px">Email Terdaftar</th>
                            <th class="min-w-125px">No.telp Terdaftar</th>
                            <th class="min-w-125px">Tanggal Mendaftar</th>
                            <th class="text-end min-w-150px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" value="1" />
                                    </div>
                                </td>
                                <td class="d-flex align-items-center">
                                    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                        <a href="#">
                                            @if ($user->user_id)
                                                <div class="symbol-label">
                                                    <img src="{{ $user->user->getPhoto() }}" alt="{{ $user->name }}"
                                                        width="50px" />
                                                </div>
                                            @else
                                                <div class="symbol-label"
                                                    style="background-color: #{{ substr(md5($user->email), 0, 6) }};">
                                                    <span
                                                        class="font-size-h5 font-weight-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                                </div>
                                            @endif
                                        </a>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <a href="#"
                                            class="text-gray-800 text-hover-primary mb-1">{{ $user->name }}</a>
                                        <span>{{ $user->email ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    {{ $user->name ?? '-' }}
                                </td>
                                <td>
                                    {{ $user->email ?? '-' }}
                                </td>
                                <td>
                                    {{ $user->phone ?? '-' }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d F Y H:i') }}
                                </td>

                                <td class="text-end">
                                    <a href="{{ route('event.eticket', $user->id) }}" target="_blank"
                                        class="btn btn-icon btn-light-info btn-sm me-1" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="E-Ticket">
                                        <i class="ki-duotone ki-tablet-book fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>

                                    </a>
                                    @if ($event->type != 'Rapat')
                                        <a href="{{ route('event.certificate', $user->id) }}" target="_blank"
                                            class="btn btn-icon btn-light-success btn-sm me-1" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Sertifikat">
                                            <i class="ki-duotone ki-document fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                        </a>
                                    @endif
                                    <a href="#" class="btn btn-icon btn-light-danger btn-sm me-1"
                                        data-bs-toggle="modal" data-bs-target="#kt_modal_delete_user{{ $user->id }}">
                                        <i class="ki-duotone ki-trash fs-2" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="Hapus peserta">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach ($users as $user)
        <div class="modal fade" id="kt_modal_delete_user{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="fw-bold">Hapus Pengguna</h2>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                    class="path2"></span></i>
                        </div>
                    </div>
                    <form class="form" method="POST"
                        action="{{ route('back.event.detail.participant.destroy', [$event->id, $user->id]) }}">
                        @method('DELETE')
                        @csrf
                        <div class="modal-body px-5">
                            <p class="">
                                Apakah Anda Yakin Ingin Menghapus peserta {{ $user->name }} ?
                            </p>
                            <p class="text-danger ">
                                <b>Warning!</b> Pengguna yang dihapus tidak dapat dikembalikan lagi, dan semua data yang
                                terkait dengan pengguna ini akan hilang.
                            </p>


                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus
                                Pengguna</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('scripts')
    <script src="{{ asset('back/js/custom/apps/user-management/users/list/table.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Load reviewers when modal is shown
            $('#kt_modal_import_reviewer').on('show.bs.modal', function() {
                loadReviewers();
            });

            // Reset search when reviewer modal is hidden
            $('#kt_modal_import_reviewer').on('hidden.bs.modal', function() {
                const searchInput = document.getElementById('reviewer_search');
                const clearButton = document.getElementById('clear_reviewer_search');
                if (searchInput) {
                    searchInput.value = '';
                }
                if (clearButton) {
                    clearButton.style.display = 'none';
                }
                // Show all rows when modal is closed
                const rows = document.querySelectorAll('#reviewer_list tr');
                rows.forEach(row => {
                    if (!row.classList.contains('no-results-row')) {
                        row.style.display = '';
                    } else {
                        row.remove();
                    }
                });
            });

            // Load editors when modal is shown
            $('#kt_modal_import_editor').on('show.bs.modal', function() {
                loadEditors();
            });

            // Reset search when modal is hidden
            $('#kt_modal_import_editor').on('hidden.bs.modal', function() {
                const searchInput = document.getElementById('editor_search');
                const clearButton = document.getElementById('clear_editor_search');
                if (searchInput) {
                    searchInput.value = '';
                }
                if (clearButton) {
                    clearButton.style.display = 'none';
                }
                // Show all rows when modal is closed
                const rows = document.querySelectorAll('#editor_list tr');
                rows.forEach(row => {
                    if (!row.classList.contains('no-results-row')) {
                        row.style.display = '';
                    } else {
                        row.remove();
                    }
                });
            });

            // Function to load reviewers
            function loadReviewers() {
                console.log('Starting to load reviewers...');
                fetch('{{ route('back.event.detail.participant.import-reviewer.modal', $event->id) }}')
                    .then(response => {
                        console.log('Response received:', response);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Reviewer data received:', data);
                        let reviewerList = '';

                        if (data.error) {
                            console.error('Server error:', data.error);
                            reviewerList = `
                                <tr>
                                    <td colspan="6" class="text-center text-danger">
                                        Error: ${data.error}
                                    </td>
                                </tr>
                            `;
                        } else if (data.reviewers && data.reviewers.length > 0) {
                            console.log('Processing', data.reviewers.length, 'reviewers');
                            data.reviewers.forEach(reviewer => {
                                reviewerList += `
                                    <tr>
                                        <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input reviewer-checkbox" type="checkbox"
                                                       name="reviewer_ids[]" value="${reviewer.id}" />
                                            </div>
                                        </td>
                                        <td class="fw-bold">${reviewer.name || '-'}</td>
                                        <td>${reviewer.email || '-'}</td>
                                        <td>${reviewer.affiliation || '-'}</td>
                                        <td>${reviewer.phone || '-'}</td>
                                        <td><small class="text-muted"> <ul>
                                            ${reviewer.journals.map(journal => `<li>${journal.title}</li>`).join('') || '<li>Tidak ada journal</li>'}
                                            </ul></small></td>
                                    </tr>
                                `;
                            });
                        } else {
                            console.log('No reviewers found');
                            reviewerList = `
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        Tidak ada data reviewer
                                    </td>
                                </tr>
                            `;
                        }

                        document.getElementById('reviewer_list').innerHTML = reviewerList;

                        // Reset checkboxes
                        document.getElementById('select_all_reviewers').checked = false;
                        document.getElementById('check_all_header').checked = false;

                        // Setup checkbox functionality
                        setupCheckboxes();

                        // Setup search functionality
                        setupReviewerSearch();
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        document.getElementById('reviewer_list').innerHTML = `
                            <tr>
                                <td colspan="6" class="text-center text-danger">
                                    Error loading data: ${error.message}
                                </td>
                            </tr>
                        `;
                    });
            }

            // Function to setup reviewer search
            function setupReviewerSearch() {
                const searchInput = document.getElementById('reviewer_search');
                const clearButton = document.getElementById('clear_reviewer_search');

                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase().trim();

                        // Show/hide clear button
                        if (clearButton) {
                            clearButton.style.display = searchTerm ? 'block' : 'none';
                        }

                        filterReviewers(searchTerm);
                    });
                }

                // Clear button functionality
                if (clearButton) {
                    clearButton.addEventListener('click', function() {
                        searchInput.value = '';
                        this.style.display = 'none';
                        filterReviewers('');
                    });
                }
            }

            // Function to filter reviewers based on search term
            function filterReviewers(searchTerm) {
                const rows = document.querySelectorAll('#reviewer_list tr');
                let visibleCount = 0;

                rows.forEach(row => {
                    // Skip if it's a loading/error/no-data row
                    if (row.children.length < 6 || row.classList.contains('no-results-row')) {
                        if (row.classList.contains('no-results-row')) {
                            row.remove();
                        }
                        return;
                    }

                    const name = row.children[1].textContent.toLowerCase();
                    const email = row.children[2].textContent.toLowerCase();
                    const affiliation = row.children[3].textContent.toLowerCase();
                    const phone = row.children[4].textContent.toLowerCase();

                    const isVisible = searchTerm === '' ||
                                    name.includes(searchTerm) ||
                                    email.includes(searchTerm) ||
                                    affiliation.includes(searchTerm) ||
                                    phone.includes(searchTerm);

                    if (isVisible) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                        // Uncheck hidden checkboxes
                        const checkbox = row.querySelector('.reviewer-checkbox');
                        if (checkbox) {
                            checkbox.checked = false;
                        }
                    }
                });

                // Update select all checkbox state
                updateReviewerSelectAllState();
                updateSubmitButton();

                // Show message if no results found
                if (visibleCount === 0 && searchTerm !== '') {
                    const noResultsRow = document.createElement('tr');
                    noResultsRow.className = 'no-results-row';
                    noResultsRow.innerHTML = `
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="ki-duotone ki-file-deleted fs-2x text-muted mb-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <br>
                            Tidak ada reviewer yang cocok dengan pencarian "<strong>${searchTerm}</strong>"
                            <br>
                            <small class="text-muted">Coba gunakan kata kunci yang berbeda</small>
                        </td>
                    `;
                    document.getElementById('reviewer_list').appendChild(noResultsRow);
                }
            }

            // Function to update reviewer select all state based on visible checkboxes
            function updateReviewerSelectAllState() {
                const selectAllMain = document.getElementById('select_all_reviewers');
                const selectAllHeader = document.getElementById('check_all_header');
                const visibleCheckboxes = Array.from(document.querySelectorAll('.reviewer-checkbox'))
                    .filter(checkbox => checkbox.closest('tr').style.display !== 'none');

                if (visibleCheckboxes.length === 0) {
                    selectAllMain.checked = false;
                    selectAllHeader.checked = false;
                    return;
                }

                const checkedVisibleCount = visibleCheckboxes.filter(cb => cb.checked).length;
                const allVisibleChecked = checkedVisibleCount === visibleCheckboxes.length;

                selectAllMain.checked = allVisibleChecked;
                selectAllHeader.checked = allVisibleChecked;
            }

            // Function to load editors
            function loadEditors() {
                console.log('Starting to load editors...');
                fetch('{{ route('back.event.detail.participant.import-editor.modal', $event->id) }}')
                    .then(response => {
                        console.log('Response received:', response);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Editor data received:', data);
                        let editorList = '';

                        if (data.error) {
                            console.error('Server error:', data.error);
                            editorList = `
                                <tr>
                                    <td colspan="6" class="text-center text-danger">
                                        Error: ${data.error}
                                    </td>
                                </tr>
                            `;
                        } else if (data.editors && data.editors.length > 0) {
                            console.log('Processing', data.editors.length, 'editors');
                            data.editors.forEach(editor => {
                                editorList += `
                                    <tr>
                                        <td>
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input editor-checkbox" type="checkbox"
                                                       name="editor_ids[]" value="${editor.id}" />
                                            </div>
                                        </td>
                                        <td class="fw-bold">${editor.name || '-'}</td>
                                        <td>${editor.email || '-'}</td>
                                        <td>${editor.affiliation || '-'}</td>
                                        <td>${editor.phone || '-'}</td>
                                        <td><small class="text-muted"> <ul>
                                            ${editor.journals.map(journal => `<li>${journal.title}</li>`).join('') || '<li>Tidak ada journal</li>'}
                                            </ul></small></td>
                                    </tr>
                                `;
                            });
                        } else {
                            console.log('No editors found');
                            editorList = `
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        Tidak ada data editor
                                    </td>
                                </tr>
                            `;
                        }

                        document.getElementById('editor_list').innerHTML = editorList;

                        // Reset checkboxes
                        document.getElementById('select_all_editors').checked = false;
                        document.getElementById('check_all_editors_header').checked = false;

                        // Setup checkbox functionality
                        setupEditorCheckboxes();

                        // Setup search functionality
                        setupEditorSearch();
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        document.getElementById('editor_list').innerHTML = `
                            <tr>
                                <td colspan="6" class="text-center text-danger">
                                    Error loading data: ${error.message}
                                </td>
                            </tr>
                        `;
                    });
            }

            // Function to setup editor search
            function setupEditorSearch() {
                const searchInput = document.getElementById('editor_search');
                const clearButton = document.getElementById('clear_editor_search');

                if (searchInput) {
                    searchInput.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase().trim();

                        // Show/hide clear button
                        if (clearButton) {
                            clearButton.style.display = searchTerm ? 'block' : 'none';
                        }

                        filterEditors(searchTerm);
                    });
                }

                // Clear button functionality
                if (clearButton) {
                    clearButton.addEventListener('click', function() {
                        searchInput.value = '';
                        this.style.display = 'none';
                        filterEditors('');
                    });
                }
            }

            // Function to filter editors based on search term
            function filterEditors(searchTerm) {
                const rows = document.querySelectorAll('#editor_list tr');
                let visibleCount = 0;

                rows.forEach(row => {
                    // Skip if it's a loading/error/no-data row
                    if (row.children.length < 6 || row.classList.contains('no-results-row')) {
                        if (row.classList.contains('no-results-row')) {
                            row.remove();
                        }
                        return;
                    }

                    const name = row.children[1].textContent.toLowerCase();
                    const email = row.children[2].textContent.toLowerCase();
                    const affiliation = row.children[3].textContent.toLowerCase();
                    const phone = row.children[4].textContent.toLowerCase();

                    const isVisible = searchTerm === '' ||
                                    name.includes(searchTerm) ||
                                    email.includes(searchTerm) ||
                                    affiliation.includes(searchTerm) ||
                                    phone.includes(searchTerm);

                    if (isVisible) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                        // Uncheck hidden checkboxes
                        const checkbox = row.querySelector('.editor-checkbox');
                        if (checkbox) {
                            checkbox.checked = false;
                        }
                    }
                });

                // Update select all checkbox state
                updateEditorSelectAllState();
                updateEditorSubmitButton();

                // Show message if no results found
                if (visibleCount === 0 && searchTerm !== '') {
                    const noResultsRow = document.createElement('tr');
                    noResultsRow.className = 'no-results-row';
                    noResultsRow.innerHTML = `
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="ki-duotone ki-file-deleted fs-2x text-muted mb-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <br>
                            Tidak ada editor yang cocok dengan pencarian "<strong>${searchTerm}</strong>"
                            <br>
                            <small class="text-muted">Coba gunakan kata kunci yang berbeda</small>
                        </td>
                    `;
                    document.getElementById('editor_list').appendChild(noResultsRow);
                }
            }

            // Function to update select all state based on visible checkboxes
            function updateEditorSelectAllState() {
                const selectAllMain = document.getElementById('select_all_editors');
                const selectAllHeader = document.getElementById('check_all_editors_header');
                const visibleCheckboxes = Array.from(document.querySelectorAll('.editor-checkbox'))
                    .filter(checkbox => checkbox.closest('tr').style.display !== 'none');

                if (visibleCheckboxes.length === 0) {
                    selectAllMain.checked = false;
                    selectAllHeader.checked = false;
                    return;
                }

                const checkedVisibleCount = visibleCheckboxes.filter(cb => cb.checked).length;
                const allVisibleChecked = checkedVisibleCount === visibleCheckboxes.length;

                selectAllMain.checked = allVisibleChecked;
                selectAllHeader.checked = allVisibleChecked;
            }

            // Function to setup checkboxes
            function setupCheckboxes() {
                const selectAllMain = document.getElementById('select_all_reviewers');
                const selectAllHeader = document.getElementById('check_all_header');
                const reviewerCheckboxes = document.querySelectorAll('.reviewer-checkbox');

                // Main select all functionality
                selectAllMain.addEventListener('change', function() {
                    const visibleCheckboxes = Array.from(reviewerCheckboxes)
                        .filter(checkbox => checkbox.closest('tr').style.display !== 'none');

                    visibleCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    selectAllHeader.checked = this.checked;
                    updateSubmitButton();
                });

                // Header select all functionality
                selectAllHeader.addEventListener('change', function() {
                    const visibleCheckboxes = Array.from(reviewerCheckboxes)
                        .filter(checkbox => checkbox.closest('tr').style.display !== 'none');

                    visibleCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    selectAllMain.checked = this.checked;
                    updateSubmitButton();
                });

                // Individual checkbox functionality
                reviewerCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        updateReviewerSelectAllState();
                        updateSubmitButton();
                    });
                });

                // Initial state
                updateSubmitButton();
            }

            // Function to setup editor checkboxes
            function setupEditorCheckboxes() {
                const selectAllMain = document.getElementById('select_all_editors');
                const selectAllHeader = document.getElementById('check_all_editors_header');
                const editorCheckboxes = document.querySelectorAll('.editor-checkbox');

                // Main select all functionality
                selectAllMain.addEventListener('change', function() {
                    const visibleCheckboxes = Array.from(editorCheckboxes)
                        .filter(checkbox => checkbox.closest('tr').style.display !== 'none');

                    visibleCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    selectAllHeader.checked = this.checked;
                    updateEditorSubmitButton();
                });

                // Header select all functionality
                selectAllHeader.addEventListener('change', function() {
                    const visibleCheckboxes = Array.from(editorCheckboxes)
                        .filter(checkbox => checkbox.closest('tr').style.display !== 'none');

                    visibleCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    selectAllMain.checked = this.checked;
                    updateEditorSubmitButton();
                });

                // Individual checkbox functionality
                editorCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        updateEditorSelectAllState();
                        updateEditorSubmitButton();
                    });
                });

                // Initial state
                updateEditorSubmitButton();
            }

            // Function to update submit button state
            function updateSubmitButton() {
                const checkedCount = document.querySelectorAll('.reviewer-checkbox:checked').length;
                const submitButton = document.getElementById('submit_import');

                if (checkedCount > 0) {
                    submitButton.disabled = false;
                    submitButton.innerHTML =
                        `<i class="ki-duotone ki-file-down fs-2"></i>Import ${checkedCount} Reviewer`;
                } else {
                    submitButton.disabled = true;
                    submitButton.innerHTML = `<i class="ki-duotone ki-file-down fs-2"></i>Import Reviewer`;
                }
            }

            // Function to update editor submit button state
            function updateEditorSubmitButton() {
                const checkedCount = document.querySelectorAll('.editor-checkbox:checked').length;
                const submitButton = document.getElementById('submit_import_editor');

                if (checkedCount > 0) {
                    submitButton.disabled = false;
                    submitButton.innerHTML =
                        `<i class="ki-duotone ki-file-down fs-2"></i>Import ${checkedCount} Editor`;
                } else {
                    submitButton.disabled = true;
                    submitButton.innerHTML = `<i class="ki-duotone ki-file-down fs-2"></i>Import Editor`;
                }
            }

            // Form submission validation
            document.getElementById('import_reviewer_form').addEventListener('submit', function(e) {
                const checkedCount = document.querySelectorAll('.reviewer-checkbox:checked').length;
                if (checkedCount === 0) {
                    e.preventDefault();
                    alert('Pilih minimal satu reviewer untuk diimport!');
                    return false;
                }
            });

            // Form submission validation for editor
            document.getElementById('import_editor_form').addEventListener('submit', function(e) {
                const checkedCount = document.querySelectorAll('.editor-checkbox:checked').length;
                if (checkedCount === 0) {
                    e.preventDefault();
                    alert('Pilih minimal satu editor untuk diimport!');
                    return false;
                }
            });
        });
    </script>
@endsection
