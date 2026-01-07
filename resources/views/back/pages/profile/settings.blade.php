@extends('back.app')
@section('content')
    <div id="kt_content_container" class="container-xxl">
        {{-- Profile Header --}}
        @include('back.pages.profile._header')

        {{-- Profile Settings Card --}}
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Detail Profil</h3>
                </div>
            </div>
            <div id="kt_account_profile_details" class="collapse show">
                <form class="form" method="POST" action="{{ route('back.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card-body border-top p-9">
                        {{-- Photo --}}
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Foto Profil</label>
                            <div class="col-lg-8">
                                <div class="image-input image-input-outline" data-kt-image-input="true"
                                    style="background-image: url('{{ asset('back/media/svg/avatars/blank.svg') }}')">
                                    <div class="image-input-wrapper w-125px h-125px"
                                        style="background-image: url('{{ $user->getPhoto() }}')"></div>
                                    <label
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah foto">
                                        <i class="ki-duotone ki-pencil fs-7">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <input type="file" name="photo" accept=".png, .jpg, .jpeg" />
                                        <input type="hidden" name="photo_remove" />
                                    </label>
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Batalkan">
                                        <i class="ki-duotone ki-cross fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Hapus foto">
                                        <i class="ki-duotone ki-cross fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                                <div class="form-text">Tipe file yang diizinkan: png, jpg, jpeg.</div>
                                @error('photo')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Name --}}
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Nama Lengkap</label>
                            <div class="col-lg-8">
                                <input type="text" name="name"
                                    class="form-control form-control-lg form-control-solid @error('name') is-invalid @enderror"
                                    placeholder="Nama lengkap" value="{{ old('name', $user->name) }}" />
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Username --}}
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Username</label>
                            <div class="col-lg-8">
                                <input type="text" name="username"
                                    class="form-control form-control-lg form-control-solid @error('username') is-invalid @enderror"
                                    placeholder="Username" value="{{ old('username', $user->username) }}" />
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Email</label>
                            <div class="col-lg-8">
                                <input type="email" name="email"
                                    class="form-control form-control-lg form-control-solid @error('email') is-invalid @enderror"
                                    placeholder="Email" value="{{ old('email', $user->email) }}" />
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                <span>No. Telepon</span>
                                <span class="ms-1" data-bs-toggle="tooltip"
                                    title="Nomor telepon harus aktif">
                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </label>
                            <div class="col-lg-8">
                                <input type="tel" name="phone"
                                    class="form-control form-control-lg form-control-solid @error('phone') is-invalid @enderror"
                                    placeholder="No. Telepon" value="{{ old('phone', $user->phone) }}" />
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Gender --}}
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">Jenis Kelamin</label>
                            <div class="col-lg-8">
                                <select name="gender" class="form-select form-select-solid form-select-lg @error('gender') is-invalid @enderror">
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="laki-laki" {{ old('gender', $user->gender) == 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="perempuan" {{ old('gender', $user->gender) == 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- SINTA ID --}}
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                <span>SINTA ID</span>
                                <span class="ms-1" data-bs-toggle="tooltip"
                                    title="ID dari sinta.kemdikbud.go.id">
                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </label>
                            <div class="col-lg-8">
                                <input type="text" name="sinta_id"
                                    class="form-control form-control-lg form-control-solid @error('sinta_id') is-invalid @enderror"
                                    placeholder="SINTA ID" value="{{ old('sinta_id', $user->sinta_id) }}" />
                                @error('sinta_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Scopus ID --}}
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                <span>Scopus ID</span>
                                <span class="ms-1" data-bs-toggle="tooltip"
                                    title="ID dari scopus.com">
                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </label>
                            <div class="col-lg-8">
                                <input type="text" name="scopus_id"
                                    class="form-control form-control-lg form-control-solid @error('scopus_id') is-invalid @enderror"
                                    placeholder="Scopus ID" value="{{ old('scopus_id', $user->scopus_id) }}" />
                                @error('scopus_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Google Scholar --}}
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-semibold fs-6">
                                <span>Google Scholar</span>
                                <span class="ms-1" data-bs-toggle="tooltip"
                                    title="URL profil Google Scholar">
                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </label>
                            <div class="col-lg-8">
                                <input type="url" name="google_scholar"
                                    class="form-control form-control-lg form-control-solid @error('google_scholar') is-invalid @enderror"
                                    placeholder="https://scholar.google.com/..." value="{{ old('google_scholar', $user->google_scholar) }}" />
                                @error('google_scholar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="reset" class="btn btn-light btn-active-light-primary me-2">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Password Change Card --}}
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                data-bs-target="#kt_account_signin_method" aria-expanded="true" aria-controls="kt_account_signin_method">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Ubah Password</h3>
                </div>
            </div>
            <div id="kt_account_signin_method" class="collapse show">
                <form class="form" method="POST" action="{{ route('back.profile.password.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="card-body border-top p-9">
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Password Saat Ini</label>
                            <div class="col-lg-8">
                                <input type="password" name="current_password"
                                    class="form-control form-control-lg form-control-solid @error('current_password') is-invalid @enderror"
                                    placeholder="Password saat ini" />
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Password Baru</label>
                            <div class="col-lg-8">
                                <input type="password" name="password"
                                    class="form-control form-control-lg form-control-solid @error('password') is-invalid @enderror"
                                    placeholder="Password baru" />
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Password minimal 8 karakter.</div>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label required fw-semibold fs-6">Konfirmasi Password</label>
                            <div class="col-lg-8">
                                <input type="password" name="password_confirmation"
                                    class="form-control form-control-lg form-control-solid"
                                    placeholder="Konfirmasi password baru" />
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="submit" class="btn btn-primary">Ubah Password</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Connected Accounts Card --}}
        @if($user->google_id)
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 cursor-pointer" role="button">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Akun Terhubung</h3>
                </div>
            </div>
            <div class="card-body border-top p-9">
                <div class="notice d-flex bg-light-success rounded border-success border border-dashed mb-6 p-6">
                    <i class="ki-duotone ki-google fs-2tx text-success me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <div class="d-flex flex-stack flex-grow-1">
                        <div class="fw-semibold">
                            <h4 class="text-gray-900 fw-bold">Terhubung dengan Google</h4>
                            <div class="fs-6 text-gray-700">Akun Anda terhubung dengan Google untuk login yang lebih mudah.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
@endsection
@section('scripts')
<script>
    // Image input handler
    var imageInputElement = document.querySelector('[data-kt-image-input="true"]');
    var imageInput = new KTImageInput(imageInputElement);
</script>
@endsection
