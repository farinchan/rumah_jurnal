@extends('front.app')

@section('content')
    @include('front.partials.breadcrumb')
    <!-- Gallery area start -->
    <div class="ltn__gallery-area mb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__gallery-menu">
                        <div class="ltn__gallery-filter-menu portfolio-filter text-uppercase mb-50">
                            <button data-filter="*" class="active">all</button>
                            <button data-filter=".filter_category_1">Scopus</button>
                            <button data-filter=".filter_category_2">Sinta 1-2</button>
                            <button data-filter=".filter_category_3">Sinta 3-4</button>
                            <button data-filter=".filter_category_4">Sinta 5-6</button>
                            <button data-filter=".filter_category_5">Non-Sinta</button>
                        </div>
                    </div>
                </div>
            </div>

            <!--Portfolio Wrapper Start-->
            <div class="ltn__gallery-active row ltn__gallery-style-1">
                <div class="ltn__gallery-sizer col-1"></div>

                @foreach ($journals as $journal)
                    <!-- gallery-item -->
                    <div class="ltn__gallery-item
                    @if (in_array('Scopus Q1', $journal->indexing) || in_array('Scopus Q2', $journal->indexing) || in_array('Scopus Q3', $journal->indexing) || in_array('Scopus Q4', $journal->indexing) || in_array('Scopus', $journal->indexing))
                        filter_category_1
                    @endif
                    @if (in_array('Sinta 1', $journal->indexing) || in_array('Sinta 2', $journal->indexing))
                        filter_category_2
                    @endif
                    @if (in_array('Sinta 3', $journal->indexing) || in_array('Sinta 4', $journal->indexing))
                        filter_category_3
                    @endif
                    @if (in_array('Sinta 5', $journal->indexing) || in_array('Sinta 6', $journal->indexing))
                        filter_category_4
                    @endif
                    @if (!in_array('Sinta 1', $journal->indexing) && !in_array('Sinta 2', $journal->indexing) && !in_array('Sinta 3', $journal->indexing) && !in_array('Sinta 4', $journal->indexing) && !in_array('Sinta 5', $journal->indexing) && !in_array('Sinta 6', $journal->indexing) && !in_array('Scopus Q1', $journal->indexing) && !in_array('Scopus Q2', $journal->indexing) && !in_array('Scopus Q3', $journal->indexing) && !in_array('Scopus Q4', $journal->indexing) && !in_array('Scopus', $journal->indexing))
                        filter_category_5
                    @endif
                    col-md-4 col-sm-6 col-12">
                        <div class="ltn__gallery-item-inner">
                            <div class="ltn__gallery-item-img">
                                <a href="{{ route('journal.detail', $journal->url_path) }}">
                                    <img src="{{ $journal->getJournalThumbnail() }}" alt="Image">
                                    <span class="ltn__gallery-action-icon">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </a>
                            </div>
                            <div class="ltn__gallery-item-info">
                                <h4><a href="{{ route('journal.detail', $journal->url_path) }}">
                                        {{ $journal->title }}
                                    </a></h4>
                                <p>Acredited:
                                    @foreach ($journal->indexing ?? [] as $akreditasi_item)
                                        <strong>{{ $akreditasi_item }},</strong>
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- gallery-item -->
                @endforeach

            </div>

            {{-- <div id="ltn__inline_description_1" style="display: none;">
                <h4 class="first">This content comes from a hidden element on that page</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam quis mi eu elit tempor facilisis id et neque. Nulla sit amet sem sapien. Vestibulum imperdiet porta ante ac ornare. Nulla et lorem eu nibh adipiscing ultricies nec at lacus. Cras laoreet ultricies sem, at blandit mi eleifend aliquam. Nunc enim ipsum, vehicula non pretium varius, cursus ac tortor.</p>
                <p>Vivamus fringilla congue laoreet. Quisque ultrices sodales orci, quis rhoncus justo auctor in. Phasellus dui eros, bibendum eu feugiat ornare, faucibus eu mi. Nunc aliquet tempus sem, id aliquam diam varius ac. Maecenas nisl nunc, molestie vitae eleifend vel.</p>
            </div> --}}

            {{-- <div class="btn-wrapper text-center">
                <a href="#" class="btn btn-transparent btn-effect-1 btn-border">LOAD MORE +</a>
            </div> --}}

            <!-- pagination start -->
            <!--
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ltn__pagination text-center margin-top-50">
                            <ul>
                                <li class="arrow-icon"><a href="#"> &leftarrow; </a></li>
                                <li class="active"><a href="blog.html">1</a></li>
                                <li><a href="blog-2.html">2</a></li>
                                <li><a href="blog-2.html">3</a></li>
                                <li><a href="blog-2.html">...</a></li>
                                <li><a href="blog-2.html">10</a></li>
                                <li class="arrow-icon"><a href="#"> &rightarrow; </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                -->
            <!-- pagination end -->

        </div>
    </div>
    <!-- Gallery area end -->
@endsection
