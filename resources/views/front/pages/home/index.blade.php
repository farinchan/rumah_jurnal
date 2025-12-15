@extends('front.app')

@section('seo')
    <title>{{ $meta['description'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('home') }}">
    <link rel="canonical" href="{{ route('home') }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
@endsection

@section('styles')
    <style>
        .latest-events {
            display: inline-block;
            max-width: 340px;
            margin-bottom: 30px;
        }

        .events-date {
            float: left;
            height: 84px;
            width: 95px;
            font-size: 13px;
            font-weight: 500;
            border-radius: 10px;
            margin-right: 20px;
            background-color: #fff;
        }

        .relative-position {
            position: relative;
        }

        .gradient-bdr {
            z-index: -1;
            width: 100%;
            height: 100%;
            position: absolute;
            border-radius: 10px;
            -webkit-transform: scale(1.06);
            -ms-transform: scale(1.06);
            transform: scale(1.06);
            background: -o-linear-gradient(69deg, #319276, #08652F);
            background: linear-gradient(21deg, #319276, #08652F);
            background: -webkit-linear-gradient(69deg, #319276, #08652F);
        }

        .events-date span {
            font-size: 50px;
            padding-top: 8px;
            color: #333333;
            line-height: 1;
            display: block;
        }

        .event-text {
            overflow: hidden;
        }

        .latest-title {
            font-size: 18px;
            color: #333333;
            margin-bottom: 10px;
        }
        .tb_onsite_upload_btn_wrap {
            display: none;
        }
        .tb_onsite_upload_btn {
            display: none;
        }
        .tb_onsite_upload_input {
            display: none;
        }
        .tb_post_modal_content {
            font-size: 14px;
        }
    </style>
@endsection

@section('content')
    @include('front.partials.slider')

    <!-- CATEGORY AREA START -->
    {{-- <div class="ltn__category-area ltn__product-gutter section-bg-1--- pt-115 pb-90---  mt--65">
        <div class="container">
            <div class="row ltn__category-slider-active--- slick-arrow-1 justify-content-center">
                <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                    <div class="ltn__category-item ltn__category-item-5 text-center">
                        <a href="shop.html">
                            <span class="category-icon"><i class="flaticon-car"></i></span>
                            <span class="category-title">Accreditation</span>
                            <span class="category-btn"><i class="flaticon-right-arrow"></i></span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                    <div class="ltn__category-item ltn__category-item-5 text-center">
                        <a href="shop.html">
                            <span class="category-icon"><i class="flaticon-excavator"></i></span>
                            <span class="category-title">Mechanical E.</span>
                            <span class="category-btn"><i class="flaticon-right-arrow"></i></span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                    <div class="ltn__category-item ltn__category-item-5 text-center">
                        <a href="shop.html">
                            <span class="category-icon"><i class="flaticon-apartment"></i></span>
                            <span class="category-title">Architecture</span>
                            <span class="category-btn"><i class="flaticon-right-arrow"></i></span>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                    <div class="ltn__category-item ltn__category-item-5 text-center">
                        <a href="shop.html">
                            <span class="category-icon"><i class="flaticon-beds"></i></span>
                            <span class="category-title">Interior Design</span>
                            <span class="category-btn"><i class="flaticon-right-arrow"></i></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    <!-- CATEGORY AREA END -->

    <!-- ABOUT US AREA START -->
    <div class="ltn__about-us-area pt-90 pb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 align-self-center">
                    <div class="about-us-img-wrap about-img-left">
                        <div class="ltn__video-img ltn__video-img-before-none ltn__animation-pulse2">
                            <img src="{{ $welcome_speech?->getImage() ?? asset('front/img/others/18.png') }}"
                                alt="video popup bg image">

                        </div>
                    </div>
                </div>
                <div class="col-lg-6 align-self-center">
                    <div class="about-us-info-wrap">
                        <div class="section-title-area ltn__section-title-2">
                            <h6 class="section-subtitle ltn__secondary-color"><span><i
                                        class="fas fa-square-full"></i></span> {{ __('front.about_us') }}</h6>
                            <h1 class="section-title">{{ $welcome_speech?->name ?? '-' }}</h1>

                        </div>
                        <p>
                            {{ Str::limit(strip_tags($welcome_speech?->content ?? '-'), 500, '...') }}
                        </p>
                        <div class="about-author-info d-flex mt-50">
                            <div class="author-name-designation  align-self-center mr-30">
                                <!-- <h4 class="mb-0">Jerry Henson</h4>
                                                                    <small>/ Shop Director</small> -->
                                <div class="btn-wrapper mt-0">
                                    <a class="btn theme-btn-2 btn-effect-1"
                                        href="about.html">{{ __('front.read_more') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ABOUT US AREA END -->

    <!-- BLOG AREA START (blog-3) -->
    <div class="ltn__blog-area pt-120 section-bg-1 pb-20">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2 text-center">
                        <h6 class="section-subtitle ltn__secondary-color"><span><i class="fas fa-square-full"></i></span>
                            {{ __('front.news_blog') }}
                        </h6>
                        <h1 class="section-title">
                            {{ __('front.news_blog_title') }}
                        </h1>
                    </div>
                </div>
            </div>
            <div class="row  ltn__blog-slider-one-active slick-arrow-1 ltn__blog-item-3-normal">
                @foreach ($list_news as $news)
                    <div class="col-lg-12">
                        <div class="ltn__blog-item ltn__blog-item-3">
                            <div class="ltn__blog-img">
                                <a href="{{ route('news.detail', $news->slug) }}"><img src="{{ $news->getThumbnail() }}"
                                        style="height: 250px; width: 100%; object-fit: cover; object-position: top;"
                                        alt="#"></a>
                            </div>
                            <div class="ltn__blog-brief">
                                <div class="ltn__blog-meta">
                                    <ul>
                                        <li class="ltn__blog-author">
                                            <a href="#"><i class="far fa-user"></i>by: {{ $news->user->name }}</a>
                                        </li>
                                        <li class="ltn__blog-tags">
                                            <a href="{{ route('news.category', $news->category->slug) }}"><i
                                                    class="fas fa-tags"></i>{{ $news->category->name }}</a>
                                        </li>
                                    </ul>
                                </div>
                                <h3 class="ltn__blog-title"><a href="{{ route('news.detail', $news->slug) }}">
                                        {{ $news->title }}
                                    </a></h3>
                                <div class="ltn__blog-meta-btn">
                                    <div class="ltn__blog-meta">
                                        <ul>
                                            <li class="ltn__blog-date"><i class="far fa-calendar-alt"></i>
                                                {{ Carbon\Carbon::parse($news->created_at)->format('d/m/Y') }}
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="ltn__blog-btn">
                                        <a href="{{ route('news.detail', $news->slug) }}">
                                            {{ __('front.read_more') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- BLOG AREA END -->

    <!-- FEATURE START -->
    <div class="ltn__feature-area section-bg-1  pb-90">
        <div class="container">

            <div class="row p-3">
                <div class=" col-md-8">
                    <h2 style="font-size: 24px; font-weight: bold; color: #333; position: relative; display: inline-block;">
                        {{ __('layout.event') }}
                        <span
                            style="display: block; width: 50px; height: 3px; background-color: #08652F; position: absolute; bottom: -15px; left: 0;"></span>
                    </h2>

                    <div class="row mt-5">
                        @foreach ($list_event as $event)
                            @php
                                $dates = explode(' - ', $event->datetime);
                                $before = $dates[0] ?? null;
                                $after = $dates[1] ?? null;
                                $date_before = $before ? \Carbon\Carbon::parse($before)->toDateTimeString() : null;
                                $date_after = $after ? \Carbon\Carbon::parse($after)->toDateTimeString() : null;
                                // dd($date_before, $date_after);
                            @endphp
                            <div class="col-md-6">
                                <div class="latest-events">
                                    <div class="latest-event-item">
                                        <div class="events-date  relative-position text-center">
                                            <div class="gradient-bdr"></div>
                                            <span
                                                class="event-date bold-font">{{ Carbon\Carbon::parse($before)->format('d') }}</span>
                                            {{ Carbon\Carbon::parse($before)->format('M Y') }}
                                        </div>
                                        <div class="event-text">
                                            <h3 class="latest-title bold-font">
                                                <a style="color: #333;" href="{{ route('event.show', $event->slug) }}"
                                                    onmouseover="this.style.color='#08652F'"
                                                    onmouseout="this.style.color='#333'">
                                                    {{ $event->name }}
                                                    @if ($date_after && \Carbon\Carbon::parse($date_after)->isPast())
                                                        <span class="badge badge-danger ml-2"
                                                            style="font-size: 12px;">End</span>
                                                    @endif
                                                </a>
                                            </h3>
                                        </div>
                                        <div class="ltn__blog-meta">
                                            <ul>
                                                <li class="ltn__blog-author">
                                                    <a href="#"><i
                                                            class="fa fa-info-circle"></i>{{ $event->type }}</a>
                                                </li>
                                                <li class="ltn__blog-author">
                                                    <a href="#"><i class="fa fa-bullhorn"></i>
                                                        {{ $event->status }}</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-4">
                    <h2 style="font-size: 24px; font-weight: bold; color: #333; position: relative; display: inline-block;"
                        class="mb-5">
                        {{ __('front.announcement') }}
                        <span
                            style="display: block; width: 50px; height: 3px; background-color: #08652F; position: absolute; bottom: -15px; left: 0;"></span>
                    </h2>

                    @foreach ($list_announcement as $pengumuman)
                        <div class="trand-right-single d-flex">
                            {{-- <div class="trand-right-img ">
                                    <img src="{{ $pengumuman->image ? Storage::url($pengumuman->image) : 'https://file.iainpare.ac.id/wp-content/uploads/2019/07/pengumuman.png' }}"
                                        alt="" style="height: 70px; width: 70px; object-fit: cover;">
                                </div> --}}

                            <div class="trand-right-cap" style="padding-left: 0;">
                                {{-- <span class="color4">Pengumuman</span> --}}
                                <div style="font-size: 12px; color: #333;">
                                    {{ $pengumuman->created_at->diffForHumans() }} </div>
                                <h4 style=" font-size: 16px;"><a
                                        href="{{ route('announcement.show', $pengumuman->slug) }}">
                                        {{ Str::limit($pengumuman->title, 80) }}
                                    </a></h4>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <!-- FEATURE END -->
    </div>


    <!-- CALL TO ACTION START (call-to-action-6) -->
    <div class="ltn__call-to-action-area call-to-action-6 before-bg-left-skew ltn__secondary-bg bg-image pt-110 pb-110"
        data-bg="{{ asset('front/img/bg/36.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="call-to-action-inner call-to-action-inner-6 p-0 text-center---">

                        <div class="section-title-area ltn__section-title-2--- mb-0 text-color-white">
                            <h1 class="section-title">{{ __('front.call_for_paper_title') }}</h1>
                            <p>
                                {{ __('front.call_for_paper_desc') }} </p>
                        </div>
                        <div class="btn-wrapper">
                            <a class="btn btn-effect-4 btn-white font-weight-bold" href="{{ route('journal.index') }}">
                                {{ __('front.call_for_paper_btn') }} <i class="icon-next"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- CALL TO ACTION END -->

    <!-- IMAGE SLIDER AREA START (img-slider-3) -->
    <div class="ltn__img-slider-area  pt-120 section-bg-1 pb-100">
        <div class="container-fluid">
            <div class="row ltn__image-slider-3-active slick-arrow-1 slick-arrow-1-inner">
                @foreach ($list_journal as $journal)
                    <div class="col-lg-12">
                        <div class="ltn__img-slide-item-3">
                            <a href="{{ $journal->getJournalThumbnail() }}" data-rel="lightcase:myCollection">
                                <img src="{{ $journal->getJournalThumbnail() }}" alt="Image"
                                    style="height: 600px; width: 100%; object-fit: cover; object-position: top;">
                            </a>
                            <div class="ltn__img-slide-info">
                                <div class="ltn__img-slide-info-brief">
                                    <h6>{{ __('front.acredited') }}:
                                        @foreach ($journal->indexing ?? [] as $akreditasi_item)
                                            <strong>{{ $akreditasi_item }},</strong>
                                        @endforeach
                                    </h6>
                                    <h1><a
                                            href="{{ route('journal.detail', $journal->url_path) }}">{{ $journal->title }}</a>
                                    </h1>
                                </div>
                                <div class="btn-wrapper">
                                    <a href="{{ route('journal.detail', $journal->url_path) }}"
                                        class="btn theme-btn-1 btn-effect-1"><i class="fas fa-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- IMAGE SLIDER AREA END -->

    <div class="ltn__blog-area pt-120  pb-20">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2 text-center">
                        <h6 class="section-subtitle ltn__secondary-color"><span><i class="fas fa-square-full"></i></span>
                            {{ __('front.instagram_feed') }}
                        </h6>
                        <h1 class="section-title">
                            {{ __('front.see_our_instagram_feed') }}
                        </h1>
                    </div>
                </div>
            </div>
            <div class="row  ">
                <div class="tagembed-widget" style="width:100%;height:100%;overflow:auto;" data-widget-id="311267"
                    data-website="1"></div>
            </div>
        </div>
    </div>

    <div class="ltn__contact-message-area pb-90 pt-90  ">
        <div class="container contact-form-box box-shadow white-bg">
            <h3 class="title-2">{{ __('front.contact_us') }}</h3>
            <div class="row ">

                <div class="col-md-5 mb-3">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d320.84241104217386!2d100.39786475676972!3d-0.3219989136461684!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2fd539e261f53f89%3A0xedfc275ef4afd8d0!2sRumah%20Jurnal%20UIN%20Bukittinggi!5e0!3m2!1sid!2sid!4v1741270898403!5m2!1sid!2sid"
                        width="100%" height="100%" frameborder="0" allowfullscreen="" aria-hidden="false"
                        tabindex="0"></iframe>

                </div>
                <div class="col-md-7">
                    <div class="ltn__form-box ">

                        <form id="contact-form" action="{{ route('contact.send') }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="input-item input-item-name ltn__custom-icon">
                                        <input type="text" name="name"
                                            placeholder="{{ __('front.enter_your_name') }}" value="{{ old('name') }}"
                                            required>
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-item input-item-email ltn__custom-icon">
                                        <input type="email" name="email"
                                            placeholder="{{ __('front.enter_your_email') }}" value="{{ old('email') }}"
                                            required>
                                        @error('email')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-item input-item-phone ltn__custom-icon">
                                        <input type="text" name="phone"
                                            placeholder="{{ __('front.enter_your_phone') }}" value="{{ old('phone') }}"
                                            required>
                                        @error('phone')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input-item  ltn__custom-icon">
                                        <input type="text" name="subject"
                                            placeholder="{{ __('front.enter_subject') }}" value="{{ old('subject') }}"
                                            required>
                                        @error('subject')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="input-item input-item-textarea ltn__custom-icon">
                                <textarea name="message" placeholder="{{ __('front.enter_message') }}" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <p><label class="input-info-save mb-0"><input type="checkbox" name="agree">
                                    {{ __('front.message_checkbox') }}
                                </label></p>
                            <div class="btn-wrapper mt-0">
                                <button class="btn theme-btn-1 btn-effect-1 text-uppercase" type="submit">
                                    {{ __('front.send_message') }}
                                </button>
                            </div>
                            <p class="form-messege mb-0 mt-20"></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://widget.tagembed.com/embed.min.js" type="text/javascript">
        < script >
            $.ajax({
                url: "{{ route('visit.ajax') }}",
                type: "GET",
                success: function(response) {
                    console.log(response);
                },
                error: function(error) {
                    console.log(error);
                }
            });
    </script>
@endsection
