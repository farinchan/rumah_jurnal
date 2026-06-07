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

@section('styles')
    <style>
        .ws-section {
            background: var(--section-bg-1);
            padding: 70px 0 100px;
        }

        /* ===== Left Column — Sticky Profile ===== */
        .ws-sidebar {
            position: sticky;
            top: 100px;
        }

        .ws-photo-card {
            background: #fff;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: var(--ltn__box-shadow-4);
        }

        .ws-photo-wrapper {
            position: relative;
            overflow: hidden;
        }

        .ws-photo-wrapper::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 80px;
            background: linear-gradient(to top, rgba(0,0,0,0.35), transparent);
            pointer-events: none;
        }

        .ws-photo {
            width: 100%;
            height: 360px;
            object-fit: cover;
            object-position: top center;
            display: block;
            transition: transform 0.5s ease;
        }

        .ws-photo-card:hover .ws-photo {
            transform: scale(1.03);
        }

        .ws-info {
            padding: 28px 28px 32px;
            text-align: center;
        }

        .ws-badge {
            display: inline-flex;
            align-items: center;
            gap: 2px;
            background: rgba(24, 105, 11, 0.08);
            color: var(--ltn__secondary-color);
            padding: 4px 10px;
            border-radius: 14px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 14px;
            font-family: var(--ltn__body-font);
        }

        .ws-badge i {
            font-size: 4px;
        }

        .ws-person-name {
            font-family: var(--ltn__heading-font);
            font-size: 1.55rem;
            font-weight: 700;
            color: var(--ltn__heading-color);
            margin: 0 0 4px;
            line-height: 1.25;
        }

        .ws-person-title {
            font-family: var(--ltn__body-font);
            font-size: 14px;
            color: var(--ltn__secondary-color);
            margin: 0 0 4px;
        }

        .ws-person-subtitle {
            font-family: var(--ltn__body-font);
            font-size: 16px;
            color: var(--ltn__color-6);
            font-style: italic;
            margin: 0;
        }

        .ws-info-divider {
            width: 40px;
            height: 3px;
            background: var(--ltn__secondary-color);
            border-radius: 2px;
            margin: 14px auto 16px;
        }

        .ws-sidebar-nav {
            margin-top: 20px;
        }

        .ws-sidebar-nav .btn {
            width: 100%;
            font-family: var(--ltn__heading-font);
            letter-spacing: 0.5px;
            border-radius: 10px;
        }

        /* ===== Right Column — Content ===== */
        .ws-content-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: var(--ltn__box-shadow-4);
            padding: 45px 50px;
            position: relative;
            overflow: hidden;
        }

        .ws-content-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, var(--ltn__secondary-color), rgba(24,105,11,0.1), transparent);
        }

        .ws-content-title-row {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            padding-bottom: 22px;
            border-bottom: 1px solid var(--border-color-1);
        }

        .ws-content-icon {
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            background: var(--ltn__secondary-color);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ws-content-icon i {
            color: #fff;
            font-size: 17px;
        }

        .ws-content-label {
            font-family: var(--ltn__heading-font);
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--ltn__heading-color);
            margin: 0;
        }

        .ws-content-label small {
            display: block;
            font-family: var(--ltn__body-font);
            font-size: 12.5px;
            font-weight: 400;
            color: var(--ltn__color-6);
            margin-top: 1px;
        }

        /* Body Text */
        .ws-body {
            font-family: var(--ltn__body-font);
            font-size: 15.5px;
            line-height: 1.9;
            color: var(--ltn__paragraph-color);
            text-align: justify;
        }

        .ws-body p {
            margin-bottom: 16px;
        }

        .ws-body img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 8px 0;
        }

        .ws-body > p:first-of-type::first-letter {
            float: left;
            font-family: var(--ltn__heading-font);
            font-size: 3.5em;
            line-height: 0.82;
            margin: 5px 12px 0 0;
            font-weight: 700;
            color: var(--ltn__secondary-color);
        }

        /* ===== Empty State ===== */
        .ws-empty {
            text-align: center;
            padding: 80px 30px;
            background: #fff;
            border-radius: 14px;
            box-shadow: var(--ltn__box-shadow-4);
        }

        .ws-empty-ic {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: var(--section-bg-1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .ws-empty-ic i {
            font-size: 32px;
            color: var(--ltn__color-6);
        }

        .ws-empty h4 {
            font-family: var(--ltn__heading-font);
            color: var(--ltn__heading-color);
            margin-bottom: 6px;
        }

        .ws-empty p {
            color: var(--ltn__color-5);
            margin-bottom: 24px;
        }

        /* ===== Responsive ===== */
        @media (max-width: 991px) {
            .ws-sidebar {
                position: static;
                margin-bottom: 30px;
            }

            .ws-photo {
                height: 300px;
            }

            .ws-sidebar-nav {
                display: none;
            }

            .ws-content-card::after {
                content: '';
                display: block;
                margin-top: 35px;
                padding-top: 22px;
                border-top: 1px solid var(--border-color-1);
            }

            .ws-mobile-back {
                display: block !important;
                margin-top: 35px;
                padding-top: 22px;
                border-top: 1px solid var(--border-color-1);
            }
        }

        @media (max-width: 575px) {
            .ws-section {
                padding: 40px 0 60px;
            }

            .ws-content-card {
                padding: 28px 22px;
            }

            .ws-body {
                font-size: 14.5px;
                text-align: left;
            }

            .ws-body > p:first-of-type::first-letter {
                font-size: 2.6em;
            }

            .ws-person-name {
                font-size: 1.3rem;
            }

            .ws-content-title-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .ws-photo {
                height: 260px;
            }
        }
    </style>
@endsection

@section('content')
    @include('front.partials.breadcrumb')

    <div class="ws-section" style="margin-top: -120px;">
        <div class="container">
            @if($welcome_speech)
                <div class="row">
                    {{-- LEFT — Sticky Photo + Info --}}
                    <div class="col-lg-4">
                        <div class="ws-sidebar">
                            <div class="ws-photo-card">
                                <div class="ws-photo-wrapper">
                                    <img src="{{ $welcome_speech->getImage() }}"
                                         alt="{{ $welcome_speech->name }}"
                                         class="ws-photo">
                                </div>
                                <div class="ws-info">
                                    <p class="ws-person-title">
                                       {{ $welcome_speech->name }}
                                    </p>
                                    <br>
                                    @if($welcome_speech->title)
                                        <h2 class="ws-person-name">{{ $welcome_speech->title }}</h2>
                                    @endif
                                    @if($welcome_speech->subtitle)
                                        <p class="ws-person-subtitle">{{ $welcome_speech->subtitle }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="ws-sidebar-nav">
                                <a href="{{ route('home') }}" class="btn theme-btn-2 btn-effect-1">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- RIGHT — Content --}}
                    <div class="col-lg-8">
                        <div class="ws-content-card">
                            <div class="ws-content-title-row">
                                <div class="ws-content-icon">
                                    <i class="fas fa-quote-right"></i>
                                </div>
                                <h1 class="ws-content-label">
                                    {{ $welcome_speech->title }}
                                    <small>{{ $welcome_speech->subtitle }}</small>
                                </h1>
                            </div>

                            <div class="ws-body">
                                {!! $welcome_speech->content !!}
                            </div>

                            {{-- Mobile back button --}}
                            <div class="ws-mobile-back d-none">
                                <a href="{{ route('home') }}" class="btn theme-btn-2 btn-effect-1">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row justify-content-center">
                    <div class="col-lg-7">
                        <div class="ws-empty">
                            <div class="ws-empty-ic">
                                <i class="far fa-file-alt"></i>
                            </div>
                            <h4>Belum Ada Kata Sambutan</h4>
                            <p>Konten kata sambutan belum tersedia saat ini.</p>
                            <a href="{{ route('home') }}" class="btn theme-btn-2 btn-effect-1">
                                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
