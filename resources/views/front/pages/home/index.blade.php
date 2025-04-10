@extends('front.app')

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
                            <img src="{{ asset('front/img/others/18.png') }}" alt="video popup bg image">

                        </div>
                    </div>
                </div>
                <div class="col-lg-6 align-self-center">
                    <div class="about-us-info-wrap">
                        <div class="section-title-area ltn__section-title-2">
                            <h6 class="section-subtitle ltn__secondary-color"><span><i
                                        class="fas fa-square-full"></i></span> Tentang Kami</h6>
                            <h1 class="section-title">Solutions For Residentials
                                & Industries!</h1>
                            <p>Construction is a general term meaning the art and science to form objects
                                systems organizations, and comes from Latin</p>
                        </div>
                        <p>Construction is a general term meaning the art and science to form objects systems
                            organizations, and comes from Latin construction and Old French construction. To
                            construct is the verb: the act of building, and the noun</p>
                        <div class="about-author-info d-flex mt-50">
                            <div class="author-name-designation  align-self-center mr-30">
                                <!-- <h4 class="mb-0">Jerry Henson</h4>
                                                <small>/ Shop Director</small> -->
                                <div class="btn-wrapper mt-0">
                                    <a class="btn theme-btn-2 btn-effect-1" href="about.html">About Us</a>
                                </div>
                            </div>
                            <div class="author-sign  align-self-center">
                                <img src="{{ asset('front/img/icons/icon-img/author-sign.png') }}" alt="#">
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
                            News & Blogs</h6>
                        <h1 class="section-title">See Our Leatest News <br> & Read Blogs</h1>
                    </div>
                </div>
            </div>
            <div class="row  ltn__blog-slider-one-active slick-arrow-1 ltn__blog-item-3-normal">
                @foreach ($list_news as $news)
                    <div class="col-lg-12">
                        <div class="ltn__blog-item ltn__blog-item-3">
                            <div class="ltn__blog-img">
                                <a href="blog-details.html"><img src="{{ $news->getThumbnail() }}" alt="#"></a>
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
                                <h3 class="ltn__blog-title"><a href="blog-details.html">
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
                                        <a href="blog-details.html">Read more</a>
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
                        <h2
                            style="font-size: 24px; font-weight: bold; color: #333; position: relative; display: inline-block;">
                            Agenda
                            <span
                                style="display: block; width: 50px; height: 3px; background-color: #08652F; position: absolute; bottom: -15px; left: 0;"></span>
                        </h2>

                        <div class="row mt-5">
                            @foreach ($list_event as $event)
                                <div class="col-md-6">
                                    <div class="latest-events">
                                        <div class="latest-event-item">
                                            <div class="events-date  relative-position text-center">
                                                <div class="gradient-bdr"></div>
                                                <span
                                                    class="event-date bold-font">{{ Carbon\Carbon::parse($event->start)->format('d') }}</span>
                                                {{ Carbon\Carbon::parse($event->start)->format('M Y') }}
                                            </div>
                                            <div class="event-text">
                                                <h3 class="latest-title bold-font">
                                                    <a style="color: #333;"
                                                        href="#"
                                                        onmouseover="this.style.color='#08652F'"
                                                        onmouseout="this.style.color='#333'">
                                                        {{ $event->title }}
                                                    </a>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-4">
                        <h2
                                style="font-size: 24px; font-weight: bold; color: #333; position: relative; display: inline-block;" class="mb-5">
                                Pengumuman
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
                                            href="#">
                                            {{ Str::limit($pengumuman->title, 80) }}
                                        </a></h4>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
        </div>
        <!-- FEATURE END -->

        <!-- PROGRESS BAR AREA START -->
        <div class="ltn__progress-bar-area section-bg-1 pt-120 pb-10">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ltn__progress-bar-wrap">
                            <div class="ltn__progress-bar-inner">
                                <div class="row">
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="ltn__progress-bar-item-2 text-center">
                                            <div class="progress" data-value='78'>
                                                <span class="progress-left">
                                                    <span class="progress-bar border-primary"></span>
                                                </span>
                                                <span class="progress-right">
                                                    <span class="progress-bar border-primary"></span>
                                                </span>
                                                <div class="progress-value">
                                                    <div class="progress-count">78<sup class="small">%</sup></div>
                                                </div>
                                            </div>
                                            <div class="ltn__progress-info">
                                                <h3>Project Done</h3>
                                                <p>Construction Simulator</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="ltn__progress-bar-item-2 text-center">
                                            <div class="progress" data-value='62'>
                                                <span class="progress-left">
                                                    <span class="progress-bar border-danger"></span>
                                                </span>
                                                <span class="progress-right">
                                                    <span class="progress-bar border-danger"></span>
                                                </span>
                                                <div class="progress-value">
                                                    <div class="progress-count">62<sup class="small">%</sup></div>
                                                </div>
                                            </div>
                                            <div class="ltn__progress-info">
                                                <h3>Country Cover</h3>
                                                <p>Construction Simulator</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="ltn__progress-bar-item-2 text-center">
                                            <div class="progress" data-value='50'>
                                                <span class="progress-left">
                                                    <span class="progress-bar border-success"></span>
                                                </span>
                                                <span class="progress-right">
                                                    <span class="progress-bar border-success"></span>
                                                </span>
                                                <div class="progress-value">
                                                    <div class="progress-count">50<sup class="small">%</sup></div>
                                                </div>
                                            </div>
                                            <div class="ltn__progress-info">
                                                <h3>Completed Co.</h3>
                                                <p>Construction Simulator</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="ltn__progress-bar-item-2 text-center">
                                            <div class="progress" data-value='90'>
                                                <span class="progress-left">
                                                    <span class="progress-bar border-warning"></span>
                                                </span>
                                                <span class="progress-right">
                                                    <span class="progress-bar border-warning"></span>
                                                </span>
                                                <div class="progress-value">
                                                    <div class="progress-count">90<sup class="small">%</sup></div>
                                                </div>
                                            </div>
                                            <div class="ltn__progress-info">
                                                <h3>Happy Clients</h3>
                                                <p>Construction Simulator</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- PROGRESS BAR AREA END -->

        <!-- IMAGE SLIDER AREA START (img-slider-3) -->
        <div class="ltn__img-slider-area pb-100">
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
                                        <h6>Acredited:
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

        <!-- CALL TO ACTION START (call-to-action-4) -->
        <div class="ltn__call-to-action-area ltn__call-to-action-4 bg-image"
            data-bg="{{ asset('front/img/bg/36.jpg') }}">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="call-to-action-inner call-to-action-inner-4 text-center--- pt-115 pb-120">
                            <div class="section-title-area ltn__section-title-2">
                                <h6 class="section-subtitle ltn__secondary-color"><a
                                        href="https://ejournal.uinbukittinggi.ac.id/">E-journal UIN Sjech M.Djamil Djambek
                                        Bukittingi</a></h6>
                                <h1 class="section-title white-color">
                                    Masukkan Artikel Terbaik mu <br>
                                    Hanya disini
                                </h1>
                            </div>
                            <div class="btn-wrapper">
                                <a href="https://ejournal.uinbukittinggi.ac.id/"
                                    class="theme-btn-1 btn btn-effect-1">E-journal</a>
                            </div>
                        </div>
                        <div class="ltn__call-to-4-img-2">
                            <img src="{{ asset('front/img/bg/35.png') }}" alt="#"
                                style="height: 90%; width: 90%;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- CALL TO ACTION END -->

        <!-- FEATURE START -->
        <div class="ltn__feature-area section-bg-1--- pt-120 pb-90">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title-area ltn__section-title-2 text-center">
                            <h6 class="section-subtitle ltn__secondary-color"><span><i
                                        class="fas fa-square-full"></i></span>
                                Our Services</h6>
                            <h1 class="section-title">Construction Solution</h1>
                        </div>
                    </div>
                </div>
                <div class="row align-self-center">
                    <div class="col-lg-4 col-sm-6">
                        <div class="ltn__feature-item ltn__feature-item-6 box-shadow-1">
                            <div class="ltn__feature-icon">
                                <span><i class="flaticon-apartment"></i></span>
                            </div>
                            <div class="ltn__feature-info">
                                <h3><a href="service-details.html">Industrial construction</a></h3>
                                <p>over 1 million+ homes for sale available
                                    on the website, we can match you with a
                                    house you will want to call home.</p>
                                <a class="ltn__service-btn ltn__service-btn-2" href="service-details.html">Service
                                    Details <i class="flaticon-right-arrow"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="ltn__feature-item ltn__feature-item-6 box-shadow-1">
                            <div class="ltn__feature-icon">
                                <span><i class="flaticon-excavator"></i></span>
                            </div>
                            <div class="ltn__feature-info">
                                <h3><a href="service-details.html">Oil Gas & Power Plant</a></h3>
                                <p>over 1 million+ homes for sale available
                                    on the website, we can match you with a
                                    house you will want to call home.</p>
                                <a class="ltn__service-btn ltn__service-btn-2" href="service-details.html">Service
                                    Details <i class="flaticon-right-arrow"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="ltn__feature-item ltn__feature-item-6 box-shadow-1">
                            <div class="ltn__feature-icon">
                                <span><i class="icon-repair"></i></span>
                            </div>
                            <div class="ltn__feature-info">
                                <h3><a href="service-details.html">Mechanical Works</a></h3>
                                <p>over 1 million+ homes for sale available
                                    on the website, we can match you with a
                                    house you will want to call home.</p>
                                <a class="ltn__service-btn ltn__service-btn-2" href="service-details.html">Service
                                    Details <i class="flaticon-right-arrow"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="ltn__feature-item ltn__feature-item-6 box-shadow-1">
                            <div class="ltn__feature-icon">
                                <span><i class="flaticon-slider"></i></span>
                            </div>
                            <div class="ltn__feature-info">
                                <h3><a href="service-details.html">Power & Energy</a></h3>
                                <p>over 1 million+ homes for sale available
                                    on the website, we can match you with a
                                    house you will want to call home.</p>
                                <a class="ltn__service-btn ltn__service-btn-2" href="service-details.html">Service
                                    Details <i class="flaticon-right-arrow"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="ltn__feature-item ltn__feature-item-6 box-shadow-1">
                            <div class="ltn__feature-icon">
                                <span><i class="flaticon-building"></i></span>
                            </div>
                            <div class="ltn__feature-info">
                                <h3><a href="service-details.html">Petroleum Refinery</a></h3>
                                <p>over 1 million+ homes for sale available
                                    on the website, we can match you with a
                                    house you will want to call home.</p>
                                <a class="ltn__service-btn ltn__service-btn-2" href="service-details.html">Service
                                    Details <i class="flaticon-right-arrow"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="ltn__feature-item ltn__feature-item-6 box-shadow-1">
                            <div class="ltn__feature-icon">
                                <span><i class="flaticon-house"></i></span>
                            </div>
                            <div class="ltn__feature-info">
                                <h3><a href="service-details.html">Interior Design</a></h3>
                                <p>over 1 million+ homes for sale available
                                    on the website, we can match you with a
                                    house you will want to call home.</p>
                                <a class="ltn__service-btn ltn__service-btn-2" href="service-details.html">Service
                                    Details <i class="flaticon-right-arrow"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- FEATURE END -->

        <!-- ABOUT US AREA START -->
        <div class="ltn__about-us-area section-bg-6 bg-image-right-before pt-120 pb-90">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 align-self-center">
                        <div class="about-us-info-wrap">
                            <div class="section-title-area ltn__section-title-2 mb-20">
                                <h6 class="section-subtitle ltn__secondary-color"><span><i
                                            class="fas fa-square-full ltn__secondary-color"></i></span> Great
                                    Experience In Building</h6>
                                <h1 class="section-title">Our Specialization &
                                    Company Features</h1>
                            </div>
                            <ul class="ltn__list-item-half ltn__list-item-half-2 list-item-margin clearfix">
                                <li>
                                    <i class="icon-done"></i>
                                    Living rooms are pre-wired for Surround
                                </li>
                                <li>
                                    <i class="icon-done"></i>
                                    Luxurious interior design and amenities
                                </li>
                                <li>
                                    <i class="icon-done"></i>
                                    Nestled in the Buckhead Vinings communities
                                </li>
                                <li>
                                    <i class="icon-done"></i>
                                    Private balconies with stunning views
                                </li>
                                <li>
                                    <i class="icon-done"></i>
                                    A rare combination of inspired architecture
                                </li>
                                <li>
                                    <i class="icon-done"></i>
                                    Outdoor grilling with dining court
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6 align-self-center">
                        <div class="about-us-img-wrap about-img-left">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ABOUT US AREA END -->
    @endsection
