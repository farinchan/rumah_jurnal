@extends('back.app')
@section('content')
    <div id="kt_content_container" class="container-xxl">
        {{-- Profile Header --}}
        @include('back.pages.profile._header')

        {{-- Events List Card --}}
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Daftar Event Saya</h3>
                </div>
            </div>
            <div class="card-body border-top p-9">
                @if($events->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                            <thead>
                                <tr class="fw-bold text-muted bg-light">
                                    <th class="ps-4 w-25px rounded-start"></th>
                                    <th class="min-w-300px">Event</th>
                                    <th class="min-w-150px">Tanggal & Lokasi</th>
                                    <th class="min-w-125px">Tanggal Daftar</th>
                                    <th class="min-w-100px">Kehadiran</th>
                                    <th class="min-w-100px text-end rounded-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($events as $eventUser)
                                    @php
                                        $userAttendances = $eventUser->Attendances;
                                        $totalSessions = $eventUser->event->attendances->count();
                                        $attendedSessions = $userAttendances->count();
                                    @endphp
                                    <tr>
                                        <td class="ps-4">
                                            @if($totalSessions > 0)
                                                <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary toggle-details"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#attendance-{{ $eventUser->id }}"
                                                        aria-expanded="false">
                                                    <i class="ki-duotone ki-plus fs-3 toggle-icon">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-50px me-5">
                                                    <img src="{{ $eventUser->event->getThumbnail() }}" class="h-50 align-self-center" alt="{{ $eventUser->event->name ?? 'Event' }}" />
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <span class="text-dark fw-bold text-hover-primary mb-1 fs-6">{{ $eventUser->event->name ?? '-' }}</span>
                                                    <span class="text-muted fw-semibold d-block fs-7">
                                                        <span class="badge badge-light-{{ $eventUser->event->status == 'online' ? 'info' : 'primary' }} me-2">
                                                            {{ ucfirst($eventUser->event->status) }}
                                                        </span>
                                                        <span class="badge badge-light-{{ $eventUser->event->access == 'terbuka' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($eventUser->event->access) }}
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-dark fw-semibold d-block fs-7">
                                                @if($eventUser->event->datetime)
                                                    <i class="ki-duotone ki-calendar fs-7 me-1">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    {{ $eventUser->event->datetime }}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                            <span class="text-muted fw-semibold d-block fs-7">
                                                <i class="ki-duotone ki-geolocation fs-7 me-1">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                {{ $eventUser->event->location ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted fw-semibold d-block fs-7">{{ $eventUser->created_at->format('d M Y') }}</span>
                                            <span class="text-muted fw-semibold d-block fs-7">{{ $eventUser->created_at->format('H:i') }} WIB</span>
                                        </td>
                                        <td>
                                            @if($totalSessions > 0)
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-light-{{ $attendedSessions == $totalSessions ? 'success' : ($attendedSessions > 0 ? 'warning' : 'danger') }} fs-7">
                                                        {{ $attendedSessions }}/{{ $totalSessions }} Sesi
                                                    </span>
                                                </div>
                                            @else
                                                <span class="text-muted fs-7">Belum ada sesi</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('event.show', $eventUser->event->slug ?? $eventUser->event->id) }}"
                                               class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                               target="_blank"
                                               data-bs-toggle="tooltip" title="Lihat Event">
                                                <i class="ki-duotone ki-eye fs-3">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                </i>
                                            </a>
                                            <a href="{{ route('event.eticket', $eventUser->id) }}"
                                               class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm"
                                               target="_blank"
                                               data-bs-toggle="tooltip" title="E-Ticket">
                                                <i class="ki-duotone ki-ticket fs-3">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </a>
                                        </td>
                                    </tr>
                                    {{-- Expandable Attendance Details --}}
                                    @if($totalSessions > 0)
                                    <tr class="collapse" id="attendance-{{ $eventUser->id }}">
                                        <td colspan="6" class="p-0">
                                            <div class="bg-gray-100 p-5 mx-4 my-3 rounded border border-gray-200">
                                                <h6 class="fw-bold text-gray-800 mb-4">
                                                    <i class="ki-duotone ki-calendar-tick fs-4 me-2 text-primary">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                        <span class="path5"></span>
                                                        <span class="path6"></span>
                                                    </i>
                                                    Daftar Sesi Kehadiran
                                                </h6>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm mb-0 bg-white rounded overflow-hidden">
                                                        <thead class="bg-gray-200">
                                                            <tr>
                                                                <th class="fw-semibold fs-7 ps-4">Nama Sesi</th>
                                                                <th class="fw-semibold fs-7">Waktu Sesi</th>
                                                                <th class="fw-semibold fs-7 text-center">Status</th>
                                                                <th class="fw-semibold fs-7 text-center">Waktu Hadir</th>
                                                                <th class="fw-semibold fs-7 text-center pe-4">Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($eventUser->event->attendances as $session)
                                                                @php
                                                                    $userAttendance = $userAttendances->where('event_attendance_id', $session->id)->first();
                                                                    $isAttended = $userAttendance !== null;
                                                                    $now = now();
                                                                    $sessionStart = $session->start_datetime ? \Carbon\Carbon::parse($session->start_datetime) : null;
                                                                    $sessionEnd = $session->end_datetime ? \Carbon\Carbon::parse($session->end_datetime) : null;
                                                                    $isSessionOpen = $sessionStart && $sessionEnd && $now->between($sessionStart, $sessionEnd);
                                                                    $isSessionPast = $sessionEnd && $now->gt($sessionEnd);
                                                                @endphp
                                                                <tr>
                                                                    <td class="ps-4">
                                                                        <span class="fw-semibold text-gray-800">{{ $session->name ?? 'Sesi ' . $loop->iteration }}</span>
                                                                        @if($session->description)
                                                                            <br><small class="text-muted">{{ Str::limit($session->description, 50) }}</small>
                                                                        @endif
                                                                    </td>
                                                                    <td>
                                                                        @if($session->start_datetime && $session->end_datetime)
                                                                            <span class="fs-7">
                                                                                {{ \Carbon\Carbon::parse($session->start_datetime)->format('d M Y H:i') }}
                                                                                <br>s/d {{ \Carbon\Carbon::parse($session->end_datetime)->format('H:i') }} WIB
                                                                            </span>
                                                                        @else
                                                                            <span class="text-muted fs-7">-</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if($isAttended)
                                                                            <span class="badge badge-success">
                                                                                <i class="ki-duotone ki-check fs-7 me-1"></i>Hadir
                                                                            </span>
                                                                        @elseif($isSessionPast)
                                                                            <span class="badge badge-danger">
                                                                                <i class="ki-duotone ki-cross fs-7 me-1"></i>Tidak Hadir
                                                                            </span>
                                                                        @elseif($isSessionOpen)
                                                                            <span class="badge badge-warning">
                                                                                <i class="ki-duotone ki-time fs-7 me-1"></i>Sedang Berlangsung
                                                                            </span>
                                                                        @else
                                                                            <span class="badge badge-secondary">
                                                                                <i class="ki-duotone ki-time fs-7 me-1"></i>Belum Dimulai
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center">
                                                                        @if($isAttended && $userAttendance->attendance_datetime)
                                                                            <span class="fs-7">{{ \Carbon\Carbon::parse($userAttendance->attendance_datetime)->format('d M Y H:i') }}</span>
                                                                        @elseif($isAttended)
                                                                            <span class="fs-7">{{ $userAttendance->created_at->format('d M Y H:i') }}</span>
                                                                        @else
                                                                            <span class="text-muted fs-7">-</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="text-center pe-4">
                                                                        @if(!$isAttended && ($isSessionOpen || !$isSessionPast))
                                                                            <a href="{{ route('event.presence', $session->code) }}"
                                                                               class="btn btn-sm btn-primary"
                                                                               target="_blank">
                                                                                <i class="ki-duotone ki-entrance-left fs-6 me-1">
                                                                                    <span class="path1"></span>
                                                                                    <span class="path2"></span>
                                                                                </i>
                                                                                Presensi
                                                                            </a>
                                                                        @elseif($isAttended)
                                                                            <span class="text-success fs-7">
                                                                                <i class="ki-duotone ki-check-circle fs-4">
                                                                                    <span class="path1"></span>
                                                                                    <span class="path2"></span>
                                                                                </i>
                                                                            </span>
                                                                        @else
                                                                            <span class="text-muted fs-7">-</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination --}}
                    <div class="d-flex justify-content-end mt-4">
                        {{ $events->links() }}
                    </div>
                @else
                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                        <i class="ki-duotone ki-calendar fs-2tx text-primary me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Belum Ada Event</h4>
                                <div class="fs-6 text-gray-700">Anda belum terdaftar di event apapun.
                                    <a href="{{ route('event.index') }}" class="fw-bold">Lihat event yang tersedia</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Certificates Card --}}
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Sertifikat</h3>
                </div>
            </div>
            <div class="card-body border-top p-9">
                @if($attendances->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                            <thead>
                                <tr class="fw-bold text-muted bg-light">
                                    <th class="ps-4 min-w-50px rounded-start">No</th>
                                    <th class="min-w-300px">Event</th>
                                    <th class="min-w-150px">Tanggal Hadir</th>
                                    <th class="min-w-100px text-end rounded-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendances as $index => $attendance)
                                    @if($attendance->eventAttendance && $attendance->eventAttendance->event)
                                        <tr>
                                            <td class="ps-4">
                                                <span class="fw-semibold text-gray-600">{{ $index + 1 }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="symbol symbol-45px me-4">
                                                        <div class="symbol-label bg-light-success">
                                                            <i class="ki-duotone ki-award fs-2 text-success">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                                <span class="path3"></span>
                                                            </i>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="text-dark fw-bold fs-6">{{ $attendance->eventAttendance->event->name ?? 'Sertifikat Event' }}</span>
                                                        <span class="text-muted fw-semibold fs-7">{{ $attendance->eventAttendance->name ?? 'Sesi Kehadiran' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-gray-800 fw-semibold d-block fs-7">{{ $attendance->attendance_datetime ? \Carbon\Carbon::parse($attendance->attendance_datetime)->format('d M Y') : $attendance->created_at->format('d M Y') }}</span>
                                                <span class="text-muted fw-semibold d-block fs-7">{{ $attendance->attendance_datetime ? \Carbon\Carbon::parse($attendance->attendance_datetime)->format('H:i') : $attendance->created_at->format('H:i') }} WIB</span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="{{ route('event.certificate', $attendance->eventUser->id ?? $attendance->id) }}"
                                                   class="btn btn-sm btn-light-success"
                                                   target="_blank">
                                                    <i class="ki-duotone ki-document fs-5 me-1">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                    Unduh Sertifikat
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-6">
                        <i class="ki-duotone ki-award fs-2tx text-warning me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Belum Ada Sertifikat</h4>
                                <div class="fs-6 text-gray-700">Sertifikat akan tersedia setelah Anda menghadiri event dan event tersebut selesai.</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // Toggle icon change on expand/collapse
    document.querySelectorAll('.toggle-details').forEach(function(button) {
        var targetId = button.getAttribute('data-bs-target');
        var targetElement = document.querySelector(targetId);

        if (targetElement) {
            targetElement.addEventListener('show.bs.collapse', function() {
                var icon = button.querySelector('.toggle-icon');
                icon.classList.remove('ki-plus');
                icon.classList.add('ki-minus');
            });

            targetElement.addEventListener('hide.bs.collapse', function() {
                var icon = button.querySelector('.toggle-icon');
                icon.classList.remove('ki-minus');
                icon.classList.add('ki-plus');
            });
        }
    });
</script>
@endsection
