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
        <div class="d-flex flex-wrap flex-stack my-5">
            <h3 class="fw-bold my-2">
                Daftar Kehadiran
                <span class="fs-6 text-gray-500 fw-semibold ms-1">
                    ( {{ $attendances->count() }} List )
                </span>
            </h3>
            <div class="d-flex my-2">
                {{-- <div class="d-flex align-items-center position-relative me-4">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute translate-middle-y top-50 ms-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <form action="" method="get">

                            <input type="text"
                                class="form-control form-control-sm form-control-solid bg-body fw-semibold fs-7 w-150px ps-11"
                                placeholder="Cari" name="q" value="{{ request()->q }}" />
                        </form>
                    </div> --}}


                <a href="#" class='btn btn-primary btn-sm fw-bolder' class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#tambahDaftarKehadiran">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    Tambah Daftar kehadiran</a>
            </div>
        </div>
        <div class="row g-6 g-xl-9">
            @forelse ($attendances as $attendance)
                <div class="col-md-6 col-xl-4">
                    <a href="{{ route('back.event.detail.attendance.detail', [$event->id, $attendance->id]) }}"
                        class="card border-hover-primary">

                        <div class="card-body p-9">
                            <div class="fs-3 fw-bold text-gray-900">
                                {{ $attendance->name }}
                            </div>

                            <p class="text-gray-500 fw-semibold fs-5 mt-1 mb-7">
                                {{ $attendance->description ?? '-' }}
                            </p>
                            <div class="fs-6 text-gray-500">
                                <span class="fw-bold">Waktu Mulai:</span>
                                {{ \Carbon\Carbon::parse($attendance->start_datetime)->format('d M Y H:i') }}
                            </div>
                            <div class="fs-6 text-gray-500">
                                <span class="fw-bold">Waktu Selesai:</span>
                                {{ \Carbon\Carbon::parse($attendance->end_datetime)->format('d M Y H:i') }}
                            </div>
                            <div class="fs-5 text-gray-900">
                                Kehadiran Peserta:
                                {{ $attendance->attendances->count() }}
                                / {{ $event->users->count() }}

                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="card">
                    <div class="card-body text-center">
                        <h3 class="text-muted">Tidak ada daftar kehadiran</h3>
                        <p class="text-muted">Silahkan buat daftar kehadiran untuk peserta event ini.</p>
                        <a href="#" class='btn btn-primary btn-sm fw-bolder' class="btn btn-primary"
                            data-bs-toggle="modal" data-bs-target="#tambahDaftarKehadiran">
                            <i class="ki-duotone ki-plus fs-2"></i>
                            Tambah Daftar kehadiran</a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="tambahDaftarKehadiran">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">
                        Tambah Daftar Kehadiran
                    </h3>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>

                <form action="{{ route('back.event.detail.attendance.store', $event->id) }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-7">
                            <label for="name" class="form-label required">Nama Daftar Kehadiran</label>
                            <input type="text" class="form-control form-control-solid" name="name" id="name"
                                placeholder="Masukkan nama daftar kehadiran" required />
                        </div>
                        <div class="mb-7">
                            <label for="decription" class="form-label">Deskripsi</label>
                            <textarea class="form-control form-control-solid" name="description" id="description" placeholder="deskripsi"
                                rows="3"></textarea>
                        </div>
                        <div class="mb-7">
                            <label for="start_datetime" class="form-label required">Waktu Mulai</label>
                            <input type="datetime-local" class="form-control form-control-solid" name="start_datetime"
                                id="start_datetime" placeholder="Masukkan tanggal mulai" required />
                        </div>
                        <div class="mb-7">
                            <label for="end_datetime" class="form-label required">Waktu Selesai</label>
                            <input type="datetime-local" class="form-control form-control-solid" name="end_datetime"
                                id="end_datetime" placeholder="Masukkan tanggal selesai" required />
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
