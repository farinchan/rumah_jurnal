@extends('front.app')
@section('seo')
    <title>{{ $meta['description'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('event.show', $event->slug) }}">
    <link rel="canonical" href="{{ route('event.show', $event->slug) }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
@endsection
@section('styles')
@endsection
@section('content')
    @include('front.partials.breadcrumb')

    {{-- @dd($event->getThumbnail()) --}}
    <!-- BLOG AREA START -->
    <div class="ltn__blog-area mb-120">
        <div class="container">
            <div class="row  mb-40">
                <div class="col-lg-12">
                    <div class="ltn__blog-details-wrap">
                        <div class="ltn__page-details-inner ltn__blog-details-inner">
                            <div class="ltn__blog-item ltn__blog-item-3">
                                <div class="ltn__blog-img">
                                    <a data-fslightbox="gallery" href="/storage/{{ $event->thumbnail }}"
                                        onmouseover="document.getElementById('center-hover-image').style.display='block'; this.querySelector('img').style.filter='brightness(70%)';"
                                        onmouseout="document.getElementById('center-hover-image').style.display='none'; this.querySelector('img').style.filter='';"
                                        onmouseout="document.getElementById('center-hover-image').style.display='none';">
                                        <img src="{{ $event->getThumbnail() }}" alt="#"
                                            style="width: 100%; object-fit: cover; height: 400px;">
                                        <img src="https://res.cloudinary.com/dh0tzenpm/image/upload/v1751400235/preview_gy8lgf.png"
                                            id="center-hover-image" alt="Center Image" class="center-hover-image"
                                            style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); display: none; max-width: 100px; pointer-events: none; z-index: 3;">


                                    </a>
                                </div>
                            </div>
                            <h2 class="ltn__blog-title" style="margin-bottom: 0px;">
                                {{ $event->name }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8">
                    <div class="ltn__blog-details-wrap">
                        <div class="ltn__page-details-inner ltn__blog-details-inner">

                            <p>
                                {!! $event->description !!}
                            </p>

                            @if ($event->attachment)
                                <object data="{{ Storage::url($event->attachment) }}" type="application/pdf" width="100%"
                                    height="800px">
                                    <embed src="{{ Storage::url($event->attachment) }}" type="application/pdf" />
                                </object>
                            @endif

                        </div>
                        <!-- blog-tags-social-media -->
                        <div class="ltn__blog-tags-social-media mt-80 row">
                            <div class="ltn__tagcloud-widget col-lg-8">
                                <h4>
                                    {{ __('front.tags') }}
                                </h4>
                                <ul>
                                    @php
                                        $tags = explode(',', $event->meta_keywords ?? '');
                                    @endphp
                                    @foreach ($tags ?? [] as $tag)
                                        <li><a href="#">{{ $tag }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-lg-4">
                    <aside class="sidebar-area blog-sidebar ltn__right-sidebar">

                        <div class="widget ltn__author-widget">
                            <div class="ltn__author-widget-inner ">
                                <h3>{{ $event->name }}</h3>
                                <div class="ltn__blog-meta">
                                    <ul style="display: flex; flex-direction: column; gap: 8px;">
                                        <li class="ltn__blog-author">
                                            <a href="#"><i class="fa fa-info-circle"></i>{{ $event->type }}</a>
                                        </li>
                                        <li class="ltn__blog-author">
                                            <a href="#"><i class="fa fa-bullhorn"></i>
                                                {{ $event->status }}</a>
                                        </li>
                                        <li class="ltn__blog-author">
                                            <a href="#"><i class="far fa-calendar"></i>
                                                {{ $event->datetime }}
                                            </a>
                                        </li>
                                        <li class="ltn__blog-author">
                                            <a href="#"><i class="fa fa-users"></i>
                                                {{ $event->limit }} Participant
                                            </a>
                                        </li>
                                        @if ($event->status == 'offline')
                                            <li class="ltn__blog-author">
                                                <a href="#"><i class="fa fa-map-marker"></i>
                                                    {{ $event->location }}
                                                </a>
                                            </li>
                                        @endif

                                    </ul>
                                </div>
                                <p>
                                    Registration is now open for the {{ $event->name }}. Don't miss out on this
                                    opportunity to participate in this exciting event. Click the button below to register
                                    now and secure your spot. We look forward to seeing you there!
                                </p>
                                @if ($check_registered)
                                    <p class="text-success">
                                        <b>
                                            <i class="fa fa-check-circle"></i>
                                            {{ __('front.registered') }}
                                        </b>
                                    </p>
                                @endif
                                <div class=" ltn__menu-widget ltn__menu-widget-2 text-uppercase">
                                    <ul>
                                        <li>
                                            @if ($check_registered)
                                                <a href="{{ route('event.eticket', $eticket->id) }}" target="_blank">
                                                    {{ __('front.print_eticket') }}
                                                    <span>
                                                        <i class="fas fa-arrow-right"></i>
                                                    </span>
                                                </a>
                                            @else
                                                @php
                                                    $dates = explode(' - ', $event->datetime);
                                                    $before = $dates[0] ?? null;
                                                    $after = $dates[1] ?? null;
                                                    $date_before = $before
                                                        ? \Carbon\Carbon::parse($before)->toDateTimeString()
                                                        : null;
                                                    $date_after = $after
                                                        ? \Carbon\Carbon::parse($after)->toDateTimeString()
                                                        : null;
                                                    // dd($date_before, $date_after);
                                                @endphp

                                                @if ($date_before && \Carbon\Carbon::now()->lt(\Carbon\Carbon::parse($date_before)))
                                                    <span class="text-danger" style="float:none" >
                                                        <i class="fa fa-exclamation-triangle"></i>
                                                        {{ __('front.registration_closed') }}
                                                    </span>
                                                @else
                                                    <a href="#" data-toggle="modal"
                                                        data-target="{{ Auth::check() ? '#regisevent' : '#exampleModal' }}">
                                                        {{ __('front.register_now') }}
                                                        <span>
                                                            <i class="fas fa-arrow-right"></i>
                                                        </span>
                                                    </a>
                                                @endif
                                            @endif

                                        </li>

                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="widget ltn__author-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border">Social Share</h4>
                            <div class="ltn__author-widget-inner text-center">

                                <div class="ltn__social-media">
                                    <ul>
                                        <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::fullUrl()) }}"
                                                title="Facebook" target="_blank"><i class="fab fa-facebook-f"></i></a>
                                        </li>
                                        <li><a href="https://twitter.com/intent/tweet?url={{ urlencode(Request::fullUrl()) }}"
                                                title="Twitter" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                        <li><a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(Request::fullUrl()) }}"
                                                title="Linkedin" target="_blank"><i class="fab fa-linkedin"></i></a></li>
                                        <li><a href="https://wa.me/?text={{ urlencode(Request::fullUrl()) }}"
                                                title="WhatsApp" target="_blank"><i class="fab fa-whatsapp"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        {{-- <!-- Social Media Widget -->
                        <div class="widget ltn__social-media-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border">Never Miss News</h4>
                            <div class="ltn__social-media-2">
                                <ul>
                                    <li><a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#" title="Twitter"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#" title="Linkedin"><i class="fab fa-linkedin"></i></a></li>
                                    <li><a href="#" title="Instagram"><i class="fab fa-instagram"></i></a></li>
                                    <li><a href="#" title="Behance"><i class="fab fa-behance"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <!-- Popular Post Widget (Twitter Post) -->
                        <div class="widget ltn__popular-post-widget ltn__twitter-post-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border">Twitter Feeds</h4>
                            <ul>
                                <li>
                                    <div class="popular-post-widget-item clearfix">
                                        <div class="popular-post-widget-img">
                                            <a href="blog-details.html"><i class="fab fa-twitter"></i></a>
                                        </div>
                                        <div class="popular-post-widget-brief">
                                            <p>Carsafe - #Gutenberg ready
                                                @wordpress
                                                Theme for Car Service, Auto Parts, Car Dealer available on
                                                @website
                                                <a href="https://website.net">https://website.net</a>
                                            </p>
                                            <div class="ltn__blog-meta">
                                                <ul>
                                                    <li class="ltn__blog-date">
                                                        <a href="#"><i class="far fa-calendar-alt"></i>June 22,
                                                            2020</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="popular-post-widget-item clearfix">
                                        <div class="popular-post-widget-img">
                                            <a href="blog-details.html"><i class="fab fa-twitter"></i></a>
                                        </div>
                                        <div class="popular-post-widget-brief">
                                            <p>Carsafe - #Gutenberg ready
                                                @wordpress
                                                Theme for Car Service, Auto Parts, Car Dealer available on
                                                @website
                                                <a href="https://website.net">https://website.net</a>
                                            </p>
                                            <div class="ltn__blog-meta">
                                                <ul>
                                                    <li class="ltn__blog-date">
                                                        <a href="#"><i class="far fa-calendar-alt"></i>June 22,
                                                            2020</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="popular-post-widget-item clearfix">
                                        <div class="popular-post-widget-img">
                                            <a href="blog-details.html"><i class="fab fa-twitter"></i></a>
                                        </div>
                                        <div class="popular-post-widget-brief">
                                            <p>Carsafe - #Gutenberg ready
                                                @wordpress
                                                Theme for Car Service, Auto Parts, Car Dealer available on
                                                @website
                                                <a href="https://website.net">https://website.net</a>
                                            </p>
                                            <div class="ltn__blog-meta">
                                                <ul>
                                                    <li class="ltn__blog-date">
                                                        <a href="#"><i class="far fa-calendar-alt"></i>June 22,
                                                            2020</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <!-- Instagram Widget -->
                        <div class="widget ltn__instagram-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border">Instagram Feeds</h4>
                            <div class="ltn__instafeed ltn__instafeed-grid insta-grid-gutter"></div>
                        </div>
                        <!-- Tagcloud Widget -->
                        <div class="widget ltn__tagcloud-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border">Popular Tags</h4>
                            <ul>
                                <li><a href="#">Popular</a></li>
                                <li><a href="#">desgin</a></li>
                                <li><a href="#">ux</a></li>
                                <li><a href="#">usability</a></li>
                                <li><a href="#">develop</a></li>
                                <li><a href="#">icon</a></li>
                                <li><a href="#">Car</a></li>
                                <li><a href="#">Service</a></li>
                                <li><a href="#">Repairs</a></li>
                                <li><a href="#">Auto Parts</a></li>
                                <li><a href="#">Oil</a></li>
                                <li><a href="#">Dealer</a></li>
                                <li><a href="#">Oil Change</a></li>
                                <li><a href="#">Body Color</a></li>
                            </ul>
                        </div>
                        <!-- Banner Widget -->
                        <div class="widget ltn__banner-widget">
                            <a href="shop.html"><img src="img/banner/banner-4.jpg" alt="Banner Image"></a>
                        </div> --}}

                    </aside>
                </div>
            </div>
        </div>
    </div>
    <!-- BLOG AREA END -->

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog " role="document" style="max-width: 600px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="ltn__login-area ">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class=" text-center">
                                        <h2 class="section-title">{!! __('front.login_title') !!}</h2>
                                        <p>
                                            {{ $setting_web->name }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="">

                                <div class="account-login-inner">
                                    <form action="{{ route('login.post') }}" class="ltn__form-box contact-form-box"
                                        method="POST">
                                        @csrf
                                        <div class="mb-4">

                                            <input class="form-control @error('login') is-invalid @enderror"
                                                type="text" name="login" placeholder="Email/Username*"
                                                style="margin-bottom: 0px;" required>
                                            @error('login')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="mb-4">

                                            <input class="form-control @error('password') is-invalid @enderror"
                                                type="password" name="password" placeholder="Password*"
                                                style="margin-bottom: 0px;" required>
                                            @error('password')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="go-to-btn mb-20 text-right">
                                            <a href="#"><small>{{ __('front.forgot_password') }}</small></a>
                                        </div>
                                        <div class="btn-wrapper mt-0">
                                            <button class="theme-btn-1 btn btn-block"
                                                type="submit">{{ __('front.sign_in') }}</button>
                                        </div>

                                        <hr style="margin: 20px 0;">

                                        <div class="btn-wrapper ">
                                            <a href="{{ route('google.redirect') }}" class="btn btn-danger btn-block"
                                                style="background: #ea4335; color: #fff;">
                                                <i class="fab fa-google"></i> Login with Google
                                            </a>
                                        </div>

                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="regisevent" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog " role="document" style="max-width: 600px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="ltn__login-area ">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class=" text-center">
                                        <h2 class="section-title">Register Event</h2>
                                        <p>
                                            By filling out the form below, you have registered to participate in the
                                            {{ $event->name }} event.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="">

                                <div class="account-login-inner">
                                    <form action="{{ route('event.register', $event->slug) }}" class="ltn__form-box "
                                        method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <label for="name" class="form-label">{{ __('front.name') }}<span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control @error('name') is-invalid @enderror" type="text"
                                                name="name" placeholder="{{ __('front.enter_your_name') }}"
                                                value="{{ Auth::user()->name ?? '' }}" style="margin-bottom: 0px;"
                                                required>
                                            @error('name')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <label for="email" class="form-label">{{ __('front.email_address') }}<span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control @error('email') is-invalid @enderror"
                                                type="text" name="email"
                                                placeholder="{{ __('front.enter_your_email') }}"
                                                value="{{ Auth::user()->email ?? '' }}" style="margin-bottom: 0px;"
                                                required>
                                            @error('email')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="mb-4">
                                            <label for="phone" class="form-label">{{ __('front.wa_number') }}<span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control @error('phone') is-invalid @enderror"
                                                type="text" name="phone"
                                                placeholder="{{ __('front.enter_your_phone') }}"
                                                value="{{ Auth::user()->phone ?? '' }}" style="margin-bottom: 0px;"
                                                required>
                                            <small class="text-muted">
                                                {{ __('front.phone_format') }}
                                            </small>
                                            @error('phone')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>


                                        <div class="btn-wrapper mt-0">
                                            <button class="theme-btn-1 btn btn-block" type="submit">Register
                                                Event</button>
                                        </div>



                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('front/js/fslightbox.js') }}"></script>
@endsection
