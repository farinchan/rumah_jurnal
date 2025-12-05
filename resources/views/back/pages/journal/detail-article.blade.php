@extends('back.app')
@section('content')
    <div id="kt_content_container" class=" container-fluid ">
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
                    <a href="#" class="btn btn-sm btn-primary my-1 me-3" data-bs-toggle="modal" id="btn_add_article"
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
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-row-bordered table-row-solid gy-4 gs-9">
                        <thead class="border-gray-200 fs-5 fw-semibold bg-lighten">
                            <tr>
                                <th class="">ID</th>
                                <th class="min-w-350px">Submission</th>
                                <th class="min-w-250px">Info Tambahan Penulis</th>
                                <th class="min-w-300px">Editor</th>
                                <th class="min-w-300px">Reviewer</th>
                                <th class="min-w-100px text-start">Status Submission</th>
                                @if ($journal->author_fee != 0)
                                    <th class="min-w-300px text-start ">Pembayaran</th>
                                @endif
                                <th class="min-w-250px text-center">Action</th>
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
                                            <span class="text-muted fw-bold">
                                                Published date: {{ $submission->datePublished ?? '-' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        NIK: {{ $submission->author_nik ?? '-' }} <br>
                                        Bank: {{ $submission->author_bank_name ?? '-' }} <br>
                                        No Rekening: {{ $submission->author_bank_account ?? '-' }} <br>
                                        NPWP: {{ $submission->author_npwp ?? '-' }} <br>
                                        Golongan: {{ $submission->author_golongan ?? '-' }}
                                    </td>
                                    <td>
                                        <ul>
                                            @forelse ($submission->editors as $editor)
                                                <li>
                                                    <span class="text-gray-800 fw-bold">
                                                        {{ $editor->name }}
                                                    </span>
                                                    <br>
                                                    {{ $editor->affiliation }}
                                                </li>
                                            @empty
                                                <li style="list-style: none" class="text-muted">Reviewer belum ditambahkan
                                                </li>
                                            @endforelse
                                        </ul>
                                    </td>
                                    <td>
                                        <ul>
                                            @forelse ($submission->reviewers as $reviewer)
                                                <li>
                                                    <span class="text-gray-800 fw-bold">
                                                        {{ $reviewer->name }}
                                                    </span>
                                                    <br>
                                                    {{ $reviewer->affiliation }}
                                                </li>
                                            @empty
                                                <li style="list-style: none" class="text-muted">Reviewer belum ditambahkan
                                                </li>
                                            @endforelse
                                        </ul>
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
                                            <span
                                                class="badge badge-light-secondary fs-7 fw-bold">{{ $submission->status_label }}</span>
                                        @endif
                                    </td>
                                    @if ($journal->author_fee != 0)
                                        @if ($submission->free_charge)
                                            <td class="text-start">
                                                <span class="badge badge-light-primary fs-7 fw-bold">Free Charge</span>
                                            </td>
                                        @else
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

                                                <ul class="mt-3">
                                                    @foreach ($submission->paymentInvoices as $invoice)
                                                        <li>
                                                            Pembayaran {{ $invoice->payment_percent }}% -
                                                            @if ($invoice->is_paid)
                                                                <span class="text-success fs-7 fw-bold">Lunas</span> <a
                                                                    href="{{ route('back.finance.verification.detail', $invoice->payments()->where('payment_status', 'accepted')->first()->id) }}">(detail)</a>
                                                            @else
                                                                <span class="text-warning fs-7 fw-bold">Belum Dibayar</span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        @endif
                                    @endif
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-light-info my-1" data-bs-toggle="modal"
                                            data-bs-target="#modal_view_article_{{ $submission->submission_id }}">
                                            <i class="ki-duotone ki-eye fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                            </i>
                                        </a>
                                        <a href="#" class="btn btn-sm btn-light-primary my-1" data-bs-toggle="modal"
                                            data-bs-target="#modal_action_article_{{ $submission->submission_id }}">
                                            <i class="ki-duotone ki-menu fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
                                                <span class="path4"></span>
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
    @foreach ($issue->submissions as $submission)
        <div class="modal fade" tabindex="-1" id="modal_view_article_{{ $submission->submission_id }}">
            <div class="modal-dialog modal-lg ">
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

                    <form
                        action="{{ route('back.journal.article.update', [$journal->url_path, $issue->id, $submission->id]) }}"
                        method="POST">
                        @method('PUT')
                        @csrf
                        <div class="modal-body">
                            @if ($journal->author_fee != 0)
                                <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab"
                                            href="#kt_tab_pane_1_submission_{{ $submission->id }}">Informasi</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab"
                                            href="#kt_tab_pane_2_submission_{{ $submission->id }}">History Pembayaran</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active"
                                        id="kt_tab_pane_1_submission_{{ $submission->id }}" role="tabpanel">
                            @endif
                            <div class="mh-550px scroll-y me-n7 pe-7" id="list_article">
                                <table class="table table-row-dashed table-row-gray-300 align-top gs-0 gy-4 my-0 fs-6">
                                    <tr>
                                        <td>Judul</td>
                                        <td>:</td>
                                        <td>
                                            {{ $submission->fullTitle }}
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
                                    <tr>
                                        <td>Editor</td>
                                        <td>:</td>
                                        <td>
                                            <select class="form-select" data-control="select2"
                                                data-placeholder="Select an option"
                                                data-dropdown-parent="#modal_view_article_{{ $submission->submission_id }}"
                                                name="editor[]" data-allow-clear="true" multiple="multiple">
                                                <option></option>
                                                @foreach ($editors as $editor)
                                                    <option value="{{ $editor->id }}"
                                                        {{ $submission->editors->contains($editor->id) ? 'selected' : '' }}>
                                                        {{ $editor->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Reviewer</td>
                                        <td>:</td>
                                        <td>
                                            <select class="form-select" data-control="select2"
                                                data-placeholder="Select an option"
                                                data-dropdown-parent="#modal_view_article_{{ $submission->submission_id }}"
                                                name="reviewer[]" data-allow-clear="true" multiple="multiple">
                                                <option></option>
                                                @foreach ($reviewers as $reviewer)
                                                    <option value="{{ $reviewer->id }}"
                                                        {{ $submission->reviewers->contains($reviewer->id) ? 'selected' : '' }}>
                                                        {{ $reviewer->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Gratis Biaya</td>
                                        <td>:</td>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    @if ($submission->free_charge) checked @endif id="free_charge"
                                                    name="free_charge" />
                                                <label class="form-check-label" for="free_charge">
                                                    Ya, (Gratis Biaya publikasi)
                                                </label>
                                            </div>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Informasi Tambahan</td>
                                        <td>:</td>
                                        <td>
                                            <div class="form-floating mb-5">
                                                <input type="text" class="form-control" id="floatingInput"
                                                    name="author_nik" placeholder="xxxxxxx" value="{{ $submission->author_nik }}" />
                                                <label for="floatingInput">Nomor Induk Kependudukan (NIK)</label>
                                            </div>
                                            <div class="form-floating mb-5">
                                                <input type="text" class="form-control" id="floatingInput"
                                                    name="author_bank_name" placeholder="xxxxxxx" value="{{ $submission->author_bank_name }}" />
                                                <label for="floatingInput">Nama Bank</label>
                                            </div>
                                            <div class="form-floating mb-5">
                                                <input type="text" class="form-control" id="floatingInput"
                                                    name="author_bank_account" placeholder="xxxxxxx" value="{{ $submission->author_bank_account }}" />
                                                <label for="floatingInput">Nomor Rekening</label>
                                            </div>
                                            <div class="form-floating mb-5">
                                                <input type="text" class="form-control" id="floatingInput"
                                                    name="author_npwp" placeholder="xxxxxxx" value="{{ $submission->author_npwp }}" />
                                                <label for="floatingInput">Nomor Pokok Wajib Pajak (NPWP)</label>
                                            </div>
                                            <div class="form-floating mb-5">
                                                <select class="form-select" id="floatingSelect" name="author_golongan"
                                                    aria-label="Floating label select example">
                                                    <option >Pilih Golongan</option>
                                                    <option value="III-A" {{ $submission->author_golongan == 'III-A' ? 'selected' : '' }}>III-A</option>
                                                    <option value="III-B" {{ $submission->author_golongan == 'III-B' ? 'selected' : '' }}>III-B</option>
                                                    <option value="III-C" {{ $submission->author_golongan == 'III-C' ? 'selected' : '' }}>III-C</option>
                                                    <option value="III-D" {{ $submission->author_golongan == 'III-D' ? 'selected' : '' }}>III-D</option>
                                                    <option value="IV-A" {{ $submission->author_golongan == 'IV-A' ? 'selected' : '' }}>IV-A</option>
                                                    <option value="IV-B" {{ $submission->author_golongan == 'IV-B' ? 'selected' : '' }}>IV-B</option>
                                                    <option value="IV-C" {{ $submission->author_golongan == 'IV-C' ? 'selected' : '' }}>IV-C</option>
                                                    <option value="IV-D" {{ $submission->author_golongan == 'IV-D' ? 'selected' : '' }}>IV-D</option>
                                                    <option value="IV-E" {{ $submission->author_golongan == 'IV-E' ? 'selected' : '' }}>IV-E</option>
                                                </select>
                                                <label for="floatingInput">Golongan</label>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @if ($journal->author_fee != 0)
                        </div>
                        <div class="tab-pane fade" id="kt_tab_pane_2_submission_{{ $submission->id }}" role="tabpanel">
                            @foreach ($submission->paymentInvoices as $invoice)
                                <div class="table-responsive">
                                    <table class="table table-hover table-rounded table-striped border gy-7 gs-7">
                                        <thead>
                                            <tr
                                                class="fw-bold text-center fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                                <th colspan="4">INVOICE
                                                    {{ $invoice->invoice_number }}/JRNL/UINSMDD/{{ $invoice->created_at->format('Y') }}
                                                    <br>
                                                    <span class="text-muted fs-7">
                                                        ({{ $invoice->payment_percent }}%)
                                                        - @money($invoice->payment_amount)

                                                    </span>
                                                </th>
                                            </tr>
                                            <tr class="fw-semibold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                                <th>Waktu</th>
                                                <th>Pembayar</th>
                                                <th>Metode Pembayaran</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($invoice->payments as $payment)
                                                <tr>
                                                    <td>
                                                        {{ Carbon\Carbon::parse($payment->created_at)->translatedFormat('l, d F Y H:i:s') }}
                                                    </td>
                                                    <td>
                                                        {{ $payment->payment_account_name }}
                                                    </td>
                                                    <td>
                                                        {{ $payment->payment_method }}
                                                    </td>
                                                    <td>
                                                        @if ($payment->payment_status == 'pending')
                                                            <span
                                                                class="badge badge-light-warning fs-7 fw-bold">{{ $payment->payment_status }}</span>
                                                        @elseif ($payment->payment_status == 'accepted')
                                                            <span
                                                                class="badge badge-light-success fs-7 fw-bold">{{ $payment->payment_status }}</span>
                                                        @elseif ($payment->payment_status == 'rejected')
                                                            <span
                                                                class="badge badge-light-danger fs-7 fw-bold">{{ $payment->payment_status }}</span>
                                                        @else
                                                            <span
                                                                class="badge badge-light-secondary fs-7 fw-bold">{{ $payment->payment_status }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted fw-semibold fs-6">
                                                        Belum ada history pembayaran
                                                    </td>
                                                </tr>
                                            @endforelse

                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                </div>
    @endif

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-warning">Update</button>
    </div>
    </form>
    </div>
    </div>
    </div>
    <div class="modal fade" tabindex="-1" id="modal_delete_article_{{ $submission->submission_id }}">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Hapus Submission</h3>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
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
    <div class="modal fade" tabindex="-1" id="modal_action_article_{{ $submission->submission_id }}">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Menu</h3>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>
                <div class="modal-body">
                    @if ($journal->author_fee != 0)
                        <div class="mb-10">
                            <div class="mb-3">
                                <label class="d-flex align-items-center fs-5 fw-semibold">
                                    <span class="required">Invoice</span>
                                </label>
                                <div class="fs-7 fw-semibold text-muted">
                                    Tagihan 1 - 60% (@money($journal->author_fee * 0.6)) -
                                    @php
                                        $tagihan1 = $submission->paymentInvoices->where('payment_percent', 60)->first();
                                    @endphp
                                    @if ($tagihan1)
                                        @if ($tagihan1->is_paid)
                                            <span class="text-success fs-7 fw-bold">Lunas</span>
                                        @else
                                            <span class="text-warning fs-7 fw-bold">Belum Dibayar</span>
                                        @endif
                                    @else
                                        <span class="text-danger fw-bold">Belum Terbit</span>
                                    @endif
                                </div>
                            </div>
                            <div class="fv-row fv-plugins-icon-container mb-3">
                                <div class="d-flex">
                                    <a href="{{ route('back.journal.invoice.mail-send1', $submission->id) }}"
                                        class="btn btn-light w-100 mx-3 btn-loading">
                                        <i class="ki-duotone ki-send fs-2 ">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Kirim ke Author
                                    </a>
                                    <a href="{{ route('back.journal.invoice.generate1', $submission->id) }}"
                                        class="btn btn-light w-100 mx-3">
                                        <i class="ki-duotone ki-file-down fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Download
                                    </a>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="fs-7 fw-semibold text-muted">
                                    Tagihan 2 - 40% (@money($journal->author_fee * 0.4)) -
                                    @php
                                        $tagihan2 = $submission->paymentInvoices->where('payment_percent', 40)->first();
                                    @endphp
                                    @if ($tagihan2)
                                        @if ($tagihan2->is_paid)
                                            <span class="text-success fs-7 fw-bold">Lunas</span>
                                        @else
                                            <span class="text-warning fs-7 fw-bold">Belum Dibayar</span>
                                        @endif
                                    @else
                                        <span class="text-danger fw-bold">Belum Terbit</span>
                                    @endif
                                </div>
                            </div>
                            <div class="fv-row fv-plugins-icon-container mb-3">
                                <div class="d-flex">
                                    <a href="{{ route('back.journal.invoice.mail-send2', $submission->id) }}"
                                        class="btn btn-light w-100 mx-3 btn-loading">
                                        <i class="ki-duotone ki-send fs-2 ">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Kirim ke Author
                                    </a>
                                    <a href="{{ route('back.journal.invoice.generate2', $submission->id) }}"
                                        class="btn btn-light w-100 mx-3">
                                        <i class="ki-duotone ki-file-down fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Download
                                    </a>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="fs-7 fw-semibold text-muted">
                                    Tagihan 100% (@money($journal->author_fee)) -
                                    @php
                                        $tagihan3 = $submission->paymentInvoices
                                            ->where('payment_percent', 100)
                                            ->first();
                                    @endphp
                                    @if ($tagihan3)
                                        @if ($tagihan3->is_paid)
                                            <span class="text-success fs-7 fw-bold">Lunas</span>
                                        @else
                                            <span class="text-warning fs-7 fw-bold">Belum Dibayar</span>
                                        @endif
                                    @else
                                        <span class="text-danger fw-bold">Belum Terbit</span>
                                    @endif
                                </div>
                            </div>
                            <div class="fv-row fv-plugins-icon-container">
                                <div class="d-flex">
                                    <a href="{{ route('back.journal.invoice.mail-send3', $submission->id) }}"
                                        class="btn btn-light w-100 mx-3 btn-loading">
                                        <i class="ki-duotone ki-send fs-2 ">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Kirim ke Author
                                    </a>
                                    <a href="{{ route('back.journal.invoice.generate3', $submission->id) }}"
                                        class="btn btn-light w-100 mx-3">
                                        <i class="ki-duotone ki-file-down fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="mb-10">
                        <div class="mb-3">

                            <label class="d-flex align-items-center fs-5 fw-semibold">
                                <span class="required">Letter of Acceptence (LOA)</span>
                            </label>
                            @php
                                $check_lunas =
                                    $submission->paymentInvoices->where('is_paid', true)->sum('payment_percent') >= 100
                                        ? true
                                        : false;
                            @endphp
                            <div class="fs-7 fw-semibold text-muted">
                                @if ($submission->free_charge)
                                    LOA dapat dikirim/download tanpa tagihan
                                @elseif ($check_lunas)
                                    <span class="text-success">Tagihan sudah lunas, LOA dapat dikirim/download</span>
                                @else
                                    <span class="text-danger">Tagihan belum lunas, LOA tidak dapat dikirim/download</span>
                                @endif
                            </div>
                        </div>
                        <div class="fv-row fv-plugins-icon-container">

                            @if ($submission->free_charge)
                                <div class="d-flex">


                                    <a href="{{ route('back.journal.loa.mail-send', $submission->id) }}"
                                        class="btn btn-light w-100 mx-3 btn-loading">
                                        <i class="ki-duotone ki-send fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Kirim ke Author
                                    </a>
                                    <a href="{{ route('back.journal.loa.generate', $submission->id) }}"
                                        class="btn btn-light w-100 mx-3 ">
                                        <i class="ki-duotone ki-file-down fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Download
                                    </a>
                                </div>
                            @else
                                @if ($check_lunas)
                                    <div class="d-flex">


                                        <a href="{{ route('back.journal.loa.mail-send', $submission->id) }}"
                                            class="btn btn-light w-100 mx-3 btn-loading">
                                            <i class="ki-duotone ki-send fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Kirim ke Author
                                        </a>
                                        <a href="{{ route('back.journal.loa.generate', $submission->id) }}"
                                            class="btn btn-light w-100 mx-3 ">
                                            <i class="ki-duotone ki-file-down fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Download
                                        </a>
                                    </div>
                                @endif
                            @endif

                        </div>
                    </div>
                </div>
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
                        console.log(response);
                        // Filter out the submissions that are already in the issue
                        let filter_data = response.data.filter(item => {
                            return !submissions.map(Number).includes(item.id);
                        });
                        console.log(filter_data);
                        $('#list_article').html('');
                        filter_data.forEach(submission => {
                            $('#list_article').append(`
                            <div class="border border-hover-primary p-7 rounded mb-7 submission-item" data-title="${submission.publications[0].fullTitle.en_US}" data-id="${submission.id}">
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
