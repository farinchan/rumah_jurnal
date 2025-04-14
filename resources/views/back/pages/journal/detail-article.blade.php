@extends('back.app')
@section('content')
    <div id="kt_content_container" class=" container-xxl ">
        @include('back.pages.journal.detail-header')

        <div class="card mb-5 mb-lg-10">
            <div class="card-header">
                <div class="card-title">
                    <h3>Artikel</h3>
                </div>
                <div class="card-toolbar">
                    <div class="my-1 me-4" data-select2-id="select2-data-119-2hcl">
                        <select class="form-select form-select-sm form-select-solid w-125px select2-hidden-accessible"
                            data-control="select2" data-placeholder="Select Hours" data-hide-search="true"
                            data-select2-id="select2-data-10-gwyz" tabindex="-1" aria-hidden="true"
                            data-kt-initialized="1">
                            <option value="1" selected="" data-select2-id="select2-data-12-evdw">1 Hours</option>
                            <option value="2" data-select2-id="select2-data-123-vaul">6 Hours</option>
                            <option value="3" data-select2-id="select2-data-124-ghz7">12 Hours</option>
                            <option value="4" data-select2-id="select2-data-125-ax5i">24 Hours</option>
                        </select>
                    </div>
                    <a href="#" class="btn btn-sm btn-primary my-1" data-bs-toggle="modal"
                        data-bs-target="#modal_select_article">
                        <i class="ki-duotone ki-plus fs-2"></i> Tambah Artikel
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-row-bordered table-row-solid gy-4 gs-9">
                        <thead class="border-gray-200 fs-5 fw-semibold bg-lighten">
                            <tr>
                                <th class="">ID</th>
                                <th class="min-w-250px">Submission</th>
                                <th class="min-w-100px text-start">Status</th>
                                <th class="min-w-100px text-start">Published</th>
                                <th class="min-w-150px text-start ">Pembayaran</th>
                                <th class="min-w-150px text-center">Action</th>

                            </tr>
                        </thead>
                        <tbody class="fw-6 fw-semibold text-gray-600">
                            @forelse ($issue->submissions as $submission)
                                <tr>
                                    <td>
                                        {{ $submission->submission_id }}
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <a href="{{ $submission->urlPublished }}" target="_blank"
                                                class="text-gray-800 text-hover-primary mb-1">{{ $submission->authorsString }}</a>
                                            <span>
                                                {!! is_array($submission->fullTitle) ? implode(', ', $submission->fullTitle) : $submission->fullTitle !!}

                                            </span>
                                        </div>
                                    </td>

                                    <td class="text-start">
                                        @if ($submission->status == 1)
                                            <span
                                                class="badge badge-light-warning fs-7 fw-bold">{{ $submission->status_label }}</span>
                                        @elseif ($submission->status == 3)
                                            <span
                                                class="badge badge-light-success fs-7 fw-bold">{{ $submission->status_label }}</span>
                                        @elseif ($submission->status == 4)
                                            <span
                                                class="badge badge-light-danger fs-7 fw-bold">{{ $submission->status_label }}</span>
                                        @else
                                            <span class="badge badge-light-secondary fs-7 fw-bold">Unknown</span>
                                        @endif
                                    </td>
                                    <td class="text-start">
                                        {{ $submission->datePublished ?? '-' }}
                                    </td>
                                    <td class="text-start">

                                        @if ($submission->payment_status == 'pending')
                                            <span
                                                class="badge badge-light-warning fs-7 fw-bold">{{ $submission->payment_status }}</span>
                                        @elseif ($submission->payment_status == 'paid')
                                            <span
                                                class="badge badge-light-success fs-7 fw-bold">{{ $submission->payment_status }}</span>
                                        @elseif ($submission->payment_status == 'refund')
                                            <span
                                                class="badge badge-light-danger fs-7 fw-bold">{{ $submission->payment_status }}</span>
                                        @elseif ($submission->payment_status == 'cancelled')
                                            <span
                                                class="badge badge-light-danger fs-7 fw-bold">{{ $submission->payment_status }}</span>
                                        @else
                                            <span class="badge badge-light-secondary fs-7 fw-bold">Unknown</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-light-primary my-1" data-bs-toggle="modal"
                                            data-bs-target="#modal_view_article_{{ $submission->submission_id }}">
                                            <i class="ki-duotone ki-eye fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-light-danger my-1" data-bs-toggle="modal"
                                            data-bs-target="#modal_delete_article_{{ $submission->submission_id }}">
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
                                        Belum ada artikel yang ditambahkan
                                    </td>
                                </tr>
                            @endforelse


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
                    <div class="mh-475px scroll-y me-n7 pe-7" id="list_article">
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach ($issue->submissions as $submission)
        <div class="modal fade" tabindex="-1" id="modal_view_article_{{ $submission->submission_id }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Submission ID {{ $submission->submission_id }}</h3>

                        <div>
                            <!--begin::synchronize-->
                            <div class="btn btn-icon btn-sm btn-active-light-warning ms-2" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Sinkronisasi Data"
                                onclick="selectArticle({{ $submission->submission_id }})">
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
                                <td>Judul</td>
                                <td>:</td>
                                <td>
                                    {{ $submission->getTitleAttribute() }}
                                </td>
                            </tr>
                            <tr>
                                <td>Penulis</td>
                                <td>:</td>
                                <td>
                                    <ul>
                                        @foreach ($submission->getAuthorsAttribute() as $author)
                                            <li>
                                                <span class="text-gray-800 fw-bold">
                                                    {{ $author['name'] }}
                                                </span>
                                                <br>
                                                {{ $author['affiliation'] }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                            </tr>
                            <tr>
                                <td>Abstrak</td>
                                <td>:</td>
                                <td>
                                    {!! $submission->abstract !!}
                                </td>
                            </tr>
                            <tr>
                                <td>Keywords</td>
                                <td>:</td>
                                <td>
                                    {{ $submission->keywords }}
                                </td>
                            </tr>
                            <tr>
                                <td>Published</td>
                                <td>:</td>
                                <td>
                                    {{ $submission->datePublished }}
                                </td>
                            </tr>
                            <tr>
                                <td>Terakhir Diubah</td>
                                <td>:</td>
                                <td>
                                    {{ $submission->lastModified }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" id="modal_delete_article_{{ $submission->submission_id }}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Modal title</h3>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                    class="path2"></span></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form
                        action="{{ route('back.journal.article.destroy', [$journal->url_path, $issue->id, $submission->id]) }}"
                        method="POST">
                        @method('DELETE')
                        @csrf
                        <div class="modal-body">
                            <p>
                                Apakah anda yakin ingin menghapus artikel ini dari edisi ini? <br>
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
    @endforeach
@endsection
@section('scripts')
    <script>
        let submissions = @json($issue->submissions->pluck('submission_id'));
        let data = [];
        $(document).ready(function() {
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
                    console.log(response);
                    // Filter out the submissions that are already in the issue
                    let filter_data = response.data.filter(item => {
                        return !submissions.map(Number).includes(item.id);
                    });
                    console.log(filter_data);
                    $('#list_article').html('');
                    filter_data.forEach(submission => {
                        $('#list_article').append(`
                        <div class="border border-hover-primary p-7 rounded mb-7">
                            <div class="d-flex flex-stack pb-3">
                                <div class="d-flex">
                                    <div class="">
                                        <div class="d-flex align-items-center">
                                            <a href="#" class="text-gray-900 fw-bold text-hover-primary fs-5 me-4">
                                                ${submission.publications[0].authorsString}
                                            </a>
                                        </div>
                                        <span class="text-muted fw-semibold mb-3">
                                            ${submission.publications[0].fullTitle.en_US}
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
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat mengambil data dari OJS' + xhr.status,
                    });
                }
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
