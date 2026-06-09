@extends('front.app')

@section('seo')
    <title>{{ $meta['title'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('loa.validate') }}">
    <link rel="canonical" href="{{ route('loa.validate') }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
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
                                <p class="mb-0 text-muted" style="font-size: 14px;">Dokumen ini adalah asli dan diterbitkan secara resmi oleh Penerbit Rumah Jurnal.</p>
                            </div>

                            <!-- Validation Details -->
                            <div class="ltn__team-details-member-about">
                                <h4 class="title-2 mb-20">Detail Dokumen</h4>
                                <table class="table table-striped table-bordered" style="font-size: 15px; width: 100%;">
                                    <tbody>
                                        <tr>
                                            <td style="width: 30%; background-color: #fcfcfc;"><strong>Nomor Surat</strong></td>
                                            <td>{{ $submission->number ?? "0000" }}/LoA/JRNL/RJ/{{ $submission->created_at->format('Y') ?? date('Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #fcfcfc;"><strong>Judul Artikel</strong></td>
                                            <td style="font-style: italic; line-height: 1.5;">“{{ $submission->fullTitle }}”</td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #fcfcfc;"><strong>Nama Penulis</strong></td>
                                            <td><strong>{{ $author['name'] ?? '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #fcfcfc;"><strong>Afiliasi</strong></td>
                                            <td>{{ $author['affiliation'] ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #fcfcfc;"><strong>Nama Jurnal</strong></td>
                                            <td><strong>{{ $submission->issue->journal->title ?? '-' }}</strong></td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #fcfcfc;"><strong>Edisi / Terbitan</strong></td>
                                            <td>Vol. {{ $submission->issue->volume ?? '-' }} No. {{ $submission->issue->number ?? '-' }} Tahun {{ $submission->issue->year ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="background-color: #fcfcfc;"><strong>Penerbit</strong></td>
                                            <td>{{ $setting_web->name }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="alert alert-success mt-30 p-4" style="background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; border-radius: 8px; font-size: 14px; line-height: 1.5;">
                                <strong>Catatan Keaslian:</strong> Informasi di atas dicocokkan langsung dengan database Rumah Jurnal UIN Sjech M. Djamil Djambek Bukittinggi. Jika data pada lembar fisik berbeda dengan informasi di atas, dokumen tersebut diindikasikan palsu.
                            </div>
                            
                            <hr class="mt-40 mb-30">
                            
                            <div class="btn-wrapper text-center">
                                <a href="{{ route('home') }}" class="btn theme-btn-1 btn-effect-1 text-uppercase" style="padding: 15px 40px; font-weight: bold; border-radius: 4px;">Kembali ke Beranda</a>
                            </div>

                        </div>
                    @else
                        <!-- INVALID LOA CARD -->
                        <div class="ltn__form-box contact-form-box box-shadow white-bg p-5" style="border-radius: 12px;">
                            
                            <!-- Danger Header -->
                            <div class="text-center mb-40 p-4" style="background-color: #fff5f5; border: 1px solid #ffe3e3; border-radius: 10px;">
                                <div class="mb-3">
                                    <i class="fas fa-times-circle" style="font-size: 56px; color: #dc3545;"></i>
                                </div>
                                <h3 class="font-weight-bold mb-10" style="color: #dc3545; font-size: 24px;">Letter of Acceptance Tidak Valid</h3>
                                <p class="mb-0 text-muted" style="font-size: 14px;">Dokumen ini tidak terdaftar di database kami.</p>
                            </div>

                            <div class="p-4 mb-30 text-center" style="font-size: 16px; line-height: 1.6; color: #555;">
                                <p>Sistem kami tidak dapat menemukan catatan Letter of Acceptance (LoA) yang valid berdasarkan parameter yang diberikan. Mohon periksa kembali link barcode atau file PDF yang Anda pindai.</p>
                            </div>
                            
                            <div class="alert alert-danger p-4 mb-30" style="background-color: #fdf2f2; color: #9b1c1c; border: 1px solid #fbd5d5; border-radius: 8px; font-size: 14px; line-height: 1.6;">
                                <strong style="display: block; margin-bottom: 5px;">Kemungkinan Penyebab:</strong>
                                <ul style="margin: 0; padding-left: 20px;">
                                    <li>Dokumen LoA telah dibatalkan atau ditarik kembali oleh pihak editorial.</li>
                                    <li>Nomor Submission atau ID Penulis tidak sesuai dalam format URL.</li>
                                    <li>Dokumen dibuat secara ilegal (palsu).</li>
                                </ul>
                            </div>

                            <p class="text-center text-muted" style="font-size: 13px;">
                                Hubungi tim redaksi jurnal jika Anda yakin ini adalah kesalahan teknis.
                            </p>
                            
                            <hr class="mt-40 mb-30">
                            
                            <div class="btn-wrapper text-center">
                                <a href="{{ route('home') }}" class="btn theme-btn-1 btn-effect-1 text-uppercase" style="padding: 15px 40px; font-weight: bold; border-radius: 4px;">Kembali ke Beranda</a>
                            </div>

                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
