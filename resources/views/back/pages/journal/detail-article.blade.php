@extends('back.app')
@section('content')
    <div id="kt_content_container" class=" container-fluid ">
        @include('back.pages.journal.detail-header')
        <div class="card mb-5 mb-lg-10">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title">
                    <h3 class="me-5 mb-0">Artikel</h3>
                </div>
                <div class="card-toolbar flex-row-fluid justify-content-end gap-3">
                    <a href="#" class="btn btn-sm btn-primary my-1" data-bs-toggle="modal" id="btn_add_article"
                        data-bs-target="#modal_select_article">
                        <i class="ki-duotone ki-plus fs-2"></i> Tambah Artikel
                    </a>
                    <a href="{{ route('back.journal.article.export', [$journal->url_path, $issue->id]) }}"
                        class="btn btn-sm btn-secondary my-1">
                        <i class="ki-duotone ki-file-up fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i> Export Excel
                    </a>
                </div>
            </div>
            <div class="card-body py-4">
                <!--begin::Filter Section-->
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ki-duotone ki-filter fs-3 text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span class="fw-bold fs-6 text-gray-800">Filter & Pencarian Artikel</span>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-light-danger" id="btn_reset_filter">
                                <i class="ki-duotone ki-arrows-circle fs-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i> Reset Filter
                            </button>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Filter Submission ID -->
                        <div class="col-12 col-md-3">
                            <label class="form-label fs-8 fw-bold text-gray-700 mb-1">Cari ID Submission</label>
                            <div class="position-relative">
                                <i class="ki-duotone ki-badge fs-4 position-absolute top-50 translate-middle-y ms-3 text-gray-500">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                                <input type="text" id="filter_submission_id"
                                    class="form-control form-control-sm form-control-solid ps-10"
                                    placeholder="Cari Submission ID..." />
                            </div>
                        </div>

                        <!-- Filter Judul Artikel -->
                        <div class="col-12 col-md-5">
                            <label class="form-label fs-8 fw-bold text-gray-700 mb-1">Cari Judul Artikel</label>
                            <div class="position-relative">
                                <i class="ki-duotone ki-document fs-4 position-absolute top-50 translate-middle-y ms-3 text-gray-500">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="text" id="filter_title" data-kt-article-table-filter="search"
                                    class="form-control form-control-sm form-control-solid ps-10"
                                    placeholder="Cari berdasarkan judul..." />
                            </div>
                        </div>

                        <!-- Filter Penulis -->
                        <div class="col-12 col-md-4">
                            <label class="form-label fs-8 fw-bold text-gray-700 mb-1">Cari Penulis</label>
                            <div class="position-relative">
                                <i class="ki-duotone ki-profile-user fs-4 position-absolute top-50 translate-middle-y ms-3 text-gray-500">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                                <input type="text" id="filter_author"
                                    class="form-control form-control-sm form-control-solid ps-10"
                                    placeholder="Cari nama penulis..." />
                            </div>
                        </div>

                        <!-- Filter Status Submission -->
                        <div class="col-12 col-sm-6 col-md-3">
                            <label class="form-label fs-8 fw-bold text-gray-700 mb-1">Status Submission</label>
                            <select id="filter_status" class="form-select form-select-sm form-select-solid">
                                <option value="all">Semua Status</option>
                                <option value="3">Published</option>
                                <option value="1">Queued / In Review</option>
                                <option value="4">Declined</option>
                            </select>
                        </div>

                        @if (($issue->author_fee ?? $journal->author_fee) != 0)
                            <!-- Filter Pembayaran -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label fs-8 fw-bold text-gray-700 mb-1">Status Pembayaran</label>
                                <select id="filter_payment" class="form-select form-select-sm form-select-solid">
                                    <option value="all">Semua Pembayaran</option>
                                    <option value="free_charge">Free Charge</option>
                                    <option value="paid">Lunas (Paid)</option>
                                    <option value="pending">Belum Bayar (Pending)</option>
                                    <option value="refund">Refund</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        @endif

                        <!-- Filter Editor -->
                        <div class="col-12 col-sm-6 col-md-3">
                            <label class="form-label fs-8 fw-bold text-gray-700 mb-1">Editor</label>
                            <select id="filter_editor" class="form-select form-select-sm form-select-solid">
                                <option value="all">Semua Editor</option>
                                @foreach ($editors as $ed)
                                    <option value="{{ $ed->id }}">{{ $ed->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Reviewer -->
                        <div class="col-12 col-sm-6 col-md-3">
                            <label class="form-label fs-8 fw-bold text-gray-700 mb-1">Reviewer</label>
                            <select id="filter_reviewer" class="form-select form-select-sm form-select-solid">
                                <option value="all">Semua Reviewer</option>
                                @foreach ($reviewers as $rev)
                                    <option value="{{ $rev->id }}">{{ $rev->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="separator separator-dashed my-5"></div>
                <!--end::Filter Section-->
                <div class="table-responsive">
                    <table id="table_articles" class="table align-middle table-row-bordered table-row-solid gy-4 gs-9">
                        <thead class="border-gray-200 fs-5 fw-semibold bg-lighten">
                            <tr>
                                <th class="">ID</th>
                                <th class="min-w-300px">Submission</th>
                                <th class="min-w-200px" data-orderable="false">Info Tambahan Penulis</th>
                                <th class="min-w-200px" data-orderable="false">Editor</th>
                                <th class="min-w-200px" data-orderable="false">Reviewer</th>
                                <th class="min-w-120px text-start">Status Submission</th>
                                @if (($issue->author_fee ?? $journal->author_fee) != 0)
                                    <th class="min-w-250px text-start" data-orderable="false">Pembayaran</th>
                                @endif
                                <th class="min-w-150px text-center" data-orderable="false" data-searchable="false">Action</th>
                            </tr>
                        </thead>
                        <tbody class="fw-6 fw-semibold text-gray-600">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_select_article" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0 d-flex justify-content-end">
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-10 pt-0 pb-15">
                    <div class="text-center mb-13">
                        <h1 class="d-flex justify-content-center align-items-center mb-3">Pilih Artikel submission
                            {{-- <span class="badge badge-circle badge-secondary ms-3">
                            </span> --}}
                        </h1>
                        <div class="text-muted fw-semibold fs-5">
                            Pilih artikel yang akan dimasukkan ke dalam edisi ini
                        </div>
                    </div>
                    <div class="mb-10">
                        <div class="input-group mb-5">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="ki-duotone ki-search-list fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span>
                            <input type="text" id="search_article" class="form-control "
                                placeholder="Cari Submission ID/Judul" />
                        </div>
                    </div>
                    <div class="mh-475px scroll-y me-n7 pe-7" id="list_article">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--begin::Modal View Article (Dynamic Content)-->
    <div class="modal fade" tabindex="-1" id="modal_view_article" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="modal_view_article_content">
                <!-- Loaded via AJAX -->
            </div>
        </div>
    </div>
    <!--end::Modal View Article-->

    <!--begin::Modal Action Article (Dynamic Content)-->
    <div class="modal fade" tabindex="-1" id="modal_action_article" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content" id="modal_action_article_content">
                <!-- Loaded via AJAX -->
            </div>
        </div>
    </div>
    <!--end::Modal Action Article-->

    <!--begin::Modal Delete Article (Single Global Modal)-->
    <div class="modal fade" tabindex="-1" id="modal_delete_article" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Hapus Submission</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <form id="form_delete_article" action="" method="POST">
                    @method('DELETE')
                    @csrf
                    <div class="modal-body">
                        <p>
                            Apakah anda yakin ingin menghapus artikel <strong id="delete_article_title"></strong> dari edisi ini? <br>
                            <span class="text-danger">
                                <strong>Warning! </strong>
                                Data yang sudah dihapus tidak dapat dikembalikan lagi.
                            </span>
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end::Modal Delete Article-->
@endsection
@section('scripts')
    <script>
        let submissions = @json($submissionIds ?? []);
        let data = [];
        $(document).ready(function() {
            let hasAuthorFee = {{ (($issue->author_fee ?? $journal->author_fee) != 0) ? 'true' : 'false' }};
            let tableColumns = [
                { data: 'submission_id', name: 'submission_id' },
                { data: 'submission', name: 'authorsString' },
                { data: 'author_info', name: 'author_info', orderable: false, searchable: false },
                { data: 'editor', name: 'editor', orderable: false, searchable: false },
                { data: 'reviewer', name: 'reviewer', orderable: false, searchable: false },
                { data: 'status', name: 'status', className: 'text-start' }
            ];

            if (hasAuthorFee) {
                tableColumns.push({ data: 'payment', name: 'payment', orderable: false, searchable: false, className: 'text-start' });
            }

            tableColumns.push({ data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' });

            var articleTable = $('#table_articles').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('back.journal.article.datatable', [$journal->url_path, $issue->id]) }}",
                    type: 'GET',
                    data: function (d) {
                        d.filter_submission_id = $('#filter_submission_id').val();
                        d.filter_title = $('#filter_title').val();
                        d.filter_author = $('#filter_author').val();
                        d.filter_status = $('#filter_status').val();
                        d.filter_payment = $('#filter_payment').val();
                        d.filter_editor = $('#filter_editor').val();
                        d.filter_reviewer = $('#filter_reviewer').val();
                    }
                },
                columns: tableColumns,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [],
                language: {
                    search: "Cari:",
                    searchPlaceholder: "Cari artikel...",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ artikel",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 artikel",
                    infoFiltered: "(disaring dari _MAX_ total artikel)",
                    zeroRecords: "Tidak ada artikel yang cocok",
                    emptyTable: "Belum ada artikel yang ditambahkan pada edisi ini",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: '<i class="ki-duotone ki-right fs-4"><span class="path1"></span><span class="path2"></span></i>',
                        previous: '<i class="ki-duotone ki-left fs-4"><span class="path1"></span><span class="path2"></span></i>'
                    }
                },
                dom: "<'table-responsive'tr>" +
                    "<'row mt-4'<'col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start'li><'col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end'p>>"
            });

            // Connect search inputs with debounce
            var searchTimer;
            $('#filter_submission_id, #filter_title, #filter_author').on('keyup input', function () {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    articleTable.draw();
                }, 300);
            });

            // Connect select dropdown filters
            $('#filter_status, #filter_payment, #filter_editor, #filter_reviewer').on('change', function () {
                articleTable.draw();
            });

            // Reset filter button
            $('#btn_reset_filter').on('click', function () {
                $('#filter_submission_id').val('');
                $('#filter_title').val('');
                $('#filter_author').val('');
                $('#filter_status').val('all');
                if ($('#filter_payment').length) {
                    $('#filter_payment').val('all');
                }
                $('#filter_editor').val('all');
                $('#filter_reviewer').val('all');
                articleTable.search('').draw();
            });

            // Re-init Metronic components when table redraws
            articleTable.on('draw', function () {
                if (typeof KTMenu !== 'undefined') {
                    KTMenu.createInstances();
                }
            });

            // Modal View Article AJAX Loader
            $(document).on('click', '.btn-view-article', function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                $('#modal_view_article_content').html(`
                    <div class="text-center py-15">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="text-gray-600 fs-6 fw-semibold mt-3">Memuat data artikel...</div>
                    </div>
                `);
                $('#modal_view_article').modal('show');
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(html) {
                        $('#modal_view_article_content').html(html);
                        $('#modal_view_article [data-control="select2"]').select2({
                            dropdownParent: $('#modal_view_article')
                        });
                        if (typeof KTComponents !== 'undefined' && KTComponents.init) {
                            KTComponents.init();
                        }
                    },
                    error: function() {
                        $('#modal_view_article_content').html(`
                            <div class="modal-body text-center py-10">
                                <i class="ki-duotone ki-cross-circle fs-3tx text-danger mb-3">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                                <div class="text-gray-800 fs-5 fw-bold mb-2">Gagal Memuat Data</div>
                                <div class="text-muted fs-7 mb-5">Terjadi kesalahan saat memuat detail artikel.</div>
                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        `);
                    }
                });
            });

            // Modal Action Article AJAX Loader
            $(document).on('click', '.btn-action-article', function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                $('#modal_action_article_content').html(`
                    <div class="text-center py-15">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="text-gray-600 fs-6 fw-semibold mt-3">Memuat aksi artikel...</div>
                    </div>
                `);
                $('#modal_action_article').modal('show');
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(html) {
                        $('#modal_action_article_content').html(html);
                        if (typeof KTComponents !== 'undefined' && KTComponents.init) {
                            KTComponents.init();
                        }
                    },
                    error: function() {
                        $('#modal_action_article_content').html(`
                            <div class="modal-body text-center py-10">
                                <i class="ki-duotone ki-cross-circle fs-3tx text-danger mb-3">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                                <div class="text-gray-800 fs-5 fw-bold mb-2">Gagal Memuat Data</div>
                                <div class="text-muted fs-7 mb-5">Terjadi kesalahan saat memuat aksi artikel.</div>
                                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        `);
                    }
                });
            });

            // Modal Delete Article Trigger
            $(document).on('click', '.btn-delete-article', function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                let title = $(this).data('title');
                $('#form_delete_article').attr('action', url);
                $('#delete_article_title').text(title);
                $('#modal_delete_article').modal('show');
            });

            $('#btn_add_article').on('click', function() {
                // Show the loading spinner
                $('#list_article').html(`
                    <div class="text-center">
                        <div class="spinner spinner-primary spinner-lg"></div>
                        Loading...
                    </div>
                `);

                $.ajax({
                    url: "{{ route('api.v1.submissions.list') }}",
                    type: 'GET',
                    data: {
                        url_path: "{{ $journal->url_path }}"
                    },
                    success: function(response) {
                        // console.log(response);
                        // Filter out the submissions that are already in the issue
                        let filter_data = response.data.filter(item => {
                            return !submissions.map(Number).includes(item.id);
                        });
                        console.log(filter_data);
                        $('#list_article').html('');
                        filter_data.forEach(submission => {
                            const publication = submission.publications?.[0] ?? {};
                            const titleKey = "{{ $journal->ojs_version }}" == '3.3' ?
                                'en_US' : 'en';
                            const title = titleKey in publication.fullTitle ? publication.fullTitle[titleKey] : '';
                            const authorsString = publication.authorsString ?? '';
                            $('#list_article').append(`
                            <div class="border border-hover-primary p-7 rounded mb-7 submission-item" data-title="${title}" data-id="${submission.id}">
                                <div class="d-flex flex-stack pb-3">
                                    <div class="d-flex">
                                        <div class="">
                                            <div class="d-flex align-items-center">
                                                <a href="#" class="text-gray-900 fw-bold text-hover-primary fs-5 me-4">
                                                    ${authorsString}
                                                </a>
                                            </div>
                                            <span class="text-muted fw-semibold mb-3">
                                                ${title}
                                            </span>
                                        </div>
                                    </div>
                                    <div clas="d-flex">
                                        <div class="text-end pb-3 w-100px">
                                            <span class="text-muted fs-7">Submission ID</span><br>
                                            <span class="text-gray-900 fw-bold fs-5">
                                                ${submission.id}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-0">
                                    <div class="d-flex flex-column">
                                        <div class="separator separator-dashed border-muted my-5"></div>
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex flex-column mw-200px">
                                                <div class="d-flex align-items-center mb-2">
                                                    ${submission.status == 1 ? `
                                                                                                                <span class="badge badge-light-warning fs-5 p-2">${submission.statusLabel}</span>
                                                                                                                ` : submission.status == 3 ? `
                                                                                                                <span class="badge badge-light-success fs-5 p-2">${submission.statusLabel}</span>
                                                                                                                ` : submission.status == 4 ? `
                                                                                                                <span class="badge badge-light-danger fs-5 p-2">${submission.statusLabel}</span>
                                                                                                                ` :
                                                    `<span class="badge badge-light-secondary fs-5 p-2">${submission.statusLabel}</span>`
                                                    }
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-primary"
                                                onclick="selectArticle(${submission.id})">
                                                Pilih Artikel
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `);
                        });

                        // Add search functionality with filter for ID or title
                        $('#search_article').on('input', function() {
                            let searchValue = $(this).val().toLowerCase();
                            $('.submission-item').each(function() {
                                let title = $(this).data('title').toLowerCase();
                                let id = $(this).data('id').toString();
                                if (title.includes(searchValue) || id.includes(
                                        searchValue)) {
                                    $(this).show();
                                } else {
                                    $(this).hide();
                                }
                            });
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan saat mengambil data dari OJS' + xhr
                                .status,
                        });
                    }
                });

            });

        });

        function selectArticle(id) {
            Swal.fire({
                title: 'Memproses...',
                text: "Mohon tunggu sebentar.",
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            // Proceed with the AJAX request
            $.ajax({
                url: "{{ route('api.v1.submissions.select') }}",
                type: 'POST',
                data: {
                    jurnal_path: "{{ $journal->url_path }}",
                    submission_id: id,
                    issue_id: "{{ $issue->id }}",
                },
                success: function(response) {
                    console.log(response);
                    if (response.success == true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: response.message,
                        });
                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menambahkan artikel',
                    });
                }
            });
        }
    </script>
@endsection
