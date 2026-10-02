<!-- Modal Sinkronisasi Pembayaran -->
<div class="modal fade" id="modal_sync_payments" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header pb-0 border-0 justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="symbol symbol-45px symbol-circle bg-light-success text-success d-flex align-items-center justify-content-center">
                        <i class="ki-duotone ki-arrows-circle fs-1 text-success">
                            <span class="path1"></span><span class="path2"></span>
                        </i>
                    </div>
                    <div>
                        <h3 class="modal-title fw-bold text-gray-900 mb-0">Sinkronisasi Status Pembayaran</h3>
                        <span class="text-muted fs-7" id="sync_modal_subtitle">Memeriksa kelengkapan pembayaran artikel (100%) dan memperbarui status menjadi Paid</span>
                    </div>
                </div>
                <div class="btn btn-sm btn-icon btn-active-color-primary" id="btn_close_sync_modal" data-bs-dismiss="modal">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body py-lg-8 px-lg-10">
                <!-- 1. LOADING STATE -->
                <div id="sync_loading_state" class="text-center py-10">
                    <div class="spinner-border text-primary mb-4" style="width: 3.5rem; height: 3.5rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h4 class="fw-bold text-gray-800 mb-2">Sedang Melakukan Sinkronisasi...</h4>
                    <p class="text-gray-500 fs-7 mb-4" id="sync_loading_msg">Memeriksa invoice dan bukti pembayaran artikel yang sudah lunas (100%)...</p>
                    <div class="progress h-8px w-100 max-w-400px mx-auto bg-light-primary rounded">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 100%"></div>
                    </div>
                    <div class="text-muted fs-8 mt-3 fst-italic">Mohon tunggu sebentar, sistem sedang memverifikasi dan memperbarui data artikel.</div>
                </div>

                <!-- 2. REPORT / RESULT STATE -->
                <div id="sync_report_state" class="d-none">
                    <!-- Status Alert Banner -->
                    <div id="sync_alert_container" class="mb-5"></div>

                    <!-- Summary Cards Grid -->
                    <div class="row g-3 mb-6">
                        <div class="col-6 col-md-3">
                            <div class="card card-bordered p-3 text-center bg-light">
                                <span class="fs-8 text-gray-500 fw-bold text-uppercase d-block mb-1">Total Diperiksa</span>
                                <span class="fs-2 fw-bolder text-gray-900" id="sync_stat_total">0</span>
                                <span class="fs-9 text-muted">Artikel</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card card-bordered p-3 text-center bg-light-success border-success">
                                <span class="fs-8 text-success fw-bold text-uppercase d-block mb-1">Diperbarui (Paid)</span>
                                <span class="fs-2 fw-bolder text-success" id="sync_stat_updated">0</span>
                                <span class="fs-9 text-success fw-semibold">Pending ➔ Paid</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card card-bordered p-3 text-center bg-light">
                                <span class="fs-8 text-gray-500 fw-bold text-uppercase d-block mb-1">Sudah Lunas</span>
                                <span class="fs-2 fw-bolder text-primary" id="sync_stat_already_paid">0</span>
                                <span class="fs-9 text-muted">Sebelumnya</span>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card card-bordered p-3 text-center bg-light">
                                <span class="fs-8 text-gray-500 fw-bold text-uppercase d-block mb-1">Belum Lunas</span>
                                <span class="fs-2 fw-bolder text-warning" id="sync_stat_incomplete">0</span>
                                <span class="fs-9 text-muted">Kurang / Belum</span>
                            </div>
                        </div>
                    </div>

                    <!-- Free charge note if any -->
                    <div id="sync_free_charge_note" class="d-none mb-4 text-muted fs-8 fst-italic">
                        * Terdapat <span id="sync_stat_free_charge" class="fw-bold text-gray-700">0</span> artikel Bebas Biaya (Free Charge) yang tidak memerlukan pembayaran.
                    </div>

                    <!-- Updated Articles Table -->
                    <div id="sync_updated_articles_section" class="d-none">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h5 class="fw-bold text-gray-800 mb-0">Rincian Artikel yang Diperbarui:</h5>
                            <span class="badge badge-light-success fw-bold" id="sync_updated_badge">0 Artikel</span>
                        </div>
                        <div class="table-responsive mh-250px scroll-y border rounded">
                            <table class="table table-row-dashed table-row-gray-200 align-middle gs-3 gy-2 mb-0 fs-8">
                                <thead class="bg-light text-uppercase text-gray-500 fw-bold fs-9 sticky-top">
                                    <tr>
                                        <th class="w-40px text-center">No</th>
                                        <th class="min-w-150px">ID & Judul</th>
                                        <th class="min-w-120px">Penulis</th>
                                        <th class="min-w-120px">Jurnal / Edisi</th>
                                        <th class="min-w-100px text-center">Perubahan Status</th>
                                        <th class="min-w-90px text-end">Total Bayar</th>
                                    </tr>
                                </thead>
                                <tbody id="sync_updated_articles_tbody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 3. ERROR STATE -->
                <div id="sync_error_state" class="d-none text-center py-8">
                    <i class="ki-duotone ki-cross-circle fs-3x text-danger mb-3"><span class="path1"></span><span class="path2"></span></i>
                    <h4 class="fw-bold text-gray-800 mb-1">Gagal Melakukan Sinkronisasi</h4>
                    <p class="text-muted fs-7 mb-4" id="sync_error_msg">Terjadi kesalahan pada server saat sinkronisasi.</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer py-3">
                <button type="button" class="btn btn-sm btn-light" id="btn_sync_modal_close" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-sm btn-success d-none" id="btn_sync_retry">
                    <i class="ki-duotone ki-arrows-circle fs-4 me-1"><span class="path1"></span><span class="path2"></span></i> Sinkronisasi Ulang
                </button>
            </div>
        </div>
    </div>
