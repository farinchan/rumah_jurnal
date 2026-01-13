@extends('back.app')
@section('styles')
    <style>
        .template-upload-area {
            border: 2px dashed #ccc;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .template-upload-area:hover {
            border-color: #009ef7;
            background-color: #f8f9fa;
        }

        .template-upload-area.dragover {
            border-color: #009ef7;
            background-color: #e8f4fd;
        }

        .preview-container {
            max-height: 400px;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .preview-container img {
            width: 100%;
            height: auto;
        }
    </style>
@endsection

@section('content')
    @php
        [$before, $after] = explode(' - ', $event->datetime);
        $date_before = \Carbon\Carbon::parse($before)->toDateTimeString();
        $date_after = \Carbon\Carbon::parse($after)->toDateTimeString();
    @endphp
    <div id="kt_content_container" class="container-xxl">
        @include('back.pages.event.detail.header')

        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Buat Template Sertifikat</h3>
                </div>
                <div class="card-toolbar">
                    <a href="{{ route('back.event.detail.overview', $event->id) }}"
                        class="btn btn-sm btn-light-primary">
                        <i class="ki-outline ki-arrow-left fs-2"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body p-9">
                <form action="{{ route('back.event.detail.certificate.store', $event->id) }}" method="POST"
                    enctype="multipart/form-data" id="createTemplateForm">
                    @csrf

                    <div class="row mb-6">
                        <div class="col-lg-6">
                            <div class="mb-6">
                                <label class="form-label required">Nama Template</label>
                                <input type="text" name="name" class="form-control"
                                    placeholder="Contoh: Sertifikat Peserta Workshop" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label class="form-label required">Orientasi</label>
                                <select name="orientation" class="form-select" required>
                                    <option value="landscape" {{ old('orientation') == 'landscape' ? 'selected' : '' }}>
                                        Landscape (Horizontal)</option>
                                    <option value="portrait" {{ old('orientation') == 'portrait' ? 'selected' : '' }}>
                                        Portrait (Vertikal)</option>
                                </select>
                            </div>

                            <div class="mb-6">
                                <label class="form-label required">Ukuran Kertas</label>
                                <select name="paper_size" class="form-select" required>
                                    <option value="A4" {{ old('paper_size') == 'A4' ? 'selected' : '' }}>A4</option>
                                    <option value="Letter" {{ old('paper_size') == 'Letter' ? 'selected' : '' }}>Letter
                                    </option>
                                    <option value="Legal" {{ old('paper_size') == 'Legal' ? 'selected' : '' }}>Legal
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="mb-6">
                                <label class="form-label required">Upload Template Gambar</label>
                                <div class="template-upload-area" id="templateUploadArea">
                                    <input type="file" name="template_image" id="templateImageInput"
                                        accept="image/jpeg,image/png,image/jpg" class="d-none" required>
                                    <div id="uploadPlaceholder">
                                        <i class="ki-outline ki-picture fs-3x text-muted mb-3"></i>
                                        <p class="text-muted mb-0">Klik atau drag & drop gambar template</p>
                                        <small class="text-muted">Format: JPG, PNG. Max: 10MB</small>
                                    </div>
                                    <div id="uploadPreview" class="d-none">
                                        <img src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                                        <p class="text-success mt-2 mb-0"><i class="ki-outline ki-check-circle"></i> Gambar
                                            terpilih</p>
                                    </div>
                                </div>
                                @error('template_image')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label class="form-label">Tanda Tangan (Opsional)</label>
                                <input type="file" name="signature_image" class="form-control"
                                    accept="image/jpeg,image/png,image/jpg">
                                <small class="text-muted">Format: JPG, PNG. Max: 5MB</small>
                                @error('signature_image')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-6"></div>

                    <div class="notice d-flex bg-light-info rounded border-info border border-dashed p-6 mb-6">
                        <i class="ki-outline ki-information-5 fs-2tx text-info me-4"></i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Langkah Selanjutnya</h4>
                                <div class="fs-6 text-gray-700">
                                    Setelah menyimpan, Anda akan diarahkan ke halaman editor untuk mengatur posisi teks
                                    (Nama Peserta, Tanggal, dll) dengan fitur drag & drop.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('back.event.detail.overview', $event->id) }}"
                            class="btn btn-light me-3">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ki-outline ki-check fs-2"></i> Simpan & Lanjut ke Editor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.getElementById('templateUploadArea');
            const fileInput = document.getElementById('templateImageInput');
            const placeholder = document.getElementById('uploadPlaceholder');
            const preview = document.getElementById('uploadPreview');
            const previewImg = preview.querySelector('img');

            // Click to upload
            uploadArea.addEventListener('click', function() {
                fileInput.click();
            });

            // Drag and drop
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    handleFileSelect(e.dataTransfer.files[0]);
                }
            });

            // File input change
            fileInput.addEventListener('change', function(e) {
                if (e.target.files.length) {
                    handleFileSelect(e.target.files[0]);
                }
            });

            function handleFileSelect(file) {
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        placeholder.classList.add('d-none');
                        preview.classList.remove('d-none');
                    };
                    reader.readAsDataURL(file);
                }
            }
        });
    </script>
@endsection
