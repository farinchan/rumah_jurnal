@extends('front.app')
@section('seo')
    <title>{{ $meta['description'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('event.index') }}">
    <link rel="canonical" href="{{ route('event.index') }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
@endsection
@section('styles')
@endsection
@section('content')
    @include('front.partials.breadcrumb')

    <!-- BLOG AREA START -->
    <div class="ltn__blog-area ltn__blog-item-3-normal mb-100">
        <div class="container">
            <div class="row">
                @forelse ($list_event as $event)
                    <div class="col-lg-4 col-sm-6 col-12">
                        <div class="ltn__blog-item ltn__blog-item-3">

                            <div class="ltn__blog-brief">
                                <div class="ltn__blog-img mb-3 position-relative">
                                    {{-- <span class="badge bg-info position-absolute" style="top: 10px; left: 10px; z-index: 2; font-size: 0.9rem; padding: 6px 14px; border-radius: 20px; opacity: 0.95;">
                                        Trending
                                    </span> --}}
                                    <a href="{{ route('event.show', $event->slug) }}">
                                        <img src="{{ $event->getThumbnail() }}" alt="#" style="aspect-ratio: 4/5; object-fit: cover; width: 100%;">
                                    </a>
                                </div>
                                <h3 class="ltn__blog-title"><a
                                        href="{{ route('event.show', $event->slug) }}">{{ $event->name }}</a>
                                </h3>
                                <div class="ltn__blog-meta">
                                    <ul>
                                        <li class="ltn__blog-author">
                                            <a href="#"><i class="fa fa-info-circle"></i>{{ $event->type }}</a>
                                        </li>
                                        <li class="ltn__blog-author">
                                            <a href="#"><i class="fa fa-bullhorn"></i>
                                                {{ $event->status }}</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="ltn__blog-meta-btn">
                                    <div class="ltn__blog-meta">
                                        <ul>
                                            <li class="ltn__blog-date"><i class="far fa-calendar-alt"></i>
                                                {{ $event->datetime }}
                                            </li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                @empty
                    <div class="col-lg-12">
                        <div class="alert alert-warning text-center">
                            <strong>Tidak Ada Pengumuman</strong>
                        </div>
                    </div>
                @endforelse
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__pagination-area text-center">
                        <div class="ltn__pagination">
                            <ul>
                                @if ($list_event->onFirstPage())
                                    <li><a href="#"><i class="fas fa-angle-double-left"></i></a></li>
                                @else
                                    <li><a href="{{ $list_event->previousPageUrl() }}"><i
                                                class="fas fa-angle-double-left"></i></a></li>
                                @endif
                                @php
                                    $start = max($list_event->currentPage() - 2, 1);
                                    $end = min($start + 2, $list_event->lastPage());
                                @endphp
                                @if ($start > 1)
                                    <li class="{{ $list_event->currentPage() == 1 ? 'active' : '' }}"><a
                                            href="{{ $list_event->url(1) }}">1</a></li>
                                    <li><a href="#">...</a></li>
                                @endif

                                @foreach ($list_event->getUrlRange($start, $end) as $page => $url)
                                    <li class="{{ $page == $list_event->currentPage() ? ' active' : '' }}"><a
                                            href="{{ $url }}">{{ $page }}</a></li>
                                @endforeach

                                @if ($end < $list_event->lastPage())
                                    <li><a href="#">...</a></li>
                                    <li
                                        class="{{ $list_event->currentPage() == $list_event->lastPage() ? ' active' : '' }}">
                                        <a
                                            href="{{ $list_event->url($list_event->lastPage()) }}">{{ $list_event->lastPage() }}</a>
                                    </li>
                                @endif

                                @if ($list_event->hasMorePages())
                                    <li><a href="{{ $list_event->nextPageUrl() }}"><i
                                                class="fas fa-angle-double-right"></i></a></li>
                                @else
                                    <li><a href="#"><i class="fas fa-angle-double-right"></i></a></li>
                                @endif


                                {{-- <li><a href="#">1</a></li>
                            <li class="active"><a href="#">2</a></li>
                            <li><a href="#">3</a></li>
                            <li><a href="#">...</a></li>
                            <li><a href="#">10</a></li>
                            <li><a href="#"><i class="fas fa-angle-double-right"></i></a></li> --}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- BLOG AREA END -->
@endsection
