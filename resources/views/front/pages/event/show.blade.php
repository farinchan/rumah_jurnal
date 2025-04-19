@extends('front.app')
@section('seo')
    <title>{{ $meta["description"] }}</title>
    <meta name="description" content="{{ $meta["description"] }}">
    <meta name="keywords" content="{{ $meta["keywords"] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta["title"] }}">
    <meta property="og:description" content="{{ $meta["description"] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('event.show', $event->slug) }}">
    <link rel="canonical" href="{{ route('event.show', $event->slug) }}">
    <meta property="og:image" content="{{ Storage::url($meta["favicon"]) }}">
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
                        <div class="ltn__blog-img">
                            <img src="{{ $event->getImage() }}" alt="Image">
                        </div>
                        <table>
                            <tr>
                                <td><strong>Dari Tanggal:</strong></td>
                                <td>{{ Carbon\Carbon::parse($event->start)->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Hingga Tanggal:</strong></td>
                                <td>{{ Carbon\Carbon::parse($event->end)->format('d M Y') }}</td>
                            </tr>
                        </table>
                        <p>
                            {!! $event->content !!}
                        </p>
                        @if ($event->file)
                        <object data="{{ Storage::url($event->file) }}" type="application/pdf"
                            width="100%" height="800px">
                            <embed src="{{ Storage::url($event->file) }}" type="application/pdf" />
                        </object>
                    @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- PAGE DETAILS AREA END -->
@endsection
