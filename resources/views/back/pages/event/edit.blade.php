@extends('back.app')
@section('content')
    @php
        $datetimeRange = explode(' - ', $event->datetime);
        $startDate = isset($datetimeRange[0])
            ? \Carbon\Carbon::parse(trim($datetimeRange[0]))->format('Y-m-d H:i')
            : '';
        $endDate = isset($datetimeRange[1])
            ? \Carbon\Carbon::parse(trim($datetimeRange[1]))->format('Y-m-d H:i')
            : $startDate;
    @endphp
    <div id="kt_content_container" class=" container-xxl ">

        <form id="kt_ecommerce_add_category_form" class="form d-flex flex-column flex-lg-row"
            action="{{ route('back.event.update', $event->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Thumbnail</h2>
                        </div>
                    </div>
                    <div class="card-body text-center pt-0">
                        <style>
                            .image-input-placeholder {
                                background-image: url('{{ asset('back/media/svg/files/blank-image.svg') }}');
                            }

                            [data-bs-theme="dark"] .image-input-placeholder {
                                background-image: url('{{ asset('back/media/svg/files/blank-image-dark.svg') }}');
                            }
                        </style>
                        <div class="image-input image-input-outline mb-3" data-kt-image-input="true"
                            @if ($event->thumbnail) style="background-image: url('{{ asset('storage/' . $event->thumbnail) }}');"
                            @else
                                style="" @endif>
                            <div class="image-input-wrapper w-150px h-150px"
                                @if ($event->thumbnail) style="background-image: url('{{ asset('storage/' . $event->thumbnail) }}');" @endif>
                            </div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah Thumbnail">
                                <i class="ki-duotone ki-pencil fs-7">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="file" name="thumbnail" accept=".png, .jpg, .jpeg" />
                                <input type="hidden" name="avatar_remove" />
                            </label>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Batalkan Thumbnail">
                                <i class="ki-duotone ki-cross fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Hapus Thumbnail">
                                <i class="ki-duotone ki-cross fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                        </div>
                        <div class="text-muted fs-7">
                            Set Thumbnail event, Hanya menerima file dengan ekstensi png, jpg, jpeg
                        </div>
                    </div>
                </div>
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Status</h2>
                        </div>
                        <div class="card-toolbar">
                            <div class="rounded-circle bg-success w-15px h-15px" id="kt_ecommerce_add_category_status">
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <select name="is_active" class="form-select mb-2" data-control="select2" data-hide-search="true"
                            data-placeholder="Select an option" id="kt_ecommerce_add_category_status_select" required>
                            <option></option>
                            <option value="1" {{ $event->is_active == 1 ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ $event->is_active == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        @error('is_active')
                            <div class="text-danger fs-7">{{ $message }}</div>
                        @enderror
                        <div class="text-muted fs-7">
                            Set Status event, jika status event aktif maka event akan tampil
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Informasi Event</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row mb-5">
                            <div class="col-md-6 fv-row">
                                <label class="required form-label">Tipe event</label>
                                <select name="type" class="form-select mb-2" data-control="select2"
                                    data-hide-search="true" data-placeholder="Pilih jenis event" required>
                                    <option></option>
                                    <option value="Rapat" {{ $event->type == 'Rapat' ? 'selected' : '' }}>Rapat</option>
                                    <option value="Seminar" {{ $event->type == 'Seminar' ? 'selected' : '' }}>Seminar
                                    </option>
                                    <option value="Workshop" {{ $event->type == 'Workshop' ? 'selected' : '' }}>Workshop
                                    </option>
                                    <option value="Pelatihan" {{ $event->type == 'Pelatihan' ? 'selected' : '' }}>Pelatihan
                                    </option>
                                    <option value="Lainnya" {{ $event->type == 'Lainnya' ? 'selected' : '' }}>Lainnya
                                    </option>
                                </select>
                                @error('type')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 fv-row">
                                <label class="required form-label">Status event</label>
                                <select name="status" class="form-select mb-2" data-control="select2"
                                    data-hide-search="true" data-placeholder="Pilih status event" required>
                                    <option></option>
                                    <option value="online" {{ $event->status == 'online' ? 'selected' : '' }}>Online
                                    </option>
                                    <option value="offline" {{ $event->status == 'offline' ? 'selected' : '' }}>Offline
                                    </option>
                                </select>
                                @error('status')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-5 fv-row">
                                <label class="required form-label">Akses event</label>
                                <select name="access" class="form-select mb-2" data-control="select2"
                                    data-hide-search="true" data-placeholder="Pilih akses event" required>
                                    <option></option>
                                    <option value="terbuka" {{ $event->access == 'terbuka' ? 'selected' : '' }}>Terbuka</option>
                                    <option value="tertutup" {{ $event->access == 'tertutup' ? 'selected' : '' }}>Tertutup</option>
                                </select>
                                @error('access')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                                <div class="text-muted fs-7">
                                    Akses event, terbuka untuk umum atau tertutup (hanya untuk undangan, tidak ditampilkan di
                                    halaman depan)
                                </div>

                        </div>
                        <div class="mb-5 fv-row">
                            <label class="required form-label">Nama event</label>
                            <input type="text" name="name" class="form-control mb-2" placeholder="Masukkan nama event"
                                value="{{ $event->name }}" required />
                            @error('name')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-5 fv-row">
                            <label class="required form-label">Tanggal dan Waktu event (Dari dan Sampai)</label>
                            <input type="text" name="datetime" class="form-control mb-2"
                                placeholder="Pilih tanggal dan waktu event" value="{{ $event->datetime }}"
                                id="kt_daterangepicker_2" required />
                            @error('datetime')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-5 fv-row">
                            <label class="form-label">Lokasi / Link Meeting</label>
                            <input type="text" name="location" class="form-control mb-2"
                                placeholder="Masukkan lokasi event atau link meeting" value="{{ $event->location }}" />
                            @error('location')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                            <div class="text-muted fs-7">
                                Lokasi event jika offline, atau link meeting jika online. Contoh: Zoom, Google Meet, atau
                                lokasi fisik seperti alamat lengkap.
                            </div>
                        </div>
                        <div class="mb-5 fv-row">
                            <label class="form-label">Batas Peserta</label>
                            <input type="number" name="limit" class="form-control mb-2"
                                placeholder="Masukkan batas peserta event" value="{{ $event->limit }}" min="1" />
                            @error('limit')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                            <div class="text-muted fs-7">
                                Batas peserta event, masukkan angka untuk menentukan jumlah maksimal peserta yang dapat
                                mendaftar. Jika tidak ada batas, biarkan kosong.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2> Deskripsi Event</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="mb-10">
                            <label class="form-label required">Deskripsi</label>
                            <div id="quill_description" name="kt_ecommerce_add_category_description"
                                class="min-h-300px mb-2">
                                {!! $event->description !!}
                            </div>
                            <input type="hidden" name="description" id="description" required>
                            @error('description')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-10">
                            <label class="form-label ">File Lampiran</label>
                            @if ($event->attachment)
                                <div class="mb-2">
                                    <a href="{{ asset('storage/' . $event->attachment) }}" target="_blank">Lihat Lampiran
                                        Saat Ini</a>
                                </div>
                            @endif
                            <input type="file" name="attachment" class="form-control mb-2" accept=".pdf" />
                            @error('attachment')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                            <div class="text-muted fs-7">
                                File event, Hanya menerima file dengan ekstensi <code>.pdf</code> , dengan ukuran
                                maksimal 8 MB
                            </div>
                        </div>
                        <div class="mb-10">
                            <label class="form-label ">File Materi</label>
                            @if ($event->material)
                                <div class="mb-2">
                                    <a href="{{ asset('storage/' . $event->material) }}" target="_blank">Lihat Materi
                                        Saat Ini</a>
                                </div>
                            @endif
                            <input type="file" name="material" class="form-control mb-2" accept=".pdf, .doc, .docx, .ppt, .pptx, .xls, .xlsx, .txt, .zip, .rar" />
                            @error('material')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                            <div class="text-muted fs-7">
                                File materi atau notulen event, Hanya menerima file dengan ekstensi <code>.pdf, .doc, .docx, .ppt, .pptx, .xls, .xlsx, .txt, .zip, .rar</code> , dengan ukuran
                                maksimal 100 MB
                            </div>
                        </div>
                        <div>
                            <label class="form-label">Meta Tag Keywords</label>
                            <input id="keyword_tagify" name="meta_keywords" class="form-control mb-2"
                                value="{{ $event->meta_keywords }}" />
                            @error('meta_keywords')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                            <div class="text-muted fs-7">
                                Meta Tag Keywords digunakan untuk SEO, pisahkan dengan koma <code>,</code> jika lebih
                                dari satu keywoard yang digunakan
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('back.event.index') }}" id="kt_ecommerce_add_product_cancel"
                        class="btn btn-light me-5">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <span class="indicator-label">Simpan Perubahan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        $("#kt_daterangepicker_2").daterangepicker({
            timePicker: true,

            startDate: moment("{{ $startDate }}"),
            endDate: moment("{{ $endDate }}"),
            locale: {
                format: "DD MMMM YYYY HH:mm",
            }
        });
        var quill = new Quill('#quill_description', {
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    ['link', 'image', 'video', 'formula'],
                    [{
                        header: [1, 2, 3, 4, 5, 6, false]
                    }],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }, {
                        'list': 'check'
                    }],
                    [{
                        'script': 'sub'
                    }, {
                        'script': 'super'
                    }],
                    [{
                        'indent': '-1'
                    }, {
                        'indent': '+1'
                    }],
                    [{
                        'direction': 'rtl'
                    }],
                    [{
                        'color': []
                    }, {
                        'background': []
                    }],
                    [{
                        'font': []
                    }],
                    [{
                        'align': []
                    }],
                    ['clean']
                ]
            },
            placeholder: 'Tulis event disini...',
            theme: 'snow'
        });

        $("#description").val(quill.root.innerHTML);
        quill.on('text-change', function() {
            $("#description").val(quill.root.innerHTML);
        });

        var tagify = new Tagify(document.querySelector("#keyword_tagify"), {
            whitelist: [],
            dropdown: {
                maxItems: 20,
                classname: "tags-look",
                enabled: 0,
                closeOnSelect: true
            }
        });
    </script>
@endsection
