@extends('front.app')
@section('seo')
    <title>{{ $meta['description'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('journal.detail', $journal->url_path) }}">
    <link rel="canonical" href="{{ route('journal.detail', $journal->url_path) }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
@endsection

@section('content')
    @include('front.partials.breadcrumb')
    <!-- TEAM DETAILS AREA START -->
    <div class="ltn__team-details-area mb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <div class="ltn__team-details-member-info text-center mb-40">
                        <div class="team-details-img">
                            <img src="{{ $journal->getJournalThumbnail() }}" alt="Image">
                        </div>
                        <h2>{{ $journal->title }}</h2>
                        <div class="widget-2 ltn__menu-widget ltn__menu-widget-2 text-uppercase">
                            <ul>
                                <li><a href="{{ $journal->url }}">{{ __('front.view_journal') }} <span><i class="fas fa-arrow-right"></i></span></a></li>
                                <li><a href="#">{{ __('front.pay_publication_fee') }}<span><i class="fas fa-arrow-right"></i></span></a></li>
                            </ul>
                        </div>
                        {{-- <h6 class="text-uppercase ltn__secondary-color">Property Seller</h6> --}}
                        {{-- <div class="ltn__social-media-3">
                            <ul>
                                <li><a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="#" title="Twitter"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="#" title="Linkedin"><i class="fab fa-linkedin"></i></a></li>

                            </ul>
                        </div> --}}
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="ltn__team-details-member-info-details">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="ltn__team-details-member-about">
                                    <ul>
                                        <li><strong>{{ __('front.journal_title') }}:</strong> {{ $journal?->title??"-" }}</li>
                                        <li><strong>e-ISSN:</strong> {{ $journal?->onlineIssn??"-" }}</li>
                                        <li><strong>p-ISSN:</strong> {{ $journal?->printIssn??"-" }}</li>

                                        <li><strong>{{ __('front.acredited') }}:</strong> {{ $journal?->indexing?implode(", ", $journal->indexing):"-" }}</li>
                                        <li><strong>{{ __('front.publication_fee') }}:</strong> @money($journal?->author_fee)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <hr>

                        {!! $journal->description !!}


                        {{-- <div class="ltn__form-box contact-form-box box-shadow white-bg mt-50">
                            <h4 class="title-2">Contact for any Inquiry</h4>
                            <form id="contact-form" action="mail.php" method="post">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="input-item input-item-name ltn__custom-icon">
                                            <input type="text" name="name" placeholder="Enter your name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-item-email ltn__custom-icon">
                                            <input type="email" name="email" placeholder="Enter email address">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item">
                                            <select class="nice-select">
                                                <option>Select Service Type</option>
                                                <option>Property Management </option>
                                                <option>Mortgage Service </option>
                                                <option>Consulting Service</option>
                                                <option>Home Buying</option>
                                                <option>Home Selling</option>
                                                <option>Escrow Services</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-item input-item-phone ltn__custom-icon">
                                            <input type="text" name="phone" placeholder="Enter phone number">
                                        </div>
                                    </div>
                                </div>
                                <div class="input-item input-item-textarea ltn__custom-icon">
                                    <textarea name="message" placeholder="Enter message"></textarea>
                                </div>
                                <p><label class="input-info-save mb-0"><input type="checkbox" name="agree"> Save my name, email, and website in this browser for the next time I comment.</label></p>
                                <div class="btn-wrapper mt-0">
                                    <button class="btn theme-btn-1 btn-effect-1 text-uppercase" type="submit">get an free service</button>
                                </div>
                                <p class="form-messege mb-0 mt-20"></p>
                            </form>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- TEAM DETAILS AREA END -->
@endsection
