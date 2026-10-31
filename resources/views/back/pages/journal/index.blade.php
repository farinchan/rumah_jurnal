@extends('back.app')
@section('styles')
    <style>
        #table_search_author_results thead {
            position: sticky;
            top: 0;
            z-index: 10;
        }
        #table_search_author_results thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: var(--bs-light, #f9f9f9) !important;
            box-shadow: inset 0 -1px 0 var(--bs-gray-300, #e4e6ef);
        }
    </style>
@endsection
@section('content')
    <div id="kt_content_container" class=" container-xxl ">
        <div class="card card-flush">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title flex-wrap gap-2">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <input type="text" data-kt-ecommerce-product-filter="search"
                            class="form-control form-control-solid w-250px ps-12" placeholder="Cari Issue" />
                    </div>
                    <button type="button" class="btn btn-light-primary my-1" data-bs-toggle="modal"
                        data-bs-target="#modal_search_author" id="btn_search_author">
                        <i class="ki-duotone ki-profile-user fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                        Cari Penulis
                    </button>
                </div>
                <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                    <a href="{{ route('back.journal.manuscript-submissions.index', $journal->url_path) }}"
                        class="btn btn-light-primary">
                        <i class="ki-duotone ki-document fs-2"></i>
                        Manuscript Submissions
                        @if ($journal->waiting_manuscript_submissions_count > 0)
                            <span class="badge badge-warning ms-2" title="Waiting">
                                Waiting: {{ $journal->waiting_manuscript_submissions_count }}
                            </span>
                        @endif
                        @if ($journal->under_review_manuscript_submissions_count > 0)
                            <span class="badge badge-info ms-2" title="Under Review">
                                Under Review: {{ $journal->under_review_manuscript_submissions_count }}
                            </span>
                        @endif
                    </a>
                    <div class="w-100 mw-150px">
                        <select class="form-select form-select-solid" data-control="select2" data-hide-search="true"
                            data-placeholder="Status" data-kt-ecommerce-product-filter="status">
                            <option></option>
                            <option value="all">Semua</option>
                            <option value="published">published</option>
                            <option value="unpublished">unpublished</option>
                        </select>
                    </div>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#add_issue" class="btn btn-primary">
                        <i class="ki-duotone ki-plus fs-2"></i>Tambah Issue</a>
                </div>
            </div>
            <div class="card-body pt-0">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
                    <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">
                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                                        data-kt-check-target="#kt_ecommerce_products_table .form-check-input"
                                        value="1" />
                                </div>
                            </th>
                            <th class="min-w-200px">Issue</th>
                            <th class="text-end ">Article</th>
                            <th class="text-end ">Editor</th>
                            <th class="text-end ">Reviewer</th>
                            <th class="text-end ">Status</th>
                            <th class="text-end min-w-70px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                        @foreach ($journal->issues as $issue)
                            <tr>
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" value="1" />
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">

                                        <div class="ms-5">
                                            <a href="#" class="text-gray-800 text-hover-primary fs-5 fw-bold mb-1"
                                                data-kt-ecommerce-category-filter="category_name">Vol. {{ $issue->volume }}
                                                No. {{ $issue->number }} ({{ $issue->year }}): {{ $issue->title }}</a>
                                            <div class="text-muted fs-7 fw-bold">
                                                {{ Str::limit(strip_tags($issue->description), 300) }}...</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end pe-0">
                                    <span class="fw-bold">
                                        {{ $issue->submissions->count() }}
                                    </span>
                                    <span class="text-muted fs-7" title="{{ is_null($issue->max_articles) ? 'Tanpa Batas' : 'Maksimal ' . $issue->max_articles . ' Artikel' }}">/ {{ is_null($issue->max_articles) ? '∞' : $issue->max_articles }}</span>
                                </td>
                                <td class="text-end pe-0">
                                    <span class="fw-bold">
                                        {{ $issue->editors->count() }}
                                    </span>
                                </td>
                                <td class="text-end pe-0">
                                    <span class="fw-bold">
                                        {{ $issue->reviewers->count() }}
                                    </span>
                                </td>
                                <td class="text-end pe-0">
                                    @if ($issue->status == 'published')
                                        <div class="badge badge-light-success">Published</div>
                                    @else
                                        <div class="badge badge-light-danger">unpublished</div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('back.journal.article.index', [$journal_path, $issue->id]) }}"
                                        class="btn btn-sm btn-light-primary my-1">
                                        <i class="ki-duotone ki-eye fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i> Detail
                                    </a>
                                    {{-- <a href="#"
                                        class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"
                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                        <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                        data-kt-menu="true">
                                        <div class="menu-item px-3">
                                            <a href="{{ route('back.journal.article.index', [$journal_path, $issue->id]) }}"
                                                class="menu-link px-3">Detail</a>
                                        </div>
                                        <div class="menu-item px-3">
                                            <a href="#" data-bs-toggle="modal"
                                                data-bs-target="#edit_issue{{ $issue->id }}"
                                                class="menu-link px-3">Edit</a>
                                        </div>
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3" data-bs-toggle="modal"
                                                data-bs-target="#delete_issue{{ $issue->id }}">Delete</a>
                                        </div>
                                    </div> --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="add_issue">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <div class="fs-4 fw-bolder">Tambah Issue</div>
                        <div class="fs-6 text-muted">Issue yang dibuat tidak akan tampil di OJS, Issue ini hanya sebagai
                            pencatatan saja.</div>
                    </div>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>
                <form method="post" action="{{ route('back.journal.issue.store', $journal_path) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label required">Volume</label>
                                <input type="text" name="volume" class="form-control" value="{{ old('volume') }}"
                                    required />
                                @error('volume')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Number</label>
                                <input type="text" name="number" class="form-control" value="{{ old('number') }}"
                                    required />
                                @error('number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Year</label>
                                <input type="text" name="year" class="form-control"
                                    placeholder="{{ date('Y') }}" value="{{ old('year') }}" required />
                                @error('year')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mt-5">
                                <label class="form-label required">Title</label>
                                <input type="text" name="title" class="form-control" placeholder="Judul Issue"
                                    value="{{ old('title') }}" required />
                                @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mt-5">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="6" placeholder="Deskripsi Issue">{{ old('description') }}</textarea>
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            @hasanyrole('super-admin|admin-ejournal|admin-proceeding|admin-student-research-hub')
                                <div class="col-md-12 mt-5">
                                    <label class="form-label">Maksimal Artikel</label>
                                    <input type="number" name="max_articles" class="form-control" placeholder="Maksimal Artikel (Default: 10, kosongkan jika tanpa batas)"
                                        value="{{ old('max_articles', 10) }}" min="1" />
                                    <div class="form-text text-muted">Jumlah maksimal artikel untuk issue ini. Default: 10. Kosongkan jika tanpa batas.</div>
                                    @error('max_articles')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            @endhasanyrole

                            @role('super-admin')
                                <div class="col-md-12 mt-5">
                                    <label class="form-label">Biaya Publikasi (APC)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" name="author_fee" class="form-control" placeholder="Biaya Penulis (Opsional, otomatis dari jurnal: Rp {{ number_format($journal->author_fee, 0, ',', '.') }})"
                                            value="{{ old('author_fee', $journal->author_fee) }}" min="0" />
                                    </div>
                                    @error('author_fee')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            @endrole
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--
    @foreach ($journal->issues as $issue)
        <div class="modal fade" tabindex="-1" id="edit_issue{{ $issue->id }}">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <div class="fs-4 fw-bolder">Edit Issue</div>
                            <div class="fs-6 text-muted">Issue yang dibuat tidak akan tampil di OJS, Issue ini hanya sebagai pencatatan saja.</div>
                        </div>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="ki-duotone
                            ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form method="post" action="{{ route('back.journal.issue.update', [$journal_path, $issue->id]) }}">
                        @csrf
                        @method('put')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label required">Volume</label>
                                    <input type="text" name="volume" class="form-control" value="{{ $issue->volume }}" required />
                                    @error('volume')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required">Number</label>
                                    <input type="text" name="number" class="form-control" value="{{ $issue->number }}" required />
                                    @error('number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required">Year</label>
                                    <input type="text" name="year" class="form-control" value="{{ $issue->year }}" required />
                                    @error('year')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-12 mt-5">
                                    <label class="form-label required">Title</label>
                                    <input type="text" name="title" class="form-control" value="{{ $issue->title }}" required />
                                    @error('title')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-12 mt-5">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="6">{{ $issue->description }}</textarea>
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                @hasanyrole('super-admin|admin-ejournal|admin-proceeding|admin-student-research-hub')
                                    <div class="col-md-12 mt-5">
                                        <label class="form-label">Maksimal Artikel</label>
                                        <input type="number" name="max_articles" class="form-control" placeholder="Kosongkan jika tidak ada batas"
                                            value="{{ old('max_articles', $issue->max_articles) }}" min="1" />
                                        <div class="form-text text-muted">Jumlah maksimal artikel untuk issue ini. Kosongkan jika tanpa batas.</div>
                                        @error('max_articles')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                @endhasanyrole
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-warning">update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" id="delete_issue{{ $issue->id }}">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="fs-4 fw-bolder">Hapus Issue</div>
                    </div>
                    <form method="post" action="{{ route('back.journal.issue.destroy', [$journal_path, $issue->id]) }}">
                        @csrf
                        @method('delete')
                        <div class="modal-body py-5">
                            <div class="text-center">
                                <div class="fs-4 text-danger text-bold"><b>Apakah Anda yakin?</b> </div>
                                <div class="fs-6  text-danger">Perhatian! Issue yang dihapus tidak dapat dikembalikan, termasuk semua data yang terkait.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach --}}

    <!--begin::Modal Search Author-->
    <div class="modal fade" id="modal_search_author" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0 justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-45px me-4">
                            <span class="symbol-label bg-light-primary">
                                <i class="ki-duotone ki-profile-user fs-1 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                        </div>
                        <div>
                            <h3 class="modal-title fw-bold text-gray-900 mb-0">Cari Penulis & Submission</h3>
                            <span class="text-muted fs-7 fw-semibold">
                                Cari penulis atau ID submission di semua edisi (issue) jurnal {{ $journal->title }}
                            </span>
                        </div>
                    </div>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>

                <div class="modal-body py-4">
                    <!--begin::Filter Section-->
                    <div class="mb-4 mt-4">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="ki-duotone ki-filter fs-3 text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <span class="fw-bold fs-6 text-gray-800">Filter & Pencarian Penulis</span>
                        </div>

                        <div class="row g-3 align-items-end">
                            <!-- Kata Kunci Pencarian -->
                            <div class="col-12 col-md-5">
                                <label class="form-label fs-8 fw-bold text-gray-700 mb-1">Kata Kunci Pencarian</label>
                                <div class="position-relative">
                                    <i class="ki-duotone ki-magnifier fs-4 position-absolute top-50 translate-middle-y ms-3 text-gray-500">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <input type="text" id="input_search_author_keyword"
                                        class="form-control form-control-sm form-control-solid ps-10"
                                        placeholder="Ketik Nama Penulis atau ID Submission..." />
                                </div>
                            </div>

                            <!-- Cari Berdasarkan -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label fs-8 fw-bold text-gray-700 mb-1">Cari Berdasarkan</label>
                                <select id="select_search_by" class="form-select form-select-sm form-select-solid">
                                    <option value="all">Semua (ID / Penulis)</option>
                                    <option value="author">Nama Penulis</option>
                                    <option value="submission_id">ID Submission</option>
                                </select>
                            </div>

                            <!-- Filter Edisi (Issue) -->
                            <div class="col-12 col-sm-6 col-md-3">
                                <label class="form-label fs-8 fw-bold text-gray-700 mb-1">Filter Edisi (Issue)</label>
                                <select id="select_search_issue" class="form-select form-select-sm form-select-solid">
                                    <option value="all">Semua Edisi (Issue)</option>
                                    @foreach ($journal->issues as $iss)
                                        <option value="{{ $iss->id }}">
                                            Vol. {{ $iss->volume }} No. {{ $iss->number }} ({{ $iss->year }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tombol Reset Filter (Icon Only) -->
                            <div class="col-12 col-md-1">
                                <button type="button" class="btn btn-sm btn-icon btn-light-danger w-100" id="btn_reset_search_author" title="Reset Filter" data-bs-toggle="tooltip">
                                    <i class="ki-duotone ki-arrows-circle fs-3 p-0">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="separator separator-dashed my-4"></div>
                    <!--end::Filter Section-->

                    <!-- Result Status Header -->
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div class="fs-7 fw-bold text-gray-700">
                            Hasil Pencarian: <span id="search_author_count" class="badge badge-light-primary fw-bold ms-1">0 Ditemukan</span>
                        </div>
                        <div class="text-muted fs-8">
                            <i class="ki-duotone ki-information-5 fs-7 text-muted me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            Menampilkan artikel dari semua issue pada jurnal ini
                        </div>
                    </div>

                    <!-- Loading State -->
                    <div id="search_author_loading" class="text-center py-10 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="text-gray-600 fs-7 fw-semibold mt-2">Sedang mencari naskah & penulis...</div>
                    </div>

                    <!-- Table Content -->
                    <div id="search_author_table_container" class="table-responsive mh-450px scroll-y">
                        <table class="table table-hover table-row-dashed table-row-gray-300 align-middle gs-0 gy-3 fs-7" id="table_search_author_results">
                            <thead class="border-bottom border-gray-300 fs-7 fw-bold text-gray-700 bg-light sticky-top">
                                <tr>
                                    <th class="w-90px ps-3 bg-light">ID</th>
                                    <th class="min-w-200px bg-light">Penulis</th>
                                    <th class="min-w-250px bg-light">Judul & Naskah</th>
                                    <th class="min-w-180px bg-light">Edisi (Issue)</th>
                                    <th class="min-w-100px text-end pe-3 bg-light">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="search_author_tbody">
                                <!-- Injected via JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div id="search_author_empty" class="text-center py-10 d-none">
                        <i class="ki-duotone ki-file-deleted fs-3tx text-muted mb-2">
                            <span class="path1"></span><span class="path2"></span>
                        </i>
                        <div class="fs-6 fw-bold text-gray-800" id="search_author_empty_title">Tidak Ada Hasil</div>
                        <div class="text-muted fs-7" id="search_author_empty_text">Tidak ditemukan artikel atau penulis yang cocok.</div>
                    </div>
                </div>

                <div class="modal-footer py-3">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!--end::Modal Search Author-->

    <!--begin::Modal View Article (Dynamic Content)-->
    <div class="modal fade" tabindex="-1" id="modal_view_article" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="modal_view_article_content">
                <!-- Loaded via AJAX -->
            </div>
        </div>
    </div>
    <!--end::Modal View Article-->
@endsection
@section('scripts')
    <script src="{{ asset('back/js/custom/apps/ecommerce/catalog/issue.js') }}"></script>
    <script>
        $(document).ready(function () {
            let searchTimer = null;
            let currentAjax = null;
            let returnToSearchModal = false;

            function fetchAuthors() {
                const keyword = ($('#input_search_author_keyword').val() || '').trim();
                const searchBy = $('#select_search_by').val();
                const issueId = $('#select_search_issue').val();

                if (currentAjax) {
                    currentAjax.abort();
                }

                $('#search_author_loading').removeClass('d-none');
                $('#search_author_table_container').addClass('d-none');
                $('#search_author_empty').addClass('d-none');

                currentAjax = $.ajax({
                    url: "{{ route('back.journal.search-author', $journal->url_path) }}",
                    type: "GET",
                    data: {
                        q: keyword,
                        search_by: searchBy,
                        issue_id: issueId
                    },
                    success: function (res) {
                        $('#search_author_loading').addClass('d-none');
                        if (res.success && res.data) {
                            $('#search_author_count').text(res.count + ' Ditemukan');
                            renderAuthorRows(res.data, keyword);
                        }
                    },
                    error: function (xhr, status) {
                        if (status === 'abort') return;
                        $('#search_author_loading').addClass('d-none');
                        $('#search_author_empty').removeClass('d-none');
                        $('#search_author_empty_title').text('Gagal Memuat Data');
                        $('#search_author_empty_text').text('Terjadi kesalahan koneksi saat mencari data.');
                    }
                });
            }

            function renderAuthorRows(items, keyword) {
                const tbody = $('#search_author_tbody');
                tbody.empty();

                if (!items || items.length === 0) {
                    $('#search_author_table_container').addClass('d-none');
                    $('#search_author_empty').removeClass('d-none');
                    if (keyword) {
                        $('#search_author_empty_title').text('Tidak Ditemukan');
                        $('#search_author_empty_text').text(`Tidak ada naskah atau penulis yang cocok dengan kata kunci "${keyword}".`);
                    } else {
                        $('#search_author_empty_title').text('Belum Ada Naskah');
                        $('#search_author_empty_text').text('Belum ada artikel pada jurnal atau edisi yang dipilih.');
                    }
                    return;
                }

                $('#search_author_empty').addClass('d-none');
                $('#search_author_table_container').removeClass('d-none');

                items.forEach(function (item) {
                    let titleLink = item.url_published
                        ? `<a href="${item.url_published}" target="_blank" class="text-gray-800 text-hover-primary fw-semibold">${escapeHtml(item.title)}</a>`
                        : `<span class="text-gray-800 fw-semibold">${escapeHtml(item.title)}</span>`;

                    let rowHtml = `
                        <tr>
                            <td class="ps-3 align-middle">
                                <span class="badge badge-light-dark fw-bold fs-7">#${item.submission_id}</span>
                            </td>
                            <td class="align-middle">
                                <div class="fw-bold text-gray-900 fs-7">${escapeHtml(item.authors_string)}</div>
                            </td>
                            <td class="align-middle">
                                <div>${titleLink}</div>
                                ${item.date_published ? `<div class="text-muted fs-8 mt-1">Terbit: ${escapeHtml(item.date_published)}</div>` : ''}
                            </td>
                            <td class="align-middle">
                                <a href="${item.issue_url}" class=" text-black text-hover-primary text-wrap text-start fs-8" title="Buka Edisi">
                                    ${escapeHtml(item.issue_title)}
                                </a>
                            </td>
                            <td class="align-middle text-end pe-3 text-nowrap">
                                <button type="button" class="btn btn-sm btn-icon btn-light-info me-1 btn-search-view-detail"
                                    data-url="${item.modal_view_url}" title="Lihat Detail Artikel">
                                    <i class="ki-duotone ki-eye fs-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                </button>
                                <a href="${item.issue_url}" class="btn btn-sm btn-icon btn-light-primary" title="Buka Halaman Issue">
                                    <i class="ki-duotone ki-exit-right-corner fs-4"><span class="path1"></span><span class="path2"></span></i>
                                </a>
                            </td>
                        </tr>
                    `;
                    tbody.append(rowHtml);
                });
            }

            function escapeHtml(text) {
                if (!text) return '';
                return String(text)
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            // Trigger search when modal is shown
            $('#modal_search_author').on('shown.bs.modal', function () {
                $('#input_search_author_keyword').trigger('focus');
                if ($('#search_author_tbody tr').length === 0) {
                    fetchAuthors();
                }
            });

            // Real-time keyword typing with debounce
            $('#input_search_author_keyword').on('input keyup', function () {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(fetchAuthors, 300);
            });

            // Filter changes
            $('#select_search_by, #select_search_issue').on('change', function () {
                fetchAuthors();
            });

            // Reset button
            $('#btn_reset_search_author').on('click', function () {
                $('#input_search_author_keyword').val('');
                $('#select_search_by').val('all');
                $('#select_search_issue').val('all');
                fetchAuthors();
            });

            // View Article Detail inside Search Author Modal
            $(document).on('click', '.btn-search-view-detail', function (e) {
                e.preventDefault();
                const url = $(this).data('url');

                returnToSearchModal = true;
                $('#modal_search_author').modal('hide');

                $('#modal_view_article_content').html(`
                    <div class="text-center py-15">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="text-gray-600 fs-6 fw-semibold mt-3">Memuat detail artikel & penulis...</div>
                    </div>
                `);
                $('#modal_view_article').modal('show');

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (html) {
                        $('#modal_view_article_content').html(html);
                        $('#modal_view_article [data-control="select2"]').select2({
                            dropdownParent: $('#modal_view_article')
                        });
                        if (typeof KTComponents !== 'undefined' && KTComponents.init) {
                            KTComponents.init();
                        }
                    },
                    error: function () {
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

            // When modal_view_article is closed, return to modal_search_author if opened from search
            $('#modal_view_article').on('hidden.bs.modal', function () {
                if (returnToSearchModal) {
                    returnToSearchModal = false;
                    $('#modal_search_author').modal('show');
                }
            });
        });
    </script>
@endsection
