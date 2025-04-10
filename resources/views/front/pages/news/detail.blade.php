@extends('front.app')

@section('content')
    @include('front.partials.breadcrumb')
    <!-- BLOG AREA START -->
    <div class="ltn__blog-area mb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="ltn__blog-details-wrap">
                        <div class="ltn__page-details-inner ltn__blog-details-inner">
                            <div class="ltn__blog-meta">
                                <ul>
                                    <li class="ltn__blog-category">
                                        <a href="#">{{ $news->category->name }}</a>
                                    </li>
                                </ul>
                            </div>
                            <h2 class="ltn__blog-title">
                                {{ $news->title }}
                            </h2>
                            <div class="ltn__blog-meta">
                                <ul>
                                    <li class="ltn__blog-author">
                                        <a href="#"><img src="{{ $news->user?->getPhoto() ?? '' }}" alt="#">By:
                                            {{ $news->user?->name ?? '' }}</a>
                                    </li>
                                    <li class="ltn__blog-date">
                                        <i class="far fa-calendar-alt"></i>{{ $news->created_at?->format('d M Y') ?? '' }}
                                    </li>
                                    <li>
                                        <a href="#"><i class="far fa-comments"></i>{{ $news->comments?->count() ?? 0 }}
                                            {{ __('front.comments')}}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <p>
                                {!! $news->content !!}
                            </p>

                        </div>
                        <!-- blog-tags-social-media -->
                        <div class="ltn__blog-tags-social-media mt-80 row">
                            <div class="ltn__tagcloud-widget col-lg-8">
                                <h4>
                                    {{ __('front.tags')}}
                                </h4>
                                <ul>
                                    @php
                                        $tags = explode(',', $news->meta_keywords ?? '');
                                    @endphp
                                    @foreach ($tags ?? [] as $tag)
                                        <li><a href="#">{{ $tag }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="ltn__social-media text-right col-lg-4">
                                <h4>
                                    {{ __('front.social_share')}}
                                </h4>
                                <ul>
                                    <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::fullUrl()) }}" title="Facebook" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                    <li><a href="https://twitter.com/intent/tweet?url={{ urlencode(Request::fullUrl()) }}" title="Twitter" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                    <li><a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(Request::fullUrl()) }}" title="Linkedin" target="_blank"><i class="fab fa-linkedin"></i></a></li>
                                    <li><a href="https://www.youtube.com" title="Youtube" target="_blank"><i class="fab fa-youtube"></i></a></li>
                                </ul>
                            </div>
                        </div>
                        <hr>
                        <!-- prev-next-btn -->
                        <div class="ltn__prev-next-btn row mb-50">
                            <div class="blog-prev col-lg-6">
                                <h6>{{ __('front.prev_news')}}</h6>
                                @if($prev_news)
                                    <h3 class="ltn__blog-title"><a href="{{ route('news.detail', $prev_news->slug) }}">{{ $prev_news->title }}</a></h3>
                                @else
                                    <h3 class="ltn__blog-title"><a href="#">No Previous Post</a></h3>
                                @endif
                            </div>
                            <div class="blog-prev blog-next text-right col-lg-6">
                                <h6>{{ __('front.next_news')}}</h6>
                                @if($next_news)
                                    <h3 class="ltn__blog-title"><a href="{{ route('news.detail', $next_news->slug) }}">{{ $next_news->title }}</a></h3>
                                @else
                                    <h3 class="ltn__blog-title"><a href="#">No Next Post</a></h3>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <!-- comment-area -->
                        <div class="ltn__comment-area mb-50">
                            <div class="ltn-author-introducing clearfix">
                                <div class="author-img">
                                    <img src="{{ $news->user?->getPhoto() ?? '' }}" alt="#">
                                </div>
                                <div class="author-info">
                                    <h6>{{ __('front.writen_by') }}</h6>
                                    <h1>{{ $news->user?->name ?? '' }}</h1>
                                    <p>
                                        {{ $news->user?->bio ?? '' }}
                                    </p>
                                </div>
                            </div>
                            <h4 class="title-2">{{ $news->comments?->count() ?? 0 }} {{ __('front.comments')}}</h4>
                            <div class="ltn__comment-inner">
                                <ul>
                                    {{-- <li>
                                        <div class="ltn__comment-item clearfix">
                                            <div class="ltn__commenter-img">
                                                <img src="img/testimonial/1.jpg" alt="Image">
                                            </div>
                                            <div class="ltn__commenter-comment">
                                                <h6><a href="#">Adam Smit</a></h6>
                                                <span class="comment-date">20th May 2020</span>
                                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Doloribus,
                                                    omnis fugit corporis iste magnam ratione.</p>
                                                <a href="#" class="ltn__comment-reply-btn"><i
                                                        class="icon-reply-1"></i>Reply</a>
                                            </div>
                                        </div>
                                        <ul>
                                            <li>
                                                <div class="ltn__comment-item clearfix">
                                                    <div class="ltn__commenter-img">
                                                        <img src="img/testimonial/3.jpg" alt="Image">
                                                    </div>
                                                    <div class="ltn__commenter-comment">
                                                        <h6><a href="#">Adam Smit</a></h6>
                                                        <span class="comment-date">21th May 2020</span>
                                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                                                            Doloribus, omnis fugit corporis iste magnam ratione.</p>
                                                        <a href="#" class="ltn__comment-reply-btn"><i
                                                                class="icon-reply-1"></i>Reply</a>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </li> --}}
                                    @foreach ($news->comments as $comment)
                                        <li>
                                            <div class="ltn__comment-item clearfix">
                                                <div class="ltn__commenter-img">
                                                    <img src="https://api.dicebear.com/9.x/bottts/png?seed={{ $comment->name }}"
                                                        alt="Image">
                                                </div>
                                                <div class="ltn__commenter-comment">
                                                    <h6><a href="#">{{ $comment->name }}</a></h6>
                                                    <span class="comment-date">{{ $comment->created_at->format('d M Y') }}</span>
                                                    <p>{{ $comment->comment }}</p>

                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <hr>
                        <!-- comment-reply -->
                        <div class="ltn__comment-reply-area ltn__form-box mb-60---">
                            <h4 class="title-2">
                                {{ __('front.post_comment')}}
                            </h4>
                            <form action="{{ route('news.comment') }}" method="POST">
                                @csrf
                                <input type="hidden" name="news_id" value="{{ $news->id }}">
                                <div class="input-item input-item-textarea ltn__custom-icon">
                                    <textarea placeholder="{{ __('front.comment_placeholder') }}" name="comment" value="{{ old('comment') }}"></textarea>
                                    @error('comment')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="input-item input-item-name ltn__custom-icon">
                                    <input type="text" placeholder="{{ __('front.enter_your_name') }}" name="name" value="{{ old('name') }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="input-item input-item-email ltn__custom-icon">
                                    <input type="email" placeholder="{{ __('front.enter_your_email') }}" name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <label class="mb-0 input-info-save"><input type="checkbox" name="agree">
                                    {{ __('front.message_checkbox') }}
                                </label>
                                <div class="btn-wrapper">
                                    <button class="btn theme-btn-1 btn-effect-1 text-uppercase" type="submit"><i
                                            class="far fa-comments"></i>
                                        {{ __('front.post_comment')}}
                                        </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <aside class="sidebar-area blog-sidebar ltn__right-sidebar">

                        <div class="widget ltn__search-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border">
                                {{ __('front.search_news') }}
                            </h4>
                            <form action="{{ route('news.index') }}" method="GET">
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
