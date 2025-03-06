@extends('front.app')

@section('content')
    @include('front.partials.slider')

    <!-- CATEGORY AREA START -->
    <div class="ltn__category-area ltn__product-gutter section-bg-1--- pt-115 pb-90---  mt--65">
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
    </div>
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

    <!-- CALL TO ACTION START (call-to-action-4) -->
    <div class="ltn__call-to-action-area ltn__call-to-action-4 bg-image" data-bg="img/bg/36.jpg")}}">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="call-to-action-inner call-to-action-inner-4 text-center--- pt-115 pb-120">
                        <div class="section-title-area ltn__section-title-2">
                            <h6 class="section-subtitle ltn__secondary-color"><a
                                    href="tel:+123456789">123-456-7890/call</a></h6>
                            <h1 class="section-title white-color">A Company Involved In <br>
                                Service, Maintenance</h1>
                        </div>
                        <div class="btn-wrapper">
                            <a href="contact.html" class="theme-btn-1 btn btn-effect-1">Get Appointment</a>
                        </div>
                    </div>
                    <div class="ltn__call-to-4-img-2">
                        <img src="{{ asset('front/img/bg/35.png') }}" alt="#">
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

    <!-- TEAM AREA START (Team - 3) -->
    <div class="ltn__team-area pt-120 pb-90---">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2 text-center">
                        <h6 class="section-subtitle ltn__secondary-color"><span><i class="fas fa-square-full"></i></span>
                            Our Expert Worker</h6>
                        <h1 class="section-title">Our Expert Worker</h1>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__team-item ltn__team-item-3 ltn__team-item-3-2">
                        <div class="team-img">
                            <img src="{{ asset('front/img/team/1.jpg') }}" alt="Image">
                        </div>
                        <div class="team-info">
                            <h4><a href="team-details.html">Rosalina D. William</a></h4>
                            <h6 class="ltn__secondary-color">Technology Officer</h6>
                            <div class="ltn__social-media">
                                <ul>
                                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__team-item ltn__team-item-3 ltn__team-item-3-2">
                        <div class="team-img">
                            <img src="{{ asset('front/img/team/2.jpg') }}" alt="Image">
                        </div>
                        <div class="team-info">
                            <h4><a href="team-details.html">Kelian Anderson</a></h4>
                            <h6 class="ltn__secondary-color">Engineering Officer</h6>
                            <div class="ltn__social-media">
                                <ul>
                                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="ltn__team-item ltn__team-item-3 ltn__team-item-3-2">
                        <div class="team-img">
                            <img src="{{ asset('front/img/team/3.jpg') }}" alt="Image">
                        </div>
                        <div class="team-info">
                            <h4><a href="team-details.html">Miranda H. Halim</a></h4>
                            <h6 class="ltn__secondary-color">Property Seller</h6>
                            <div class="ltn__social-media">
                                <ul>
                                    <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#"><i class="fab fa-linkedin"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- TEAM AREA END -->

    <!-- PROGRESS BAR AREA START -->
    <div class="ltn__progress-bar-area pt-45 pb-10">
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

    <!-- TESTIMONIAL AREA START (testimonial-8) -->
    <div class="ltn__testimonial-area section-bg-1--- pt-115--- pb-75">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area ltn__section-title-2 text-center">
                        <h6 class="section-subtitle ltn__secondary-color"><span><i class="fas fa-square-full"></i></span>
                            Client,s Testimonial</h6>
                        <h1 class="section-title">Client,S Feedback</h1>
                    </div>
                </div>
            </div>
            <div class="row ltn__testimonial-slider-6-active slick-arrow-1">
                <div class="col-lg-4">
                    <div class="ltn__testimonial-item ltn__testimonial-item-7 ltn__testimonial-item-8">
                        <div class="ltn__testimoni-info">
                            <div class="ltn__testimoni-author-ratting">
                                <div class="ltn__testimoni-info-inner">
                                    <div class="ltn__testimoni-img">
                                        <img src="{{ asset('front/img/testimonial/1.jpg') }}" alt="#">
                                    </div>
                                    <div class="ltn__testimoni-name-designation">
                                        <h5>Jacob William</h5>
                                        <label>Selling Agents</label>
                                    </div>
                                </div>
                                <div class="ltn__testimoni-rating">
                                    <div class="product-ratting">
                                        <ul>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <p>
                                Precious ipsum dolor sit amet
                                consectetur adipisicing elit, sed dos
                                mod tempor incididunt ut labore et
                                dolore magna aliqua. Ut enim ad min
                                veniam, quis nostrud Precious ips
                                um dolor sit amet, consecte</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="ltn__testimonial-item ltn__testimonial-item-7 ltn__testimonial-item-8">
                        <div class="ltn__testimoni-info">
                            <div class="ltn__testimoni-author-ratting">
                                <div class="ltn__testimoni-info-inner">
                                    <div class="ltn__testimoni-img">
                                        <img src="{{ asset('front/img/testimonial/2.jpg') }}" alt="#">
                                    </div>
                                    <div class="ltn__testimoni-name-designation">
                                        <h5>Jacob William</h5>
                                        <label>Selling Agents</label>
                                    </div>
                                </div>
                                <div class="ltn__testimoni-rating">
                                    <div class="product-ratting">
                                        <ul>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <p>
                                Precious ipsum dolor sit amet
                                consectetur adipisicing elit, sed dos
                                mod tempor incididunt ut labore et
                                dolore magna aliqua. Ut enim ad min
                                veniam, quis nostrud Precious ips
                                um dolor sit amet, consecte</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="ltn__testimonial-item ltn__testimonial-item-7 ltn__testimonial-item-8">
                        <div class="ltn__testimoni-info">
                            <div class="ltn__testimoni-author-ratting">
                                <div class="ltn__testimoni-info-inner">
                                    <div class="ltn__testimoni-img">
                                        <img src="{{ asset('front/img/testimonial/3.jpg') }}" alt="#">
                                    </div>
                                    <div class="ltn__testimoni-name-designation">
                                        <h5>Jacob William</h5>
                                        <label>Selling Agents</label>
                                    </div>
                                </div>
                                <div class="ltn__testimoni-rating">
                                    <div class="product-ratting">
                                        <ul>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <p>
                                Precious ipsum dolor sit amet
                                consectetur adipisicing elit, sed dos
                                mod tempor incididunt ut labore et
                                dolore magna aliqua. Ut enim ad min
                                veniam, quis nostrud Precious ips
                                um dolor sit amet, consecte</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="ltn__testimonial-item ltn__testimonial-item-7 ltn__testimonial-item-8">
                        <div class="ltn__testimoni-info">
                            <div class="ltn__testimoni-author-ratting">
                                <div class="ltn__testimoni-info-inner">
                                    <div class="ltn__testimoni-img">
                                        <img src="{{ asset('front/img/testimonial/4.jpg') }}" alt="#">
                                    </div>
                                    <div class="ltn__testimoni-name-designation">
                                        <h5>Jacob William</h5>
                                        <label>Selling Agents</label>
                                    </div>
                                </div>
                                <div class="ltn__testimoni-rating">
                                    <div class="product-ratting">
                                        <ul>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                            <li><a href="#"><i class="fas fa-star"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <p>
                                Precious ipsum dolor sit amet
                                consectetur adipisicing elit, sed dos
                                mod tempor incididunt ut labore et
                                dolore magna aliqua. Ut enim ad min
                                veniam, quis nostrud Precious ips
                                um dolor sit amet, consecte</p>
                        </div>
                    </div>
                </div>
                <!--  -->
            </div>
        </div>
    </div>
    <!-- TESTIMONIAL AREA END -->

    <!-- VIDEO AREA START -->
    <div class="ltn__video-popup-area ltn__video-popup-margin---">
        <div class="ltn__video-bg-img ltn__video-popup-height-600--- bg-overlay-black-30 bg-image bg-fixed ltn__animation-pulse1"
            data-bg="img/bg/37.jpg")}}">
            <a class="ltn__video-icon-2 ltn__video-icon-2-border---"
                href="https://www.youtube.com/embed/X7R-q9rsrtU?autoplay=1&showinfo=0" data-rel="lightcase:myCollection">
                <i class="fa fa-play"></i>
            </a>
        </div>
    </div>
    <!-- VIDEO AREA END -->

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
                <!-- Blog Item -->
                <div class="col-lg-12">
                    <div class="ltn__blog-item ltn__blog-item-3">
                        <div class="ltn__blog-img">
                            <a href="blog-details.html"><img src="{{ asset('front/img/blog/1.jpg') }}"
                                    alt="#"></a>
                        </div>
                        <div class="ltn__blog-brief">
                            <div class="ltn__blog-meta">
                                <ul>
                                    <li class="ltn__blog-author">
                                        <a href="#"><i class="far fa-user"></i>by: Admin</a>
                                    </li>
                                    <li class="ltn__blog-tags">
                                        <a href="#"><i class="fas fa-tags"></i>Decorate</a>
                                    </li>
                                </ul>
                            </div>
                            <h3 class="ltn__blog-title"><a href="blog-details.html">10 Brilliant Ways To Decorate
                                    Your Home</a></h3>
                            <div class="ltn__blog-meta-btn">
                                <div class="ltn__blog-meta">
                                    <ul>
                                        <li class="ltn__blog-date"><i class="far fa-calendar-alt"></i>June 24,
                                            2021</li>
                                    </ul>
                                </div>
                                <div class="ltn__blog-btn">
                                    <a href="blog-details.html">Read more</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Blog Item -->
                <div class="col-lg-12">
                    <div class="ltn__blog-item ltn__blog-item-3">
                        <div class="ltn__blog-img">
                            <a href="blog-details.html"><img src="{{ asset('front/img/blog/2.jpg') }}"
                                    alt="#"></a>
                        </div>
                        <div class="ltn__blog-brief">
                            <div class="ltn__blog-meta">
                                <ul>
                                    <li class="ltn__blog-author">
                                        <a href="#"><i class="far fa-user"></i>by: Admin</a>
                                    </li>
                                    <li class="ltn__blog-tags">
                                        <a href="#"><i class="fas fa-tags"></i>Interior</a>
                                    </li>
                                </ul>
                            </div>
                            <h3 class="ltn__blog-title"><a href="blog-details.html">The Most Inspiring Interior
                                    Design Of 2021</a></h3>
                            <div class="ltn__blog-meta-btn">
                                <div class="ltn__blog-meta">
                                    <ul>
                                        <li class="ltn__blog-date"><i class="far fa-calendar-alt"></i>July 23,
                                            2021</li>
                                    </ul>
                                </div>
                                <div class="ltn__blog-btn">
                                    <a href="blog-details.html">Read more</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Blog Item -->
                <div class="col-lg-12">
                    <div class="ltn__blog-item ltn__blog-item-3">
                        <div class="ltn__blog-img">
                            <a href="blog-details.html"><img src="{{ asset('front/img/blog/3.jpg') }}"
                                    alt="#"></a>
                        </div>
                        <div class="ltn__blog-brief">
                            <div class="ltn__blog-meta">
                                <ul>
                                    <li class="ltn__blog-author">
                                        <a href="#"><i class="far fa-user"></i>by: Admin</a>
                                    </li>
                                    <li class="ltn__blog-tags">
                                        <a href="#"><i class="fas fa-tags"></i>Estate</a>
                                    </li>
                                </ul>
                            </div>
                            <h3 class="ltn__blog-title"><a href="blog-details.html">Recent Commercial Real Estate
                                    Transactions</a></h3>
                            <div class="ltn__blog-meta-btn">
                                <div class="ltn__blog-meta">
                                    <ul>
                                        <li class="ltn__blog-date"><i class="far fa-calendar-alt"></i>May 22, 2021
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
                <!-- Blog Item -->
                <div class="col-lg-12">
                    <div class="ltn__blog-item ltn__blog-item-3">
                        <div class="ltn__blog-img">
                            <a href="blog-details.html"><img src="{{ asset('front/img/blog/4.jpg') }}"
                                    alt="#"></a>
                        </div>
                        <div class="ltn__blog-brief">
                            <div class="ltn__blog-meta">
                                <ul>
                                    <li class="ltn__blog-author">
                                        <a href="#"><i class="far fa-user"></i>by: Admin</a>
                                    </li>
                                    <li class="ltn__blog-tags">
                                        <a href="#"><i class="fas fa-tags"></i>Room</a>
                                    </li>
                                </ul>
                            </div>
                            <h3 class="ltn__blog-title"><a href="blog-details.html">Renovating a Living Room?
                                    Experts Share Their Secrets</a></h3>
                            <div class="ltn__blog-meta-btn">
                                <div class="ltn__blog-meta">
                                    <ul>
                                        <li class="ltn__blog-date"><i class="far fa-calendar-alt"></i>June 24,
                                            2021</li>
                                    </ul>
                                </div>
                                <div class="ltn__blog-btn">
                                    <a href="blog-details.html">Read more</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Blog Item -->
                <div class="col-lg-12">
                    <div class="ltn__blog-item ltn__blog-item-3">
                        <div class="ltn__blog-img">
                            <a href="blog-details.html"><img src="{{ asset('front/img/blog/5.jpg') }}"
                                    alt="#"></a>
                        </div>
                        <div class="ltn__blog-brief">
                            <div class="ltn__blog-meta">
                                <ul>
                                    <li class="ltn__blog-author">
                                        <a href="#"><i class="far fa-user"></i>by: Admin</a>
                                    </li>
                                    <li class="ltn__blog-tags">
                                        <a href="#"><i class="fas fa-tags"></i>Trends</a>
                                    </li>
                                </ul>
                            </div>
                            <h3 class="ltn__blog-title"><a href="blog-details.html">7 home trends that will shape
                                    your house in 2021</a></h3>
                            <div class="ltn__blog-meta-btn">
                                <div class="ltn__blog-meta">
                                    <ul>
                                        <li class="ltn__blog-date"><i class="far fa-calendar-alt"></i>June 24,
                                            2021</li>
                                    </ul>
                                </div>
                                <div class="ltn__blog-btn">
                                    <a href="blog-details.html">Read more</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--  -->
            </div>
        </div>
    </div>
    <!-- BLOG AREA END -->

@endsection
