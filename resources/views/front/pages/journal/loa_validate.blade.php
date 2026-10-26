@extends('front.app')

@section('seo')
    <title>{{ $meta['title'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="{{ $setting_web->name ?? 'Rumah Jurnal' }}">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('loa.validate') }}">
    <link rel="canonical" href="{{ route('loa.validate') }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon'] ?? '') }}">
@endsection

@section('content')
    @include('front.partials.breadcrumb')

    <div class="ltn__contact-message-area mb-120 mt-50">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 offset-lg-2 col-md-10 col-12">

                    @if ($isValid && $submission)
                        <!-- VALID LOA CARD -->
                        <div class="ltn__form-box contact-form-box box-shadow white-bg p-5" style="border-radius: 12px;">

                            <!-- Success Header -->
                            <div class="text-center mb-40 p-4" style="background-color: #f4fbf7; border: 1px solid #d1f2e1; border-radius: 10px;">
                                <div class="mb-3">
                                    <i class="fas fa-check-circle" style="font-size: 56px; color: #28a745;"></i>
                                </div>
                                <h3 class="font-weight-bold mb-10" style="color: #15365F; font-size: 24px;">Letter of Acceptance Terverifikasi</h3>
                                <p class="mb-0 text-muted" style="font-size: 14px;">Dokumen ini adalah asli dan diterbitkan secara resmi oleh Penerbit {{ $setting_web->name ?? 'Rumah Jurnal' }}.</p>
                            </div>

                            <!-- Validation Details -->
                            <div class="ltn__team-details-member-about">
                                <h4 class="title-2 mb-20">Detail Dokumen</h4>
                                <table class="table table-striped table-bordered" style="font-size: 15px; width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td style="width: 30%; background-color: #fcfcfc;"><strong>Nomor Surat</strong></td>
                                            <td>
                                                <span class="badge" style="background-color: #e8f5e9; color: #2e7d32; font-size: 13px; font-weight: 600; padding: 6px 12px;">
                                                    {{ $submission->number ?? "0000" }}/LoA/JRNL/RJ/{{ $submission->created_at ? $submission->created_at->format('Y') : date('Y') }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #fcfcfc;"><strong>Submission ID</strong></td>
                                            <td><strong>#{{ $submission->submission_id }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #fcfcfc;"><strong>Judul Artikel</strong></td>
                                            <td style="font-style: italic; line-height: 1.5; font-weight: 500;">
                                                “{{ $submission->fullTitle ?: 'Tanpa Judul' }}”
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #fcfcfc;"><strong>Penulis</strong></td>
                                            <td>
                                                @if (request()->filled('author_id') && !empty($author))
                                                    <div><strong>{{ $author['name'] ?? '-' }}</strong></div>
                                                    @if(!empty($author['affiliation']) && $author['affiliation'] !== '-')
                                                        <div class="text-muted" style="font-size: 13px;">Afiliasi: {{ $author['affiliation'] }}</div>
                                                    @endif
                                                @elseif (!empty($submission->authors) && count($submission->authors) > 0)
                                                    <div class="d-flex flex-column gap-1">
                                                        @foreach ($submission->authors as $index => $auth)
                                                            <div style="margin-bottom: 6px;">
                                                                <strong>{{ $auth['name'] ?? '-' }}</strong>
                                                                @if (!empty($auth['affiliation']) && $auth['affiliation'] !== '-')
                                                                    <div class="text-muted" style="font-size: 13px;">{{ $auth['affiliation'] }}</div>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <strong>{{ $submission->authorsString ?? '-' }}</strong>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #fcfcfc;"><strong>Nama Jurnal</strong></td>
                                            <td><strong>{{ $submission->issue?->journal?->title ?? '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #fcfcfc;"><strong>Edisi / Terbitan</strong></td>
                                            <td>Vol. {{ $submission->issue?->volume ?? '-' }} No. {{ $submission->issue?->number ?? '-' }} Tahun {{ $submission->issue?->year ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #fcfcfc;"><strong>Penerbit</strong></td>
                                            <td>{{ $setting_web->name ?? 'Rumah Jurnal' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #fcfcfc;"><strong>Status Verifikasi</strong></td>
                                            <td>
                                                <span class="badge" style="background-color: #28a745; color: #fff; font-size: 13px; padding: 6px 12px; border-radius: 4px;">
                                                    <i class="fas fa-shield-alt me-1"></i> Terverifikasi Resmi
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-success mt-30 p-4" style="background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; border-radius: 8px; font-size: 14px; line-height: 1.5;">
                                <strong>Catatan Keaslian:</strong> Informasi di atas dicocokkan langsung secara real-time dengan database penerbitan kami. Jika data pada lembar fisik berbeda dengan informasi di atas, dokumen tersebut diindikasikan palsu atau telah mengalami perubahan tanpa izin.
                            </div>

                            <hr class="mt-40 mb-30">

                            <div class="d-flex justify-content-center gap-3 flex-wrap">
                                <a href="{{ route('loa.validate') }}" class="btn btn-secondary text-uppercase" style="padding: 12px 30px; font-weight: 600; border-radius: 4px;">Cek Dokumen Lain</a>
                                <a href="{{ route('home') }}" class="btn theme-btn-1 btn-effect-1 text-uppercase" style="padding: 12px 30px; font-weight: bold; border-radius: 4px;">Kembali ke Beranda</a>
                            </div>

                        </div>
                    @elseif ($hasSearched)
                        <!-- INVALID LOA CARD -->
                        <div class="ltn__form-box contact-form-box box-shadow white-bg p-5" style="border-radius: 12px;">

                            <!-- Danger Header -->
                            <div class="text-center mb-40 p-4" style="background-color: #fff5f5; border: 1px solid #ffe3e3; border-radius: 10px;">
                                <div class="mb-3">
                                    <i class="fas fa-times-circle" style="font-size: 56px; color: #dc3545;"></i>
                                </div>
                                <h3 class="font-weight-bold mb-10" style="color: #dc3545; font-size: 24px;">Letter of Acceptance Tidak Valid</h3>
                                <p class="mb-0 text-muted" style="font-size: 14px;">Dokumen dengan ID <strong>"{{ $searchedSubmissionId }}"</strong> tidak ditemukan di database kami.</p>
                            </div>

                            <div class="p-4 mb-30 text-center" style="font-size: 15px; line-height: 1.6; color: #555;">
                                <p>Sistem kami tidak dapat menemukan catatan Letter of Acceptance (LoA) yang valid berdasarkan parameter yang diberikan. Mohon periksa kembali link barcode atau file PDF yang Anda pindai.</p>
                            </div>

                            <div class="alert alert-danger p-4 mb-30" style="background-color: #fdf2f2; color: #9b1c1c; border: 1px solid #fbd5d5; border-radius: 8px; font-size: 14px; line-height: 1.6;">
                                <strong style="display: block; margin-bottom: 5px;">Kemungkinan Penyebab:</strong>
                                <ul style="margin: 0; padding-left: 20px;">
                                    <li>Nomor Submission atau ID Penulis tidak sesuai dalam database.</li>
                                    <li>Dokumen LoA telah dibatalkan atau ditarik kembali oleh pihak editorial.</li>
                                    <li>Dokumen dibuat secara ilegal (palsu).</li>
                                </ul>
                            </div>

                            <!-- Form Cari Ulang -->
                            <div class="card p-4 mb-30" style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px;">
                                <h5 class="mb-3" style="font-size: 16px; color: #15365F;"><i class="fas fa-search me-2"></i> Coba Verifikasi Ulang</h5>
                                <form action="{{ route('loa.validate') }}" method="GET">
                                    <div class="input-group">
                                        <input type="text" name="submission_id" class="form-control" placeholder="Masukkan Submission ID (contoh: 1683)" required value="{{ $searchedSubmissionId }}" style="height: 48px; border-radius: 4px 0 0 4px;">
                                        <button class="btn theme-btn-1" type="submit" style="padding: 0 25px; font-weight: bold; border-radius: 0 4px 4px 0;">Cek Sekarang</button>
                                    </div>
                                </form>
                            </div>

                            <p class="text-center text-muted" style="font-size: 13px;">
                                Hubungi tim redaksi jurnal jika Anda yakin ini adalah kesalahan teknis.
                            </p>

                            <hr class="mt-40 mb-30">

                            <div class="btn-wrapper text-center">
                                <a href="{{ route('home') }}" class="btn theme-btn-1 btn-effect-1 text-uppercase" style="padding: 12px 35px; font-weight: bold; border-radius: 4px;">Kembali ke Beranda</a>
                            </div>

                        </div>
                    @else
                        <!-- SEARCH FORM CARD (Direct Visit) -->
                        <div class="ltn__form-box contact-form-box box-shadow white-bg p-5" style="border-radius: 12px;">

                            <!-- Info Header -->
                            <div class="text-center mb-40 p-4" style="background-color: #f0f7ff; border: 1px solid #cce5ff; border-radius: 10px;">
                                <div class="mb-3">
                                    <i class="fas fa-stamp" style="font-size: 56px; color: #15365F;"></i>
                                </div>
                                <h3 class="font-weight-bold mb-10" style="color: #15365F; font-size: 24px;">Verifikasi Keaslian LoA</h3>
                                <p class="mb-0 text-muted" style="font-size: 14px;">Periksa keaslian dokumen Letter of Acceptance (LoA) yang diterbitkan oleh {{ $setting_web->name ?? 'Rumah Jurnal' }}.</p>
                            </div>

                            <form action="{{ route('loa.validate') }}" method="GET" class="mb-30">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold mb-2" style="font-size: 14px; color: #333;">Submission ID / Nomor Artikel:</label>
                                    <input type="text" name="submission_id" class="form-control" placeholder="Contoh: 1683" required style="height: 50px; font-size: 15px; border-radius: 6px;">
                                    <small class="text-muted mt-1 d-block">Masukkan ID Submission naskah yang tertera pada lembar LoA Anda.</small>
                                </div>

                                <div class="btn-wrapper text-center mt-4">
                                    <button type="submit" class="btn theme-btn-1 btn-effect-1 text-uppercase w-100" style="padding: 15px 40px; font-weight: bold; border-radius: 6px;">
                                        <i class="fas fa-search me-2"></i> Verifikasi Dokumen
                                    </button>
                                </div>
                            </form>

                            <div class="p-3 text-center" style="background-color: #fafafa; border-radius: 6px; font-size: 13px; color: #666;">
                                <i class="fas fa-qrcode me-1 text-primary"></i> <strong>Tips:</strong> Anda juga dapat memindai langsung kode QR yang tertera di sudut kiri bawah dokumen PDF LoA menggunakan kamera smartphone.
                            </div>

                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
