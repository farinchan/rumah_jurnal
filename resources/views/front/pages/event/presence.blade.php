@extends('front.app')
@section('seo')
    <title>{{ $meta['title'] ?? ($title ?? 'Event Presence') }}</title>
    <meta name="description" content="{{ $meta['description'] ?? ($title ?? 'Event Presence') }}">
    <meta name="keywords" content="{{ $meta['keywords'] ?? '' }}">
    <meta name="author" content="UIN Sjech M.Djamil Djambek Bukittinggi">

    <meta property="og:title" content="{{ $meta['title'] ?? ($title ?? 'Event Presence') }}">
    <meta property="og:description" content="{{ $meta['description'] ?? ($title ?? 'Event Presence') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('event.presence', $event_attendance->code) }}">
    <link rel="canonical" href="{{ route('event.presence', $event_attendance->code) }}">
    <meta property="og:image"
        content="{{ isset($meta['favicon']) ? Storage::url($meta['favicon']) : $event_attendance->event?->getThumbnail() ?? asset('back/media/svg/files/blank-image.svg') }}">
@endsection

@section('styles')
@endsection

@section('content')
    @include('front.partials.breadcrumb')

    <!-- PRESENCE AREA START -->
    <div class="ltn__contact-message-area mb-120">
        <div class="container">
            <div class="row">
                <div class="col-lg-4">
                    <aside class="sidebar-area blog-sidebar ltn__right-sidebar">
                        <div class="widget ltn__author-widget">
                            <h4 class="ltn__widget-title ltn__widget-title-border">Informasi Acara</h4>
                            <div class="">
                                <img src="{{ $event_attendance->event?->getThumbnail() }}" alt="thumbnail"
                                    style="width:auto;object-fit:cover;border-radius:8px; margin-bottom: 10px;">
                                <div style="">
                                    <h5 class="">{{ $event_attendance->event?->name }}</h5>
                                    <div class="ltn__blog-meta">
                                        <ul style="display: flex; flex-direction: column; gap: 2px;">
                                            <li class="ltn__blog-author">
                                                <a href="#"><i
                                                        class="fa fa-info-circle"></i>{{ $event_attendance->event->type }}</a>
                                            </li>
                                            <li class="ltn__blog-author">
                                                <a href="#"><i class="fa fa-bullhorn"></i>
                                                    {{ $event_attendance->event->status }}</a>
                                            </li>
                                            <li class="ltn__blog-author">
                                                <a href="#"><i class="far fa-calendar"></i>
                                                    {{ $event_attendance->event->datetime }}
                                                </a>
                                            </li>
                                            @if ($event_attendance->event->status == 'offline')
                                                <li class="ltn__blog-author">
                                                    <a href="#"><i class="fa fa-map-marker"></i>
                                                        {{ $event_attendance->event->location }}
                                                    </a>
                                                </li>
                                            @endif

                                        </ul>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </aside>
                </div>
                <div class="col-lg-8">
                    <div class="ltn__form-box contact-form-box box-shadow white-bg">
                        <h4 class="title-2 mb-10">Presensi Kegiatan</h4>
                        <p class="mb-20">Silakan konfirmasi kehadiran Anda untuk sesi berikut.</p>

                        <div class="border rounded p-3 mb-25">
                            <div class="d-flex align-items-start gap-3">

                                <div style="">
                                    <h5 class="">{{ $event_attendance->name }}</h5>
                                    <div class="ltn__blog-meta">
                                        <ul style="display: flex; flex-direction: column; gap: 2px;">

                                            <li class="ltn__blog-author">
                                                <i class="far fa-calendar"></i>
                                                {{ \Carbon\Carbon::parse($event_attendance->start_datetime)->translatedFormat('d M Y H:i') }} -
                                                {{ \Carbon\Carbon::parse($event_attendance->end_datetime)->translatedFormat('d M Y H:i') }}

                                            </li>


                                        </ul>
                                    </div>
                                    @if ($event_attendance->description)
                                        <div class=" small text-muted">{!! nl2br(e($event_attendance->description)) !!}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="border rounded p-3 mb-25">
                            <div class="ltn__author-widget-inner">
                                <ul class="list-unstyled mb-0">

                                    <li class="mb-10">
                                        <i class="far fa-user mr-10"></i> {{ auth()->user()->name ?? '-' }}
                                    </li>
                                    <li class="mb-10">
                                        <i class="far fa-envelope mr-10"></i>
                                        {{ auth()->user()->email ?? '-' }}
                                    </li>
                                    <li class="mb-10">
                                        <i class="fa fa-phone mr-10"></i>
                                        {{ auth()->user()->phone ?? '-' }}
                                    </li>
                                </ul>
                            </div>

                        </div>
                        <div class="border rounded p-3 mb-25">
                            <div class="ltn__author-widget-inner">
                                <ul class="list-unstyled mb-0">

                                    <li class="mb-10">
                                        <i class="far fa-clock mr-10"></i> Waktu Presensi:
                                        {{ \Carbon\Carbon::now()->translatedFormat('d M Y H:i') }}
                                    </li>

                                </ul>
                            </div>

                        </div>

                        @if ($attendance_check)
                            <div class="alert alert-success d-flex align-items-center" role="alert">
                                <i class="fas fa-check-circle mr-10"></i>
                                <div>
                                    Anda sudah tercatat hadir pada
                                    <strong>{{ \Carbon\Carbon::parse($attendance_check->attendance_datetime)->translatedFormat('d M Y H:i') }}</strong>.
                                </div>
                            </div>
                        @else
                            <form action="{{ route('event.presence.store', $event_attendance->code) }}" method="POST"
                                class="ltn__form-box mt-15">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <div class="input-item input-item-textarea ltn__custom-icon">
                                            <textarea name="notes" placeholder="Catatan/Kritik/Saran (opsional)">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="user_agent" value="{{ request()->header('User-Agent') }}">
                                <div class="btn-wrapper mt-0">
                                    <button type="submit" class="btn theme-btn-1 btn-effect-1 text-uppercase">
                                        Konfirmasi Hadir
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>


            </div>
        </div>
    </div>
    <!-- PRESENCE AREA END -->
@endsection

@section('scripts')
@endsection
