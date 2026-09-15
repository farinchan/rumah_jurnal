<div class="modal-header">
    <h3 class="modal-title">Submission ID {{ $submission->submission_id }}</h3>
    <div>
        <!--begin::synchronize-->
        <div class="btn btn-icon btn-sm btn-active-light-warning ms-2" data-bs-toggle="tooltip"
            data-bs-placement="top" title="Sinkronisasi Data"
            onclick="selectArticle({{ $submission->submission_id }})">
            <i class="ki-duotone ki-arrows-circle fs-1">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </div>
        <!--begin::Close-->
        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
            aria-label="Close">
            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                    class="path2"></span></i>
        </div>
        <!--end::Close-->
    </div>
</div>

<div class="modal-body">
    <ul class="nav nav-tabs nav-line-tabs mb-5 fs-6">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab"
                href="#kt_tab_pane_1_submission_{{ $submission->id }}">Informasi</a>
        </li>
        @if (($issue->author_fee ?? $journal->author_fee) != 0)
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab"
                    href="#kt_tab_pane_2_submission_{{ $submission->id }}">History Pembayaran</a>
            </li>
        @endif
        @if ($allIssues->count() > 0)
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab"
                    href="#kt_tab_pane_3_submission_{{ $submission->id }}">Pindah Issue</a>
            </li>
        @endif
    </ul>

    <div class="tab-content">
        {{-- Tab 1: Informasi --}}
        <div class="tab-pane fade show active" id="kt_tab_pane_1_submission_{{ $submission->id }}"
            role="tabpanel">
            <form
                action="{{ route('back.journal.article.update', [$journal->url_path, $issue->id, $submission->id]) }}"
                method="POST">
                @method('PUT')
                @csrf
                <div class="mh-550px scroll-y me-n7 pe-7">
                    <table
                        class="table table-row-dashed table-row-gray-300 align-top gs-0 gy-4 my-0 fs-6">
                        <tr>
                            <td>Judul</td>
                            <td>:</td>
                            <td>{{ $submission->fullTitle }}</td>
                        </tr>
                        <tr>
                            <td>Penulis</td>
                            <td>:</td>
                            <td>
                                <ul>
                                    @foreach ($submission->getAuthorsAttribute() as $author)
                                        <li>
                                            <span
                                                class="text-gray-800 fw-bold">{{ $author['name'] }}</span>
                                            <br>{{ $author['affiliation'] }}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td>Abstrak</td>
                            <td>:</td>
                            <td>{!! $submission->abstract !!}</td>
                        </tr>
                        <tr>
                            <td>Keywords</td>
                            <td>:</td>
                            <td>{{ $submission->keywords }}</td>
                        </tr>
                        <tr>
                            <td>Published</td>
                            <td>:</td>
                            <td>{{ $submission->datePublished }}</td>
                        </tr>
                        <tr>
                            <td>Terakhir Diubah</td>
                            <td>:</td>
                            <td>{{ $submission->lastModified }}</td>
                        </tr>
                        <tr>
                            <td>Editor</td>
                            <td>:</td>
                            <td>
                                <select class="form-select" data-control="select2"
                                    data-placeholder="Select an option"
                                    data-dropdown-parent="#modal_view_article"
                                    name="editor[]" data-allow-clear="true" multiple="multiple">
                                    <option></option>
                                    @foreach ($editors as $editor)
                                        <option value="{{ $editor->id }}"
                                            {{ $submission->editors->contains($editor->id) ? 'selected' : '' }}>
                                            {{ $editor->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Reviewer</td>
                            <td>:</td>
                            <td>
                                <select class="form-select" data-control="select2"
                                    data-placeholder="Select an option"
                                    data-dropdown-parent="#modal_view_article"
                                    name="reviewer[]" data-allow-clear="true" multiple="multiple">
                                    <option></option>
                                    @foreach ($reviewers as $reviewer)
                                        <option value="{{ $reviewer->id }}"
                                            {{ $submission->reviewers->contains($reviewer->id) ? 'selected' : '' }}>
                                            {{ $reviewer->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Gratis Biaya</td>
                            <td>:</td>
                            <td>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1"
                                        @if ($submission->free_charge) checked @endif
                                        id="free_charge_{{ $submission->id }}" name="free_charge" />
                                    <label class="form-check-label"
                                        for="free_charge_{{ $submission->id }}">
                                        Ya, (Gratis Biaya publikasi)
                                    </label>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="separator my-5"></div>
                <div class="text-end">
                    <button type="submit" class="btn btn-warning">
                        <i class="ki-duotone ki-check fs-4 me-1"><span class="path1"></span><span
                                class="path2"></span></i>
                        Update
                    </button>
                </div>
            </form>
        </div>

        {{-- Tab 2: History Pembayaran --}}
        @if (($issue->author_fee ?? $journal->author_fee) != 0)
            <div class="tab-pane fade" id="kt_tab_pane_2_submission_{{ $submission->id }}"
                role="tabpanel">
                @forelse ($submission->paymentInvoices as $invoice)
                    <div class="table-responsive">
                        <table class="table table-hover table-rounded table-striped border gy-7 gs-7">
                            <thead>
                                <tr
                                    class="fw-bold text-center fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                    <th colspan="4">INVOICE
                                        {{ $invoice->invoice_number }}/JRNL/UINSMDD/{{ $invoice->created_at->format('Y') }}
                                        <br>
                                        @php
                                            $percentLabel = $invoice->is_custom
                                                ? 'Custom 100%'
                                                : (is_null($invoice->payment_percent)
                                                    ? '100%'
                                                    : $invoice->payment_percent . '%');
                                        @endphp
                                        <span class="text-muted fs-7">
                                            ({{ $percentLabel }})
                                            - @money($invoice->payment_amount)
                                        </span>
                                    </th>
                                </tr>
                                <tr
                                    class="fw-semibold fs-6 text-gray-800 border-bottom-2 border-gray-200">
                                    <th>Waktu</th>
                                    <th>Pembayar</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoice->payments as $payment)
                                    <tr>
                                        <td>{{ Carbon\Carbon::parse($payment->created_at)->translatedFormat('l, d F Y H:i:s') }}
                                        </td>
                                        <td>{{ $payment->payment_account_name }}</td>
                                        <td>{{ $payment->payment_method }}</td>
                                        <td>
                                            @if ($payment->payment_status == 'pending')
                                                <span
                                                    class="badge badge-light-warning fs-7 fw-bold">{{ $payment->payment_status }}</span>
                                            @elseif ($payment->payment_status == 'accepted')
                                                <span
                                                    class="badge badge-light-success fs-7 fw-bold">{{ $payment->payment_status }}</span>
                                            @elseif ($payment->payment_status == 'rejected')
                                                <span
                                                    class="badge badge-light-danger fs-7 fw-bold">{{ $payment->payment_status }}</span>
                                            @else
                                                <span
                                                    class="badge badge-light-secondary fs-7 fw-bold">{{ $payment->payment_status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="text-center text-muted fw-semibold fs-6">
                                            Belum ada history pembayaran
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @empty
                    <div class="text-center text-muted fw-semibold fs-6 py-10">
                        Belum ada invoice yang diterbitkan
                    </div>
                @endforelse
            </div>
        @endif

        {{-- Tab 3: Pindah Issue --}}
        @if ($allIssues->count() > 0)
            <div class="tab-pane fade" id="kt_tab_pane_3_submission_{{ $submission->id }}"
                role="tabpanel">
                <div class="d-flex align-items-center bg-light-primary rounded p-4 mb-7">
                    <i class="ki-duotone ki-book-open fs-2x text-primary me-4">
                        <span class="path1"></span><span class="path2"></span>
                        <span class="path3"></span><span class="path4"></span>
                    </i>
                    <div>
                        <div class="text-gray-500 fs-7">Issue saat ini</div>
                        <div class="text-gray-800 fw-bold fs-6">
                            Vol. {{ $issue->volume }} No. {{ $issue->number }} ({{ $issue->year }})
                            &mdash; {{ $issue->title }}
                        </div>
                    </div>
                </div>

                <form
                    action="{{ route('back.journal.article.move-issue', [$journal->url_path, $issue->id, $submission->id]) }}"
                    method="POST">
                    @method('PUT')
                    @csrf
                    <div class="mb-7">
                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                            <span class="required">Pindahkan ke Issue</span>
                        </label>
                        <select class="form-select form-select-solid" name="target_issue_id" required
                            data-control="select2" data-placeholder="Pilih issue tujuan"
                            data-dropdown-parent="#modal_view_article">
                            <option></option>
                            @foreach ($allIssues as $targetIssue)
                                <option value="{{ $targetIssue->id }}">
                                    Vol. {{ $targetIssue->volume }} No. {{ $targetIssue->number }}
                                    ({{ $targetIssue->year }})
                                    - {{ $targetIssue->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div
                        class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mb-7">
                        <i class="ki-duotone ki-information-5 fs-2tx text-warning me-4">
                            <span class="path1"></span><span class="path2"></span><span
                                class="path3"></span>
                        </i>
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="fw-semibold">
                                <div class="fs-7 text-gray-700">
                                    Artikel akan dipindahkan dari issue saat ini ke issue yang dipilih.
                                    Data editor, reviewer, dan pembayaran akan tetap tersimpan.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="separator my-5"></div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-warning">
                            <i class="ki-duotone ki-arrow-right-left fs-4 me-1">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                            Pindahkan Artikel
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
