@extends('back.app')
@section('content')
    @php
        [$before, $after] = explode(' - ', $event->datetime);
        $date_before = \Carbon\Carbon::parse($before)->toDateTimeString();
        $date_after = \Carbon\Carbon::parse($after)->toDateTimeString();
        // dd($date_before, $date_after);
    @endphp
    <div id="kt_content_container" class=" container-xxl ">
        @include('back.pages.event.detail.header')
        <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
            <div class="card-header cursor-pointer">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Overview</h3>
                </div>
            </div>
            <div class="card-body p-9">
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Tipe Event</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $event->type }}</span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Status Event</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $event->status }}</span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Nama</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $event->name }}</span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Tanggal Event</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $date_before }} - {{ $date_after }}</span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Lokasi Event / Link Meet</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $event->location }}</span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Peserta Pendaftar</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $event->users->count() }} / {{ $event->limit }}
                            Peserta</span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Lampiran</label>
                    <div class="col-lg-8">
                        @if ($event->attachment)
                            <a href="{{ asset('storage/' . $event->attachment) }}" class="btn btn-sm btn-light-primary">
                                <i class="fa fa-file"></i> Download Lampiran
                            </a>
                        @else
                            <span class="fw-bold fs-6 text-gray-800">Tidak ada lampiran</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-7">
                    {!! $event->description !!}
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection
