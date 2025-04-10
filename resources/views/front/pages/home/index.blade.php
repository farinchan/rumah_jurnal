@extends('front.app')

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
                            <a class="ltn__video-icon-2 ltn__video-icon-2-border--- border-radius-no ltn__secondary-bg"
                                href="https://www.youtube.com/embed/X7R-q9rsrtU?autoplay=1&showinfo=0"
                                data-rel="lightcase:myCollection">
                                <i class="fa fa-play"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 align-self-center">
                    <div class="about-us-info-wrap">
                        <div class="section-title-area ltn__section-title-2">
                            <h6 class="section-subtitle ltn__secondary-color"><span><i
                                        class="fas fa-square-full"></i></span> Great Experience In Building</h6>
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
                                <img src="{{ $journal->getJournalThumbnail() }}" alt="Image" style="height: 600px; width: 100%; object-fit: cover; object-position: top;">
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
                                    <a href="{{ route('journal.detail', $journal->url_path) }}" class="btn theme-btn-1 btn-effect-1"><i
                                            class="fas fa-arrow-right"></i></a>
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
    <div class="ltn__call-to-action-area ltn__call-to-action-4 bg-image" data-bg="{{ asset('front/img/bg/36.jpg') }}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="call-to-action-inner call-to-action-inner-4 text-center--- pt-115 pb-120">
                        <div class="section-title-area ltn__section-title-2">
                            <h6 class="section-subtitle ltn__secondary-color"><a
                                    href="https://ejournal.uinbukittinggi.ac.id/">E-journal UIN Sjech M.Djamil Djambek Bukittingi</a></h6>
                            <h1 class="section-title white-color">
                                Masukkan Artikel Terbaik mu <br>
                                Hanya disini
                            </h1>
                        </div>
                        <div class="btn-wrapper">
                            <a href="https://ejournal.uinbukittinggi.ac.id/" class="theme-btn-1 btn btn-effect-1">E-journal</a>
                        </div>
                    </div>
                    <div class="ltn__call-to-4-img-2">
                        <img src="{{ asset('front/img/bg/35.png') }}" alt="#" style="height: 90%; width: 90%;">
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
                        <h6 class="section-subtitle ltn__secondary-color"><span><i class="fas fa-square-full"></i></span>
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



    <!-- BLOG AREA START (blog-3) -->
    <div class="ltn__blog-area pt-120 pb-70">
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
@endsection
