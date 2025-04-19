<!doctype html>
<html class="no-js" lang="zxx">

@php
    $setting_web = \App\Models\SettingWebsite::first();
@endphp

<head>
    <title>
        @isset($title)
            {{ $title }} |
        @endisset
        {{ $setting_web->name }}
    </title>
    @yield('seo')
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <!-- Place favicon.png in the root directory -->
    <link rel="shortcut icon" href="{{ asset('front/img/favicon.png') }}" type="image/x-icon" />
    <!-- Font Icons css -->
    <link rel="stylesheet" href="{{ asset('front/css/font-icons.css') }}">
    <!-- plugins css -->
    <link rel="stylesheet" href="{{ asset('front/css/plugins.css') }}">
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="{{ asset('front/css/style.css') }}">
    <!-- Responsive css -->
    <link rel="stylesheet" href="{{ asset('front/css/responsive.css') }}">
    @yield('styles')
</head>

<body>
    <!--[if lte IE 9]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="https://browsehappy.com/">upgrade your browser</a> to improve your experience and security.</p>
    <![endif]-->

    <!-- Add your site or application content here -->

    <!-- Body main wrapper start -->
    <div class="body-wrapper">
        @include('front.layouts.header')




        @yield('content')


        @include('front.layouts.footer')


    </div>
    <!-- Body main wrapper end -->

    {{-- <!-- preloader area start -->
    <div class="preloader d-none" id="preloader">
        <div class="preloader-inner">
            <div class="spinner">
                <div class="dot1"></div>
                <div class="dot2"></div>
            </div>
        </div>
    </div>
    <!-- preloader area end --> --}}

    <!-- All JS Plugins -->
    <script src="{{ asset('front/js/plugins.js') }} "></script>
    <!-- Main JS -->
    <script src="{{ asset('front/js/main.js') }}"></script>
    @include('sweetalert::alert')

    @yield('scripts')

</body>

</html>
