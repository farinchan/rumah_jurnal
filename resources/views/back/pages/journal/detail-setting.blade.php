@extends('back.app')
@section('content')
    <div id="kt_content_container" class=" container-xxl ">
        @include('back.pages.journal.detail-header')
        <div class="card ">
            <div class="card-header">
                <h3 class="card-title">Pengaturan</h3>
            </div>
            <form action="{{ route('back.journal.issue.update', [$journal->url_path, $issue->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label required">Volume</label>
                            <input type="text" name="volume" class="form-control" value="{{ $issue->volume }}"
                                required />
                            @error('volume')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Number</label>
                            <input type="text" name="number" class="form-control" value="{{ $issue->number }}"
                                required />
                            @error('number')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label required">Year</label>
                            <input type="text" name="year" class="form-control" placeholder="{{ date('Y') }}"
                                value="{{ $issue->year }}" required />
                            @error('year')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mt-5">
                            <label class="form-label required">Title</label>
                            <input type="text" name="title" class="form-control" placeholder="Judul Issue"
                                value="{{ $issue->title }}" required />
                            @error('title')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mt-5">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="6" placeholder="Deskripsi Issue">{{ $issue->description }}</textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mt-5">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" data-control="select2"
                                data-placeholder="Pilih Status" data-hide-search="true">
                                <option value="published" {{ $issue->status == 'published' ? 'selected' : '' }}>
                                    Published</option>
                                <option value="unpublished" {{ $issue->status == 'unpublished' ? 'selected' : '' }}>
                                    Unpublished</option>
                            </select>
                            @error('status')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="col-md-12 mt-5">
                            <label class="form-label">LOA Template</label>
                            <input type="file" name="loa_template" class="form-control"
                                placeholder="URL LOA Template" value="{{ $issue->loa_template }}" />
                                @if ($issue->loa_template)
                                   File Template: <a href="{{ asset('storage/' . $issue->loa_template) }}" target="_blank">Lihat
                                        Template LOA</a>
                                @endif
                            @error('loa_template')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>
            </form>
        </div>
        <div class="card mt-5">
            <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                data-bs-target="#kt_account_deactivate" aria-expanded="true" aria-controls="kt_account_deactivate">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Hapus Issue</h3>
                </div>
            </div>
            <div id="kt_account_settings_deactivate" class="collapse show">

                <div class="card-body border-top p-9">
                    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed mb-9 p-6">
                        <i class="ki-duotone ki-information fs-2tx text-warning me-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <h4 class="text-gray-900 fw-bold">Anda Yakin Hapus Issue ini?</h4>
                                <div class="fs-6 text-gray-700">
                                    Jika Anda menghapus issue ini, semua artikel, Reviewer, dan data terkait lainnya
                                    akan dihapus
                                    secara permanen. <br>Anda tidak akan dapat mengembalikannya.
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="form-check form-check-solid fv-row">
                        <input name="deactivate" class="form-check-input" type="checkbox" value="" id="deactivate" />
                        <label class="form-check-label fw-semibold ps-2 fs-6" for="deactivate">
                            Saya mengkonfirmasi bahwa saya ingin menghapus issue ini.
                        </label>
                    </div>
                </div>
                <div id="delete_issue" style="display: none">
                    <form action="{{ route('back.journal.issue.destroy', [$journal->url_path, $issue->id]) }}"
                        method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="card-footer d-flex justify-content-end py-6 px-9">
                            <button type="submit" class="btn btn-danger fw-semibold"
                                style="background: linear-gradient(45deg, #ff0000, #ff7f7f); color: white; animation: shimmer 2s infinite; border: none;">
                                Hapus Issue
                            </button>
                            <style>
                                @keyframes shimmer {
                                    0% {
                                        background-position: -200px 0;
                                    }

                                    100% {
                                        background-position: 200px 0;
                                    }
                                }

                                #kt_account_deactivate_account_submit {
                                    background-size: 400% 100%;
                                }
                            </style>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#deactivate').change(function() {
                if ($(this).is(':checked')) {
                    $('#delete_issue').css('display', 'block');
                } else {
                    $('#delete_issue').css('display', 'none');
                }
            });
        });
    </script>
@endsection
