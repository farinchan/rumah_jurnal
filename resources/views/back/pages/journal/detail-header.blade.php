<div class="card mb-9">
    <div class="card-body pt-9 pb-0">
        <div class="d-flex flex-wrap flex-sm-nowrap mb-6">

            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center mb-1">
                            <a href="#" class="text-gray-800 text-hover-primary fs-2 fw-bold me-3">
                                Vol. {{ $issue->volume }} No. {{ $issue->number }} ({{ $issue->year }}):
                                {{ $issue->title }}
                            </a>
                            @if ($issue->status == 'published')
                                <div class="badge badge-light-success">Published</div>
                            @else
                                <div class="badge badge-light-danger">unpublished</div>
                            @endif
                        </div>
                        <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-500">
                            {{ $journal->title }}
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap justify-content-start">
                    <div class="d-flex flex-wrap">
                        {{-- <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold">
                                    NOT SET
                                </div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">NOT SET</div>
                        </div> --}}
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold" >
                                    @money($journal->author_fee)
                                </div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Biaya Publikasi</div>
                        </div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold" data-kt-countup="true"
                                    data-kt-countup-value="{{ $issue->submissions->count() }}">
                                    0
                                </div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Article</div>
                        </div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold" data-kt-countup="true"
                                    data-kt-countup-value="{{ $issue->reviewers->count() }}">0</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Reviewer</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="separator"></div>
        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
            <li class="nav-item">
                <a class="nav-link text-active-primary py-5 me-6 @if (request()->routeIs('back.journal.dashboard.index', [$journal->url_path, $issue->id])) active @endif"
                    href="{{ route('back.journal.dashboard.index', [$journal->url_path, $issue->id]) }}">Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-active-primary py-5 me-6 @if (request()->routeIs('back.journal.article.index', [$journal->url_path, $issue->id])) active @endif"
                    href="{{ route('back.journal.article.index', [$journal->url_path, $issue->id]) }}">Artikel</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-active-primary py-5 me-6 @if (request()->routeIs('back.journal.reviewer.index', [$journal->url_path, $issue->id])) active @endif"
                    href="{{ route('back.journal.reviewer.index', [$journal->url_path, $issue->id]) }}">Reviewer</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-active-primary py-5 me-6 @if (request()->routeIs('back.journal.setting.index', [$journal->url_path, $issue->id])) active @endif"
                    href="{{ route('back.journal.setting.index', [$journal->url_path, $issue->id]) }}">Pengaturan</a>
            </li>
        </ul>
    </div>
</div>
