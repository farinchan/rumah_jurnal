@extends('back.app')
@section('content')
    <div id="kt_content_container" class=" container-xxl ">
        <div class="card card-flush">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <input type="text" data-kt-ecommerce-product-filter="search"
                            class="form-control form-control-solid w-250px ps-12" placeholder="Cari Issue" />
                    </div>
                </div>
                <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                    <div class="w-100 mw-150px">
                        <select class="form-select form-select-solid" data-control="select2" data-hide-search="true"
                            data-placeholder="Status" data-kt-ecommerce-product-filter="status">
                            <option></option>
                            <option value="all">Semua</option>
                            <option value="published">published</option>
                            <option value="unpublished">unpublished</option>
                        </select>
                    </div>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#add_issue" class="btn btn-primary">
                        <i class="ki-duotone ki-plus fs-2"></i>Tambah Issue</a>
                </div>
            </div>
            <div class="card-body pt-0">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_products_table">
                    <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">
                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                                        data-kt-check-target="#kt_ecommerce_products_table .form-check-input"
                                        value="1" />
                                </div>
                            </th>
                            <th class="min-w-200px">Issue</th>
                            <th class="text-end ">Article</th>
                            <th class="text-end ">Editor</th>
                            <th class="text-end ">Reviewer</th>
                            <th class="text-end ">Status</th>
                            <th class="text-end min-w-70px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                        @foreach ($journal->issues as $issue)
                            <tr>
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" value="1" />
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">

                                        <div class="ms-5">
                                            <a href="#" class="text-gray-800 text-hover-primary fs-5 fw-bold mb-1"
                                                data-kt-ecommerce-category-filter="category_name">Vol. {{ $issue->volume }}
                                                No. {{ $issue->number }} ({{ $issue->year }}): {{ $issue->title }}</a>
                                            <div class="text-muted fs-7 fw-bold">
                                                {{ Str::limit(strip_tags($issue->description), 300) }}...</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end pe-0">
                                    <span class="fw-bold">
                                        {{ $issue->editors->count() }}
                                    </span>
                                </td>
                                <td class="text-end pe-0">
                                    <span class="fw-bold">
                                        {{ $issue->submissions->count() }}
                                    </span>
                                </td>
                                <td class="text-end pe-0">
                                    <span class="fw-bold">
                                        {{ $issue->reviewers->count() }}
                                    </span>
                                </td>
                                <td class="text-end pe-0">
                                    @if ($issue->status == 'published')
                                        <div class="badge badge-light-success">Published</div>
                                    @else
                                        <div class="badge badge-light-danger">unpublished</div>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('back.journal.article.index', [$journal_path, $issue->id]) }}"
                                        class="btn btn-sm btn-light-primary my-1">
                                        <i class="ki-duotone ki-eye fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i> Detail
                                    </a>
                                    {{-- <a href="#"
                                        class="btn btn-sm btn-light btn-flex btn-center btn-active-light-primary"
                                        data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">Actions
                                        <i class="ki-duotone ki-down fs-5 ms-1"></i></a>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                        data-kt-menu="true">
                                        <div class="menu-item px-3">
                                            <a href="{{ route('back.journal.article.index', [$journal_path, $issue->id]) }}"
                                                class="menu-link px-3">Detail</a>
                                        </div>
                                        <div class="menu-item px-3">
                                            <a href="#" data-bs-toggle="modal"
                                                data-bs-target="#edit_issue{{ $issue->id }}"
                                                class="menu-link px-3">Edit</a>
                                        </div>
                                        <div class="menu-item px-3">
                                            <a href="#" class="menu-link px-3" data-bs-toggle="modal"
                                                data-bs-target="#delete_issue{{ $issue->id }}">Delete</a>
                                        </div>
                                    </div> --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="add_issue">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <div class="fs-4 fw-bolder">Tambah Issue</div>
                        <div class="fs-6 text-muted">Issue yang dibuat tidak akan tampil di OJS, Issue ini hanya sebagai
                            pencatatan saja.</div>
                    </div>

                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>
                <form method="post" action="{{ route('back.journal.issue.store', $journal_path) }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label required">Volume</label>
                                <input type="text" name="volume" class="form-control" value="{{ old('volume') }}"
                                    required />
                                @error('volume')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Number</label>
                                <input type="text" name="number" class="form-control" value="{{ old('number') }}"
                                    required />
                                @error('number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Year</label>
                                <input type="text" name="year" class="form-control"
                                    placeholder="{{ date('Y') }}" value="{{ old('year') }}" required />
                                @error('year')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mt-5">
                                <label class="form-label required">Title</label>
                                <input type="text" name="title" class="form-control" placeholder="Judul Issue"
                                    value="{{ old('title') }}" required />
                                @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-12 mt-5">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="6" placeholder="Deskripsi Issue">{{ old('description') }}</textarea>
                                @error('description')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--
    @foreach ($journal->issues as $issue)
        <div class="modal fade" tabindex="-1" id="edit_issue{{ $issue->id }}">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <div class="fs-4 fw-bolder">Edit Issue</div>
                            <div class="fs-6 text-muted">Issue yang dibuat tidak akan tampil di OJS, Issue ini hanya sebagai pencatatan saja.</div>
                        </div>

                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="ki-duotone
                            ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form method="post" action="{{ route('back.journal.issue.update', [$journal_path, $issue->id]) }}">
                        @csrf
                        @method('put')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label required">Volume</label>
                                    <input type="text" name="volume" class="form-control" value="{{ $issue->volume }}" required />
                                    @error('volume')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required">Number</label>
                                    <input type="text" name="number" class="form-control" value="{{ $issue->number }}" required />
                                    @error('number')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label required">Year</label>
                                    <input type="text" name="year" class="form-control" value="{{ $issue->year }}" required />
                                    @error('year')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-12 mt-5">
                                    <label class="form-label required">Title</label>
                                    <input type="text" name="title" class="form-control" value="{{ $issue->title }}" required />
                                    @error('title')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-md-12 mt-5">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="6">{{ $issue->description }}</textarea>
                                    @error('description')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-warning">update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" id="delete_issue{{ $issue->id }}">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="fs-4 fw-bolder">Hapus Issue</div>
                    </div>
                    <form method="post" action="{{ route('back.journal.issue.destroy', [$journal_path, $issue->id]) }}">
                        @csrf
                        @method('delete')
                        <div class="modal-body py-5">
                            <div class="text-center">
                                <div class="fs-4 text-danger text-bold"><b>Apakah Anda yakin?</b> </div>
                                <div class="fs-6  text-danger">Perhatian! Issue yang dihapus tidak dapat dikembalikan, termasuk semua data yang terkait.</div>
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
    @endforeach --}}
@endsection
@section('scripts')
    <script src="{{ asset('back/js/custom/apps/ecommerce/catalog/issue.js') }}"></script>
@endsection
