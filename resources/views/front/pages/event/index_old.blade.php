@extends('front.app')
@section('seo')
    <title>{{ $meta["description"] }}</title>
    <meta name="description" content="{{ $meta["description"] }}">
    <meta name="keywords" content="{{ $meta["keywords"] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta["title"] }}">
    <meta property="og:description" content="{{ $meta["description"] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('event.index') }}">
    <link rel="canonical" href="{{ route('event.index') }}">
    <meta property="og:image" content="{{ Storage::url($meta["favicon"]) }}">
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
    </style>
@endsection
@section('content')
    @include('front.partials.breadcrumb')

<!-- FEATURE START -->
<div class="ltn__feature-area   pb-90">
    <div class="container">

        <div class="row p-3">
            <div class=" col-md-12">

                <div class="row mt-5">
                    @foreach ($list_event as $event)
                        <div class="col-md-4">
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
                                            <a style="color: #333;" href="{{ route('event.show', $event->slug) }}"
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
        </div>
    </div>
    <!-- FEATURE END -->
</div>

@endsection
