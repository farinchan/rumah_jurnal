@php
    $banner1 = \App\Models\SettingBanner::where('status', 1)->where('id', 1)->first() ?? null;
    $banner2 = \App\Models\SettingBanner::where('status', 1)->where('id', 2)->first() ?? null;
    $banner3 = \App\Models\SettingBanner::where('status', 1)->where('id', 3)->first() ?? null;
    // dd($banner1);
@endphp

<!-- SLIDER AREA START (slider-3) -->
<div class="ltn__slider-area ltn__slider-3  section-bg-1">
    <div class="ltn__slide-one-active slick-slide-arrow-1 slick-slide-dots-1">
        <!-- ltn__slide-item -->
        @if ($banner1)
            <div class="ltn__slide-item ltn__slide-item-2 ltn__slide-item-3-normal ltn__slide-item-3 bg-image"
                data-bg="{{ $banner1->getImage() }}">
                <div class="ltn__slide-item-inner">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 align-self-center">
                                <div class="slide-item-info">
                                    <div class="slide-item-info-inner ltn__slide-animation">
                                        {{-- <div class="slide-video mb-50 d-none">
                                        <a class="ltn__video-icon-2 ltn__video-icon-2-border"
                                            href="https://www.youtube.com/embed/tlThdr3O5Qo"
                                            data-rel="lightcase:myCollection">
                                            <i class="fa fa-play"></i>
                                        </a>
                                    </div> --}}
                                        {{-- <h6 class="slide-sub-title ltn__secondary-color animated text-uppercase">
                                        <span><i class="fas fa-square-full"></i></span> Great Experience In
                                        Building
                                    </h6> --}}
                                        <h1 class="slide-title animated ">
                                            {{ $banner1->title }}
                                        </h1>
                                        <div class="slide-brief animated">
                                            <p>
                                                {{ $banner1->subtitle }}
                                            </p>
                                        </div>
                                        <div class="btn-wrapper animated">
                                            <a href="{{ $banner1->url }}" class="theme-btn-1 btn btn-effect-1">
                                                {{ __('front.more') }}

                                            </a>
                                            {{-- <a class="ltn__video-play-btn bg-white"
                                            href="https://www.youtube.com/embed/HnbMYzdjuBs?autoplay=1&amp;showinfo=0"
                                            data-rel="lightcase">
                                            <i class="icon-play  ltn__secondary-color"></i>
                                        </a> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="slide-item-img">
                                    <!-- <img src="{{ asset('front/img/slider/21.png') }}" alt="#"> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <!-- ltn__slide-item -->
        @if ($banner2)
            <div class="ltn__slide-item ltn__slide-item-2 ltn__slide-item-3-normal ltn__slide-item-3 bg-image"
                data-bg="{{ $banner2->getImage() }}">
                <div class="ltn__slide-item-inner  text-right">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 align-self-center">
                                <div class="slide-item-info">
                                    <div class="slide-item-info-inner ltn__slide-animation">
                                        {{-- <div class="slide-video mb-50 d-none">
                                        <a class="ltn__video-icon-2 ltn__video-icon-2-border"
                                            href="https://www.youtube.com/embed/tlThdr3O5Qo"
                                            data-rel="lightcase:myCollection">
                                            <i class="fa fa-play"></i>
                                        </a>
                                    </div> --}}
                                        {{-- <h6 class="slide-sub-title ltn__secondary-color animated text-uppercase">
                                        <span><i class="fas fa-square-full"></i></span> Great Experience In
                                        Building
                                    </h6> --}}
                                        <h1 class="slide-title animated ">
                                            {{ $banner2->title }}
                                        </h1>
                                        <div class="slide-brief animated">
                                            <p>
                                                {{ $banner2->subtitle }}
                                            </p>
                                        </div>
                                        <div class="btn-wrapper animated">
                                            <a href="{{ $banner2->url }}" class="theme-btn-1 btn btn-effect-1">
                                                {{ __('front.more') }}

                                            </a>
                                            {{-- <a class="ltn__video-play-btn bg-white"
                                            href="https://www.youtube.com/embed/HnbMYzdjuBs?autoplay=1&amp;showinfo=0"
                                            data-rel="lightcase">
                                            <i class="icon-play  ltn__secondary-color"></i>
                                        </a> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="slide-item-img">
                                    <!-- <img src="{{ asset('front/img/slider/21.png') }}" alt="#"> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($banner3)
            <div class="ltn__slide-item ltn__slide-item-2 ltn__slide-item-3-normal ltn__slide-item-3 bg-image"
                data-bg="{{ $banner3->getImage() }}">
                <div class="ltn__slide-item-inner">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-12 align-self-center">
                                <div class="slide-item-info">
                                    <div class="slide-item-info-inner ltn__slide-animation">
                                        {{-- <div class="slide-video mb-50 d-none">
                                        <a class="ltn__video-icon-2 ltn__video-icon-2-border"
                                            href="https://www.youtube.com/embed/tlThdr3O5Qo"
                                            data-rel="lightcase:myCollection">
                                            <i class="fa fa-play"></i>
                                        </a>
                                    </div> --}}
                                        {{-- <h6 class="slide-sub-title ltn__secondary-color animated text-uppercase">
                                        <span><i class="fas fa-square-full"></i></span> Great Experience In
                                        Building
                                    </h6> --}}
                                        <h1 class="slide-title animated ">
                                            {{ $banner3->title }}
                                        </h1>
                                        <div class="slide-brief animated">
                                            <p>
                                                {{ $banner3->subtitle }}
                                            </p>
                                        </div>
                                        <div class="btn-wrapper animated">
                                            <a href="{{ $banner3->url }}" class="theme-btn-1 btn btn-effect-1">
                                                {{ __('front.more') }}
                                            </a>
                                            {{-- <a class="ltn__video-play-btn bg-white"
                                            href="https://www.youtube.com/embed/HnbMYzdjuBs?autoplay=1&amp;showinfo=0"
                                            data-rel="lightcase">
                                            <i class="icon-play  ltn__secondary-color"></i>
                                        </a> --}}
                                        </div>
                                    </div>
                                </div>
                                <div class="slide-item-img">
                                    <!-- <img src="{{ asset('front/img/slider/21.png') }}" alt="#"> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
<!-- SLIDER AREA END -->
