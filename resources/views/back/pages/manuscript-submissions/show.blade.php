@extends('back.app')

@section('content')
    @php
        $statusMeta = match ($submission->status) {
            'waiting' => ['label' => 'Waiting', 'class' => 'warning'],
            'under_review' => ['label' => 'Under Review', 'class' => 'info'],
            'accepted' => ['label' => 'Accepted', 'class' => 'success'],
            'rejected' => ['label' => 'Rejected', 'class' => 'danger'],
            default => ['label' => $submission->status, 'class' => 'secondary'],
        };
        $declarations = [
            'is_original_work' => 'Artikel merupakan karya asli.',
            'not_previously_published' => 'Artikel belum pernah dipublikasikan.',
            'not_under_consideration' => 'Artikel tidak sedang dipertimbangkan jurnal lain.',
            'all_authors_approved' => 'Semua penulis menyetujui submission.',
            'authorship_information_correct' => 'Informasi dan urutan penulis sudah benar.',
            'international_authors_agreed' => 'Penulis internasional telah menyetujui pencantuman data.',
            'uses_official_template' => 'Manuskrip menggunakan template resmi jurnal.',
            'agrees_peer_review' => 'Bersedia mengikuti peer-review dan revisi.',
            'agrees_publication_process' => 'Bersedia mengikuti seluruh proses publikasi.',
            'agrees_publication_fees' => 'Bersedia membayar biaya publikasi yang berlaku.',
        ];
    @endphp

    <div id="kt_content_container" class="container-xxl">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-6 gap-3">
            <div>
                <div class="d-flex align-items-center gap-3 mb-2">
                    <h2 class="mb-0">Submission Detail</h2>
                    <span class="badge badge-light-{{ $statusMeta['class'] }}">{{ $statusMeta['label'] }}</span>
                </div>
                <div class="text-muted font-monospace">{{ $submission->submission_code }}</div>
            </div>
            <a href="{{ route('back.journal.manuscript-submissions.index', $journal->url_path) }}" class="btn btn-light">
                <i class="ki-duotone ki-arrow-left fs-3"></i> Kembali
            </a>
        </div>

        <div class="row g-6">
            <div class="col-xl-8">
                <div class="card mb-6">
                    <div class="card-header"><h3 class="card-title">A. Corresponding Author Account Information</h3></div>
                    <div class="card-body">
                        <div class="row g-5">
                            @foreach ([
                                'Full Name' => $submission->first_name.' '.$submission->last_name,
                                'Email' => $submission->email,
                                'Username' => $submission->username,
                                'WhatsApp' => $submission->whatsapp_number,
                                'Institution/Affiliation' => $submission->institution,
                                'Country' => $submission->country,
                                'ORCID iD' => $submission->orcid_id ?: '—',
                                'Scopus/Google Scholar' => $submission->scopus_or_scholar_url ?: '—',
                            ] as $label => $value)
                                <div class="col-md-6">
                                    <div class="text-muted fs-7 text-uppercase fw-bold mb-1">{{ $label }}</div>
                                    <div class="text-gray-800 text-break">{{ $value }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="card mb-6">
                    <div class="card-header"><h3 class="card-title">B. Article Information</h3></div>
                    <div class="card-body">
                        <div class="row g-5 mb-6">
                            <div class="col-md-6">
                                <div class="text-muted fs-7 text-uppercase fw-bold mb-1">Target Journal</div>
                                <div class="text-gray-800">{{ $journal->title }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted fs-7 text-uppercase fw-bold mb-1">Article Type</div>
                                <div class="text-gray-800">{{ str($submission->article_type)->replace('_', ' ')->title() }}</div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted fs-7 text-uppercase fw-bold mb-1">Language</div>
                                <div class="text-gray-800">{{ $submission->article_language }}</div>
                            </div>
                        </div>
                        <div class="mb-6">
                            <div class="text-muted fs-7 text-uppercase fw-bold mb-2">Article Title</div>
                            <div class="fs-4 fw-bold text-gray-900">{{ $submission->article_title }}</div>
                        </div>
                        <div class="mb-6">
                            <div class="text-muted fs-7 text-uppercase fw-bold mb-2">Abstract</div>
                            <div class="text-gray-800 lh-lg">{!! nl2br(e($submission->abstract)) !!}</div>
                        </div>
                        <div class="mb-6">
                            <div class="text-muted fs-7 text-uppercase fw-bold mb-2">Keywords</div>
                            @foreach ($submission->keywords as $keyword)
                                <span class="badge badge-light me-2 mb-2">{{ $keyword }}</span>
                            @endforeach
                        </div>
                        <div class="mb-6">
                            <div class="text-muted fs-7 text-uppercase fw-bold mb-2">Reference List</div>
                            <div class="bg-light rounded p-5 text-gray-800 lh-lg">{!! nl2br(e($submission->reference_list)) !!}</div>
                        </div>
                        <div class="row g-5">
                            <div class="col-md-6">
                                <div class="text-muted fs-7 text-uppercase fw-bold mb-1">Correspondence Email</div>
                                <div>{{ $submission->correspondence_email ?: $submission->email }}</div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-muted fs-7 text-uppercase fw-bold mb-1">Funding Source</div>
                                <div>{{ $submission->funding_source ?: '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-6">
                    <div class="card-header"><h3 class="card-title">C. International Authors</h3></div>
                    <div class="card-body">
                        @forelse ($submission->international_authors ?? [] as $index => $author)
                            <div class="border rounded p-5 @unless($loop->last) mb-5 @endunless">
                                <div class="fw-bold fs-5 mb-4">International Author {{ $index + 1 }}</div>
                                <div class="row g-4">
                                    @foreach ([
                                        'Institution' => $author['institution_name'] ?? '—',
                                        'Department/Faculty' => $author['department'] ?? '—',
                                        'Country' => $author['country'] ?? '—',
                                        'Institutional Email' => $author['institutional_email'] ?? '—',
                                        'ORCID/Scopus ID' => $author['orcid_or_scopus_id'] ?? '—',
                                        'Contribution' => $author['contribution'] ?? '—',
                                    ] as $label => $value)
                                        <div class="col-md-6">
                                            <div class="text-muted fs-7 text-uppercase fw-bold">{{ $label }}</div>
                                            <div class="text-gray-800 text-break">{{ $value }}</div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-4">
                                    <span class="badge badge-light-success">Consent confirmed</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-muted">Artikel ini tidak mencantumkan penulis internasional.</div>
                        @endforelse
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h3 class="card-title">D. Author Declarations</h3></div>
                    <div class="card-body">
                        @foreach ($declarations as $field => $label)
                            <div class="d-flex align-items-start gap-3 @unless($loop->last) mb-4 @endunless">
                                <i class="ki-duotone ki-check-circle fs-2 text-{{ $submission->{$field} ? 'success' : 'danger' }}"></i>
                                <span class="text-gray-800">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="card mb-6">
                    <div class="card-header"><h3 class="card-title">Editorial Review</h3></div>
                    <div class="card-body">
                        <form method="POST"
                            action="{{ route('back.journal.manuscript-submissions.status', [$journal->url_path, $submission->submission_code]) }}">
                            @csrf
                            @method('PATCH')
                            <div class="mb-5">
                                <label class="form-label required">Status</label>
                                <select name="status" id="review_status" class="form-select" required>
                                    <option value="under_review" @selected(old('status', $submission->status) === 'under_review')>Under Review</option>
                                    <option value="accepted" @selected(old('status', $submission->status) === 'accepted')>Accepted</option>
                                    <option value="rejected" @selected(old('status', $submission->status) === 'rejected')>Rejected</option>
                                </select>
                                @error('status')<div class="text-danger fs-7 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-5">
                                <label class="form-label">Internal Editor Notes</label>
                                <textarea name="editor_notes" rows="5" class="form-control"
                                    placeholder="Catatan internal, tidak dikirim kepada penulis">{{ old('editor_notes', $submission->editor_notes) }}</textarea>
                                @error('editor_notes')<div class="text-danger fs-7 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-5" id="rejection_reason_group">
                                <label class="form-label">Rejection Reason</label>
                                <textarea name="rejection_reason" rows="5" class="form-control"
                                    placeholder="Alasan ini akan dikirim kepada penulis">{{ old('rejection_reason', $submission->rejection_reason) }}</textarea>
                                @error('rejection_reason')<div class="text-danger fs-7 mt-1">{{ $message }}</div>@enderror
                            </div>
                            <div class="alert alert-light-primary">
                                Perubahan status akan dikirim kepada penulis melalui email dan WhatsApp.
                            </div>
                            @error('ojs_account')
                                <div class="alert alert-danger">
                                    <strong>Akun OJS gagal dibuat.</strong><br>{{ $message }}
                                </div>
                            @enderror
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="ki-duotone ki-check fs-2"></i> Update Status
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h3 class="card-title">Review Audit</h3></div>
                    <div class="card-body">
                        <div class="mb-4">
                            <div class="text-muted fs-7 text-uppercase fw-bold">Submitted At</div>
                            <div>{{ $submission->submitted_at?->format('d M Y H:i') }} WIB</div>
                        </div>
                        <div class="mb-4">
                            <div class="text-muted fs-7 text-uppercase fw-bold">Reviewed By</div>
                            <div>{{ $submission->reviewer?->name ?: '—' }}</div>
                            @if ($submission->reviewer?->email)
                                <div class="text-muted fs-7">{{ $submission->reviewer->email }}</div>
                            @endif
                        </div>
                        <div>
                            <div class="text-muted fs-7 text-uppercase fw-bold">Last Reviewed At</div>
                            <div>{{ $submission->reviewed_at?->format('d M Y H:i') ?: '—' }}</div>
                        </div>
                        <div class="separator my-5"></div>
                        <div class="mb-4">
                            <div class="text-muted fs-7 text-uppercase fw-bold">OJS Account</div>
                            @if ($submission->ojs_account_created_at)
                                <span class="badge badge-light-success">Created</span>
                            @else
                                <span class="badge badge-light-secondary">Not Created</span>
                            @endif
                        </div>
                        @if ($submission->ojs_account_created_at)
                            <div class="mb-4">
                                <div class="text-muted fs-7 text-uppercase fw-bold">OJS User ID</div>
                                <div>{{ $submission->ojs_user_id ?: '—' }}</div>
                            </div>
                            <div>
                                <div class="text-muted fs-7 text-uppercase fw-bold">Created At</div>
                                <div>{{ $submission->ojs_account_created_at->format('d M Y H:i') }} WIB</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const statusSelect = document.getElementById('review_status');
        const rejectionGroup = document.getElementById('rejection_reason_group');
        const rejectionInput = rejectionGroup.querySelector('textarea');

        function syncRejectionField() {
            const rejected = statusSelect.value === 'rejected';
            rejectionGroup.classList.toggle('d-none', !rejected);
            rejectionInput.required = rejected;
        }

        statusSelect.addEventListener('change', syncRejectionField);
        syncRejectionField();
    </script>
@endsection
