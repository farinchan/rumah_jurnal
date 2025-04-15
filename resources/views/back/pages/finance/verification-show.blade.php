@extends('back.app')
@section('content')
    <div id="kt_content_container" class=" container-xxl ">
        <div class="card card-flush mb-5">
            <div class="card-header ">
                <div class="card-title">
                    <div class="fw-bold">Submission Artikel</div>
                </div>
            </div>
            <div class="card-body pt-0">
                <h3 class="text-gray-800 text-hover-primary mb-4">
                    {{ $payment->submission->submission_id }} - {{ $payment->submission->full_title }}
                </h3>

                <div class="mb-4">
                    <h5 class="fw-bold">Authors:</h5>
                    <ul class="list-unstyled ms-5">
                        @foreach ($payment->submission->authors as $author)
                            <li class="mb-2">
                                <span class="text-gray-800 fw-bold">{{ $author['name'] }}</span>,
                                <span class="text-muted">{{ $author['affiliation'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="mb-2">
                    <h5 class="fw-bold">Journal:</h5>
                    <a href="{{ route('journal.detail', $payment->submission->issue->journal->url_path) }}"
                        class="text-gray-800 text-hover-primary ms-5">
                        {{ $payment->submission->issue->journal->title }}
                    </a>
                </div>
                <div>
                    <h5 class="fw-bold">Issue:</h5>
                    <a href="#" class="text-gray-800 text-hover-primary ms-5">
                        {{ $payment->submission->issue->title }}
                    </a>
                </div>
            </div>
        </div>
        <div class="card card-flush mb-5">
            <div class="card-header">
                <div class="card-title">
                    <h2>Data Penulis/Pembayar</h2>
                </div>
            </div>
            <div class="card-body pt-0">

                <div class="mb-5 fv-row">
                    <label class="form-label">Nama</label>
                    <input type="text" name="name" class="form-control mb-2" placeholder="Nama"
                        value="{{ $payment->name }}" readonly />
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="text" name="email" class="form-control mb-2" placeholder="Email"
                            value="{{ $payment->email }}" readonly />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">No Handphone/Whatsapp</label>
                        <input type="text" name="phone" class="form-control mb-2" placeholder="Phone"
                            value="{{ $payment->phone }}" readonly />
                    </div>
                </div>


            </div>
        </div>
        <div class="card card-flush mb-5">
            <div class="card-header">
                <div class="card-title">
                    <h2>Data Pembayaran</h2>
                </div>
            </div>
            <div class="card-body pt-0">


                <div class="mb-5 fv-row">
                    <label class="form-label">Bukti Pembayaran</label>
                    <div>
                        <a href="{{ asset('storage/' . $payment->payment_file) }}" target="_blank"
                            class="btn btn-light-primary">
                            <i class="ki-duotone ki-document fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Lihat Bukti Pembayaran
                        </a>
                    </div>
                </div>
                <div class="mb-5 fv-row">
                    <label class="form-label">Tanggal & Waktu Pembayaran</label>
                    <input type="text" name="payment_timestamp" class="form-control mb-2"
                        placeholder="Tanggal & Waktu Pembayaran"
                        value="{{ Carbon\Carbon::parse($payment->payment_timestamp)->translatedFormat('l, d F Y H:i:s') }}"
                        readonly />
                </div>
                <div class="row mb-5">
                    <div class="col-md-6">
                        <label class="form-label">Metode Pembayaran</label>
                        <input type="text" name="payment_method" class="form-control mb-2"
                            placeholder="Metode Pembayaran" value="{{ $payment->payment_method }}" readonly />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nominal Transfer</label>
                        <input type="text" name="payment_amount" class="form-control mb-2" placeholder="Nominal Transfer"
                            value="@money($payment->payment_amount)" readonly />
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-6">
                        <label class="form-label">Nama Pemilik Rekening</label>
                        <input type="text" name="payment_account_name" class="form-control mb-2"
                            placeholder="Nama Pemilik Rekening" value="{{ $payment->payment_account_name }}" readonly />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Nomor Rekening Pengirim</label>
                        <input type="text" name="payment_account_number" class="form-control mb-2"
                            placeholder="Nomor Rekening Pengirim" value="{{ $payment->payment_account_number }}"
                            readonly />
                    </div>

                </div>


            </div>
        </div>
        <div class="card card-flush mb-5">
            <div class="card-header">
                <div class="card-title">
                    <h2 class="text-warning">Verifikasi Pembayaran</h2>
                </div>
            </div>
            <form action="{{ route('back.finance.verification.update', $payment->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body pt-0">


                    <div class="mb-5 fv-row">
                        <label class="form-label required">Status Pembayaran</label>
                        <select name="payment_status" class="form-select mb-2" id="payment_status">
                            <option value="pending" @if ($payment->status == 'pending') selected @endif>Pending</option>
                            <option value="accepted" @if ($payment->status == 'accepted') selected @endif>Accepted</option>
                            <option value="rejected" @if ($payment->status == 'rejected') selected @endif>Rejected</option>
                        </select>
                    </div>
                    <div class="mb-5 fv-row">
                        <label class="form-label">Keterangan</label>
                        <textarea name="payment_note" class="form-control mb-2" rows="5"
                            placeholder="Berikan Keterangan Untuk Pembayaran Ini">{{ $payment->payment_note }}</textarea>
                    </div>

                </div>
                <div class="card-footer d-flex justify-content-end py-6 px-9">
                    <a href="{{ route('back.finance.verification.index') }}" class="btn btn-light ms-3">Kembali</a>
                    <button type="submit" id="btn-submit" class="btn btn-warning">Simpan & Selesai</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script></script>
@endsection
