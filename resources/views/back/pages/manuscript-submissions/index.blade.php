@extends('back.app')

@section('content')
    @php
        $statusMeta = [
            'waiting' => ['label' => 'Waiting', 'class' => 'warning'],
            'under_review' => ['label' => 'Under Review', 'class' => 'info'],
            'accepted' => ['label' => 'Accepted', 'class' => 'success'],
            'rejected' => ['label' => 'Rejected', 'class' => 'danger'],
        ];
    @endphp

    <div id="kt_content_container" class="container-xxl">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-6 gap-3">
            <div>
                <h2 class="mb-1">Manuscript Submissions</h2>
                <div class="text-muted">{{ $journal->title }}</div>
            </div>
            <a href="{{ route('back.journal.index', $journal->url_path) }}" class="btn btn-light">
                <i class="ki-duotone ki-arrow-left fs-3"></i> Kembali ke Jurnal
            </a>
        </div>

        <div class="row g-4 mb-7">
            @foreach ([
                ['key' => 'waiting', 'label' => 'Waiting', 'class' => 'warning'],
                ['key' => 'under_review', 'label' => 'Under Review', 'class' => 'info'],
                ['key' => 'accepted', 'label' => 'Accepted', 'class' => 'success'],
                ['key' => 'rejected', 'label' => 'Rejected', 'class' => 'danger'],
            ] as $card)
                <div class="col-6 col-lg-3">
                    <a href="{{ route('back.journal.manuscript-submissions.index', [$journal->url_path, 'status' => $card['key']]) }}"
                        class="card card-flush h-100 text-decoration-none border border-gray-200">
                        <div class="card-body py-5">
                            <div class="fs-2hx fw-bold text-{{ $card['class'] }}">{{ $statusCounts[$card['key']] }}</div>
                            <div class="text-gray-600 fw-semibold">{{ $card['label'] }}</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>

        <div class="card card-flush">
            <div class="card-header border-0 pt-6">
                <div class="card-title w-100">
                    <form method="GET" class="row g-3 w-100 align-items-center">
                        <div class="col-md-7">
                            <div class="position-relative">
                                <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4 top-50 translate-middle-y"></i>
                                <input type="search" name="search" value="{{ $filters['search'] }}"
                                    class="form-control form-control-solid ps-12"
                                    placeholder="Cari kode, judul, penulis, email, atau institusi">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select form-select-solid">
                                <option value="">Semua status</option>
                                @foreach ($statusMeta as $value => $meta)
                                    <option value="{{ $value }}" @selected($filters['status'] === $value)>
                                        {{ $meta['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button class="btn btn-primary flex-grow-1">Filter</button>
                            @if ($filters['search'] || $filters['status'])
                                <a href="{{ route('back.journal.manuscript-submissions.index', $journal->url_path) }}"
                                    class="btn btn-light btn-icon" title="Reset filter">
                                    <i class="ki-duotone ki-cross fs-2"></i>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-body pt-4">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase">
                                <th>Submission</th>
                                <th>Penulis</th>
                                <th>Artikel</th>
                                <th>Status</th>
                                <th>Dikirim</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @forelse ($submissions as $submission)
                                @php($meta = $statusMeta[$submission->status] ?? ['label' => $submission->status, 'class' => 'secondary'])
                                <tr>
                                    <td>
                                        <div class="font-monospace text-gray-800">{{ Str::limit($submission->submission_code, 18) }}</div>
                                        <div class="text-muted fs-7">{{ $submission->article_language }}</div>
                                    </td>
                                    <td>
                                        <div class="text-gray-800">{{ $submission->first_name }} {{ $submission->last_name }}</div>
                                        <div class="text-muted fs-7">{{ $submission->email }}</div>
                                        <div class="text-muted fs-7">{{ $submission->institution }}</div>
                                    </td>
                                    <td class="mw-350px">
                                        <div class="text-gray-800 fw-bold">{{ Str::limit($submission->article_title, 100) }}</div>
                                        <div class="text-muted fs-7">{{ str($submission->article_type)->replace('_', ' ')->title() }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-light-{{ $meta['class'] }}">{{ $meta['label'] }}</span>
                                        @if ($submission->reviewer)
                                            <div class="text-muted fs-8 mt-1">oleh {{ $submission->reviewer->name }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $submission->submitted_at?->format('d M Y') }}</div>
                                        <div class="text-muted fs-7">{{ $submission->submitted_at?->format('H:i') }} WIB</div>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('back.journal.manuscript-submissions.show', [$journal->url_path, $submission->submission_code]) }}"
                                                class="btn btn-sm btn-light-primary">
                                                Detail
                                            </a>
                                            @hasrole('super-admin')
                                                <button type="button"
                                                    class="btn btn-sm btn-light-danger btn-delete-submission"
                                                    data-url="{{ route('back.journal.manuscript-submissions.destroy', [$journal->url_path, $submission->submission_code]) }}"
                                                    data-title="{{ $submission->article_title }}"
                                                    data-code="{{ $submission->submission_code }}">
                                                    Hapus
                                                </button>
                                            @endhasrole
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-10">
                                        <div class="text-gray-600 fw-semibold">Tidak ada submission yang ditemukan.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-5">
                    <div class="text-muted">
                        Menampilkan {{ $submissions->firstItem() ?? 0 }}–{{ $submissions->lastItem() ?? 0 }}
                        dari {{ $submissions->total() }} submission
                    </div>
                    {{ $submissions->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    @hasrole('super-admin')
        <div class="modal fade" tabindex="-1" id="modal_delete_submission" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Hapus Manuscript Submission</h3>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <form id="form_delete_submission" action="" method="POST">
                        @method('DELETE')
                        @csrf
                        <div class="modal-body">
                            <p>
                                Apakah Anda yakin ingin menghapus manuscript submission <strong id="delete_submission_title"></strong> (<span id="delete_submission_code"></span>)?
                                <br>
                                <span class="text-danger">
                                    <strong>Warning!</strong> Data yang sudah dihapus tidak dapat dikembalikan lagi.
                                </span>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endhasrole
@endsection

@section('scripts')
    @hasrole('super-admin')
        <script>
            $(document).on('click', '.btn-delete-submission', function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                let title = $(this).data('title');
                let code = $(this).data('code');
                $('#form_delete_submission').attr('action', url);
                $('#delete_submission_title').text(title);
                $('#delete_submission_code').text(code);
                $('#modal_delete_submission').modal('show');
            });
        </script>
    @endhasrole
@endsection
