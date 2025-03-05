@extends('back.app')
@section('content')
    <div id="kt_content_container" class=" container-xxl ">
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                data-bs-target="#kt_account_profile_details" aria-expanded="true"
                aria-controls="kt_account_profile_details">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Banner 1</h3>
                </div>
            </div>
            <div id="kt_account_settings_profile_details" class="collapse show">
                <form id="kt_account_profile_details_form" class="form" method="POST" enctype="multipart/form-data"
                    action="{{ route('back.setting.banner-update', 1) }}">
                    @method('PUT')
                    @csrf
                    <div class="card-body border-top p-9">
                        <div class="mb-6">
                            <label class="form-label">Judul</label>
                            <div>
                                    <label for="name" class=" form-label required">Banner</label>
                                    <div class="card card-custom card-stretch" style="cursor: pointer;"
                                        onclick="$('#banner1').click()">
                                        <div class="card-body">
                                            <img src="{{ $banner1?->getImage()?? asset('ext_images/no_image.png') }}"
                                                id="banner_preview1" class="rounded" alt=""
                                                style="height: 200px; margin: auto; display: block; object-fit: cover;" />
                                        </div>
                                    </div>
                                    <input type="file" style="display: none" id="banner1"
                                        name="image" accept="image/*">
                                    <small class="text-muted">Klik gambar untuk mengganti thumbnail</small>

                            </div>
                        </div>
                        <div class="mb-6">
                            <label class="form-label">Judul</label>
                            <input type="text" class="form-control form-control-solid" name="title"
                              value="{{ $banner1->title?? "" }}"   placeholder="Judul Banner" required>
                        </div>
                        <div class="mb-6">
                            <label class="form-label">Sub Judul</label>
                            <input type="text" class="form-control form-control-solid" name="subtitle"
                            value="{{ $banner1->subtitle?? "" }}"  placeholder="Sub Judul Banner" required>
                        </div>
                        <div class="mb-6">
                            <label class="form-label">Remote URL</label>
                            <input type="url" class="form-control form-control-solid" name="url"
                            value="{{ $banner1->url?? "" }}"   placeholder="URL Tujuan" required>
                        </div>
                        <div class="mb-6">
                            <label class="form-label">Status Aktif Banner</label>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" id="flexSwitchChecked" name="status"
                                 @if ($banner1->status?? 0 == 1)
                                      checked
                                 @endif    />
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
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                data-bs-target="#kt_account_profile_details" aria-expanded="true"
                aria-controls="kt_account_profile_details">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Banner 2</h3>
                </div>
            </div>
            <div id="kt_account_settings_profile_details" class="collapse show">
                <form id="kt_account_profile_details_form" class="form" method="POST" enctype="multipart/form-data"
                    action="{{ route('back.setting.banner-update', 2) }}">
                    @method('PUT')
                    @csrf
                    <div class="card-body border-top p-9">
                        <div class="mb-6">
                            <label class="form-label">Judul</label>
                            <div>
                                    <label for="name" class=" form-label required">Banner</label>
                                    <div class="card card-custom card-stretch" style="cursor: pointer;"
                                        onclick="$('#banner2').click()">
                                        <div class="card-body">
                                            <img src="{{ $banner2?->getImage()?? asset('ext_images/no_image.png') }}"
                                                id="banner_preview2" class="rounded" alt=""
                                                style="height: 200px; margin: auto; display: block; object-fit: cover;" />
                                        </div>
                                    </div>
                                    <input type="file" style="display: none" id="banner2"
                                        name="image" accept="image/*">
                                    <small class="text-muted">Klik gambar untuk mengganti thumbnail</small>

                            </div>
                        </div>
                        <div class="mb-6">
                            <label class="form-label">Judul</label>
                            <input type="text" class="form-control form-control-solid" name="title"
                              value="{{ $banner2->title?? "" }}"   placeholder="Judul Banner" required>
                        </div>
                        <div class="mb-6">
                            <label class="form-label">Sub Judul</label>
                            <input type="text" class="form-control form-control-solid" name="subtitle"
                            value="{{ $banner2->subtitle?? "" }}"  placeholder="Sub Judul Banner" required>
                        </div>
                        <div class="mb-6">
                            <label class="form-label">Remote URL</label>
                            <input type="url" class="form-control form-control-solid" name="url"
                            value="{{ $banner2->url?? "" }}"   placeholder="URL Tujuan" required>
                        </div>
                        <div class="mb-6">
                            <label class="form-label">Status Aktif Banner</label>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" id="flexSwitchChecked" name="status"
                                 @if ($banner2->status?? 0 == 1)
                                      checked
                                 @endif    />
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
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                data-bs-target="#kt_account_profile_details" aria-expanded="true"
                aria-controls="kt_account_profile_details">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Banner 3</h3>
                </div>
            </div>
            <div id="kt_account_settings_profile_details" class="collapse show">
                <form id="kt_account_profile_details_form" class="form" method="POST" enctype="multipart/form-data"
                    action="{{ route('back.setting.banner-update', 3) }}">
                    @method('PUT')
                    @csrf
                    <div class="card-body border-top p-9">
                        <div class="mb-6">
                            <label class="form-label">Judul</label>
                            <div>
                                    <label for="name" class=" form-label required">Banner</label>
                                    <div class="card card-custom card-stretch" style="cursor: pointer;"
                                        onclick="$('#banner3').click()">
                                        <div class="card-body">
                                            <img src="{{ $banner3?->getImage()?? asset('ext_images/no_image.png') }}"
                                                id="banner_preview3" class="rounded" alt=""
                                                style="height: 200px; margin: auto; display: block; object-fit: cover;" />
                                        </div>
                                    </div>
                                    <input type="file" style="display: none" id="banner3"
                                        name="image" accept="image/*">
                                    <small class="text-muted">Klik gambar untuk mengganti thumbnail</small>

                            </div>
                        </div>
                        <div class="mb-6">
                            <label class="form-label">Judul</label>
                            <input type="text" class="form-control form-control-solid" name="title"
                              value="{{ $banner3->title?? "" }}"   placeholder="Judul Banner" required>
                        </div>
                        <div class="mb-6">
                            <label class="form-label">Sub Judul</label>
                            <input type="text" class="form-control form-control-solid" name="subtitle"
                            value="{{ $banner3->subtitle?? "" }}"  placeholder="Sub Judul Banner" required>
                        </div>
                        <div class="mb-6">
                            <label class="form-label">Remote URL</label>
                            <input type="url" class="form-control form-control-solid" name="url"
                            value="{{ $banner3->url?? "" }}"   placeholder="URL Tujuan" required>
                        </div>
                        <div class="mb-6">
                            <label class="form-label">Status Aktif Banner</label>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="1" id="flexSwitchChecked" name="status"
                                 @if ($banner3->status?? 0 == 1)
                                      checked
                                 @endif    />
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
    </div>
@endsection
@section('scripts')
<script>
    $('#banner1').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#banner_preview1').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });

        $('#banner2').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#banner_preview2').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });

        $('#banner3').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#banner_preview3').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });
</script>
@endsection
