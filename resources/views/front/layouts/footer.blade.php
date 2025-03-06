@php
    $journals = \App\Models\Journal::all();

    $journal_chunks = $journals->split(2);
    $part1 = $journal_chunks->get(0) ?? collect();
    $part2 = $journal_chunks->get(1) ?? collect();
@endphp
<!-- BRAND LOGO AREA START -->
{{-- <div class="ltn__brand-logo-area ltn__brand-logo-1 before-bg-bottom">
        <div class="container">
            <div class="row ltn__brand-logo-active ltn__secondary-bg ltn__border-radius pt-30 pb-20">
                <div class="col-lg-12">
                    <div class="ltn__brand-logo-item">
                        <img src="{{ asset('front/img/brand-logo/b11.png') }}" alt="Brand Logo">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="ltn__brand-logo-item">
                        <img src="{{ asset('front/img/brand-logo/b12.png') }}" alt="Brand Logo">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="ltn__brand-logo-item">
                        <img src="{{ asset('front/img/brand-logo/b13.png') }}" alt="Brand Logo">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="ltn__brand-logo-item">
                        <img src="{{ asset('front/img/brand-logo/b14.png') }}" alt="Brand Logo">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="ltn__brand-logo-item">
                        <img src="{{ asset('front/img/brand-logo/b15.png') }}" alt="Brand Logo">
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="ltn__brand-logo-item">
                        <img src="{{ asset('front/img/brand-logo/b13.png') }}" alt="Brand Logo">
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
<!-- BRAND LOGO AREA END -->

<!-- FOOTER AREA START -->
<footer class="ltn__footer-area  ">
    <div class="footer-top-area  section-bg-2 plr--5">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xl-3 col-md-6 col-sm-6 col-12">
                    <div class="footer-widget footer-about-widget">
                        <div class="footer-logo">
                            <div class="site-logo">
                                <img src="{{ $setting_web?->getLogo() ?? '' }}" alt="Logo">
                            </div>
                        </div>
                        <p>
                            {{ Str::limit($setting_web?->getAboutRaw() ?? '', 100, '...') }}
                        </p>
                        <div class="footer-address">
                            <ul>
                                <li>
                                    <div class="footer-address-icon">
                                        <i class="icon-placeholder"></i>
                                    </div>
                                    <div class="footer-address-info">
                                        <p>
                                            {{ $setting_web?->address ?? '' }}
                                        </p>
                                    </div>
                                </li>
                                <li>
                                    <div class="footer-address-icon">
                                        <i class="icon-call"></i>
                                    </div>
                                    <div class="footer-address-info">
                                        <p><a href="tel:{{ $setting_web?->phone ?? '' }}">
                                                {{ $setting_web?->phone ?? '' }}
                                            </a></p>
                                    </div>
                                </li>
                                <li>
                                    <div class="footer-address-icon">
                                        <i class="icon-mail"></i>
                                    </div>
                                    <div class="footer-address-info">
                                        <p><a href="mailto:{{ $setting_web?->email ?? '' }}">
                                                {{ $setting_web?->email ?? '' }}
                                            </a></p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="ltn__social-media mt-20">
                            <ul>
                                @if ($setting_web?->facebook)
                                    <li><a href="{{ $setting_web?->facebook }}" title="Facebook"><i
                                                class="fab fa-facebook-f"></i></a>
                                    </li>
                                @endif
                                @if ($setting_web?->twitter)
                                    <li><a href="{{ $setting_web?->twitter }}" title="Twitter"><i
                                                class="fab fa-twitter"></i></a></li>
                                @endif
                                @if ($setting_web?->linkedin)
                                    <li><a href="{{ $setting_web?->linkedin }}" title="Linkedin"><i
                                                class="fab fa-linkedin"></i></a>
                                    </li>
                                @endif
                                @if ($setting_web?->youtube)
                                    <li><a href="{{ $setting_web?->youtube }}" title="Youtube"><i
                                                class="fab fa-youtube"></i></a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-6 col-sm-6 col-12">
                    <div class="footer-widget footer-menu-widget clearfix">
                        <h4 class="footer-title">Web</h4>
                        <div class="footer-menu">
                            <ul>
                                <li><a href="{{ route("home") }}">{{ __('layout.home') }}</a></li>
                                <li><a href="{{route("news.index")}}">{{ __('layout.news') }}</a></li>
                                <li><a href="{{ route("journal.index") }}">{{ __('layout.journal') }}</a></li>
                                <li><a href="#">{{ __('layout.payment') }}</a></li>
                                <li><a href="{{ route("contact.index") }}">{{ __('layout.contact') }}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-6 col-sm-6 col-12">
                    <div class="footer-widget footer-menu-widget clearfix">
                        <h4 class="footer-title">Journal</h4>
                        <div class="footer-menu">
                            <ul>
                                @foreach ($part1 as $journal)
                                    <li><a href="{{ route('journal.detail', $journal->url_path) }}">
                                            {{ $journal->title }}
                                        </a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-md-6 col-sm-6 col-12">
                    <div class="footer-widget footer-menu-widget clearfix">
                        <div class="footer-menu">
                            <ul>
                                @foreach ($part2 as $journal)
                                    <li><a href="{{ route('journal.detail', $journal->url_path) }}">
                                            {{ $journal->title }}
                                        </a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 col-sm-12 col-12">
                    <div class="footer-widget footer-newsletter-widget">
                        <h4 class="footer-title">Newsletter</h4>
                        <p>
                            {{ __('layout.newsletter') }}
                        </p>
                        <div class="footer-newsletter">
                            <form action="#">
                                <input type="email" name="email" placeholder="Email*">
                                <div class="btn-wrapper">
                                    <button class="theme-btn-1 btn" type="submit"><i
                                            class="fas fa-location-arrow"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="ltn__copyright-area ltn__copyright-2 section-bg-7  plr--5">
        <div class="container-fluid ltn__border-top-2">
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="ltn__copyright-design clearfix">
                        <p>All Rights Reserved @ {{ $setting_web?->name ?? '' }} <span class="current-year"></span></p>
                    </div>
                </div>
                <div class="col-md-6 col-12 align-self-center">
                    <div class="ltn__copyright-menu text-right">
                        <ul>
                            <li><a href="#">Support</a></li>
                            <li><a href="#">Terms & Conditions</a></li>
                            <li><a href="#">Privacy & Policy</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- FOOTER AREA END -->
