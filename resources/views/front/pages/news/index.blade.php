@extends('front.app')
@section('seo')
    <title>{{ $meta['description'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('news.index') }}">
    <link rel="canonical" href="{{ route('news.index') }}">
    <meta property="og:image" content="{{ Storage::url($meta['favicon']) }}">
@endsection

@section('content')
    @include('front.partials.breadcrumb')
    <!-- BLOG AREA START -->
    <div class="ltn__blog-area mb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="ltn__blog-list-wrap">
                        @forelse ($news as $item)
                            <div class="ltn__blog-item ltn__blog-item-5">
                                <div class="ltn__blog-img">
                                    <a href="blog-details.html"><img src="{{ $item->getThumbnail() }}" alt="Image"></a>
                                </div>
                                <div class="ltn__blog-brief">
                                    <div class="ltn__blog-meta">
                                        <ul>
                                            <li class="ltn__blog-category">
                                                <a
                                                    href="{{ route('news.category', $item->category?->slug ?? '') }}">{{ $item->category?->name ?? '' }}</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <h3 class="ltn__blog-title"><a href="{{ route('news.detail', $item->slug) }}">
                                            {{ $item->title }}
                                        </a></h3>
                                    <div class="ltn__blog-meta">
                                        <ul>
                                            <li>
                                                <a href="#"><i class="far fa-eye"></i>{{ $item->viewers->count() }}
                                                    {{ __('front.views') }}</a>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#"><i
                                                        class="far fa-comments"></i>{{ $item->comments->count() }}
                                                    {{ __('front.comments') }}
                                                </a>
                                            </li>
                                            <li class="ltn__blog-date">
                                                <i class="far fa-calendar-alt"></i>{{ $item->created_at->diffForHumans() }}
                                            </li>
                                        </ul>
                                    </div>
                                    <p>
                                        {{ Str::limit(strip_tags($item->content), 200) }}
                                    </p>
                                    <div class="ltn__blog-meta-btn">
                                        <div class="ltn__blog-meta">
                                            <ul>
                                                <li class="ltn__blog-author">
                                                    <a href="#"><img src="{{ $item->user?->getPhoto() }}"
                                                            alt="#">
                                                        By: {{ $item->user?->name ?? '' }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="ltn__blog-btn">
                                            <a href="{{ route('news.detail', $item->slug) }}"><i
                                                    class="fas fa-arrow-right"></i>
                                                {{ __('front.read_more') }}
                                                </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="ltn__blog-item ltn__blog-item-5">
                                <div class="ltn__blog-brief">
                                    <h3 class="ltn__blog-title">
                                        {{ __('front.no_news_found') }}
                                    </h3>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="ltn__pagination-area text-center">
                                <div class="ltn__pagination">
                                    <ul>
                                        @if ($news->onFirstPage())
                                            <li><a href="#"><i class="fas fa-angle-double-left"></i></a></li>
                                        @else
                                            <li><a href="{{ $news->previousPageUrl() }}"><i
                                                        class="fas fa-angle-double-left"></i></a></li>
                                        @endif
                                        @php
                                            $start = max($news->currentPage() - 2, 1);
                                            $end = min($start + 2, $news->lastPage());
                                        @endphp
                                        @if ($start > 1)
                                            <li class="{{ $news->currentPage() == 1 ? 'active' : '' }}"><a
                                                    href="{{ $news->url(1) }}">1</a></li>
                                            <li><a href="#">...</a></li>
                                        @endif

                                        @foreach ($news->getUrlRange($start, $end) as $page => $url)
                                            <li class="{{ $page == $news->currentPage() ? ' active' : '' }}"><a
                                                    href="{{ $url }}">{{ $page }}</a></li>
                                        @endforeach

                                        @if ($end < $news->lastPage())
                                            <li><a href="#">...</a></li>
                                            <li class="{{ $news->currentPage() == $news->lastPage() ? ' active' : '' }}">
                                                <a href="{{ $news->url($news->lastPage()) }}">{{ $news->lastPage() }}</a>
                                            </li>
                                        @endif

                                        @if ($news->hasMorePages())
                                            <li><a href="{{ $news->nextPageUrl() }}"><i
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
                <div class="col-lg-4">
                    <aside class="sidebar-area blog-sidebar ltn__right-sidebar">

                        <div class="widget ltn__search-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border">
                                {{ __('front.search_news') }}
                            </h4>
                            <form>
                                <input type="text" name="q" placeholder="{{ __('front.search_news_placeholder') }}"
                                    value="{{ request()->q }}">
                                <button type="submit"><i class="fas fa-search"></i></button>
                            </form>
                        </div>
                        <!-- Popular Post Widget -->
                        <div class="widget ltn__popular-post-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border">
                                {{ __('front.popular_news') }}
                            </h4>
                            <ul>
                                @foreach($news_trending as $item_trending)
                                <li>
                                    <div class="popular-post-widget-item clearfix">
                                        <div class="popular-post-widget-img">
                                            <a href="{{ route('news.detail', $item_trending->slug) }}"><img src="{{ $item_trending->getThumbnail() }}" alt="#"></a>
                                        </div>
                                        <div class="popular-post-widget-brief">
                                            <h6><a href="{{ route('news.detail', $item_trending->slug) }}">{{ $item_trending->title }}</a></h6>
                                            <div class="ltn__blog-meta">
                                                <ul>
                                                    <li class="ltn__blog-date">
                                                        <a href="#"><i class="far fa-calendar-alt"></i>{{ $item_trending->created_at->format('d M Y') }}</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endforeach

                            </ul>
                        </div>
                        <!-- Menu Widget (Category) -->
                        <div class="widget ltn__menu-widget ltn__menu-widget-2 ltn__menu-widget-2-color-2">
                            <h4 class="ltn__widget-title ltn__widget-title-border">
                                {{ __('front.category_news') }}
                            </h4>
                            <ul>
                                @foreach ($categories as $category)
                                    <li><a href="{{ route('news.category', $category->slug) }}">{{ $category->name }}
                                            <span>{{ $category->news->count() }}</span></a></li>
                                @endforeach
                            </ul>
                        </div>
                        {{-- <!-- Social Media Widget -->
                        <div class="widget ltn__social-media-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border">Never Miss News</h4>
                            <div class="ltn__social-media-2">
                                <ul>
                                    <li><a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="#" title="Twitter"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="#" title="Linkedin"><i class="fab fa-linkedin"></i></a></li>
                                    <li><a href="#" title="Instagram"><i class="fab fa-instagram"></i></a></li>
                                    <li><a href="#" title="Behance"><i class="fab fa-behance"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <!-- Popular Post Widget (Twitter Post) -->
                        <div class="widget ltn__popular-post-widget ltn__twitter-post-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border">Twitter Feeds</h4>
                            <ul>
                                <li>
                                    <div class="popular-post-widget-item clearfix">
                                        <div class="popular-post-widget-img">
                                            <a href="blog-details.html"><i class="fab fa-twitter"></i></a>
                                        </div>
                                        <div class="popular-post-widget-brief">
                                            <p>Carsafe - #Gutenberg ready
                                                @wordpress
                                                Theme for Car Service, Auto Parts, Car Dealer available on
                                                @website
                                                <a href="https://website.net">https://website.net</a>
                                            </p>
                                            <div class="ltn__blog-meta">
                                                <ul>
                                                    <li class="ltn__blog-date">
                                                        <a href="#"><i class="far fa-calendar-alt"></i>June 22,
                                                            2020</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="popular-post-widget-item clearfix">
                                        <div class="popular-post-widget-img">
                                            <a href="blog-details.html"><i class="fab fa-twitter"></i></a>
                                        </div>
                                        <div class="popular-post-widget-brief">
                                            <p>Carsafe - #Gutenberg ready
                                                @wordpress
                                                Theme for Car Service, Auto Parts, Car Dealer available on
                                                @website
                                                <a href="https://website.net">https://website.net</a>
                                            </p>
                                            <div class="ltn__blog-meta">
                                                <ul>
                                                    <li class="ltn__blog-date">
                                                        <a href="#"><i class="far fa-calendar-alt"></i>June 22,
                                                            2020</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="popular-post-widget-item clearfix">
                                        <div class="popular-post-widget-img">
                                            <a href="blog-details.html"><i class="fab fa-twitter"></i></a>
                                        </div>
                                        <div class="popular-post-widget-brief">
                                            <p>Carsafe - #Gutenberg ready
                                                @wordpress
                                                Theme for Car Service, Auto Parts, Car Dealer available on
                                                @website
                                                <a href="https://website.net">https://website.net</a>
                                            </p>
                                            <div class="ltn__blog-meta">
                                                <ul>
                                                    <li class="ltn__blog-date">
                                                        <a href="#"><i class="far fa-calendar-alt"></i>June 22,
                                                            2020</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <!-- Instagram Widget -->
                        <div class="widget ltn__instagram-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border">Instagram Feeds</h4>
                            <div class="ltn__instafeed ltn__instafeed-grid insta-grid-gutter"></div>
                        </div>
                        <!-- Tagcloud Widget -->
                        <div class="widget ltn__tagcloud-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border">Popular Tags</h4>
                            <ul>
                                <li><a href="#">Popular</a></li>
                                <li><a href="#">desgin</a></li>
                                <li><a href="#">ux</a></li>
                                <li><a href="#">usability</a></li>
                                <li><a href="#">develop</a></li>
                                <li><a href="#">icon</a></li>
                                <li><a href="#">Car</a></li>
                                <li><a href="#">Service</a></li>
                                <li><a href="#">Repairs</a></li>
                                <li><a href="#">Auto Parts</a></li>
                                <li><a href="#">Oil</a></li>
                                <li><a href="#">Dealer</a></li>
                                <li><a href="#">Oil Change</a></li>
                                <li><a href="#">Body Color</a></li>
                            </ul>
                        </div>
                        <!-- Banner Widget -->
                        <div class="widget ltn__banner-widget">
                            <a href="shop.html"><img src="img/banner/banner-4.jpg" alt="Banner Image"></a>
                        </div> --}}

                    </aside>
                </div>
            </div>
        </div>
    </div>
    <!-- BLOG AREA END -->
@endsection
