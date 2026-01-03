@extends('front.app')
@section('seo')
    <title>{{ $meta['title'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('2fa.select-method') }}">
    <link rel="canonical" href="{{ route('2fa.select-method') }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
@endsection

@section('content')
    @include('front.partials.breadcrumb')

    <!-- 2FA SELECT METHOD AREA START -->
    <div class="ltn__login-area pb-65">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area text-center">
                        <h1 class="section-title">Verifikasi Dua Langkah (2FA)</h1>
                        <p>
                            Pilih metode untuk menerima kode verifikasi
                        </p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="account-login-inner">
                        <form action="{{ route('2fa.send-code') }}" method="POST" class="ltn__form-box contact-form-box" style="padding-bottom: 20px;">
                            @csrf

                            <div class="mb-4">
                                <h5 class="method-section-title">Pilih Metode Verifikasi</h5>

                                <div class="info-box">
                                    <p><i class="fas fa-info-circle"></i> Kode verifikasi akan dikirim ke metode yang Anda pilih untuk memastikan keamanan akun.</p>
                                </div>

                                <!-- Email Method -->
                                <div class="method-card selected" onclick="selectMethod('email', this)">
                                    <input type="radio" name="method" id="method_email" value="email" checked>
                                    <div class="d-flex align-items-center">
                                        <div class="method-icon active">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div class="method-content">
                                            <div class="method-title">Email</div>
                                            <p class="method-desc">Kirim kode verifikasi ke email terdaftar</p>
                                        </div>
                                    </div>
                                    <div class="check-indicator">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>

                                <!-- SMS Method (Coming Soon) -->
                                <div class="method-card disabled">
                                    <input type="radio" name="method" id="method_sms" value="sms" disabled>
                                    <div class="d-flex align-items-center">
                                        <div class="method-icon inactive">
                                            <i class="fas fa-sms"></i>
                                        </div>
                                        <div class="method-content">
                                            <div class="method-title">SMS <span class="badge-coming-soon">Segera Hadir</span></div>
                                            <p class="method-desc">Kirim kode verifikasi via SMS ke nomor HP</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Authenticator App (Coming Soon) -->
                                <div class="method-card disabled">
                                    <input type="radio" name="method" id="method_authenticator" value="authenticator" disabled>
                                    <div class="d-flex align-items-center">
                                        <div class="method-icon inactive">
                                            <i class="fas fa-mobile-alt"></i>
                                        </div>
                                        <div class="method-content">
                                            <div class="method-title">Authenticator App <span class="badge-coming-soon">Segera Hadir</span></div>
                                            <p class="method-desc">Gunakan Google Authenticator atau aplikasi sejenis</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="btn-wrapper mt-0">
                                <button class="theme-btn-1 btn btn-block" type="submit">
                                    <i class="fas fa-paper-plane me-2"></i> Kirim Kode Verifikasi
                                </button>
                            </div>

                            <div class="go-to-btn mt-20 text-center">
                                <a href="{{ route('2fa.cancel') }}"><small><i class="fas fa-arrow-left me-1"></i> Kembali ke Login</small></a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 2FA SELECT METHOD AREA END -->
@endsection

@section('styles')
<style>
    /* Method Card Styling */
    .method-card {
        border: 2px solid #e0e0e0 !important;
        border-radius: 12px !important;
        padding: 20px !important;
        margin-bottom: 15px !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        background-color: #ffffff !important;
        position: relative !important;
    }
    .method-card:hover:not(.disabled) {
        border-color: #15365F !important;
        background-color: #f8f9fa !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 12px rgba(21, 54, 95, 0.15) !important;
    }
    .method-card.selected {
        border-color: #15365F !important;
        background-color: #f0f4f8 !important;
        box-shadow: 0 4px 12px rgba(21, 54, 95, 0.2) !important;
    }
    .method-card.disabled {
        opacity: 0.5 !important;
        cursor: not-allowed !important;
        background-color: #f5f5f5 !important;
    }
    .method-card .method-icon {
        width: 55px !important;
        height: 55px !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
    }
    .method-card .method-icon.active {
        background: linear-gradient(135deg, #15365F 0%, #1e4a7a 100%) !important;
    }
    .method-card .method-icon.inactive {
        background: #9ca3af !important;
    }
    .method-card .method-icon i {
        font-size: 22px !important;
        color: #C3A356 !important;
    }
    .method-card.disabled .method-icon i {
        color: #ffffff !important;
    }
    .method-card .method-content {
        margin-left: 15px !important;
        flex: 1 !important;
    }
    .method-card .method-title {
        font-size: 17px !important;
        font-weight: 600 !important;
        color: #1f2937 !important;
        margin-bottom: 4px !important;
    }
    .method-card .method-desc {
        font-size: 13px !important;
        color: #6b7280 !important;
        margin: 0 !important;
        line-height: 1.4 !important;
    }
    .method-card .badge-coming-soon {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%) !important;
        color: #ffffff !important;
        font-size: 10px !important;
        padding: 3px 8px !important;
        border-radius: 20px !important;
        margin-left: 8px !important;
        font-weight: 500 !important;
    }
    .method-card .check-indicator {
        position: absolute !important;
        top: 15px !important;
        right: 15px !important;
        width: 24px !important;
        height: 24px !important;
        border-radius: 50% !important;
        border: 2px solid #e0e0e0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.3s ease !important;
    }
    .method-card.selected .check-indicator {
        background: #15365F !important;
        border-color: #15365F !important;
    }
    .method-card.selected .check-indicator i {
        color: #ffffff !important;
        font-size: 12px !important;
    }
    .method-card input[type="radio"] {
        display: none !important;
    }

    /* Section Title */
    .method-section-title {
        font-size: 18px !important;
        font-weight: 600 !important;
        color: #1f2937 !important;
        margin-bottom: 20px !important;
        text-align: center !important;
    }

    /* Info Box */
    .info-box {
        background: #f0f9ff !important;
        border: 1px solid #bae6fd !important;
        border-radius: 10px !important;
        padding: 15px !important;
        margin-bottom: 20px !important;
    }
    .info-box p {
        margin: 0 !important;
        font-size: 13px !important;
        color: #0369a1 !important;
    }
    .info-box i {
        color: #0ea5e9 !important;
        margin-right: 8px !important;
    }
</style>
@endsection

@section('scripts')
<script>
    function selectMethod(method, element) {
        // Remove selected class from all cards
        document.querySelectorAll('.method-card').forEach(function(card) {
            card.classList.remove('selected');
        });

        // Add selected class to clicked card
        element.classList.add('selected');

        // Check the radio button
        document.getElementById('method_' + method).checked = true;
    }
</script>
@endsection
