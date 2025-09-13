@extends('front.app')
@section('seo')
    <title>{{ $meta['description'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('profil.show', $menu_profil->slug) }}">
    <link rel="canonical" href="{{ route('profil.show', $menu_profil->slug) }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
@endsection
@section('styles')
    <style>
        #content-wrapper {
            all: initial !important;
            font-family: inherit !important;
            color: inherit !important;
            display: block !important;
            contain: layout style !important;
            isolation: isolate !important;
        }

        #content-wrapper *,
        #content-wrapper *::before,
        #content-wrapper *::after {
            all: unset !important;
            font-family: inherit !important;
            color: inherit !important;
            display: revert !important;
            box-sizing: border-box !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            background: none !important;
            text-decoration: none !important;
            list-style: none !important;
            outline: none !important;
            box-shadow: none !important;
            text-shadow: none !important;
            transform: none !important;
            transition: none !important;
            animation: none !important;
            filter: none !important;
            opacity: 1 !important;
            z-index: auto !important;
            position: static !important;
            float: none !important;
            clear: none !important;
            overflow: visible !important;
            clip: auto !important;
            visibility: visible !important;
        }

        #content-wrapper p {
            display: block !important;
            margin: 1em 0 !important;
            line-height: 1.6 !important;
            text-align: left !important;
            font-size: 1rem !important;
            font-weight: normal !important;
        }

        #content-wrapper h1 {
            display: block !important;
            font-size: 2em !important;
            font-weight: bold !important;
            margin: 1em 0 !important;
            line-height: 1.2 !important;
        }

        #content-wrapper h2 {
            display: block !important;
            font-size: 1.5em !important;
            font-weight: bold !important;
            margin: 1em 0 !important;
            line-height: 1.2 !important;
        }

        #content-wrapper h3 {
            display: block !important;
            font-size: 1.17em !important;
            font-weight: bold !important;
            margin: 1em 0 !important;
            line-height: 1.2 !important;
        }

        #content-wrapper h4 {
            display: block !important;
            font-size: 1em !important;
            font-weight: bold !important;
            margin: 1em 0 !important;
            line-height: 1.2 !important;
        }

        #content-wrapper h5 {
            display: block !important;
            font-size: 0.83em !important;
            font-weight: bold !important;
            margin: 1em 0 !important;
            line-height: 1.2 !important;
        }

        #content-wrapper h6 {
            display: block !important;
            font-size: 0.67em !important;
            font-weight: bold !important;
            margin: 1em 0 !important;
            line-height: 1.2 !important;
        }

        #content-wrapper ul,
        #content-wrapper ol {
            display: block !important;
            margin: 1em 0 !important;
            padding-left: 2em !important;
        }

        #content-wrapper ul {
            list-style-type: disc !important;
        }

        #content-wrapper ol {
            list-style-type: decimal !important;
        }

        #content-wrapper li {
            display: list-item !important;
            margin: 0.5em 0 !important;
            line-height: 1.6 !important;
        }

        #content-wrapper a {
            color: #007bff !important;
            text-decoration: underline !important;
            cursor: pointer !important;
        }

        #content-wrapper img {
            max-width: 100% !important;
            height: auto !important;
            display: block !important;
            margin: 1em 0 !important;
        }

        #content-wrapper strong,
        #content-wrapper b {
            font-weight: bold !important;
        }

        #content-wrapper em,
        #content-wrapper i {
            font-style: italic !important;
        }

        #content-wrapper u {
            text-decoration: underline !important;
        }

        #content-wrapper br {
            display: block !important;
            content: "" !important;
            margin-top: 0.5em !important;
        }

        #content-wrapper div {
            display: block !important;
        }

        #content-wrapper span {
            display: inline !important;
        }

        #content-wrapper table {
            display: table !important;
            border-collapse: collapse !important;
            margin: 1em 0 !important;
            width: 100% !important;
        }

        #content-wrapper tr {
            display: table-row !important;
        }

        #content-wrapper td,
        #content-wrapper th {
            display: table-cell !important;
            padding: 0.5em !important;
            border: 1px solid #ddd !important;
            text-align: left !important;
        }

        #content-wrapper th {
            font-weight: bold !important;
            background-color: #f5f5f5 !important;
        }

        #content-wrapper blockquote {
            display: block !important;
            margin: 1em 2em !important;
            padding: 0.5em 1em !important;
            border-left: 3px solid #ddd !important;
            background-color: #f9f9f9 !important;
        }

        #content-wrapper pre {
            display: block !important;
            font-family: monospace !important;
            white-space: pre !important;
            margin: 1em 0 !important;
            padding: 1em !important;
            background-color: #f5f5f5 !important;
            border: 1px solid #ddd !important;
            overflow-x: auto !important;
        }

        #content-wrapper code {
            font-family: monospace !important;
            background-color: #f5f5f5 !important;
            padding: 0.2em 0.4em !important;
            border-radius: 3px !important;
        }
    </style>
@endsection
@section('content')
    @include('front.partials.breadcrumb')

    <!-- PAGE DETAILS AREA START (portfolio-details) -->
    <div class="ltn__page-details-area ltn__portfolio-details-area mb-105">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__page-details-inner ltn__portfolio-details-inner">
                        <h2 class="ltn__blog-title">
                            {{ $menu_profil->name }}
                        </h2>
                        <div id="content-wrapper">
                            <div id="content">
                                {!! $menu_profil->content !!}
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <hr>

            <div class="ltn__social-media text-right ">
                <h4>
                    {{ __('front.social_share') }}
                </h4>
                <ul>
                    <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::fullUrl()) }}"
                            title="Facebook" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                    <li><a href="https://twitter.com/intent/tweet?url={{ urlencode(Request::fullUrl()) }}" title="Twitter"
                            target="_blank"><i class="fab fa-twitter"></i></a></li>
                    <li><a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(Request::fullUrl()) }}"
                            title="Linkedin" target="_blank"><i class="fab fa-linkedin"></i></a></li>
                    
                    <li>
                        <a href="https://wa.me/?text={{ urlencode(Request::fullUrl()) }}" title="WhatsApp" target="_blank">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

    </div>
    <!-- PAGE DETAILS AREA END -->
@endsection
