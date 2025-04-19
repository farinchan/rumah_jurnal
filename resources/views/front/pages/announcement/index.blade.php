@extends('front.app')
@section('seo')
    <title>{{ $meta["description"] }}</title>
    <meta name="description" content="{{ $meta["description"] }}">
    <meta name="keywords" content="{{ $meta["keywords"] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta["title"] }}">
    <meta property="og:description" content="{{ $meta["description"] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('announcement.index') }}">
    <link rel="canonical" href="{{ route('announcement.index') }}">
    <meta property="og:image" content="{{ Storage::url($meta["favicon"]) }}">
@endsection
@section('styles')

@endsection
@section('content')
    @include('front.partials.breadcrumb')

 <!-- BLOG AREA START -->
 <div class="ltn__blog-area ltn__blog-item-3-normal mb-100">
    <div class="container">
        <div class="row">
            @forelse ($list_announcement as $announcement)
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="ltn__blog-item ltn__blog-item-3">

                        <div class="ltn__blog-brief">
                            <div class="ltn__blog-meta">
                                <ul>
                                    <li class="ltn__blog-author">
                                        <a href="#"><i class="far fa-user"></i>by: {{ $announcement->user->name }}</a>
                                    </li>
                                </ul>
                            </div>
                            <h3 class="ltn__blog-title"><a href="{{ route('announcement.show', $announcement->slug) }}">{{ $announcement->title }}</a></h3>
                            <div class="ltn__blog-meta-btn">
                                <div class="ltn__blog-meta">
                                    <ul>
                                        <li class="ltn__blog-date"><i class="far fa-calendar-alt"></i>{{ Carbon\Carbon::parse($announcement->start)->format('d M Y') }}</li>
                                    </ul>
                                </div>
                                <div class="ltn__blog-btn">
                                    <a href="{{ route('announcement.show', $announcement->slug) }}">Read more</a>
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
                            @if ($list_announcement->onFirstPage())
                                <li><a href="#"><i class="fas fa-angle-double-left"></i></a></li>
                            @else
                                <li><a href="{{ $list_announcement->previousPageUrl() }}"><i
                                            class="fas fa-angle-double-left"></i></a></li>
                            @endif
                            @php
                                $start = max($list_announcement->currentPage() - 2, 1);
                                $end = min($start + 2, $list_announcement->lastPage());
                            @endphp
                            @if ($start > 1)
                                <li class="{{ $list_announcement->currentPage() == 1 ? 'active' : '' }}"><a
                                        href="{{ $list_announcement->url(1) }}">1</a></li>
                                <li><a href="#">...</a></li>
                            @endif

                            @foreach ($list_announcement->getUrlRange($start, $end) as $page => $url)
                                <li class="{{ $page == $list_announcement->currentPage() ? ' active' : '' }}"><a
                                        href="{{ $url }}">{{ $page }}</a></li>
                            @endforeach

                            @if ($end < $list_announcement->lastPage())
                                <li><a href="#">...</a></li>
                                <li class="{{ $list_announcement->currentPage() == $list_announcement->lastPage() ? ' active' : '' }}">
                                    <a href="{{ $list_announcement->url($list_announcement->lastPage()) }}">{{ $list_announcement->lastPage() }}</a>
                                </li>
                            @endif

                            @if ($list_announcement->hasMorePages())
                                <li><a href="{{ $list_announcement->nextPageUrl() }}"><i
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
