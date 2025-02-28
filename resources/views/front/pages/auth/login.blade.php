@extends('front.app')

@section('content')
    @include('front.partials.breadcrumb')

    <!-- LOGIN AREA START -->
    <div class="ltn__login-area pb-65">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area text-center">
                        <h1 class="section-title">Masuk <br> Ke Akun Anda </h1>
                        <p>
                            Rumah Jurnal UIN Sjech M. Djamil Jambek Bukittinggi
                        </p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="account-login-inner">
                        <form action="{{ route("login.post") }}" class="ltn__form-box contact-form-box" method="POST">
                            @csrf
                            <div class="mb-4">

                                <input class="form-control @error('login') is-invalid @enderror" type="text" name="login" placeholder="Email/Username*" style="margin-bottom: 0px;" required>
                                @error('login')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="mb-4">

                                <input class="form-control @error('password') is-invalid @enderror" type="password" name="password" placeholder="Password*" style="margin-bottom: 0px;" required>
                                @error('password')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="btn-wrapper mt-0">
                                <button class="theme-btn-1 btn btn-block" type="submit">SIGN IN</button>
                            </div>
                            <div class="go-to-btn mt-20">
                                <a href="#"><small>Lupa Password?</small></a>
                            </div>
                        </form>
                    </div>
                </div>
                {{-- <div class="col-lg-6">
                    <div class="account-create text-center pt-50">
                        <h4>DON'T HAVE AN ACCOUNT?</h4>
                        <p>Add items to your wishlistget personalised recommendations <br>
                            check out more quickly track your orders register</p>
                        <div class="btn-wrapper">
                            <a href="register.html" class="theme-btn-1 btn black-btn">CREATE ACCOUNT</a>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
    <!-- LOGIN AREA END -->
@endsection
