@extends('front.app')
@section('seo')
    <title>{{ $meta['description'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}">
    <meta name="keywords" content="{{ $meta['keywords'] }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] }}">
    <meta property="og:description" content="{{ $meta['description'] }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('payment.pay', [$journal->url_path, $submission->submission_id]) }}">
    <link rel="canonical" href="{{ route('payment.pay', [$journal->url_path, $submission->submission_id]) }}">
    <meta property="og:image" content="{{ $meta['favicon'] }}">
@endsection

@section('content')
    @include('front.partials.breadcrumb')
    <!-- APPOINTMENT AREA START -->
    <div class="ltn__appointment-area pt-115--- pb-120">
        <div class="container" id="tabbar">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('payment.pay.store', [$journal?->url_path, $submission?->submission_id]) }}"
                        enctype="multipart/form-data" method="POST">
                        <div class="ltn__tab-menu ltn__tab-menu-3 ltn__tab-menu-top-right-- text-uppercase--- text-center">
                            <div class="nav">
                                <a class="active show" data-toggle="tab" href="#liton_tab_3_1" id="tab_step_1">1.
                                    {{ __('front.information') }}</a>
                                <a data-toggle="tab" href="#liton_tab_3_2" class="" id="tab_step_2">2.
                                    {{ __('front.payment') }}</a>
                                <a data-toggle="tab" href="#liton_tab_3_3" class="" id="tab_step_3">3.
                                    {{ __('front.finalization') }}</a>
                            </div>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade active show" id="liton_tab_3_1">
                                <div class="ltn__apartments-tab-content-inner">
                                    <h6>{{ __('front.article') }}</h6>
                                    <div class="row">
                                        @csrf
                                        <input type="hidden" name="submission_id" value="{{ $submission->id }}">
                                        <div class="col-lg-12">
                                            <div class="ltn__product-item ltn__product-item-3" style="margin-bottom: 20px;">
                                                <div class="product-info">
                                                    {{-- <h2 class="product-title">
                                                        <a
                                                            href="{{ route('payment.submission', [$submission?->issue?->journal?->url_path, $submission?->submission_id]) }}">{{ $submission->full_title }}</a>
                                                    </h2> --}}
                                                    <h2 class="product-title"><a
                                                            href="{{ route('payment.submission', [$submission?->issue?->journal?->url_path, $submission?->submission_id]) }}">
                                                            Submmission ID: {{ $submission->submission_id }}</a>
                                                    </h2>
                                                    {{-- <div class="product-brief">
                                                        <ul>
                                                            @foreach ($submission->authors as $author)
                                                                <li style="margin-top: 0px;">
                                                                    <span
                                                                        style="font-weight: 900">{{ $author['name'] }}</span>,
                                                                    {{ $author['affiliation'] }}
                                                                </li>
                                                            @endforeach

                                                        </ul>
                                                    </div> --}}

                                                    <div>
                                                        <p>
                                                            <strong>{{ __('front.journal') }}:</strong>
                                                            <a
                                                                href="{{ route('journal.detail', $submission->issue->journal->url_path) }}">
                                                                {{ $submission->issue->journal->title }}
                                                            </a>


                                                            <br>
                                                            {{-- <strong>Issue:</strong>
                                                            <a
                                                                href="{{ route('journal.detail', $submission->issue->journal->url_path) }}">
                                                                {{ $submission->issue->title }}
                                                            </a> --}}
                                                        </p>
                                                    </div>
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

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <h6>{{ __('front.author_information') }} ></h6>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-item input-item-textarea ltn__custom-icon">
                                                <input type="text" name="name" placeholder="*Full Name of Author"
                                                    required value="{{ old('name') }}">
                                                @error('name')
                                                    <div class="text-danger"
                                                        style="margin-top: -30px; margin-bottom: 20px; font-size: 12px;">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-item  input-item-textarea ltn__custom-icon">
                                                <input type="email" name="email" placeholder="*Email Address (Active)"
                                                    required value="{{ old('email') }}">
                                                @error('email')
                                                    <div class="text-danger"
                                                        style="margin-top: -30px; margin-bottom: 20px; font-size: 12px;">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-item input-item-textarea ltn__custom-icon">
                                                <input type="tel" name="phone"
                                                    placeholder="*Phone/WhatsApp Number (Active)"
                                                    value="{{ old('phone') }}">
                                                @error('phone')
                                                    <div class="text-danger"
                                                        style="margin-top: -30px; margin-bottom: 20px; font-size: 12px;">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>

                                    <div class="btn-wrapper text-center--- mt-0">
                                        <a href="#tabbar" class="btn theme-btn-1 btn-effect-1 text-uppercase"
                                            id="next_1">Next Step</a>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="liton_tab_3_2">
                                <div class="ltn__product-tab-content-inner">
                                    <h6>{{ __('front.payment_information') }}</h6>
                                    <div>
                                        {{ __('front.payment_information_text') }}
                                    </div>
                                    @forelse ($payment_accounts as $account)
                                        <div class="ltn__checkout-single-content ltn__coupon-code-wrap">
                                            <h5>
                                                Transfer Bank - {{ $account->bank }} <br>
                                                <a class="ltn__secondary-color">
                                                    {{ $account->account_number }} - An. {{ $account->account_name }}
                                                </a>
                                            </h5>
                                        </div>
                                    @empty
                                        <div class="ltn__checkout-single-content ltn__coupon-code-wrap">
                                            <h5>
                                                No Accounts Available
                                            </h5>
                                        </div>
                                    @endforelse
                                    <div>
                                        {{ __('front.payment_information_fee') }}
                                    </div>
                                    <div class="input-item input-item-textarea ltn__custom-icon">
                                        <select class="nice-select" name="payment_invoice_id" required>
                                            @forelse ($payment_invoices as $invoice)
                                                <option value="{{ $invoice->id }}" int="{{ $invoice->payment_amount }}"
                                                    percentage="{{ $invoice->payment_percent }}"
                                                    {{ old('payment_invoice_id') == $invoice->id ? 'selected' : '' }}>
                                                    INVOICE
                                                    {{ $invoice->invoice_number }}/JRNL/UINSMDD/{{ $invoice->created_at->format('Y') }}
                                                </option>
                                            @empty
                                                <option value="" disabled selected>
                                                    {{ __('front.no_invoice_found') }}
                                                </option>
                                            @endforelse
                                        </select>

                                        @error('payment_invoice_id')
                                            <div class="text-danger"
                                                style="margin-top: -30px; margin-bottom: 20px; font-size: 12px;">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div>
                                        {{ __('front.payment_information_amount') }}
                                    </div>
                                    <div class="ltn__checkout-single-content ltn__coupon-code-wrap">

                                        <h5> {{ __('front.percentage') }}:
                                            <a style="color: #f00; font-weight: 900" id="payment_amount_percentage">
                                                0
                                            </a>
                                            <br>
                                            {{ __('front.amount_to_be_paid') }}:
                                            <a style="color: #f00; font-weight: 900" id="payment_amount_int">
                                                0
                                            </a>
                                        </h5>
                                    </div>
                                    <h6>{{ __('front.payment_proff') }}</h6>

                                    <input type="file" id="myFile" name="payment_file"
                                        class="btn theme-btn-3 mb-10" accept=".png, .jpg, .jpeg, .pdf"><br>
                                    @error('payment_file')
                                        <div class="text-danger"
                                            style="margin-top: -30px; margin-bottom: 20px; font-size: 12px;">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                    <p>
                                        <small>* At least 1 image is required for a valid submission. Minimum file size is
                                            10 MB.</small><br>
                                        <small>* PDF files upload supported as well.</small><br>
                                        <small>* Images might take longer to be processed.</small>
                                    </p>
                                    <h6>{{ __('front.payment_timestamp') }}</h6>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-item input-item-textarea ltn__custom-icon">
                                                <input type="datetime-local" name="payment_timestamp"
                                                    placeholder="Payment Time" required
                                                    value="{{ old('payment_timestamp') }}">
                                                @error('payment_timestamp')
                                                    <div class="text-danger"
                                                        style="margin-top: -30px; margin-bottom: 20px; font-size: 12px;">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <h6>{{ __('front.payment_data') }}</h6>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="input-item">
                                                <select class="nice-select" name="payment_method" required>
                                                    <option value="" disabled>*{{ __('front.payment_method') }}
                                                    </option>
                                                    <option value="Transfer ATM"
                                                        {{ old('payment_method') == 'Transfer ATM' ? 'selected' : '' }}>
                                                        Transfer ATM</option>
                                                    <option value="Mobile Banking"
                                                        {{ old('payment_method') == 'Mobile Banking' ? 'selected' : '' }}>
                                                        Mobile Banking</option>
                                                    <option value="Transfer Teller"
                                                        {{ old('payment_method') == 'Transfer Teller' ? 'selected' : '' }}>
                                                        Transfer Teller</option>
                                                    <option value="Internet Banking"
                                                        {{ old('payment_method') == 'Internet Banking' ? 'selected' : '' }}>
                                                        Internet Banking</option>
                                                    <option value="Lainnya"
                                                        {{ old('payment_method') == 'Lainnya' ? 'selected' : '' }}>Lainnya
                                                    </option>
                                                </select>
                                                @error('payment_method')
                                                    <div class="text-danger"
                                                        style="margin-top: -30px; margin-bottom: 20px; font-size: 12px;">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="input-item input-item-textarea ltn__custom-icon">
                                                <input type="text" name="payment_account_name"
                                                    placeholder="*Account Holder Name" required
                                                    value="{{ old('payment_account_name') }}">
                                                @error('payment_account_name')
                                                    <div class="text-danger"
                                                        style="margin-top: -30px; margin-bottom: 20px; font-size: 12px;">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-item input-item-textarea ltn__custom-icon">
                                                <input type="text" name="payment_account_number"
                                                    placeholder="*Sender's Account Number" required
                                                    value="{{ old('payment_account_number') }}">
                                                @error('payment_account_number')
                                                    <div class="text-danger"
                                                        style="margin-top: -30px; margin-bottom: 20px; font-size: 12px;">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                    <div class="btn-wrapper text-center--- mt-0">
                                        <!-- <button type="submit" class="btn theme-btn-1 btn-effect-1 text-uppercase" >Next Step</button> -->
                                        <a href="#tabbar" class="btn theme-btn-1 btn-effect-1 text-uppercase"
                                            id="prev_2">Prev
                                            Step</a>
                                        <a href="#tabbar" class="btn theme-btn-1 btn-effect-1 text-uppercase"
                                            id="next_2">Next
                                            Step</a>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="liton_tab_3_3">
                                <div class="ltn__product-tab-content-inner">
                                    <h6>{{ __('front.finalization') }}</h6>
                                    <div class="row mb-30">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ __('front.payment_declaration_title') }}
                                                    </h5>
                                                    <p class="card-text">
                                                        {{ __('front.payment_declaration_text') }}
                                                    </p>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox"
                                                            id="declarationCheck" required checked
                                                            onclick="return false;">
                                                        <label class="form-check-label" for="declarationCheck">
                                                            {{ __('front.payment_declaration_checkbox') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="btn-wrapper text-center--- mt-0">
                                        <!-- <button type="submit" class="btn theme-btn-1 btn-effect-1 text-uppercase" >Next Step</button> -->
                                        <a href="#tabbar" class="btn theme-btn-1 btn-effect-1 text-uppercase"
                                            id="prev_3">Prev
                                            Step</a>
                                        <button id="submit" type="submit_btn"
                                            class="btn theme-btn-1 btn-effect-1 text-uppercase">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- APPOINTMENT AREA END -->
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $('#next_1').on('click', function(e) {
            // Cek field pada tab 1
            var name = $('input[name="name"]').val().trim();
            var email = $('input[name="email"]').val().trim();
            var phone = $('input[name="phone"]').val().trim();

            if (!name || !email || !phone) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Fields',
                    text: 'Please fill in all required fields (Name, Email, Phone) before proceeding.',
                });
                e.preventDefault();
                return false;
            }
            console.log('next_1 clicked');
            $('#tab_step_2').trigger('click');
        });
        $('#tab_step_2').on('click', function() {
            console.log('tab_step_2 clicked');
            // Cek apakah tab 2 sudah diisi
            var name = $('input[name="name"]').val().trim();
            var email = $('input[name="email"]').val().trim();
            var phone = $('input[name="phone"]').val().trim();
            if (!name || !email || !phone) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Fields',
                    text: 'Please fill in all required fields (Name, Email, Phone) before proceeding to Payment Information.',
                });
                return false;
            }
        });
        $('#prev_2').on('click', function() {
            console.log('prev_2 clicked');
            $('#tab_step_1').trigger('click');
        });
        $('#next_2').on('click', function() {
            // Cek field pada tab 2
            var paymentInvoiceId = $('select[name="payment_invoice_id"]').val();
            var paymentFile = $('input[name="payment_file"]').val();
            var paymentTimestamp = $('input[name="payment_timestamp"]').val();
            var paymentMethod = $('select[name="payment_method"]').val();
            var paymentAccountName = $('input[name="payment_account_name"]').val().trim();
            var paymentAccountNumber = $('input[name="payment_account_number"]').val().trim();

            if (!paymentInvoiceId || !paymentFile || !paymentTimestamp || !paymentMethod || !paymentAccountName || !
                paymentAccountNumber) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Fields',
                    text: 'Please fill in all required fields (Payment Invoice, Payment File, Payment Timestamp, Payment Method, Account Holder Name, Account Number) before proceeding.',
                });
                return false;
            }
            console.log('next_2 clicked');
            $('#tab_step_3').trigger('click');
        });
        $('#tab_step_3').on('click', function() {
            console.log('tab_step_3 clicked');
            // Cek apakah tab 3 sudah diisi
            var paymentInvoiceId = $('select[name="payment_invoice_id"]').val();
            var paymentFile = $('input[name="payment_file"]').val();
            var paymentTimestamp = $('input[name="payment_timestamp"]').val();
            var paymentMethod = $('select[name="payment_method"]').val();
            var paymentAccountName = $('input[name="payment_account_name"]').val().trim();
            var paymentAccountNumber = $('input[name="payment_account_number"]').val().trim();
            if (!paymentInvoiceId || !paymentFile || !paymentTimestamp || !paymentMethod || !paymentAccountName || !
                paymentAccountNumber) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Fields',
                    text: 'Please fill in all required fields (Payment Invoice, Payment File, Payment Timestamp, Payment Method, Account Holder Name, Account Number) before proceeding to Finalization.',
                });
                return false;
            }
        });
        $('#prev_3').on('click', function() {
            console.log('prev_3 clicked');
            $('#tab_step_2').trigger('click');
        });
    </script>
    <script>
        $(document).ready(function() {
            $('select[name="payment_invoice_id"]').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var intValue = selectedOption.attr('int');
                var formattedValue = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                }).format(intValue);
                var percentage = selectedOption.attr('percentage');

                $('#payment_amount_int').text(formattedValue);
                $('#payment_amount_percentage').text(percentage + '%');
            });
            // Trigger change event on page load to set the initial value
            $('select[name="payment_invoice_id"]').trigger('change');


        });
    </script>
@endsection
