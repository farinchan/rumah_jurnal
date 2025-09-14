@extends('front.app')
@section('seo')
    <title>{{ $meta['description'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
@endsection

@section('content')
    @include('front.partials.breadcrumb')

    <!-- RESET PASSWORD AREA START -->
    <div class="ltn__login-area pb-65">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area text-center">
                        <h1 class="section-title">{{ __('auth.reset_password') }}</h1>
                        <p>
                            {{ __('auth.reset_password_subtitle') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="account-login-inner">
                        <form action="{{ route('password.update') }}" class="ltn__form-box contact-form-box" method="POST" style="padding-bottom: 20px;">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="mb-4">
                                <input class="form-control @error('email') is-invalid @enderror" type="email"
                                    name="email" value="{{ $email ?? old('email') }}" placeholder="{{ __('auth.email_address') }}*" style="margin-bottom: 0px;" required readonly>
                                @error('email')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <input class="form-control @error('password') is-invalid @enderror" type="password"
                                    name="password" placeholder="{{ __('auth.new_password') }}*" style="margin-bottom: 0px;" required>
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <input class="form-control @error('password_confirmation') is-invalid @enderror" type="password"
                                    name="password_confirmation" placeholder="{{ __('auth.confirm_new_password') }}*" style="margin-bottom: 0px;" required>
                                @error('password_confirmation')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="btn-wrapper mt-0">
                                <button class="theme-btn-1 btn btn-block" type="submit">{{ __('auth.reset_password_button') }}</button>
                            </div>

                            <div class="go-to-btn mt-20 text-center">
                                <span>{{ __('auth.remember_password') }} <a href="{{ route('login') }}">{{ __('front.sign_in') }}</a></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- RESET PASSWORD AREA END -->
@endsection
