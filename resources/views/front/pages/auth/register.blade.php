@extends('front.app')
@section('seo')
    <title>{{ $meta['description'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('register') }}">
    <link rel="canonical" href="{{ route('register') }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
@endsection

@section('content')
    @include('front.partials.breadcrumb')

    <!-- REGISTER AREA START -->
    <div class="ltn__login-area pb-65">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area text-center">
                        <h1 class="section-title">{{ __('auth.register') }}</h1>
                        <p>
                            {{ __('auth.create_account_subtitle') }} {{ $setting_web->name }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="account-login-inner">
                        <form action="{{ route('register.post') }}" class="ltn__form-box contact-form-box" method="POST"
                            style="padding-bottom: 20px;">
                            @csrf

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-4">
                                        <input class="form-control @error('name') is-invalid @enderror" type="text"
                                            name="name" value="{{ old('name') }}" placeholder="Full Name*"
                                            style="margin-bottom: 0px;" required>
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <input class="form-control @error('email') is-invalid @enderror" type="email"
                                            name="email" value="{{ old('email') }}" placeholder="Email Address*"
                                            style="margin-bottom: 0px;" required>
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <input class="form-control @error('phone') is-invalid @enderror" type="text"
                                            name="phone" value="{{ old('phone') }}" placeholder="Phone Number"
                                            style="margin-bottom: 0px;">
                                        @error('phone')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <input class="form-control @error('password') is-invalid @enderror" type="password"
                                            name="password" placeholder="Password*" style="margin-bottom: 0px;" required>
                                        @error('password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <input class="form-control @error('password_confirmation') is-invalid @enderror"
                                            type="password" name="password_confirmation" placeholder="Confirm Password*"
                                            style="margin-bottom: 0px;" required>
                                        @error('password_confirmation')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Optional fields for academic information -->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <input class="form-control @error('sinta_id') is-invalid @enderror" type="text"
                                            name="sinta_id" value="{{ old('sinta_id') }}" placeholder="SINTA ID (Optional)"
                                            style="margin-bottom: 0px;">
                                        @error('sinta_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <input class="form-control @error('scopus_id') is-invalid @enderror" type="text"
                                            name="scopus_id" value="{{ old('scopus_id') }}"
                                            placeholder="Scopus ID (Optional)" style="margin-bottom: 0px;">
                                        @error('scopus_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <input class="form-control @error('google_scholar') is-invalid @enderror"
                                            type="text" name="google_scholar" value="{{ old('google_scholar') }}"
                                            placeholder="Google Scholar URL (Optional)" style="margin-bottom: 0px;">
                                        @error('google_scholar')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-12">
                                    <label class="mb-0 input-info-save">
                                        <input type="checkbox" name="agree_terms" required>
                                        Saya setuju dengan <a href="{{ route('terms.service') }}" target="_blank">Syarat
                                            dan Ketentuan</a>
                                        serta <a href="{{ route('privacy.policy') }}" target="_blank">Kebijakan
                                            Privasi</a>
                                    </label>
                                    @error('agree_terms')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-12">

                                    {!! NoCaptcha::renderJs() !!}
                                    {!! NoCaptcha::display() !!}
    
                                    @error('g-recaptcha-response')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="btn-wrapper mt-0">
                                <button class="theme-btn-1 btn btn-block"
                                    type="submit">{{ __('auth.create_account') }}</button>
                            </div>

                            <div class="go-to-btn mt-20 text-center">
                                <span>{{ __('auth.already_have_account') }} <a
                                        href="{{ route('login') }}">{{ __('front.sign_in') }}</a></span>
                            </div>
                        </form>

                        <div style="padding: 0px 50px;">
                            <hr style="margin: 20px 0;">
                            <div class="btn-wrapper">
                                <a href="{{ route('google.redirect') }}" class="btn btn-danger btn-block"
                                    style="background: #ea4335; color: #fff;">
                                    <i class="fab fa-google"></i> Register with Google
                                </a>
                                <small class="text-muted text-center d-block mt-2">
                                    Dengan menggunakan login Google, Anda menyetujui <a
                                        href="{{ route('privacy.policy') }}" target="_blank">Kebijakan Privasi</a> kami.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- REGISTER AREA END -->
@endsection
