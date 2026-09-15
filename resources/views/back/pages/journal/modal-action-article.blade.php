<div class="modal-header">
    <h3 class="modal-title">Menu - Submission ID {{ $submission->submission_id }}</h3>
    <!--begin::Close-->
    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
        aria-label="Close">
        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                class="path2"></span></i>
    </div>
    <!--end::Close-->
</div>
<div class="modal-body">
    @if (($issue->author_fee ?? $journal->author_fee) != 0)
        <div class="mb-10">
            <div class="mb-3">
                <label class="d-flex align-items-center fs-5 fw-semibold">
                    <span class="required">Invoice</span>
                </label>
                <div class="fs-7 fw-semibold text-muted">
                    Tagihan 1 - 60% (@money(($issue->author_fee ?? $journal->author_fee) * 0.6)) -
                    @php
                        $tagihan1 = $submission->paymentInvoices
                            ->where('payment_percent', 60)
                            ->first();
                    @endphp
                    @if ($tagihan1)
                        @if ($tagihan1->is_paid)
                            <span class="text-success fs-7 fw-bold">Lunas</span>
                        @else
                            <span class="text-warning fs-7 fw-bold">Belum Dibayar</span>
                            <form action="{{ route('back.journal.invoice.destroy', $tagihan1->id) }}"
                                method="POST" class="d-inline ms-2"
                                onsubmit="return confirm('Apakah anda yakin ingin membatalkan invoice tagihan 1? Data invoice dan history pembayaran terkait akan dihapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="btn btn-sm btn-light-danger py-0 px-2 fs-8">
                                    <i class="ki-duotone ki-cross-circle fs-7"><span
                                            class="path1"></span><span class="path2"></span></i>
                                    Batalkan
                                </button>
                            </form>
                        @endif
                    @else
                        <span class="text-danger fw-bold">Belum Terbit</span>
                    @endif
                </div>
            </div>
            <div class="fv-row fv-plugins-icon-container mb-3">
                <div class="d-flex">
                    <a href="{{ route('back.journal.invoice.mail-send1', $submission->id) }}"
                        class="btn btn-light w-100 mx-3 btn-loading">
                        <i class="ki-duotone ki-send fs-2 ">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Kirim ke Author
                    </a>
                    <a href="{{ route('back.journal.invoice.generate1', $submission->id) }}"
                        class="btn btn-light w-100 mx-3">
                        <i class="ki-duotone ki-file-down fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Download
                    </a>
                </div>
            </div>
            <div class="mb-3">
                <div class="fs-7 fw-semibold text-muted">
                    Tagihan 2 - 40% (@money(($issue->author_fee ?? $journal->author_fee) * 0.4)) -
                    @php
                        $tagihan2 = $submission->paymentInvoices
                            ->where('payment_percent', 40)
                            ->first();
                    @endphp
                    @if ($tagihan2)
                        @if ($tagihan2->is_paid)
                            <span class="text-success fs-7 fw-bold">Lunas</span>
                        @else
                            <span class="text-warning fs-7 fw-bold">Belum Dibayar</span>
                            <form action="{{ route('back.journal.invoice.destroy', $tagihan2->id) }}"
                                method="POST" class="d-inline ms-2"
                                onsubmit="return confirm('Apakah anda yakin ingin membatalkan invoice tagihan 2? Data invoice dan history pembayaran terkait akan dihapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="btn btn-sm btn-light-danger py-0 px-2 fs-8">
                                    <i class="ki-duotone ki-cross-circle fs-7"><span
                                            class="path1"></span><span class="path2"></span></i>
                                    Batalkan
                                </button>
                            </form>
                        @endif
                    @else
                        <span class="text-danger fw-bold">Belum Terbit</span>
                    @endif
                </div>
            </div>
            <div class="fv-row fv-plugins-icon-container mb-3">
                <div class="d-flex">
                    <a href="{{ route('back.journal.invoice.mail-send2', $submission->id) }}"
                        class="btn btn-light w-100 mx-3 btn-loading">
                        <i class="ki-duotone ki-send fs-2 ">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Kirim ke Author
                    </a>
                    <a href="{{ route('back.journal.invoice.generate2', $submission->id) }}"
                        class="btn btn-light w-100 mx-3">
                        <i class="ki-duotone ki-file-down fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Download
                    </a>
                </div>
            </div>
            <div class="mb-3">
                <div class="fs-7 fw-semibold text-muted">
                    @php
                        $tagihan3 = $submission->paymentInvoices->first(function ($invoice) {
                            return (int) $invoice->payment_percent === 100 && !$invoice->is_custom;
                        });
                        $tagihan3_amount = $tagihan3
                            ? $tagihan3->payment_amount
                            : ($issue->author_fee ?? $journal->author_fee);
                        $tagihan_custom = $submission->paymentInvoices->first(function ($invoice) {
                            return (int) $invoice->payment_percent === 100 && $invoice->is_custom;
                        });
                    @endphp
                    Tagihan 3 (100%) (@money($tagihan3_amount)) -
                    @if ($tagihan3)
                        @if ($tagihan3->is_paid)
                            <span class="text-success fs-7 fw-bold">Lunas</span>
                        @else
                            <span class="text-warning fs-7 fw-bold">Belum Dibayar</span>
                            <form action="{{ route('back.journal.invoice.destroy', $tagihan3->id) }}"
                                method="POST" class="d-inline ms-2"
                                onsubmit="return confirm('Apakah anda yakin ingin membatalkan invoice tagihan 3? Data invoice dan history pembayaran terkait akan dihapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="btn btn-sm btn-light-danger py-0 px-2 fs-8">
                                    <i class="ki-duotone ki-cross-circle fs-7"><span
                                            class="path1"></span><span class="path2"></span></i>
                                    Batalkan
                                </button>
                            </form>
                        @endif
                    @else
                        <span class="text-danger fw-bold">Belum Terbit</span>
                    @endif
                </div>
            </div>
            <div class="fv-row fv-plugins-icon-container mb-3">
                <div class="d-flex">
                    <a href="{{ route('back.journal.invoice.mail-send3', $submission->id) }}"
                        class="btn btn-light w-100 mx-3 btn-loading">
                        <i class="ki-duotone ki-send fs-2 ">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Kirim ke Author
                    </a>
                    <a href="{{ route('back.journal.invoice.generate3', $submission->id) }}"
                        class="btn btn-light w-100 mx-3">
                        <i class="ki-duotone ki-file-down fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Download
                    </a>
                </div>
            </div>
            <div class="mb-3">
                <div class="fs-7 fw-semibold text-muted">
                    @if ($tagihan_custom)
                        Tagihan Custom (100%) (@money($tagihan_custom->payment_amount)) -
                        @if ($tagihan_custom->is_paid)
                            <span class="text-success fs-7 fw-bold">Lunas</span>
                        @else
                            <span class="text-warning fs-7 fw-bold">Belum Dibayar</span>
                            <form
                                action="{{ route('back.journal.invoice.destroy', $tagihan_custom->id) }}"
                                method="POST" class="d-inline ms-2"
                                onsubmit="return confirm('Apakah anda yakin ingin membatalkan invoice tagihan custom? Data invoice dan history pembayaran terkait akan dihapus.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="btn btn-sm btn-light-danger py-0 px-2 fs-8">
                                    <i class="ki-duotone ki-cross-circle fs-7"><span
                                            class="path1"></span><span class="path2"></span></i>
                                    Batalkan
                                </button>
                            </form>
                        @endif
                    @else
                        Tagihan Custom (100%) - <span class="text-danger fw-bold">Belum Terbit</span>
                    @endif
                </div>
            </div>

            @if ($tagihan_custom)
                <div class="fv-row fv-plugins-icon-container mb-3">
                    <div class="d-flex">
                        <a href="{{ route('back.journal.invoice.custom.mail-send', $tagihan_custom->id) }}"
                            class="btn btn-light w-100 mx-3 btn-loading">
                            <i class="ki-duotone ki-send fs-2 ">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Kirim ke Author
                        </a>
                        <a href="{{ route('back.journal.invoice.custom.generate', $tagihan_custom->id) }}"
                            class="btn btn-light w-100 mx-3">
                            <i class="ki-duotone ki-file-down fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Download
                        </a>
                    </div>
                </div>
            @else
                <div class="fv-row fv-plugins-icon-container mb-3">
                    <form action="{{ route('back.journal.invoice.custom.store', $submission->id) }}"
                        method="POST" class="d-flex align-items-center">
                        @csrf
                        <input type="number" name="custom_amount" min="1" step="1"
                            class="form-control form-control-solid me-3"
                            value="{{ $tagihan_custom ? $tagihan_custom->payment_amount : '' }}"
                            placeholder="Jumlah tagihan custom 100%" required />
                        <button type="submit" class="btn btn-light">Buat</button>
                    </form>
                </div>
            @endif
        </div>
    @endif
    <div class="mb-10">
        <div class="mb-3">
            <label class="d-flex align-items-center fs-5 fw-semibold">
                <span class="required">Letter of Acceptence (LOA)</span>
            </label>
            @php
                $check_lunas =
                    $submission->paymentInvoices->where('is_paid', true)->sum('payment_percent') >=
                    100
                        ? true
                        : false;
            @endphp
            <div class="fs-7 fw-semibold text-muted">
                @if ($submission->free_charge)
                    LOA dapat dikirim/download tanpa tagihan
                @elseif ($check_lunas)
                    <span class="text-success">Tagihan sudah lunas, LOA dapat dikirim/download</span>
                @else
                    <span class="text-danger">Tagihan belum lunas, LOA tidak dapat
                        dikirim/download</span>
                @endif
            </div>
        </div>
        <div class="fv-row fv-plugins-icon-container">
            @if ($submission->free_charge)
                <div class="d-flex">
                    <a href="{{ route('back.journal.loa.mail-send', $submission->id) }}"
                        class="btn btn-light w-100 mx-3 btn-loading">
                        <i class="ki-duotone ki-send fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Kirim ke Author
                    </a>
                    <a href="{{ route('back.journal.loa.generate', $submission->id) }}"
                        class="btn btn-light w-100 mx-3 ">
                        <i class="ki-duotone ki-file-down fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Download
                    </a>
                </div>
            @else
                @if ($check_lunas)
                    <div class="d-flex">
                        <a href="{{ route('back.journal.loa.mail-send', $submission->id) }}"
                            class="btn btn-light w-100 mx-3 btn-loading">
                            <i class="ki-duotone ki-send fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Kirim ke Author
                        </a>
                        <a href="{{ route('back.journal.loa.generate', $submission->id) }}"
                            class="btn btn-light w-100 mx-3 ">
                            <i class="ki-duotone ki-file-down fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Download
                        </a>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
