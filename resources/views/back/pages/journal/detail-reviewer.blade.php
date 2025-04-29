@extends('back.app')
@section('content')
    <div id="kt_content_container" class=" container-xxl ">
        @include('back.pages.journal.detail-header')
        <div class="row">
            <div class="col-xxl-8">
                <div class="card mb-5 mb-lg-10">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>Reviewer</h3>
                        </div>
                        <div class="card-toolbar">
                            <a href="#" class="btn btn-sm btn-primary my-1" data-bs-toggle="modal" id="btn_add_reviewer"
                                data-bs-target="#modal_select_article">
                                <i class="ki-duotone ki-plus fs-2"></i> Tambah Reviewer
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-bordered table-row-solid gy-4 gs-9">
                                <thead class="border-gray-200 fs-5 fw-semibold bg-lighten">
                                    <tr>
                                        <th class="">No</th>
                                        <th class="min-w-250px">Reviewer</th>
                                        <th class="min-w-150px text-start">Email</th>
                                        <th class="min-w-100px text-start">No. Telp</th>
                                        <th class="min-w-150px text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-6 fw-semibold text-gray-600">
                                    @forelse ($issue->reviewers as $reviewer)
                                        <tr>
                                            <td>
                                                {{ $loop->iteration }}
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <a href="#" target="_blank"
                                                        class="text-gray-800 text-hover-primary mb-1">{{ $reviewer->name }}</a>
                                                    <span>
                                                        {{ $reviewer->affiliation }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="text-start">
                                                <span class="text-bold text-gray-800">
                                                    {{ $reviewer->email }}
                                                </span>


                                                <a class="badge badge-light-success cursor-pointer my-1">
                                                    <i class="ki-duotone ki-send fs-5 text-success me-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Kirim SK
                                                </a>
                                                <span class="badge badge-light-warning cursor-pointer my-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modal_send_email_{{ $reviewer->reviewer_id }}">
                                                    <i class="ki-duotone ki-sms fs-5 text-warning me-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Kirim Email
                                                </span>
                                            </td>
                                            <td class="text-start">
                                                {{ $reviewer->phone }}
                                            </td>
                                            <td class="text-end">
                                                <a href="#" class="btn btn-sm btn-light-primary my-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modal_view_article_{{ $reviewer->reviewer_id }}">
                                                    <i class="ki-duotone ki-eye fs-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-light-danger my-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modal_delete_article_{{ $reviewer->reviewer_id }}">
                                                    <i class="ki-duotone ki-trash fs-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                        <span class="path5"></span>
                                                    </i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted fw-semibold fs-6">
                                                Belum ada data reviewer
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4">
                <div class="card card-xxl-stretch mb-5 mb-xxl-10">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="text-gray-800">SK Reviewer</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <span class="fs-5 fw-semibold text-gray-600 pb-6 d-block">
                            Upload SK Reviewer untuk edisi ini
                        </span>
                        <div class="d-flex align-self-center mb-3">
                            <div class="flex-grow-1 me-3">
                                <input type="file" class="form-control form-control-solid" name="sk_reviewer"
                                    id="sk_reviewer" accept=".pdf" />
                                <small class="form-text text-muted">
                                    File harus dalam format PDF
                                </small>
                            </div>
                            <button type="button" class="btn btn-primary btn-icon flex-shrink-0" data-bs-toggle="tooltip"
                                data-bs-placement="right" title="Upload untuk menambah/mengubah SK">
                                <i class="ki-duotone ki-file-up fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </button>
                        </div>

                        <button type="button" class="btn btn-light-info w-100 mb-5" data-bs-toggle="tooltip"
                            data-bs-placement="right" title="Lihat File SK ">
                            <i class="ki-duotone ki-eye fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            Lihat File
                        </button>
                        <button type="button" class="btn btn-light-success w-100 mb-3" data-bs-toggle="tooltip"
                            data-bs-placement="right" title="Kirim SK ke semua reviewer via email">
                            <i class="ki-duotone ki-send fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Kirim SK Reviewer
                        </button>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_select_article" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog mw-650px">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0 d-flex justify-content-end">
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-outline ki-cross fs-1"></i>
                    </div>
                </div>
                <div class="modal-body scroll-y mx-5 mx-xl-10 pt-0 pb-15">
                    <div class="text-center mb-13">
                        <h1 class="d-flex justify-content-center align-items-center mb-3">Pilih Reviewer
                            {{-- <span class="badge badge-circle badge-secondary ms-3">
                            </span> --}}
                        </h1>
                        <div class="text-muted fw-semibold fs-5">
                            Pilih reviewer yang akan ditambahkan ke edisi ini
                        </div>
                    </div>
                    <div class="mb-10">
                        <div class="input-group mb-5">
                            <span class="input-group-text" id="basic-addon1">
                                <i class="ki-duotone ki-profile-user fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <input type="text" id="search_reviewer" class="form-control "
                                placeholder="Cari Nama Reviewer" />
                        </div>
                    </div>
                    <div class="mh-475px scroll-y me-n7 pe-7" id="list_article">
                    </div>
                </div>
            </div>
        </div>
    </div>
    @foreach ($issue->reviewers as $reviewer)
        <div class="modal fade" tabindex="-1" id="modal_view_article_{{ $reviewer->reviewer_id }}">
            <div class="modal-dialog mw-650px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Data Reviewer</h3>
                        <div>
                            <!--begin::synchronize-->
                            <div class="btn btn-icon btn-sm btn-active-light-warning ms-2" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Sinkronisasi Data"
                                onclick="selectReviewer({{ $reviewer->reviewer_id }})">
                                <i class="ki-duotone ki-arrows-circle fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <!--begin::Close-->
                            <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                        class="path2"></span></i>
                            </div>
                            <!--end::Close-->
                        </div>
                    </div>
                    <div class="modal-body">
                        <table class="table table-row-dashed table-row-gray-300 align-top gs-0 gy-4 my-0 fs-6">
                            <tr>
                                <td>Nama</td>
                                <td>:</td>
                                <td>
                                    {{ $reviewer->name }}
                                </td>
                            </tr>
                            <tr>
                                <td>Affiliasi</td>
                                <td>:</td>
                                <td>
                                    {{ $reviewer->affiliation }}
                                </td>
                            </tr>
                            <tr>
                                <td>username</td>
                                <td>:</td>
                                <td>
                                    {{ $reviewer->username }}
                                </td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>:</td>
                                <td>
                                    {{ $reviewer->email }}
                                </td>
                            </tr>
                            <tr>
                                <td>No. Telp</td>
                                <td>:</td>
                                <td>
                                    {{ $reviewer->phone }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" id="modal_delete_article_{{ $reviewer->reviewer_id }}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Hapus Reviewer</h3>
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                    class="path2"></span></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form
                        action="{{ route('back.journal.reviewer.destroy', [$journal->url_path, $issue->id, $reviewer->id]) }}"
                        method="POST">
                        @method('DELETE')
                        @csrf
                        <div class="modal-body">
                            <p>
                                Apakah anda yakin ingin menghapus Reviewer ini dari edisi ini? <br>
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
        <div class="modal fade" tabindex="-1" id="modal_send_email_{{ $reviewer->reviewer_id }}">
            <div class="modal-dialog mw-650px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Kirim email</h3>
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                    class="path2"></span></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form action="{{ route('back.email.send-mail') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-5">
                                <label class="form-label">Kepada (Email)</label>
                                <input type="email" class="form-control form-control-solid" placeholder="Email"
                                    name="email" value="{{ $reviewer->email }}" readonly />
                            </div>
                            <div class="mb-5">
                                <label class="form-label">Subjek</label>
                                <input type="text" class="form-control form-control-solid" placeholder="Subjek Email"
                                    name="subject" value="" />
                            </div>
                            <div class="mb-5">
                                <label class="form-label">Body</label>
                                <textarea class="form-control form-control-solid" rows="5" placeholder="Body"
                                    id="kt_docs_ckeditor_classic_{{ $reviewer->id }}" name="body">
                                    <p>
                                        Yth. <b>{{ $reviewer->name }}</b><br>
                                        {{ $reviewer->affiliation }}
                                    </p>
                                    <br>
                                    <hr>
                                        <table border="0" cellpadding="5" cellspacing="0">
                                            <tr>
                                                <td colspan="3">
                                                    <b>{{ $setting_web->name }}</b>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Alamat</td>
                                                <td>:</td>
                                                <td>{{ $setting_web->address }}</td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td>:</td>
                                                <td>{{ $setting_web->email }}</td>
                                            </tr>
                                            <tr>
                                                <td>Website</td>
                                                <td>:</td>
                                                <td>{{ request()->getSchemeAndHttpHost() }}</td>
                                            </tr>
                                        </table>
                                </textarea>
                            </div>
                            <div class="mb-5">
                                <label class="form-label">Lampiran</label>
                                <input type="file" class="form-control form-control-solid" name="attachment"
                                    id="attachment" accept=".pdf" />
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
@section('scripts')
    <script>
        let reviewer = @json($issue->reviewers->pluck('reviewer_id'));
        let data = [];
        $(document).ready(function() {
            $('#btn_add_reviewer').on('click', function() {
                $('#list_article').html(`
                    <div class="text-center">
                        <div class="spinner spinner-primary spinner-lg"></div>
                        Loading...
                    </div>
                `);
                $.ajax({
                    url: "{{ route('api.v1.reviewer.list') }}",
                    type: 'GET',
                    data: {
                        url_path: "{{ $journal->url_path }}"
                    },
                    success: function(response) {
                        console.log(response);
                        // Filter out the reviewer that are already in the issue
                        let filter_data = response.data.filter(item => {
                            return !reviewer.map(Number).includes(item.id);
                        });
                        console.log(filter_data);
                        $('#list_article').html('');
                        filter_data.forEach(reviewer => {
                            $('#list_article').append(`
                        <div class="border border-hover-primary p-7 rounded mb-7 reviewer-item" data-name="${reviewer.fullName.toLowerCase()}">
                            <div class="d-flex flex-stack pb-3">
                                <div class="d-flex">
                                    <div class="">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="text-gray-900 fw-bold text-hover-primary fs-5 me-4">
                                                ${reviewer.fullName}
                                            </a>
                                        </div>
                                        <span class="text-muted fw-semibold mb-3">
                                            ${reviewer.affiliation.en_US}
                                        </span>
                                    </div>
                                </div>
                                <div clas="d-flex">
                                    <div class="text-end pb-3 w-100px">
                                        <button type="button" class="btn btn-sm btn-light-primary"
                                            onclick="selectReviewer(${reviewer.id})">
                                            <i class="ki-duotone ki-plus fs-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                        });

                        // Add search functionality
                        $('#search_reviewer').on('input', function() {
                            let searchValue = $(this).val().toLowerCase();
                            $('.reviewer-item').each(function() {
                                let name = $(this).data('name');
                                if (name.includes(searchValue)) {
                                    $(this).show();
                                } else {
                                    $(this).hide();
                                }
                            });
                        });
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan');
                    }
                });
            });
        });

        function selectReviewer(id) {
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
                url: "{{ route('api.v1.reviewer.select') }}",
                type: 'POST',
                data: {
                    jurnal_path: "{{ $journal->url_path }}",
                    reviewer_id: id,
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
                        text: 'Terjadi kesalahan saat menambah reviewer',
                    });
                }
            });
        }
    </script>
    <script src="{{ asset('back/plugins/custom/ckeditor/ckeditor-classic.bundle.js') }}"></script>

    @foreach ($issue->reviewers as $reviewer)
        <script>
            ClassicEditor
                .create(document.querySelector('#kt_docs_ckeditor_classic_' + {{ $reviewer->id }}))
                .then(editor => {
                    console.log(editor);
                })
                .catch(error => {
                    console.error(error);
                });
        </script>
    @endforeach
@endsection
