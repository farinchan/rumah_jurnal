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
                                    data-kt-countup-value="{{ $issue->editors->count() }}">0</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Editor</div>
                        </div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold" data-kt-countup="true"
                                    data-kt-countup-value="{{ $issue->reviewers->count() }}">0</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Reviewer</div>
                        </div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold" data-kt-countup="true"
                                    data-kt-countup-value="{{ $issue->submissions->flatMap->paymentInvoices->where('is_paid', true)->sum('payment_amount') }}"
                                    data-kt-countup-prefix="Rp. " data-kt-countup-decimal-separator=","
                                    data-kt-countup-thousand-separator=".">
                                    0
                                </div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Dana pembayaran Accepted</div>
                        </div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold" data-kt-countup="true"
                                    data-kt-countup-value="{{ $issue->submissions->flatMap->paymentInvoices->where('is_paid', false)->sum('payment_amount') }}"
                                    data-kt-countup-prefix="Rp. " data-kt-countup-decimal-separator="."
                                    data-kt-countup-thousand-separator=".">
                                    0
                                </div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Dana pembayaran Pending</div>
                        </div>
                    </div>
                </div>
                {{-- Statistik Status Pembayaran --}}
                <div class="separator my-4"></div>
                <div class="d-flex flex-wrap justify-content-start">
                    <div class="d-flex flex-wrap">
                        @php
                            $totalSubmissions = $issue->submissions->count();
                            $freeChargeCount = $issue->submissions->where('free_charge', true)->count();

                            // Submissions dengan invoice
                            $submissionsWithInvoice = $issue->submissions->filter(function($s) {
                                return !$s->free_charge && $s->paymentInvoices->count() > 0;
                            });

                            // Submissions tanpa invoice (belum ada tagihan)
                            $noInvoiceCount = $issue->submissions->filter(function($s) {
                                return !$s->free_charge && $s->paymentInvoices->count() == 0;
                            })->count();

                            // Hitung berdasarkan total persentase yang sudah dibayar
                            $lunasCount = $submissionsWithInvoice->filter(function($s) {
                                return $s->paymentInvoices->where('is_paid', true)->sum('payment_percent') >= 100;
                            })->count();

                            $belumBayarCount = $submissionsWithInvoice->filter(function($s) {
                                return $s->paymentInvoices->where('is_paid', true)->sum('payment_percent') == 0;
                            })->count();

                            $sebagianCount = $submissionsWithInvoice->filter(function($s) {
                                $paid = $s->paymentInvoices->where('is_paid', true)->sum('payment_percent');
                                return $paid > 0 && $paid < 100;
                            })->count();

                            // Detail invoice per tipe
                            $allInvoices = $issue->submissions->flatMap->paymentInvoices;

                            $invoice60 = $allInvoices->where('payment_percent', 60);
                            $invoice60Lunas = $invoice60->where('is_paid', true)->count();
                            $invoice60Pending = $invoice60->where('is_paid', false)->count();

                            $invoice40 = $allInvoices->where('payment_percent', 40);
                            $invoice40Lunas = $invoice40->where('is_paid', true)->count();
                            $invoice40Pending = $invoice40->where('is_paid', false)->count();

                            $invoice100 = $allInvoices->where('payment_percent', 100);
                            $invoice100Lunas = $invoice100->where('is_paid', true)->count();
                            $invoice100Pending = $invoice100->where('is_paid', false)->count();
                        @endphp

                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold text-success" data-kt-countup="true"
                                    data-kt-countup-value="{{ $lunasCount }}">0</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Artikel Lunas</div>
                        </div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold text-warning" data-kt-countup="true"
                                    data-kt-countup-value="{{ $sebagianCount }}">0</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Bayar Sebagian</div>
                        </div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold text-danger" data-kt-countup="true"
                                    data-kt-countup-value="{{ $belumBayarCount }}">0</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Belum Bayar</div>
                        </div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold text-info" data-kt-countup="true"
                                    data-kt-countup-value="{{ $freeChargeCount }}">0</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Gratis Biaya</div>
                        </div>
                        <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center">
                                <div class="fs-4 fw-bold text-muted" data-kt-countup="true"
                                    data-kt-countup-value="{{ $noInvoiceCount }}">0</div>
                            </div>
                            <div class="fw-semibold fs-6 text-gray-500">Belum Ada Tagihan</div>
                        </div>
                    </div>
                </div>
                {{-- Detail Invoice per Tipe --}}
                <div class="d-flex flex-wrap justify-content-start mt-2">
                    <div class="d-flex flex-wrap">
                        <div class="border border-gray-300 border-dashed rounded min-w-150px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fs-6 fw-semibold text-gray-700">Invoice 60%</span>
                            </div>
                            <div class="d-flex align-items-center mt-2">
                                <span class="badge badge-light-success me-2">{{ $invoice60Lunas }} Lunas</span>
                                <span class="badge badge-light-warning">{{ $invoice60Pending }} Pending</span>
                            </div>
                        </div>
                        <div class="border border-gray-300 border-dashed rounded min-w-150px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fs-6 fw-semibold text-gray-700">Invoice 40%</span>
                            </div>
                            <div class="d-flex align-items-center mt-2">
                                <span class="badge badge-light-success me-2">{{ $invoice40Lunas }} Lunas</span>
                                <span class="badge badge-light-warning">{{ $invoice40Pending }} Pending</span>
                            </div>
                        </div>
                        <div class="border border-gray-300 border-dashed rounded min-w-150px py-3 px-4 me-6 mb-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fs-6 fw-semibold text-gray-700">Invoice 100%</span>
                            </div>
                            <div class="d-flex align-items-center mt-2">
                                <span class="badge badge-light-success me-2">{{ $invoice100Lunas }} Lunas</span>
                                <span class="badge badge-light-warning">{{ $invoice100Pending }} Pending</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="separator"></div>
        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
            {{-- <li class="nav-item">
                <a class="nav-link text-active-primary py-5 me-6 @if (request()->routeIs('back.journal.dashboard.index', [$journal->url_path, $issue->id])) active @endif"
                    href="{{ route('back.journal.dashboard.index', [$journal->url_path, $issue->id]) }}">Dashboard</a>
            </li> --}}
            <li class="nav-item">
                <a class="nav-link text-active-primary py-5 me-6 @if (request()->routeIs('back.journal.article.index', [$journal->url_path, $issue->id])) active @endif"
                    href="{{ route('back.journal.article.index', [$journal->url_path, $issue->id]) }}">Artikel</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-active-primary py-5 me-6 @if (request()->routeIs('back.journal.editor.index', [$journal->url_path, $issue->id])) active @endif"
                    href="{{ route('back.journal.editor.index', [$journal->url_path, $issue->id]) }}">Editor</a>
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
