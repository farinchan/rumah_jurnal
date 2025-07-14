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
                            <a href="#" class="btn btn-sm btn-primary my-1 me-3" data-bs-toggle="modal" id="btn_add_reviewer"
                                data-bs-target="#modal_select_article">
                                <i class="ki-duotone ki-plus fs-2"></i> Tambah Reviewer
                            </a>
                            <a href="{{ route('back.journal.reviewer.export', [$journal->url_path, $issue->id]) }}"
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
                                        <th class="">No</th>
                                        <th class="min-w-250px">Reviewer</th>
                                        <th class="min-w-150px text-start">Email</th>
                                        <th class="min-w-100px text-start">No. Telp</th>
                                        <th class="min-w-150px text-start">Rekening</th>
                                        <th class="min-w-150px text-start">NPWP</th>
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
                                                    <div class="d-flex align-items-center">
                                                        <a href="#" target="_blank"
                                                            class="text-gray-800 text-hover-primary mb-1 me-2">{{ $reviewer->name }}
                                                        </a>
                                                        @if ($reviewer->number)
                                                            <a href="#" data-bs-toggle="tooltip"
                                                                data-bs-placement="top" title="Sertifikat Sudah Dikirim"><i
                                                                    class="ki-outline ki-file-added fs-2 text-primary"></i></a>
                                                        @endif
                                                    </div>
                                                    <span>
                                                        {{ $reviewer->affiliation }}
                                                    </span>
                                                    <span class="">
                                                        NIK. {{ $reviewer->nik ?? '-' }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="text-start">
                                                <span class="text-bold text-gray-800">
                                                    {{ $reviewer->email }}
                                                </span>

                                                <span class="badge badge-light-warning cursor-pointer my-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modal_send_email_{{ $reviewer->reviewer_id }}">
                                                    <i class="ki-duotone ki-sms fs-5 text-warning me-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Kirim Email
                                                </span>
                                                <a href="{{ route('back.journal.reviewer.certificate.send-mail', [$journal->url_path, $issue->id, $reviewer->id]) }}"
                                                    class="badge badge-light-success cursor-pointer my-1 btn-loading">
                                                    <i class="ki-duotone ki-sms fs-5 text-success me-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Kirim Sertifikat
                                                </a>
                                                <a href="{{ route('back.journal.reviewer.certificate.download', [$journal->url_path, $issue->id, $reviewer->id]) }}"
                                                    class="badge badge-light-success cursor-pointer my-1 btn-loading">
                                                    <i class="ki-duotone ki-file-down fs-5 text-success me-3">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Download Sertifikat
                                                </a>
                                            </td>
                                            <td class="text-start">
                                                {{ $reviewer->phone }}
                                            </td>
                                            <td class="text-start">
                                                <span class="fw-bold">{{ $reviewer->account_bank }}</span><br>
                                                @if ($reviewer->account_number)
                                                    No. Rek: {{ $reviewer->account_number }}
                                                @endif
                                            </td>
                                            <td class="text-start">
                                                @if ($reviewer->npwp)
                                                    <span class="fw-bold">{{ $reviewer->npwp }}</span>
                                                @else
                                                    <span class="text-muted">Tidak ada NPWP</span>
                                                @endif
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
                            <h3 class="text-gray-800">Sertifikat Reviewer</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="fs-6 fw-semibold text-gray-600 pb-6 d-block">
                            Sertifikat reviewer untuk edisi ini akan dibuat secara otomatis berdasarkan data reviewer yang
                            telah ditambahkan. Sertifikat ini dapat diunduh dan dikirim melalui email kepada semua reviewer
                            yang terdaftar.
                        </p>
                        <div class="btn-group w-100 mb-5">
                            <a href="{{ route('back.journal.reviewer.certificate.download', [$journal->url_path, $issue->id]) }}"
                                class="btn btn-light-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                                title="Lihat File Sertifikat ">
                                <i class="ki-duotone ki-file-down fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Unduh
                            </a>

                            <a href="{{ route('back.journal.reviewer.certificate.send-mail', [$journal->url_path, $issue->id]) }}"
                                class="btn btn-light-success btn-loading" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Kirim Sertifikat ke semua editor via email">
                                <i class="ki-duotone ki-file-added fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Kirim
                            </a>
                        </div>

                    </div>
                </div>
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
                            <form
                                action="{{ route('back.journal.reviewer.file-sk.store', [$journal->url_path, $issue->id]) }}"
                                method="POST" enctype="multipart/form-data" class="d-flex flex-grow-1">
                                @csrf
                                <div class="flex-grow-1 me-3">
                                    <input type="file" class="form-control form-control-solid" name="file"
                                        id="sk_reviewer" accept=".pdf" />
                                    <small class="form-text text-muted">
                                        File harus dalam format PDF
                                    </small>
                                </div>
                                <button type="submit" class="btn btn-primary btn-icon flex-shrink-0"
                                    data-bs-toggle="tooltip" data-bs-placement="right"
                                    title="Upload untuk menambah/mengubah SK">
                                    <i class="ki-duotone ki-file-up fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </button>
                            </form>
                        </div>

                        @if ($file_sk)
                            <div class="btn-group w-100 mb-5">
                                <a href="{{ Storage::url($file_sk->file) }}" class="btn btn-light-primary"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat File SK ">
                                    <i class="ki-duotone ki-eye fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Lihat
                                </a>

                                <a href="{{ route('back.journal.reviewer.file-sk.send-mail', [$journal->url_path, $issue->id]) }}"
                                    class="btn btn-light-success btn-loading" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Kirim SK ke semua reviewer via email">
                                    <i class="ki-duotone ki-file-added fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Kirim
                                </a>
                            </div>
                        @endif

                    </div>
                </div>
                <div class="card card-xxl-stretch mb-5 mb-xxl-10">
                    <div class="card-header">
                        <div class="card-title">
                            <h3 class="text-gray-800">Pembayaran Reviewer</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <span class="fs-5 fw-semibold text-gray-600 pb-6 d-block">
                            Upload File Pembayaran Reviewer untuk edisi ini
                        </span>
                        <div class="d-flex align-self-center mb-3">
                            <form
                                action="{{ route('back.journal.reviewer.file-fee.store', [$journal->url_path, $issue->id]) }}"
                                method="POST" enctype="multipart/form-data" class="d-flex flex-grow-1">
                                @csrf
                                <div class="flex-grow-1 me-3">
                                    <input type="file" class="form-control form-control-solid" name="file"
                                        id="file_fee_reviewer" accept=".pdf" />
                                    <small class="form-text text-muted">
                                        File harus dalam format PDF
                                    </small>
                                </div>
                                <button type="submit" class="btn btn-primary btn-icon flex-shrink-0"
                                    data-bs-toggle="tooltip" data-bs-placement="right"
                                    title="Upload untuk menambah/mengubah File Fee">
                                    <i class="ki-duotone ki-file-up fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </button>
                            </form>
                        </div>

                        @if ($file_fee)
                            <div class="btn-group w-100 mb-5">
                                <a href="{{ Storage::url($file_fee->file) }}" class="btn btn-light-primary"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat File Fee ">
                                    <i class="ki-duotone ki-eye fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                    Lihat
                                </a>

                                <a href="{{ route('back.journal.reviewer.file-fee.send-mail', [$journal->url_path, $issue->id]) }}"
                                    class="btn btn-light-success btn-loading" data-bs-toggle="tooltip"
                                    data-bs-placement="top" title="Kirim File Fee ke semua reviewer via email">
                                    <i class="ki-duotone ki-file-added fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Kirim
                                </a>
                            </div>
                        @endif

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
                    <form
                        action="{{ route('back.journal.reviewer.update', [$journal->url_path, $issue->id, $reviewer->id]) }}"
                        method="post">
                        @csrf
                        @method('PUT')
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
                                <tr>
                                    <td class="required">NIK</td>
                                    <td>:</td>
                                    <td>
                                        <input type="text" class="form-control" name="nik"
                                            value="{{ $reviewer->nik }}" placeholder="Nomor Induk Kependudukan"
                                            required />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="required">Rekening</td>
                                    <td>:</td>
                                    <td>
                                        <select class="form-select" data-control="select2" data-placeholder="Pilih Bank"
                                            data-dropdown-parent="#modal_view_article_{{ $reviewer->reviewer_id }}"
                                            name="account_bank" data-allow-clear="false" required>
                                            <option></option>
                                            @php
                                                $banks = [
                                                    'Bank Mandiri',
                                                    'Bank Rakyat Indonesia (BRI)',
                                                    'Bank Central Asia (BCA)',
                                                    'Bank Negara Indonesia (BNI)',
                                                    'Bank Tabungan Negara (BTN)',
                                                    'Bank CIMB Niaga',
                                                    'Bank Danamon',
                                                    'Bank Permata',
                                                    'Bank Panin',
                                                    'Bank Maybank Indonesia',
                                                    'Bank Mega',
                                                    'Bank Sinarmas',
                                                    'Bank Bukopin',
                                                    'Bank OCBC NISP',
                                                    'Bank BTPN',
                                                    'Bank JTrust Indonesia',
                                                    'Bank UOB Indonesia',
                                                    'Bank Commonwealth',
                                                    'Bank BJB',
                                                    'Bank DKI',
                                                    'Bank Jateng',
                                                    'Bank Jatim',
                                                    'Bank Sumut',
                                                    'Bank Nagari',
                                                    'Bank Sumsel Babel',
                                                    'Bank Lampung',
                                                    'Bank Kalsel',
                                                    'Bank Kaltimtara',
                                                    'Bank Kalteng',
                                                    'Bank Sulselbar',
                                                    'Bank SulutGo',
                                                    'Bank NTB Syariah',
                                                    'Bank NTT',
                                                    'Bank Maluku Malut',
                                                    'Bank Papua',
                                                    'Bank Bengkulu',
                                                    'Bank Sulteng',
                                                    'Bank Sultra',
                                                    'Bank Aceh Syariah',
                                                    'Bank Banten',
                                                    'Bank Muamalat',
                                                    'Bank Syariah Indonesia (BSI)',
                                                    'Bank Victoria',
                                                    'Bank MNC',
                                                    'Bank Artos Indonesia',
                                                    'Bank QNB Indonesia',
                                                    'Bank INA Perdana',
                                                    'Bank Amar Indonesia',
                                                    'Bank Capital Indonesia',
                                                    'Bank Harda Internasional',
                                                    'Bank Index Selindo',
                                                    'Bank Mestika Dharma',
                                                    'Bank Mayapada',
                                                    'Bank Mayora',
                                                    'Bank Multiarta Sentosa',
                                                    'Bank Nationalnobu',
                                                    'Bank Prima Master',
                                                    'Bank Sahabat Sampoerna',
                                                    'Bank SBI Indonesia',
                                                    'Bank Seabank Indonesia',
                                                    'Bank Shinhan Indonesia',
                                                    'Bank Woori Saudara',
                                                    'Bank HSBC Indonesia',
                                                    'Bank Standard Chartered',
                                                    'Bank Citibank',
                                                    'Bank ANZ Indonesia',
                                                    'Bank DBS Indonesia',
                                                    'Bank Resona Perdania',
                                                    'Bank Mizuho Indonesia',
                                                    'Bank Sumitomo Mitsui Indonesia',
                                                    'Bank BNP Paribas Indonesia',
                                                    'Bank Rabobank International Indonesia',
                                                    'Bank of China Indonesia',
                                                    'Bank of India Indonesia',
                                                    'Bank ICBC Indonesia',
                                                    'Bank CTBC Indonesia',
                                                    'Bank Maybank Syariah Indonesia',
                                                    'Bank BCA Syariah',
                                                    'Bank BRI Syariah',
                                                    'Bank BNI Syariah',
                                                    'Bank Panin Dubai Syariah',
                                                    'Bank Victoria Syariah',
                                                    'Bank Aladin Syariah',
                                                    'Bank Jago',
                                                    'Bank Neo Commerce',
                                                    'Bank Digital BCA (blu)',
                                                    'Bank SeaBank',
                                                    'Bank Allo Bank',
                                                    'Bank Raya Indonesia',
                                                    'Bank KEB Hana Indonesia',
                                                    'Bank Mandiri Taspen',
                                                    'Bank Fama International',
                                                    'Bank Bisnis Internasional',
                                                    'Bank Oke Indonesia',
                                                    'Bank KB Bukopin Syariah',
                                                    'Bank BSI (Bank Syariah Indonesia)',
                                                ];
                                            @endphp
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank }}"
                                                    {{ isset($reviewer->account_bank) && $reviewer->account_bank == $bank ? 'selected' : '' }}>
                                                    {{ $bank }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" class="form-control mt-2" placeholder="No. Rekening"
                                            name="account_number" value="{{ $reviewer->account_number }}" required />

                                    </td>
                                </tr>
                                <tr>
                                    <td class="">NPWP</td>
                                    <td>:</td>
                                    <td>
                                        <input type="text" class="form-control" name="npwp"
                                            value="{{ $reviewer->npwp }}" placeholder="Nomor Pokok Wajib Pajak" />
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
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
