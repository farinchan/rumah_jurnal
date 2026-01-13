<div class="card mb-5 mb-xl-10">
    <div class="card-body pt-9 pb-0">
        <div class="d-flex flex-wrap flex-sm-nowrap">
            <div class="me-7 mb-4">
                <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                    <img src="{{ $event->getThumbnail() }}" alt="image" />

                </div>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center mb-2">
                            <a href="#"
                                class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{{ $event->name }}</a>
                            @if ($date_before > now() && $date_after < now())
                                <span class="badge badge-light-warning">Berlangsung</span>
                            @elseif($date_before > now())
                                <span class="badge badge-light-info">Akan Datang</span>
                            @elseif($date_after < now())
                                <span class="badge badge-light-danger">Selesai</span>
                            @endif
                        </div>
                        <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                            <a href="#"
                                class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                <i class="ki-outline ki-information-4 fs-4 me-1"></i>{{ $event->type }}</a>
                            <a href="#"
                                class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                <i class="ki-outline ki-watch fs-4 me-1"></i>{{ $event->status }}</a>
                            <a href="#"
                                class="d-flex align-items-center text-gray-500 text-hover-primary me-5 mb-2">
                                <i class="ki-outline ki-calendar fs-4 me-1"></i>{{ $event->datetime }}</a>
                        </div>
                    </div>
                    <div class="d-flex my-4">

                        <a href="{{ route('back.event.edit', $event->id) }}"
                            class="btn btn-sm btn-light-primary me-3">Edit</a>
                        <a href="#" class="btn btn-sm btn-danger me-3" data-bs-toggle="modal"
                            data-bs-target="#delete_agenda">Hapus</a>

                    </div>
                </div>
                <div class="d-flex flex-wrap flex-stack">
                    <div class="d-flex flex-column flex-grow-1 pe-8">
                        <div class="d-flex flex-wrap">

                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="ki-outline ki-arrow-up fs-3 text-success me-2"></i>
                                    <div class="fs-2 fw-bold" data-kt-countup="true" data-kt-countup-value="{{ $event->users->count() }}">
                                        0
                                    </div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-500">Peserta</div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ request()->routeIs('back.event.detail.overview') ? 'active' : '' }}"
                    href="{{ route('back.event.detail.overview', $event->id) }}">Overview</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ request()->routeIs('back.event.detail.participant') ? 'active' : '' }}"
                    href="{{ route('back.event.detail.participant', $event->id) }}">Peserta</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ request()->routeIs('back.event.detail.attendance') || request()->routeIs('back.event.detail.attendance.*') ? 'active' : '' }}"
                    href="{{ route('back.event.detail.attendance', $event->id) }}">Kehadiran</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ request()->routeIs('back.event.detail.certificate') || request()->routeIs('back.event.detail.certificate.*') ? 'active' : '' }}"
                    href="{{ route('back.event.detail.certificate.index', $event->id) }}">Sertifikat</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ request()->routeIs('back.event.detail.notification')  ? 'active' : '' }}"
                    href="{{ route('back.event.detail.notification', $event->id) }}">Pemberitahunan (WA/Email)</a>
            </li>


        </ul>
    </div>
</div>

<div class="modal fade" tabindex="-1" id="delete_agenda">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Hapus agenda</h3>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
                <!--end::Close-->
            </div>

            <form action="{{ route('back.event.destroy', $event->id) }}" method="POST">
                @method('DELETE')
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <p>Apakah Anda yakin ingin menghapus agenda <strong>{{ $event->name }}</strong>?</p>
                        <small class="text-danger">
                            <strong>Warning! </strong>
                            Menghapus agenda akan menghapus semua data terkait, termasuk peserta dan kehadiran.
                            <br>
                        </small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
