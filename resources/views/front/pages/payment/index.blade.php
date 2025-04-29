@extends('front.app')
@section('seo')
    <title>{{ $meta['description'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('payment.index') }}">
    <link rel="canonical" href="{{ route('payment.index') }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
@endsection

@section('content')
    @include('front.partials.breadcrumb')
    <div class="ltn__contact-message-area mb-50 ">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__form-box contact-form-box box-shadow white-bg">
                        <form id="contact-form" method="GET">
                            <div class="row">
                                <div class="col-md-3">
                                    <img src="{{ asset('ext_images/research.png') }}" alt="Image" class="img-fluid">
                                </div>
                                <div class="col-md-9">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4 class="title-2">{{ __('front.search_article') }}</h4>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="input-item input-item-name ltn__custom-icon">
                                                <select name="journal_id" class="nice-select">
                                                    <option value="">{{ __('front.all_journal') }}</option>
                                                    @foreach ($journals as $journal)
                                                        <option value="{{ $journal->id }}">{{ $journal->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="input-item input-item-name ltn__custom-icon">
                                                <input type="text" name="q" value="{{ request('q') }}"
                                                    placeholder="{{ __('front.search_article_placeholder') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="btn-wrapper mt-0">
                                                <button class="btn theme-btn-1 btn-effect-1 text-uppercase" type="submit">
                                                    {{ __('front.search_article_btn') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="ltn__product-tab-content-inner ltn__product-list-view">
            <div class="row">
                @foreach ($submissions as $submission)
                    <div class="col-lg-12">
                        <div class="ltn__product-item ltn__product-item-3" style="margin-bottom: 20px;">
                            <div class="product-info">
                                <h2 class="product-title"><a
                                        href="{{ route('payment.submission', [$submission?->issue?->journal?->url_path, $submission?->submission_id]) }}">{{ $submission->fullTitle }}</a>
                                </h2>
                                <div class="product-brief">
                                    <ul>
                                        @foreach ($submission->authors as $author)
                                            <li style="margin-top: 0px;">
                                                <span style="font-weight: 900">{{ $author['name'] }}</span>,
                                                {{ $author['affiliation'] }}
                                            </li>
                                        @endforeach

                                    </ul>
                                </div>

                                <div>
                                    <p>
                                        <strong>{{ __('front.journal') }}:</strong>
                                        <a href="{{ route('journal.detail', $submission->issue->journal->url_path) }}">
                                            {{ $submission->issue->journal->title }}
                                        </a>
                                        <br>
                                        <strong>Issue:</strong>
                                        <a href="{{ route('journal.detail', $submission->issue->journal->url_path) }}">
                                            Vol. {{ $submission->issue->volume }} No. {{ $submission->issue->number }}
                                            ({{ $submission->issue->year }}): {{ $submission->issue->title }}
                                        </a>
                                    </p>
                                </div>
                                @if ($submission?->issue?->journal?->author_fee > 0)
                                    <div>
                                        <span>{{ __('front.payment_status') }}: </span>
                                        @if ($submission->payment_status == 'paid')
                                            <span
                                                class="badge badge-success text-white">{{ $submission->payment_status }}</span>
                                        @elseif ($submission->payment_status == 'pending')
                                            <span
                                                class="badge badge-warning text-white">{{ $submission->payment_status }}</span>
                                        @elseif ($submission->payment_status == 'cencelled')
                                            <span
                                                class="badge badge-danger text-white">{{ $submission->payment_status }}</span>
                                        @elseif ($submission->payment_status == 'refunded')
                                            <span
                                                class="badge badge-danger text-white">{{ $submission->payment_status }}</span>
                                        @endif
                                    </div>
                                @endif
                                <div>
                                    <span>{{ __('front.submission_status') }}: </span>
                                    @if ($submission->status == 1)
                                                    <span
                                                        class="badge badge-warning fs-7 text-white">{{ $submission->status_label }}</span>
                                                @elseif ($submission->status == 3)
                                                    <span
                                                        class="badge badge-success fs-7 text-white">{{ $submission->status_label }}</span>
                                                @elseif ($submission->status == 4)
                                                    <span
                                                        class="badge badge-danger fs-7 text-white">{{ $submission->status_label }}</span>
                                                @else
                                                    <span
                                                        class="badge badge-secondary fs-7 text-white">{{ $submission->status_label }}</span>
                                                @endif
                                </div>
                                <div>
                                    <span>{{ __('front.date_published') }}: </span>
                                    <span>
                                        {{ $submission->datePublished ?? '-' }}
                                    </span>
                                </div>


                                <div class="product-hover-action">
                                    <ul>
                                        <li>
                                            <a
                                                href="{{ route('payment.submission', [$submission?->issue?->journal?->url_path, $submission?->submission_id]) }}"title="Detail">
                                                <i class="far fa-eye"></i>
                                            </a>
                                        </li>
                                        @if ($submission?->issue?->journal?->author_fee > 0)
                                            <li>
                                                <a href="{{ route('payment.pay', [$submission?->issue?->journal?->url_path, $submission?->submission_id]) }}"
                                                    title="Pay Now">
                                                    <i class="fas fa-credit-card"></i>
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a href="{{ $submission->urlPublished }}" title="View Publish"
                                                data-toggle="modal" data-target="#liton_wishlist_modal">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
