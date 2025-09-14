@extends('front.app')
@section('seo')
    <title>{{ $meta['description'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('contact.index') }}">
    <link rel="canonical" href="{{ route('contact.index') }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
@endsection

@section('content')
    @include('front.partials.breadcrumb')

    <!-- LOGIN AREA START -->
    <div class="ltn__login-area pb-65">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area text-center">
                        <h1 class="section-title">{!! __('front.login_title') !!}</h1>
                        <p>
                            {{ $setting_web->name }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="account-login-inner">
                        <form action="{{ route('login.post') }}" class="ltn__form-box contact-form-box" method="POST" style="padding-bottom: 20px;">
                            @csrf
                            <div class="mb-4">

                                <input class="form-control @error('login') is-invalid @enderror" type="text"
                                    name="login" placeholder="Email/Username*" style="margin-bottom: 0px;" required>
                                @error('login')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-4">

                                <input class="form-control @error('password') is-invalid @enderror" type="password"
                                    name="password" placeholder="Password*" style="margin-bottom: 0px;" required>
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="btn-wrapper mt-0">
                                <button class="theme-btn-1 btn btn-block" type="submit">{{ __('front.sign_in') }}</button>
                            </div>
                            <div class="go-to-btn mt-20">
                                <a href="#"><small>{{ __('front.forgot_password') }}</small></a>
                            </div>
                        </form>

                        <div style="padding: 0px 50px;">
                            <hr style="margin: 0px 0;">
                            <div class="btn-wrapper ">
                                <a href="{{ route('google.redirect') }}" class="btn btn-danger btn-block"
                                    style="background: #ea4335; color: #fff;">
                                    <i class="fab fa-google"></i> Login with Google
                                </a>
                            </div>
                        </div>



                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="account-create text-center pt-50">
                        <h4>{{ __('auth.dont_have_account') }}</h4>
                        <p>
                            {{ __('auth.create_account_info') }}
                        </p>
                        <div class="btn-wrapper">
                            <a href="{{ route('register') }}" class="theme-btn-1 btn black-btn">{{ __('auth.create_account') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- LOGIN AREA END -->
@endsection
