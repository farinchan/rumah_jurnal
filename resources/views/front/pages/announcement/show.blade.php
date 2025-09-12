@extends('front.app')
@section('seo')
    <title>{{ $meta['description'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('announcement.show', $announcement->slug) }}">
    <link rel="canonical" href="{{ route('announcement.show', $announcement->slug) }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
@endsection
@section('styles')
@endsection
@section('content')
    @include('front.partials.breadcrumb')

    <!-- PAGE DETAILS AREA START (portfolio-details) -->
    <div class="ltn__page-details-area ltn__portfolio-details-area mb-105">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__page-details-inner ltn__portfolio-details-inner">
                        <h2 class="ltn__blog-title">
                            {{ $announcement->title }}  
                        </h2>
                        <div class="ltn__blog-meta">
                            <ul>
                                <li class="ltn__blog-author">
                                    <a href="#"><img src="{{ $announcement->user?->getPhoto() ?? '' }}"
                                            alt="#">By:
                                        {{ $announcement->user?->name ?? '' }}</a>
                                </li>
                                <li class="ltn__blog-date">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ Carbon\Carbon::parse($announcement->created_at)->format('d M Y') }}
                                </li>
                            </ul>
                        </div>
                        <p>
                            {!! $announcement->content !!}
                        </p>
                        @if ($announcement->file)
                            <object data="{{ Storage::url($announcement->file) }}" type="application/pdf" width="100%"
                                height="800px">
                                <embed src="{{ Storage::url($announcement->file) }}" type="application/pdf" />
                            </object>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- PAGE DETAILS AREA END -->
@endsection
