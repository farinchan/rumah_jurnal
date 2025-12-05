@extends('back.app')
@section('content')
    <div id="kt_content_container" class=" container-xxl ">
        <!-- Add Banner Button -->
        <div class="d-flex justify-content-end mb-5">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addBannerModal">
                <i class="fas fa-plus"></i> Tambah Banner
            </button>
        </div>

        @foreach($banners as $banner)
            <div class="card mb-5 mb-xl-10">
                <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                    data-bs-target="#kt_account_profile_details_{{ $banner->id }}" aria-expanded="true"
                    aria-controls="kt_account_profile_details_{{ $banner->id }}">
                    <div class="card-title m-0">
                        <h3 class="fw-bold m-0">Banner {{ $loop->iteration }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteBanner({{ $banner->id }}, 'Banner {{ $loop->iteration }}')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
                <div id="kt_account_profile_details_{{ $banner->id }}" class="collapse show">
                    <form id="kt_account_profile_details_form_{{ $banner->id }}" class="form" method="POST" enctype="multipart/form-data"
                        action="{{ route('back.setting.banner-update', $banner->id) }}">
                        @method('PUT')
                        @csrf
                        <div class="card-body border-top p-9">
                            <div class="mb-6">
                                <label class="form-label">Banner</label>
                                <div>
                                    <div class="card card-custom card-stretch" style="cursor: pointer;"
                                        onclick="$('#banner_{{ $banner->id }}').click()">
                                        <div class="card-body">
                                            <img src="{{ $banner?->getImage() ?? asset('ext_images/no_image.png') }}"
                                                id="banner_preview_{{ $banner->id }}" class="rounded" alt=""
                                                style="height: 200px; margin: auto; display: block; object-fit: cover;" />
                                        </div>
                                    </div>
                                    <input type="file" style="display: none" id="banner_{{ $banner->id }}"
                                        name="image" accept="image/*" class="banner-input" data-preview="#banner_preview_{{ $banner->id }}">
                                    <small class="text-muted">Klik gambar untuk mengganti thumbnail</small>
                                </div>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Judul</label>
                                <input type="text" class="form-control form-control-solid" name="title"
                                    value="{{ $banner->title ?? '' }}" placeholder="Judul Banner" required>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Sub Judul</label>
                                <input type="text" class="form-control form-control-solid" name="subtitle"
                                    value="{{ $banner->subtitle ?? '' }}" placeholder="Sub Judul Banner" required>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Remote URL</label>
                                <input type="url" class="form-control form-control-solid" name="url"
                                    value="{{ $banner->url ?? '' }}" placeholder="URL Tujuan" required>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Status Aktif Banner</label>
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="1" id="flexSwitchChecked_{{ $banner->id }}" name="status"
                                        @if (($banner->status ?? 0) == 1) checked @endif />
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
        @endforeach

        <!-- Add Banner Modal -->
        <div class="modal fade" id="addBannerModal" tabindex="-1" aria-labelledby="addBannerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form method="POST" enctype="multipart/form-data" action="{{ route('back.setting.banner-create') }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addBannerModalLabel">Tambah Banner Baru</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-6">
                                <label class="form-label">Banner</label>
                                <div>
                                    <div class="card card-custom card-stretch" style="cursor: pointer;"
                                        onclick="$('#new_banner').click()">
                                        <div class="card-body">
                                            <img src="{{ asset('ext_images/no_image.png') }}"
                                                id="new_banner_preview" class="rounded" alt=""
                                                style="height: 200px; margin: auto; display: block; object-fit: cover;" />
                                        </div>
                                    </div>
                                    <input type="file" style="display: none" id="new_banner"
                                        name="image" accept="image/*" class="banner-input" data-preview="#new_banner_preview" required>
                                    <small class="text-muted">Klik gambar untuk memilih gambar</small>
                                </div>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Judul</label>
                                <input type="text" class="form-control form-control-solid" name="title"
                                    placeholder="Judul Banner" required>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Sub Judul</label>
                                <input type="text" class="form-control form-control-solid" name="subtitle"
                                    placeholder="Sub Judul Banner" required>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Remote URL</label>
                                <input type="url" class="form-control form-control-solid" name="url"
                                    placeholder="URL Tujuan" required>
                            </div>
                            <div class="mb-6">
                                <label class="form-label">Status Aktif Banner</label>
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="1" id="newBannerStatus" name="status" checked />
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Banner</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    // Dynamic event handling for all banner inputs
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.banner-input').forEach(function(input) {
            input.addEventListener('change', function() {
                const previewSelector = this.dataset.preview;
                const file = this.files && this.files[0];

                if (!file) return;

                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.querySelector(previewSelector);
                    if (img) {
                        img.setAttribute('src', e.target.result);
                    }
                }
                reader.readAsDataURL(file);
            });
        });
    });

    // Delete banner function
    function deleteBanner(bannerId, bannerName) {
        Swal.fire({
            title: 'Hapus Banner?',
            text: `Apakah Anda yakin ingin menghapus ${bannerName}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('back.setting.banner-delete', '') }}/${bannerId}`;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection
