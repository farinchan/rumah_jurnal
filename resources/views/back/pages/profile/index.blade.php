@extends('back.app')
@section('content')
    <div id="kt_content_container" class="container-xxl">
        {{-- Profile Header --}}
        @include('back.pages.profile._header')

        {{-- Profile Details Card --}}
        <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
            <div class="card-header cursor-pointer">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Detail Profil</h3>
                </div>
                <a href="{{ route('back.profile.settings') }}" class="btn btn-sm btn-primary align-self-center">Edit Profil</a>
            </div>
            <div class="card-body p-9">
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Nama Lengkap</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $user->name }}</span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Username</label>
                    <div class="col-lg-8 fv-row">
                        <span class="fw-semibold text-gray-800 fs-6">{{ $user->username ?? '-' }}</span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Email</label>
                    <div class="col-lg-8 d-flex align-items-center">
                        <span class="fw-bold fs-6 text-gray-800 me-2">{{ $user->email }}</span>
                        {{-- @if($user->email_verified_at)
                            <span class="badge badge-success">Terverifikasi</span>
                        @else
                            <span class="badge badge-warning">Belum Verifikasi</span>
                        @endif --}}
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">No. Telepon</label>
                    <div class="col-lg-8">
                        <span class="fw-semibold fs-6 text-gray-800">{{ $user->phone ?? '-' }}</span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Jenis Kelamin</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">
                            @if($user->gender == 'laki-laki')
                                Laki-laki
                            @elseif($user->gender == 'perempuan')
                                Perempuan
                            @else
                                -
                            @endif
                        </span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">SINTA ID</label>
                    <div class="col-lg-8">
                        @if($user->sinta_id)
                            <a href="https://sinta.kemdikbud.go.id/authors/profile/{{ $user->sinta_id }}" target="_blank" class="fw-semibold fs-6 text-primary text-hover-primary">
                                {{ $user->sinta_id }}
                                <i class="ki-duotone ki-exit-right-corner fs-6 ms-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                        @else
                            <span class="fw-semibold fs-6 text-gray-800">-</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Scopus ID</label>
                    <div class="col-lg-8">
                        @if($user->scopus_id)
                            <a href="https://www.scopus.com/authid/detail.uri?authorId={{ $user->scopus_id }}" target="_blank" class="fw-semibold fs-6 text-primary text-hover-primary">
                                {{ $user->scopus_id }}
                                <i class="ki-duotone ki-exit-right-corner fs-6 ms-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                        @else
                            <span class="fw-semibold fs-6 text-gray-800">-</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Google Scholar</label>
                    <div class="col-lg-8">
                        @if($user->google_scholar)
                            <a href="{{ $user->google_scholar }}" target="_blank" class="fw-semibold fs-6 text-primary text-hover-primary">
                                Lihat Profil
                                <i class="ki-duotone ki-exit-right-corner fs-6 ms-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                        @else
                            <span class="fw-semibold fs-6 text-gray-800">-</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Role</label>
                    <div class="col-lg-8">
                        @if($user->getRoleNames()->count() > 0)
                            @foreach($user->getRoleNames() as $role)
                                <span class="badge badge-light-primary me-1">{{ ucfirst($role) }}</span>
                            @endforeach
                        @else
                            <span class="fw-semibold fs-6 text-gray-800">-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Events Card --}}
        <div class="card mb-5 mb-xl-10">
            <div class="card-header cursor-pointer">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Event Terdaftar Terbaru</h3>
                </div>
                <a href="{{ route('back.profile.events') }}" class="btn btn-sm btn-light-primary align-self-center">Lihat Semua</a>
            </div>
            <div class="card-body p-9">
                @if($recentEvents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4">
                            <thead>
                                <tr class="fw-bold text-muted">
                                    <th class="min-w-200px">Event</th>
                                    <th class="min-w-150px">Tanggal Daftar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentEvents as $eventUser)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px me-5">
                                                    <img src="{{ $eventUser->event->getThumbnail() }}" alt="{{ $eventUser->event->name ?? 'Event' }}" />
                                                </div>
                                                <div class="d-flex justify-content-start flex-column">
                                                    <span class="text-dark fw-bold text-hover-primary fs-6">{{ $eventUser->event->name ?? '-' }}</span>
                                                    <span class="text-muted fw-semibold text-muted d-block fs-7">
                                                        {{ $eventUser->event->location ?? '-' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted fw-semibold d-block fs-7">{{ $eventUser->created_at->format('d M Y H:i') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed p-6">
                        <i class="ki-duotone ki-information fs-2tx text-primary me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-6 text-gray-700">Anda belum terdaftar di event apapun.
                                    <a href="{{ route('event.index') }}" class="fw-bold">Lihat event yang tersedia</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection
