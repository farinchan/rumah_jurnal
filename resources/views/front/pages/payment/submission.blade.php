@extends('front.app')

@section('content')
    @include('front.partials.breadcrumb')


    <!-- SHOP DETAILS AREA START -->
    <div class="ltn__shop-details-area pb-85">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="ltn__shop-details-inner mb-60">
                        <div class="row">
                            <div class="col-md-4">
                                <img src="{{ $journal->getJournalThumbnail() }}" alt="Image" class="img-fluid">
                            </div>
                            <div class="col-md-8">
                                <div class="modal-product-info shop-details-info pl-0">

                                    <h3>
                                        {{ $submission->fullTitle }}
                                    </h3>

                                    <ul>
                                        @foreach ($submission->authors as $author)
                                            <li style="margin-top: 0px;">
                                                <span style="font-weight: 900">{{ $author['name'] }}</span>,
                                                {{ $author['affiliation'] }}
                                            </li>
                                        @endforeach

                                    </ul>

                                    <hr>
                                    <div class="ltn__social-media">

                                        <ul>
                                            <li> {{ __('front.journal') }}:</li>
                                            <a href="{{ route('journal.detail', $submission->issue->journal->url_path) }}">
                                                {{ $submission->issue->journal->title }}
                                            </a>
                                        </ul>
                                    </div>

                                    <hr>
                                    <div class="ltn__social-media">
                                        <ul>
                                            <li>{{ __('front.payment_status') }}:</li>
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

                                        </ul>
                                        <ul>
                                            <li>
                                                {{ __('front.submission_status') }}:
                                            </li>
                                            <td class="text-start">
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
                                            </td>

                                        </ul>
                                        <ul>
                                            <li>
                                                {{ __('front.date_published') }}:
                                            </li>
                                            <span>
                                                {{ $submission->datePublished ?? '-' }}
                                            </span>
                                        </ul>
                                    </div>
                                    <hr>
                                    <div class="ltn__product-details-menu-2">

                                        {{ __('front.pay_now_desc') }}

                                        <ul>

                                            <li>
                                                <a href="#" class="theme-btn-1 btn btn-effect-1" >
                                                    <i class="fas fa-file-invoice"></i>
                                                    <span>{{ __('front.view_invoice') }}</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{ route('payment.pay', [$journal->url_path, $submission->submission_id]) }}" class="theme-btn-1 btn btn-effect-1"
                                                    title="{{ __('front.pay_now_btn') }}" >
                                                    <i class="fas fa-credit-card"></i>
                                                    <span>{{ __('front.pay_now_btn') }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Shop Tab Start -->
                    <div class="ltn__shop-details-tab-inner ltn__shop-details-tab-inner-2">
                        <div class="ltn__shop-details-tab-menu">
                            <div class="nav">
                                <a class="active show" data-toggle="tab"
                                    href="#liton_tab_details_1_1">{{ __('front.abstract') }}</a>
                                <a data-toggle="tab" href="#liton_tab_details_1_2"
                                    class="">{{ __('front.references') }}</a>
                                <a data-toggle="tab" href="#liton_tab_details_1_3"
                                    class="">{{ __('front.payment') }}</a>
                            </div>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="liton_tab_details_1_1">
                                <div class="ltn__shop-details-tab-content-inner">
                                    <h4 class="title-2">
                                        {{ __('front.keywords') }} : {{ $submission->getKeywordsAttribute() }}
                                    </h4>
                                    <p>
                                    <h4>
                                        {{ __('front.abstract') }} :
                                    </h4>
                                    {!! $submission->getAbstractAttribute() !!}
                                    </p>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="liton_tab_details_1_2">
                                <div class="ltn__shop-details-tab-content-inner">

                                    <ul>
                                        @foreach ($submission->citations as $citation)
                                            <li>
                                                {{ $citation }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="liton_tab_details_1_3">
                                <div class="ltn__shop-details-tab-content-inner">

                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col">Waktu</th>
                                                <th scope="col">Pengguna</th>
                                                <th scope="col">Pembayaran</th>
                                                <th scope="col">Jumlah</th>
                                                <th scope="col">Status</th>
                                                <th scope="col">Keterangan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($submission->payments as $payment)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($payment->created_at)->translatedFormat('l, d F Y H:i') }}</td>
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
                                                    <td>@money($payment->payment_amount)</td>
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
                    </div>
                    <!-- Shop Tab End -->
                </div>

            </div>
        </div>
    </div>
    <!-- SHOP DETAILS AREA END -->
@endsection
