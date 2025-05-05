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
    <div class="ltn__contact-message-area mb-50 ">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__form-box contact-form-box box-shadow white-bg">
                        <form id="contact-form" method="GET">
                            <div class="row">
                                <div class="col-md-3">
                                    <img src="{{ asset('ext_images/research.png') }}" alt="Image" class="img-fluid">
                                </div>
                                <div class="col-md-9">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <h4 class="title-2">{{ __('front.search_article') }}</h4>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="input-item input-item-name ltn__custom-icon">
                                                <select name="journal_id" class="nice-select">
                                                    <option value="">{{ __('front.all_journal') }}</option>
                                                    @foreach ($journals as $journal)
                                                        <option value="{{ $journal->id }}">{{ $journal->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="input-item input-item-name ltn__custom-icon">
                                                <input type="text" name="q" value="{{ request('q') }}"
                                                    placeholder="{{ __('front.search_article_placeholder') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="btn-wrapper mt-0">
                                                <button class="btn theme-btn-1 btn-effect-1 text-uppercase" type="submit">
                                                    {{ __('front.search_article_btn') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="ltn__product-tab-content-inner ltn__product-list-view">
            <div class="row">
                @foreach ($submissions as $submission)
                    <div class="col-lg-12">
                        <div class="ltn__product-item ltn__product-item-3" style="margin-bottom: 20px;">
                            <div class="product-info">
                                <h2 class="product-title"><a
                                        href="{{ route('payment.submission', [$submission?->issue?->journal?->url_path, $submission?->submission_id]) }}">
                                        Submmission ID: {{ $submission->submission_id }}</a>
                                </h2>
                                {{-- <h2 class="product-title"><a
                                        href="{{ route('payment.submission', [$submission?->issue?->journal?->url_path, $submission?->submission_id]) }}">{{ $submission->fullTitle }}</a>
                                </h2>
                                <div class="product-brief">
                                    <ul>
                                        @foreach ($submission->authors as $author)
                                            <li style="margin-top: 0px;">
                                                <span style="font-weight: 900">{{ $author['name'] }}</span>,
                                                {{ $author['affiliation'] }}
                                            </li>
                                        @endforeach

                                    </ul>
                                </div> --}}

                                <div>
                                    <p>
                                        <strong>{{ __('front.journal') }}:</strong>
                                        <a href="{{ route('journal.detail', $submission->issue->journal->url_path) }}">
                                            {{ $submission->issue->journal->title }}
                                        </a>
                                        <br>
                                        {{-- <strong>Issue:</strong>
                                        <a href="{{ route('journal.detail', $submission->issue->journal->url_path) }}">
                                            Vol. {{ $submission->issue->volume }} No. {{ $submission->issue->number }}
                                            ({{ $submission->issue->year }}): {{ $submission->issue->title }}
                                        </a> --}}
                                    </p>
                                </div>
                                @if ($submission?->issue?->journal?->author_fee > 0)
                                    <div>
                                        <span>{{ __('front.payment_status') }}: </span>
                                        @if ($submission->payment_status == 'paid')
                                            <span
                                                class="badge badge-success text-white">{{ $submission->payment_status }}</span>
                                        @elseif ($submission->payment_status == 'pending')
                                            <span
                                                class="badge badge-warning text-white">{{ $submission->payment_status }}</span>
                                        @elseif ($submission->payment_status == 'cencelled')
                                            <span
                                                class="badge badge-danger text-white">{{ $submission->payment_status }}</span>
                                        @elseif ($submission->payment_status == 'refunded')
                                            <span
                                                class="badge badge-danger text-white">{{ $submission->payment_status }}</span>
                                        @endif
                                    </div>
                                @endif
                                <div>
                                    <span>{{ __('front.submission_status') }}: </span>
                                    @if ($submission->status == 1)
                                        <span
                                            class="badge badge-warning fs-7 text-white">{{ $submission->status_label }}</span>
                                    @elseif ($submission->status == 3)
                                        <span
                                            class="badge badge-success fs-7 text-white">{{ $submission->status_label }}</span>
                                    @elseif ($submission->status == 4)
                                        <span
                                            class="badge badge-danger fs-7 text-white">{{ $submission->status_label }}</span>
                                    @else
                                        <span
                                            class="badge badge-secondary fs-7 text-white">{{ $submission->status_label }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span>{{ __('front.date_published') }}: </span>
                                    <span>
                                        {{ $submission->datePublished ?? '-' }}
                                    </span>
                                </div>
                                <br>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="ltn__faq-inner ltn__faq-inner-2">
                                            <div id="accordion_2">
                                                @foreach ($submission->paymentInvoices as $invoice)
                                                    <div class="card">
                                                        <h6 class="collapsed ltn__card-title" data-toggle="collapse"
                                                            data-target="#item-{{ $invoice->id }}" aria-expanded="false">
                                                            INVOICE {{ $invoice->invoice_number }}/JRNL/UINSMDD/{{ $invoice->created_at->format('Y') }} -
                                                            @if ($invoice->is_paid)
                                                                <span
                                                                    class="badge badge-success text-white">{{ __('front.paid') }}</span>
                                                            @else
                                                                <span
                                                                    class="badge badge-danger text-white">{{ __('front.unpaid') }}</span>
                                                            @endif
                                                        </h6>
                                                        <div id="item-{{ $invoice->id }}"
                                                            class="collapse {{ $invoice->is_paid ? '' : 'show' }}"
                                                            data-parent="#accordion_2">
                                                            <div class="card-body">
                                                                <p>

                                                                    <table>
                                                                        <tr>
                                                                            <th>{{ __('front.invoice_number') }}</th>
                                                                            <td> : </td>
                                                                            <td>{{ $invoice->invoice_number }}/JRNL/UINSMDD/{{ $invoice->created_at->format('Y') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ __('front.due_date') }}</th>
                                                                            <td> : </td>
                                                                            <td>{{ \Carbon\Carbon::parse($invoice->payment_due_date)->translatedFormat('l, d F Y') }}</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ __('front.percentage') }}</th>
                                                                            <td> : </td>
                                                                            <td>{{ $invoice->payment_percent }}%</td>
                                                                        </tr>
                                                                        <tr>
                                                                            <th>{{ __('front.amount_to_be_paid') }}</th>
                                                                            <td> : </td>
                                                                            <td>@money($invoice->payment_amount)</td>
                                                                        </tr>
                                                                    </table>

                                                                </p>
                                                                @if (!$invoice->is_paid)
                                                                <button class="btn theme-btn-1 btn-effect-1 text-uppercase"
                                                                    onclick="window.location.href='{{ route('payment.pay', [$submission?->issue?->journal?->url_path, $submission?->submission_id]) }}'">
                                                                    {{ __('front.pay_now_btn') }}
                                                                </button>
                                                                @endif
                                                                <table class="table table-bordered mt-3">
                                                                    <thead>
                                                                        <tr>
                                                                            <th class="text-center" scope="col" colspan="6">
                                                                               History
                                                                                    Pembayaran
                                                                            </th>
                                                                        </tr>
                                                                        <tr>
                                                                            <th scope="col">Waktu</th>
                                                                            <th scope="col">Pengguna</th>
                                                                            <th scope="col">Pembayaran</th>
                                                                            <th scope="col">Status</th>
                                                                            <th scope="col">Keterangan</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @forelse ($invoice->payments as $payment)
                                                                            <tr>
                                                                                <td>{{ \Carbon\Carbon::parse($payment->created_at)->translatedFormat('l, d F Y H:i') }}
                                                                                </td>
                                                                                <td>
                                                                                    <strong>{{ $payment->name }}</strong>
                                                                                    <br>
                                                                                    {{ Str::mask($payment->phone, '*', 4, strlen($payment->phone) - 7) }}
                                                                                    <br>
                                                                                    {{ Str::mask($payment->email, '*', 3, strpos($payment->email, '@') - 3) }}

                                                                                </td>
                                                                                <td>
                                                                                    {{ $payment->payment_method }}
                                                                                    <br>
                                                                                    {{ $payment->payment_account_name }}
                                                                                    <br>
                                                                                    {{ Str::mask($payment->payment_account_number, '*', 4, strlen($payment->payment_account_number) - 8) }}
                                                                                </td>
                                                                                <td>
                                                                                    @if ($payment->payment_status == 'pending')
                                                                                        <span
                                                                                            class="badge badge-warning text-white">{{ $payment->payment_status }}</span>
                                                                                    @elseif ($payment->payment_status == 'accepted')
                                                                                        <span
                                                                                            class="badge badge-success text-white">{{ $payment->payment_status }}</span>
                                                                                    @elseif ($payment->payment_status == 'rejected')
                                                                                        <span
                                                                                            class="badge badge-danger text-white">{{ $payment->payment_status }}</span>
                                                                                    @else
                                                                                        <span
                                                                                            class="badge badge-secondary text-white">{{ $payment->payment_status }}</span>
                                                                                    @endif
                                                                                </td>
                                                                                <td>{{ $payment->payment_note }}</td>
                                                                            </tr>

                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="6" class="text-center">
                                                                                    <strong>{{ __('front.no_payment_found') }}</strong>
                                                                                </td>
                                                                            </tr>
                                                                        @endforelse

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="product-hover-action">
                                    <ul>
                                        <li>
                                            <a
                                                href="{{ route('payment.submission', [$submission?->issue?->journal?->url_path, $submission?->submission_id]) }}"title="Detail">
                                                <i class="far fa-eye"></i>
                                            </a>
                                        </li>
                                        @if ($submission?->issue?->journal?->author_fee > 0)
                                            <li>
                                                <a href="{{ route('payment.pay', [$submission?->issue?->journal?->url_path, $submission?->submission_id]) }}"
                                                    title="Pay Now">
                                                    <i class="fas fa-credit-card"></i>
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a href="{{ $submission->urlPublished }}" title="View Publish"
                                                data-toggle="modal" data-target="#liton_wishlist_modal">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
