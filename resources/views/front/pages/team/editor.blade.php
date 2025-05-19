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
    <div class="ltn__contact-message-area mb-30 ">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__form-box contact-form-box box-shadow white-bg">
                        <form id="contact-form" method="GET">
                            <div class="row">
                                <div class="col-md-12">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4 class="title-2">Journal Editor</h4>
                                        </div>
                                        <form action="">


                                            <div class="col-md-12">
                                                <div class="input-item input-item-name ltn__custom-icon">
                                                    <select name="journal" class="nice-select">
                                                        @foreach ($journals as $journal)
                                                            <option
                                                                {{ $journal->url_path == request('journal') ? 'selected' : '' }}
                                                                value="{{ $journal->url_path }}">{{ $journal->title }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="btn-wrapper mt-0">
                                                    <button class="btn theme-btn-1 btn-effect-1 text-uppercase"
                                                        type="submit">
                                                        {{ __('front.search_article_btn') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container mb-100 ">
        <div class="ltn__faq-inner ltn__faq-inner-2">
            <div id="accordion_2">
                @forelse ($issues as $issue)
                    <div class="card">
                        <h6 class="collapsed ltn__card-title" data-toggle="collapse"
                            data-target="#team-{{ $issue->id }}" aria-expanded="{{ $loop->last ? 'true' : 'false' }}">
                            Vol. {{ $issue->volume }} No. {{ $issue->number }} ({{ $issue->year }}):
                            {{ $issue->title }}
                        </h6>
                        <div id="team-{{ $issue->id }}" class="collapse {{ $loop->last ? 'show' : '' }}"
                            data-parent="#accordion_2">
                            <div class="card-body">
                                <ul>
                                    @forelse ($issue->editors as $editor)
                                        <li>
                                            <p>
                                            <h5 style="margin-bottom: 0px;">
                                                {{ $editor->name }}
                                            </h5>
                                            {{ $editor->affiliation }}
                                            </p>
                                        </li>
                                        @empty
                                        <li>
                                            <p>
                                            <h5 style="margin-bottom: 0px;">
                                                {{ __('front.no_editor') }}
                                            </h5>
                                            </p>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="card">
                        <h3 class="card-title" style="text-align: center; margin: 10px;">
                           Editor Not Found
                        </h3>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
