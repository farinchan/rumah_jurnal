@extends('front.app')
@section('seo')
    <title>{{ $meta['title'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('2fa.verify') }}">
    <link rel="canonical" href="{{ route('2fa.verify') }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
@endsection

@section('content')
    @include('front.partials.breadcrumb')

    <!-- 2FA VERIFY AREA START -->
    <div class="ltn__login-area pb-65">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area text-center">
                        <h1 class="section-title">Masukkan Kode Verifikasi</h1>
                        <p>
                            @if($method === 'email')
                                Kode verifikasi telah dikirim ke <strong>{{ $masked_email }}</strong>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-5">
                    <div class="account-login-inner">
                        <form action="{{ route('2fa.verify.post') }}" method="POST" class="ltn__form-box contact-form-box" style="padding-bottom: 20px;">
                            @csrf

                            <div class="mb-4 text-center">
                                <div style="width: 80px; height: 80px; background: #15365F; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                                    <i class="fas fa-shield-alt" style="color: #C3A356; font-size: 35px;"></i>
                                </div>

                                <p class="text-muted mb-4">
                                    Masukkan 6 digit kode yang telah dikirim
                                </p>
                            </div>

                            <div class="mb-4">
                                <div class="otp-input-wrapper" id="code-inputs">
                                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-index="0" autofocus>
                                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-index="1">
                                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-index="2">
                                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-index="3">
                                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-index="4">
                                    <input type="text" class="otp-input" maxlength="1" inputmode="numeric" pattern="[0-9]*" data-index="5">
                                </div>
                                <input type="hidden" name="code" id="code-hidden">
                                @error('code')
                                    <small class="text-danger d-block text-center mt-2">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="btn-wrapper mt-4">
                                <button class="theme-btn-1 btn btn-block" type="submit" id="verify-btn">
                                    <i class="fas fa-check-circle me-2"></i> Verifikasi
                                </button>
                            </div>

                            <div class="text-center mt-4">
                                <p class="text-muted mb-2">Tidak menerima kode?</p>
                                <a href="{{ route('2fa.resend') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-redo me-1"></i> Kirim Ulang Kode
                                </a>
                            </div>

                            <div class="go-to-btn mt-20 text-center">
                                <a href="{{ route('2fa.select-method') }}"><small><i class="fas fa-arrow-left me-1"></i> Pilih Metode Lain</small></a>
                                <span class="mx-2">|</span>
                                <a href="{{ route('2fa.cancel') }}"><small>Batalkan Login</small></a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- 2FA VERIFY AREA END -->
@endsection

@section('styles')
<style>
    .otp-input-wrapper {
        display: flex;
        justify-content: center;
        gap: 10px;
    }
    .ltn__form-box input.otp-input,
    .contact-form-box input.otp-input,
    input.otp-input[type="text"] {
        width: 50px !important;
        height: 60px !important;
        text-align: center !important;
        font-size: 28px !important;
        font-weight: 700 !important;
        color: #000000 !important;
        background-color: #ffffff !important;
        background: #ffffff !important;
        border: 2px solid #e0e0e0 !important;
        border-radius: 10px !important;
        transition: all 0.3s !important;
        -webkit-appearance: none !important;
        -moz-appearance: textfield !important;
        padding: 0 !important;
        margin: 0 5px !important;
        line-height: 60px !important;
        text-indent: 0 !important;
        -webkit-text-fill-color: #000000 !important;
        opacity: 1 !important;
        caret-color: #000000 !important;
    }
    .ltn__form-box input.otp-input::-webkit-outer-spin-button,
    .ltn__form-box input.otp-input::-webkit-inner-spin-button {
        -webkit-appearance: none !important;
        margin: 0 !important;
    }
    .ltn__form-box input.otp-input:focus,
    .contact-form-box input.otp-input:focus,
    input.otp-input[type="text"]:focus {
        border-color: #15365F !important;
        box-shadow: 0 0 0 0.2rem rgba(21, 54, 95, 0.25) !important;
        outline: none !important;
        color: #000000 !important;
        -webkit-text-fill-color: #000000 !important;
        background-color: #ffffff !important;
        background: #ffffff !important;
    }
    .ltn__form-box input.otp-input.filled,
    .contact-form-box input.otp-input.filled,
    input.otp-input[type="text"].filled {
        border-color: #15365F !important;
        background-color: #f0f4f8 !important;
        background: #f0f4f8 !important;
        color: #15365F !important;
        -webkit-text-fill-color: #15365F !important;
    }
    /* Reset any placeholder styling */
    .otp-input::placeholder {
        color: transparent !important;
    }
    .otp-input::-webkit-input-placeholder {
        color: transparent !important;
    }
    .otp-input::-moz-placeholder {
        color: transparent !important;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.otp-input');
        const hiddenInput = document.getElementById('code-hidden');
        const form = document.querySelector('form');

        function updateHiddenInput() {
            let code = '';
            inputs.forEach(input => {
                code += input.value;
            });
            hiddenInput.value = code;
        }

        inputs.forEach((input, index) => {
            // Handle input
            input.addEventListener('input', function(e) {
                let value = e.target.value;

                // Only allow numbers
                value = value.replace(/[^0-9]/g, '');
                e.target.value = value;

                if (value.length === 1) {
                    input.classList.add('filled');
                    // Move to next input
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                } else {
                    input.classList.remove('filled');
                }

                updateHiddenInput();

                // Auto submit when all filled
                if (hiddenInput.value.length === 6) {
                    setTimeout(function() {
                        form.submit();
                    }, 300);
                }
            });

            // Handle keydown for better control
            input.addEventListener('keydown', function(e) {
                // Handle backspace
                if (e.key === 'Backspace') {
                    e.preventDefault();

                    if (input.value !== '') {
                        // Clear current input
                        input.value = '';
                        input.classList.remove('filled');
                        updateHiddenInput();
                    } else if (index > 0) {
                        // Move to previous input and clear it
                        inputs[index - 1].value = '';
                        inputs[index - 1].classList.remove('filled');
                        inputs[index - 1].focus();
                        updateHiddenInput();
                    }
                    return;
                }

                // Handle delete key
                if (e.key === 'Delete') {
                    e.preventDefault();
                    input.value = '';
                    input.classList.remove('filled');
                    updateHiddenInput();
                    return;
                }

                // Handle arrow keys
                if (e.key === 'ArrowLeft' && index > 0) {
                    e.preventDefault();
                    inputs[index - 1].focus();
                    return;
                }
                if (e.key === 'ArrowRight' && index < inputs.length - 1) {
                    e.preventDefault();
                    inputs[index + 1].focus();
                    return;
                }

                // Allow: tab, enter
                if (e.key === 'Tab' || e.key === 'Enter') {
                    return;
                }

                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                if (e.ctrlKey && ['a', 'c', 'v', 'x'].includes(e.key.toLowerCase())) {
                    return;
                }

                // Block non-numeric keys
                if (!/^[0-9]$/.test(e.key)) {
                    e.preventDefault();
                    return;
                }

                // If there's already a value, replace it
                if (input.value.length === 1) {
                    input.value = '';
                }
            });

            // Handle paste
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasteData = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '').slice(0, 6);

                if (pasteData.length > 0) {
                    pasteData.split('').forEach((char, i) => {
                        if (inputs[i]) {
                            inputs[i].value = char;
                            inputs[i].classList.add('filled');
                        }
                    });
                    updateHiddenInput();

                    // Focus on the next empty input or last input
                    const nextEmpty = Array.from(inputs).findIndex(inp => !inp.value);
                    if (nextEmpty !== -1) {
                        inputs[nextEmpty].focus();
                    } else {
                        inputs[inputs.length - 1].focus();
                    }

                    // Auto submit when all filled
                    if (hiddenInput.value.length === 6) {
                        setTimeout(function() {
                            form.submit();
                        }, 300);
                    }
                }
            });

            // Handle focus
            input.addEventListener('focus', function() {
                input.select();
            });

            // Handle click
            input.addEventListener('click', function() {
                input.select();
            });
        });

        // Focus first input on page load
        if (inputs[0]) {
            inputs[0].focus();
        }
    });
</script>
@endsection
