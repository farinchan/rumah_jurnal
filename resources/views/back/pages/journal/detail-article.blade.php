@extends('back.app')
@section('content')
    <div id="kt_content_container" class=" container-xxl ">
        @include('back.pages.journal.detail-header')
        <div class="card mb-3">
            <div class="card-header">
                <h3 class="card-title">Artikel</h3>
                <div class="card-toolbar">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modal_select_article "
                        class='btn btn-primary btn-sm fw-bolder' class="btn btn-primary">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Tambah Artikel
                    </a>
                </div>
            </div>
        </div>
        @forelse ($issue->submissions as $submission)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="py-0" data-kt-customer-payment-method="row">
                        <div class="py-3 d-flex flex-stack flex-wrap">
                            <div class="d-flex align-items-center collapsible collapsed rotate" data-bs-toggle="collapse"
                                href="#article_list_{{ $submission->id }}" role="button" aria-expanded="false"
                                aria-controls="article_list_{{ $submission->id }}">
                                <div class="me-3 rotate-90">
                                    <i class="ki-outline ki-right fs-3"></i>
                                </div>
                                <div class="me-3">
                                    <div class="d-flex align-items-center">
                                        <div class="text-gray-800 fw-bold">
                                            {{ $submission->authors }}
                                        </div>
                                    </div>
                                    <div class="text-muted">
                                        {{ $submission->publication_title }}
                                    </div>
                                </div>

                            </div>

                            {{-- <a href="#" class="btn btn-light-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#edit_schedule_{{ $submission->id }}">
                                <i class="ki-duotone ki-pencil fs-5">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a> --}}


                        </div>
                        <div id="article_list_{{ $submission->id }}" class="collapse fs-6 ps-10"
                            data-bs-parent="#kt_customer_view_payment_method">


                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <img src="{{ asset('back/media/illustrations/empty.svg') }}" class="w-50px mb-5" alt="" />
                        <h3>Belum ada Artikel</h3>
                        <p class="text-muted">Tambahkan artikel untuk edisi ini</p>
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modal_select_article">Tambah
                            Artikel</a>
                    </div>
                </div>
            </div>
        @endforelse
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
@endsection
@section('scripts')
    <script>
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
                    $('#list_article').html('');
                    response.data.forEach(submission => {
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
                                        <button type="button" class="btn btn-sm btn-primary">Pilih</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                    });
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan');
                }
            });
        });
    </script>
@endsection
