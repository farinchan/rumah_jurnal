<div class="card mb-5 mb-xl-10">
    <div class="card-body pt-9 pb-0">
        <div class="d-flex flex-wrap flex-sm-nowrap">
            <div class="me-7 mb-4">
                <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                    <img src="{{ $user->getPhoto() }}" alt="Foto {{ $user->name }}" />
                    <div class="position-absolute translate-middle bottom-0 start-100 mb-6 bg-success rounded-circle border border-4 border-body h-20px w-20px">
                    </div>
                </div>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                    <div class="d-flex flex-column">
                        <div class="d-flex align-items-center mb-2">
                            <span class="text-gray-900 fs-2 fw-bold me-1">{{ $user->name }}</span>
                            @if($user->email_verified_at)
                            <i class="ki-duotone ki-verify fs-1 text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            @endif
                        </div>
                        <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                            @if($user->getRoleNames()->count() > 0)
                            <span class="d-flex align-items-center text-gray-500 me-5 mb-2">
                                <i class="ki-duotone ki-profile-circle fs-4 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                {{ $user->getRoleNames()->implode(', ') }}
                            </span>
                            @endif
                            @if($user->phone)
                            <span class="d-flex align-items-center text-gray-500 me-5 mb-2">
                                <i class="ki-duotone ki-phone fs-4 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {{ $user->phone }}
                            </span>
                            @endif
                            <span class="d-flex align-items-center text-gray-500 mb-2">
                                <i class="ki-duotone ki-sms fs-4 me-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                {{ $user->email }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-wrap flex-stack">
                    <div class="d-flex flex-column flex-grow-1 pe-8">
                        <div class="d-flex flex-wrap">
                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-calendar fs-3 text-primary me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="fs-2 fw-bold" data-kt-countup="true" data-kt-countup-value="{{ $totalEvents ?? 0 }}">0</div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-500">Event Terdaftar</div>
                            </div>

                            <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="ki-duotone ki-time fs-3 text-info me-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <div class="fs-6 fw-bold">{{ $user->created_at->format('d M Y') }}</div>
                                </div>
                                <div class="fw-semibold fs-6 text-gray-500">Bergabung Sejak</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bold">
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ $activeTab == 'overview' ? 'active' : '' }}"
                    href="{{ route('back.profile.index') }}">Overview</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ $activeTab == 'settings' ? 'active' : '' }}"
                    href="{{ route('back.profile.settings') }}">Pengaturan</a>
            </li>
            <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 {{ $activeTab == 'events' ? 'active' : '' }}"
                    href="{{ route('back.profile.events') }}">Event Saya</a>
            </li>
        </ul>
    </div>
</div>