</div>

<script>
window.initPaymentSyncModal = function(options) {
    const triggerSelector = options.triggerSelector || '#btn_sync_payments';
    const getPayload = options.getPayload || function() { return {}; };
    const onFinished = options.onFinished || function(count) {};

    $(document).on('click', triggerSelector, function(e) {
        e.preventDefault();

        const modalEl = document.getElementById('modal_sync_payments');
        if (!modalEl) return;
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();

        // UI Reset
        $('#sync_loading_state').removeClass('d-none');
        $('#sync_report_state').addClass('d-none');
        $('#sync_error_state').addClass('d-none');
        $('#btn_close_sync_modal, #btn_sync_modal_close').prop('disabled', true);
        $('#btn_sync_retry').addClass('d-none');

        const payload = typeof getPayload === 'function' ? getPayload() : getPayload;

        $.ajax({
            url: "{{ route('back.journal.sync-payments') }}",
            type: 'POST',
            data: Object.assign({
                _token: '{{ csrf_token() }}'
            }, payload),
            success: function(res) {
                $('#sync_loading_state').addClass('d-none');
                $('#btn_close_sync_modal, #btn_sync_modal_close').prop('disabled', false);

                if (!res.success) {
                    $('#sync_error_state').removeClass('d-none');
                    $('#sync_error_msg').text(res.message || 'Gagal melakukan sinkronisasi.');
                    $('#btn_sync_retry').removeClass('d-none');
                    return;
                }

                const summary = res.summary || {};
                const updatedArticles = res.updated_articles || [];

                $('#sync_stat_total').text((summary.total_checked || 0).toLocaleString('id-ID'));
                $('#sync_stat_updated').text((summary.updated_count || 0).toLocaleString('id-ID'));
                $('#sync_stat_already_paid').text((summary.already_paid_count || 0).toLocaleString('id-ID'));
                $('#sync_stat_incomplete').text((summary.incomplete_count || 0).toLocaleString('id-ID'));

                if (summary.free_charge_count > 0) {
                    $('#sync_free_charge_note').removeClass('d-none');
                    $('#sync_stat_free_charge').text(summary.free_charge_count);
                } else {
                    $('#sync_free_charge_note').addClass('d-none');
                }

                if (summary.updated_count > 0) {
                    $('#sync_alert_container').html(`
                        <div class="alert alert-success d-flex align-items-center p-4">
                            <i class="ki-duotone ki-check-circle fs-2hx text-success me-3"><span class="path1"></span><span class="path2"></span></i>
                            <div>
                                <h5 class="mb-1 text-success fw-bold">Sinkronisasi Berhasil!</h5>
                                <span class="fs-7">Sebanyak <strong>${summary.updated_count} artikel</strong> yang pembayarannya sudah 100% telah diperbarui statusnya dari <strong>Pending</strong> menjadi <strong class="text-success">Paid</strong>.</span>
                            </div>
                        </div>
                    `);

                    let rowsHtml = '';
                    updatedArticles.forEach((art, idx) => {
                        rowsHtml += `
                            <tr>
                                <td class="text-center fw-bold text-gray-600">${idx + 1}</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-gray-900">${escapeSyncHtml(art.submission_id)}</span>
                                        <span class="text-muted fs-9">${escapeSyncHtml(art.title)}</span>
                                    </div>
                                </td>
                                <td><span class="text-gray-700">${escapeSyncHtml(art.author)}</span></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-gray-800 fw-semibold">${escapeSyncHtml(art.journal)}</span>
                                        <span class="text-muted fs-9">${escapeSyncHtml(art.edition)}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light-warning fs-9">Pending</span>
                                    <i class="ki-duotone ki-arrow-right fs-9 mx-1 text-gray-400"></i>
                                    <span class="badge badge-success fs-9">Paid</span>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    Rp ${(art.paid_amount || 0).toLocaleString('id-ID')}
                                </td>
                            </tr>
                        `;
                    });
                    $('#sync_updated_articles_tbody').html(rowsHtml);
                    $('#sync_updated_badge').text(summary.updated_count + ' Artikel');
                    $('#sync_updated_articles_section').removeClass('d-none');
                } else {
                    $('#sync_alert_container').html(`
                        <div class="alert alert-info d-flex align-items-center p-4">
                            <i class="ki-duotone ki-information-5 fs-2hx text-info me-3"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            <div>
                                <h5 class="mb-1 text-info fw-bold">Semua Data Sudah Sinkron</h5>
                                <span class="fs-7">Seluruh artikel yang pembayarannya telah 100% sudah berstatus <strong>Paid</strong>. Tidak ada artikel pending yang perlu diperbarui.</span>
                            </div>
                        </div>
                    `);
                    $('#sync_updated_articles_section').addClass('d-none');
                }

                $('#sync_report_state').removeClass('d-none');

                if (typeof onFinished === 'function') {
                    onFinished(summary.updated_count);
                }
            },
            error: function(xhr) {
                $('#sync_loading_state').addClass('d-none');
                $('#btn_close_sync_modal, #btn_sync_modal_close').prop('disabled', false);
                $('#btn_sync_retry').removeClass('d-none');
                $('#sync_error_state').removeClass('d-none');
                let errMsg = 'Terjadi kesalahan saat menghubungi server.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                $('#sync_error_msg').text(errMsg);
            }
        });
    });

    $(document).on('click', '#btn_sync_retry', function() {
        $(triggerSelector).trigger('click');
    });

    function escapeSyncHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
};
</script>
