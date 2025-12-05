@extends('back.app')
@section('content')
    <div id="kt_content_container" class=" container-xxl ">
        <form id="kt_ecommerce_add_category_form" class="form d-flex flex-column flex-lg-row"
            action="{{ route('back.welcomeSpeech.update') }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                <div class="card card-flush py-4">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>Foto Thumbnail</h2>
                        </div>
                    </div>
                    <div class="card-body text-center pt-0">
                        <style>
                            .image-input-placeholder {
                                background-image: url('{{ $data->getImage() }}');
                            }

                            [data-bs-theme="dark"] .image-input-placeholder {
                                background-image: url('{{ $data->getImage() }}');
                            }
                        </style>
                        <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3"
                            data-kt-image-input="true">
                            <div class="image-input-wrapper w-150px h-150px"></div>
                            <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah Thumbnail">
                                <i class="ki-duotone ki-pencil fs-7">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <input type="file" name="image" accept=".png, .jpg, .jpeg" />
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
                            Set foto thumbnail disini.
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                <div class="card card-flush py-4">
                    {{-- <div class="card-header">
                            <div class="card-title">
                                <h2>Sekapur Sirih</h2>
                            </div>
                        </div> --}}
                    <div class="card-body pt-0">
                        <div class="mb-5">
                            <label class="form-label required">Judul</label>
                            <input type="text" name="name" class="form-control form-control-solid mb-3"
                                placeholder="Judul" value="{{ $data->name }}" required />
                            @error('name')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="form-label">Judul Utama</label>
                            <input type="text" name="title" class="form-control form-control-solid mb-3"
                                placeholder="Judul Utama" value="{{ $data->title }}" />
                            @error('title')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-5">
                            <label class="form-label">Subjudul</label>
                            <input type="text" name="subtitle" class="form-control form-control-solid mb-3"
                                placeholder="Subjudul" value="{{ $data->subtitle }}" />
                            @error('subtitle')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-10">
                            <label class="form-label required">Konten</label>
                            <div id="quill_content" name="kt_ecommerce_add_category_description" class="min-h-500px mb-2">
                                {!! $data->content !!}
                            </div>
                            <input type="hidden" name="content" id="content" required>
                            @error('content')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label class="form-label">Nama Penulis</label>
                                <input type="text" name="author_name" class="form-control form-control-solid mb-3"
                                    placeholder="Nama Penulis" value="{{ $data->author_name }}" />
                                @error('author_name')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Posisi/Jabatan</label>
                                <input type="text" name="author_position" class="form-control form-control-solid mb-3"
                                    placeholder="Posisi/Jabatan" value="{{ $data->author_position }}" />
                                @error('author_position')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="form-label">Tanda Tangan Penulis</label>
                            <input type="file" name="author_signature" class="form-control form-control-solid mb-3"
                                accept=".png, .jpg, .jpeg" />
                            @if($data->author_signature)
                                <div class="mt-3">
                                    <img src="{{ $data->getAuthorSignature() }}" alt="Current Signature" style="max-height: 100px; border: 1px solid #ddd; padding: 10px;">
                                </div>
                            @endif
                            @error('author_signature')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror
                            <div class="text-muted fs-7">Upload tanda tangan penulis (opsional)</div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">

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
        var quill = new Quill('#quill_content', {
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'], // toggled buttons
                    ['blockquote', 'code-block'],
                    ['link', 'image', 'video', 'formula'],

                    [{
                        header: [1, 2, 3, 4, 5, 6, false]
                    }], // custom button values
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
                    }], // superscript/subscript
                    [{
                        'indent': '-1'
                    }, {
                        'indent': '+1'
                    }], // outdent/indent
                    [{
                        'direction': 'rtl'
                    }], // text direction

                    [{
                        'color': []
                    }, {
                        'background': []
                    }], // dropdown with defaults from theme
                    [{
                        'font': []
                    }],
                    [{
                        'align': []
                    }],
                    ['clean'] // remove formatting button
                ]
            },
            placeholder: 'Tulis Konten disini...',
            theme: 'snow' // or 'bubble'
        });

        $("#content").val(quill.root.innerHTML);
        quill.on('text-change', function() {
            $("#content").val(quill.root.innerHTML);
        });

        var tagify = new Tagify(document.querySelector("#keyword_tagify"), {
            whitelist: [],
            dropdown: {
                maxItems: 20, // <- mixumum allowed rendered suggestions
                classname: "tags-look",
                enabled: 0,
                closeOnSelect: true
            }
        });
    </script>
@endsection
