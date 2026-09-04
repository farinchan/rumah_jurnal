@extends('back.app')

@section('styles')
<style>
    .card-hover-interactive {
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .card-hover-interactive:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08) !important;
    }
</style>
@endsection

@section('content')
    <div id="kt_content_container" class="container-xxl">

        {{-- Header & Dropdown Toolbar --}}
        <div class="card card-flush mb-5 mb-xl-8">
            <div class="card-body py-5">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="symbol symbol-50px symbol-circle bg-light-primary text-primary d-flex align-items-center justify-content-center flex-shrink-0">
                            <i class="ki-duotone ki-book-open fs-2x text-primary">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </div>
                        <div>
                            <h2 class="text-gray-900 fw-bold mb-1" id="journal_name_display">Dashboard Jurnal</h2>
                            <div class="text-gray-500 fs-7 d-flex flex-wrap align-items-center gap-2">
                                <span>Pilih jurnal & edisi untuk melihat statistik artikel dan rekap data</span>
                                <span class="badge badge-light-primary fw-bold" id="journal_fee_badge">Author Fee: Rp 0</span>
                                <span class="badge badge-light-info fw-bold d-none" id="filtered_issue_badge">Issue: -</span>
                            </div>
                        </div>
                    </div>

                    {{-- Dropdown Jurnal, Dropdown Issue & Refresh (1 Baris) --}}
                    <div class="d-flex flex-nowrap align-items-end gap-2 gap-sm-3 flex-shrink-0">
                        <div class="d-flex flex-column">
                            <label class="text-gray-600 fs-8 fw-bold mb-1">PILIH JURNAL</label>
                            <div class="position-relative w-160px w-sm-200px w-md-250px w-lg-280px">
                                <select id="journal_select" class="form-select form-select-solid form-select-sm fw-bold"
                                    data-control="select2" data-placeholder="Pilih Jurnal">
                                    @forelse ($grouped_journals as $typeLabel => $journalGroup)
                                        <optgroup label="{{ $typeLabel }}">
                                            @foreach ($journalGroup as $item)
                                                <option value="{{ $item->id }}" @if ($selected_journal_id == $item->id) selected @endif>
                                                    {{ $item->name }} ({{ $item->url_path }})
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @empty
                                        <option value="">Tidak ada jurnal yang tersedia</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        <div class="d-flex flex-column">
                            <label class="text-gray-600 fs-8 fw-bold mb-1">FILTER ISSUE</label>
                            <div class="position-relative w-140px w-sm-180px w-md-200px w-lg-220px">
                                <select id="issue_select" class="form-select form-select-solid form-select-sm fw-bold"
                                    data-control="select2" data-placeholder="Semua Issue">
                                    <option value="all" @if(empty($selected_issue_id) || $selected_issue_id === 'all') selected @endif>Semua Issue</option>
                                    @if(isset($initial_issues))
                                        @foreach ($initial_issues as $iss)
                                            <option value="{{ $iss->id }}" @if(isset($selected_issue_id) && $selected_issue_id == $iss->id) selected @endif>
                                                Vol. {{ $iss->volume }} No. {{ $iss->number }} ({{ $iss->year ?? '-' }})@if(!empty($iss->title) && $iss->title !== '-') - {{ Str::limit($iss->title, 25) }}@endif
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div>
                            <button type="button" id="btn_refresh" class="btn btn-sm btn-icon btn-light-primary h-38px w-38px flex-shrink-0" title="Muat Ulang Data">
                                <i class="ki-duotone ki-arrows-circle fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loading Spinner Overlay Wrapper --}}
        <div id="dashboard_content_area" class="position-relative">
            <div id="dashboard_loading_overlay" class="d-none position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex align-items-center justify-content-center z-index-3 rounded">
                <div class="d-flex flex-column align-items-center">
                    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="text-gray-700 fw-bold fs-6">Memuat data jurnal...</span>
                </div>
            </div>

            {{-- Row 1: Artikel & Rekap Pembayaran --}}
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                {{-- Total Artikel --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-flush h-100 border-start border-1 border-primary shadow-sm">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-gray-900 lh-1" id="stat_total_articles">0</span>
                                <span class="text-gray-500 pt-1 fw-semibold fs-6">Total Artikel Masuk</span>
                            </div>
                            <div class="symbol symbol-45px">
                                <div class="symbol-label bg-light-primary">
                                    <i class="ki-duotone ki-document fs-2x text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-5">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge badge-light-success fs-8 fw-bold">
                                    <i class="ki-duotone ki-check fs-8 me-1 text-success"></i>
                                    <span id="stat_published_articles">0</span> Publish
                                </span>
                                <span class="badge badge-light-warning fs-8 fw-bold">
                                    <i class="ki-duotone ki-time fs-8 me-1 text-warning"></i>
                                    <span id="stat_unpublished_articles">0</span> Belum Publish
                                </span>
                            </div>
                            <div class="progress h-6px bg-light-warning">
                                <div id="stat_published_bar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                            </div>
                            <span class="text-gray-400 fs-8 mt-1 d-block text-end" id="stat_published_percent">0% telah terbit</span>
                        </div>
                    </div>
                </div>

                {{-- Status Lunas --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-flush h-100 border-start border-1 border-success shadow-sm">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-success lh-1" id="stat_lunas_count">0</span>
                                <span class="text-gray-500 pt-1 fw-semibold fs-6">Artikel Lunas (100%)</span>
                            </div>
                            <div class="symbol symbol-45px">
                                <div class="symbol-label bg-light-success">
                                    <i class="ki-duotone ki-check-circle fs-2x text-success">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-5">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-gray-600 fs-7">Uang Diterima:</span>
                                <span class="fw-bold text-gray-900 fs-7" id="stat_lunas_amount">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-gray-600 fs-7">Bebas Biaya (Free):</span>
                                <span class="badge badge-light-primary fw-bold fs-8" id="stat_free_count">0 Artikel</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Belum Lunas (DP/Cicil) --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-flush h-100 border-start border-1 border-warning shadow-sm card-hover-interactive"
                        id="card_belum_lunas" role="button" tabindex="0" title="Klik untuk melihat rincian submission belum lunas">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-warning lh-1" id="stat_belum_lunas_count">0</span>
                                <span class="text-gray-500 pt-1 fw-semibold fs-6">Belum Lunas (DP/Cicil)</span>
                            </div>
                            <div class="symbol symbol-45px">
                                <div class="symbol-label bg-light-warning">
                                    <i class="ki-duotone ki-bill fs-2x text-warning">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                    </i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-gray-600 fs-7">Sudah Masuk:</span>
                                <span class="fw-bold text-success fs-7" id="stat_belum_lunas_paid">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-gray-600 fs-7">Sisa Tagihan:</span>
                                <span class="fw-bold text-danger fs-7" id="stat_belum_lunas_remaining">Rp 0</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-2 border-top border-gray-200">
                                <span class=" fs-8 fw-semibold">Rincian Submission</span>
                                <i class="ki-duotone ki-arrow-right fs-7 ">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Belum Bayar --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-flush h-100 border-start border-1 border-danger shadow-sm card-hover-interactive"
                        id="card_belum_bayar" role="button" tabindex="0" title="Klik untuk melihat rincian submission belum bayar">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <span class="fs-2hx fw-bold text-danger lh-1" id="stat_belum_bayar_count">0</span>
                                <span class="text-gray-500 pt-1 fw-semibold fs-6">Belum Bayar (0%)</span>
                            </div>
                            <div class="symbol symbol-45px">
                                <div class="symbol-label bg-light-danger">
                                    <i class="ki-duotone ki-cross-circle fs-2x text-danger">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-2 pb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-gray-600 fs-7">Total Piutang:</span>
                                <span class="fw-bold text-danger fs-7" id="stat_belum_bayar_amount">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-gray-600 fs-7">Status:</span>
                                <span class="badge badge-light-danger fs-8 fw-bold">Menunggu Pembayaran</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between pt-2 border-top border-gray-200">
                                <span class=" fs-8 fw-semibold">Rincian Submission</span>
                                <i class="ki-duotone ki-arrow-right fs-7 ">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Row 2: Keuangan & Naskah Masuk --}}
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                {{-- Total Pendapatan Masuk --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-flush bg-light-success border border-success border-dashed h-100">
                        <div class="card-body py-4">
                            <span class="text-success fw-semibold fs-7 d-block">TOTAL PEMASUKAN DITERIMA</span>
                            <span class="fs-2x fw-bold text-gray-900 d-block mt-1" id="stat_total_paid_received">Rp 0</span>
                            <span class="text-gray-600 fs-8">Dari artikel lunas dan cicilan</span>
                        </div>
                    </div>
                </div>

                {{-- Total Piutang / Belum Terbayar --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-flush bg-light-danger border border-danger border-dashed h-100">
                        <div class="card-body py-4">
                            <span class="text-danger fw-semibold fs-7 d-block">SISA PIUTANG / BELUM TERBAYAR</span>
                            <span class="fs-2x fw-bold text-gray-900 d-block mt-1" id="stat_total_outstanding">Rp 0</span>
                            <span class="text-gray-600 fs-8">Belum bayar + sisa cicilan</span>
                        </div>
                    </div>
                </div>

                {{-- Total Potensi Omzet --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-flush bg-light-primary border border-primary border-dashed h-100">
                        <div class="card-body py-4">
                            <span class="text-primary fw-semibold fs-7 d-block">TOTAL POTENSI PENDAPATAN</span>
                            <span class="fs-2x fw-bold text-gray-900 d-block mt-1" id="stat_total_potential">Rp 0</span>
                            <span class="text-gray-600 fs-8">Jika seluruh artikel melunasi fee</span>
                        </div>
                    </div>
                </div>

                {{-- Total Issue & Waiting Submissions --}}
                <div class="col-sm-6 col-xl-3">
                    <div class="card card-flush bg-light-info border border-info border-dashed h-100">
                        <div class="card-body py-4">
                            <span class="text-info fw-semibold fs-7 d-block">EDISI & NASKAH MENUNGGU</span>
                            <div class="d-flex align-items-center justify-content-between mt-1">
                                <div>
                                    <span class="fs-2x fw-bold text-gray-900" id="stat_total_issues">0</span>
                                    <span class="fs-8 text-gray-600 ms-1">Edisi</span>
                                </div>
                                <div>
                                    <span class="badge badge-info fs-7 fw-bold" id="stat_waiting_count">0 Naskah Baru</span>
                                </div>
                            </div>
                            <span class="text-gray-600 fs-8" id="stat_waiting_detail">Waiting: 0 | Under Review: 0</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Row 3: Grafik 1 (Artikel per Issue) & Grafik 2 (Donut Pembayaran) --}}
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                {{-- Grafik Artikel Publish vs Belum Publish per Edisi --}}
                <div class="col-xl-8">
                    <div class="card card-flush h-100 shadow-sm">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <h3 class="card-label fw-bold text-gray-900 fs-4">Statistik Artikel Publish vs Belum Publish per Edisi</h3>
                                <span class="text-gray-500 fs-7">Perbandingan jumlah artikel terbit dan dalam proses per edisi/issue</span>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div id="chart_articles_per_issue" style="min-height: 350px;"></div>
                            <div id="empty_chart_issue" class="d-none text-center py-10 text-gray-500">
                                <i class="ki-duotone ki-chart-simple fs-3x text-gray-400 mb-2"></i>
                                <p class="mb-0">Belum ada data edisi/issue untuk ditampilkan pada grafik ini.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Donut Chart: Rekap Status Pembayaran --}}
                <div class="col-xl-4">
                    <div class="card card-flush h-100 shadow-sm">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <h3 class="card-label fw-bold text-gray-900 fs-4">Distribusi Status Pembayaran</h3>
                                <span class="text-gray-500 fs-7">Proporsi lunas, belum lunas, belum bayar, dan free</span>
                            </div>
                        </div>
                        <div class="card-body pt-0 d-flex flex-column justify-content-between">
                            <div id="chart_payment_distribution" style="min-height: 280px;"></div>
                            <div class="mt-4 pt-4 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="d-flex align-items-center fs-7 text-gray-700">
                                        <span class="bullet bullet-dot bg-success me-2"></span>Lunas
                                    </span>
                                    <span class="fw-bold fs-7 text-gray-900" id="legend_lunas">0 (Rp 0)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="d-flex align-items-center fs-7 text-gray-700">
                                        <span class="bullet bullet-dot bg-warning me-2"></span>Belum Lunas
                                    </span>
                                    <span class="fw-bold fs-7 text-gray-900" id="legend_belum_lunas">0 (Rp 0)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="d-flex align-items-center fs-7 text-gray-700">
                                        <span class="bullet bullet-dot bg-danger me-2"></span>Belum Bayar
                                    </span>
                                    <span class="fw-bold fs-7 text-gray-900" id="legend_belum_bayar">0 (Rp 0)</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="d-flex align-items-center fs-7 text-gray-700">
                                        <span class="bullet bullet-dot bg-primary me-2"></span>Free Charge
                                    </span>
                                    <span class="fw-bold fs-7 text-gray-900" id="legend_free">0 Artikel</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Row 4: Grafik 3 (Tren per Tahun) & Grafik 4 (Status Naskah) --}}
            <div class="row g-5 g-xl-8 mb-5 mb-xl-8">
                {{-- Tren Publikasi per Tahun --}}
                <div class="col-xl-8">
                    <div class="card card-flush h-100 shadow-sm">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <h3 class="card-label fw-bold text-gray-900 fs-4">Tren Publikasi per Tahun</h3>
                                <span class="text-gray-500 fs-7">Jumlah publikasi dan naskah diproses berdasarkan tahun terbit</span>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div id="chart_articles_per_year" style="min-height: 320px;"></div>
                            <div id="empty_chart_year" class="d-none text-center py-10 text-gray-500">
                                <i class="ki-duotone ki-chart-line fs-3x text-gray-400 mb-2"></i>
                                <p class="mb-0">Belum ada data tahunan untuk ditampilkan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Donut Chart: Status Naskah Keseluruhan --}}
                <div class="col-xl-4">
                    <div class="card card-flush h-100 shadow-sm">
                        <div class="card-header pt-5">
                            <div class="card-title d-flex flex-column">
                                <h3 class="card-label fw-bold text-gray-900 fs-4">Status Naskah & Alur</h3>
                                <span class="text-gray-500 fs-7">Distribusi artikel terbit, proses, dan naskah baru</span>
                            </div>
                        </div>
                        <div class="card-body pt-0 d-flex flex-column justify-content-between">
                            <div id="chart_article_status" style="min-height: 280px;"></div>
                            <div class="mt-4 pt-4 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="d-flex align-items-center fs-7 text-gray-700">
                                        <span class="bullet bullet-dot bg-success me-2"></span>Published
                                    </span>
                                    <span class="fw-bold fs-7 text-gray-900" id="legend_stat_published">0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="d-flex align-items-center fs-7 text-gray-700">
                                        <span class="bullet bullet-dot bg-warning me-2"></span>Belum Publish
                                    </span>
                                    <span class="fw-bold fs-7 text-gray-900" id="legend_stat_unpublished">0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="d-flex align-items-center fs-7 text-gray-700">
                                        <span class="bullet bullet-dot bg-info me-2"></span>Naskah Baru (Waiting)
                                    </span>
                                    <span class="fw-bold fs-7 text-gray-900" id="legend_stat_waiting">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Row 5: Tabel Rekapitulasi per Edisi (Issue) --}}
            <div class="card card-flush shadow-sm mb-5 mb-xl-8">
                <div class="card-header pt-5">
                    <div class="card-title d-flex flex-column">
                        <h3 class="card-label fw-bold text-gray-900 fs-4">Rekapitulasi Artikel & Pembayaran per Edisi (Issue)</h3>
                        <span class="text-gray-500 fs-7">Rincian status publikasi, pembayaran, dan realisasi pendapatan per edisi</span>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-4 gy-4">
                            <thead>
                                <tr class="fw-bold fs-7 text-gray-500 text-uppercase bg-light">
                                    <th class="min-w-40px text-center">No</th>
                                    <th class="min-w-175px">Edisi / Issue</th>
                                    <th class="min-w-80px text-center">Tahun</th>
                                    <th class="min-w-100px text-end">Author Fee</th>
                                    <th class="min-w-90px text-center">Total Artikel</th>
                                    <th class="min-w-90px text-center">Publish</th>
                                    <th class="min-w-90px text-center">Belum Publish</th>
                                    <th class="min-w-80px text-center">Lunas</th>
                                    <th class="min-w-80px text-center">Belum Lunas</th>
                                    <th class="min-w-80px text-center">Belum Bayar</th>
                                    <th class="min-w-80px text-center">Free</th>
                                    <th class="min-w-120px text-end">Uang Masuk</th>
                                    <th class="min-w-80px text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="table_issues_body">
                                <tr>
                                    <td colspan="13" class="text-center py-5 text-gray-500">
                                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                                        Memuat data tabel edisi...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Modal Detail Submissions (Belum Lunas & Belum Bayar) -->
    <div class="modal fade" id="modal_submission_payment_detail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header pb-0 border-0 justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="symbol symbol-40px" id="modal_type_symbol">
                                <span class="symbol-label bg-light-warning">
                                    <i class="ki-duotone ki-bill fs-2 text-warning">
                                        <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span>
                                    </i>
                                </span>
                            </div>
                            <div>
                                <h3 class="modal-title fw-bold text-gray-900 mb-0" id="modal_submission_title">Daftar Submission</h3>
                                <span class="text-muted fs-7 fw-semibold" id="modal_submission_subtitle">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>

                <!-- Modal Subheader / Summary & Search -->
                <div class="px-7 pt-4 pb-3 border-bottom">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 bg-light rounded p-3">
                        <div class="d-flex flex-wrap align-items-center gap-3 gap-md-4">
                            <div>
                                <span class="text-gray-500 fs-8 fw-bold d-block">TOTAL NASKAH</span>
                                <span class="fw-bold fs-6 text-gray-900" id="modal_summary_count">0 Naskah</span>
                            </div>
                            <div class="bullet bullet-vertical h-20px bg-gray-300"></div>
                            <div>
                                <span class="text-gray-500 fs-8 fw-bold d-block">TOTAL BIAYA</span>
                                <span class="fw-bold fs-6 text-gray-900" id="modal_summary_fee">Rp 0</span>
                            </div>
                            <div class="bullet bullet-vertical h-20px bg-gray-300 col-modal-paid" id="modal_summary_paid_sep"></div>
                            <div class="col-modal-paid" id="modal_summary_paid_box">
                                <span class="text-gray-500 fs-8 fw-bold d-block">SUDAH MASUK</span>
                                <span class="fw-bold fs-6 text-success" id="modal_summary_paid">Rp 0</span>
                            </div>
                            <div class="bullet bullet-vertical h-20px bg-gray-300"></div>
                            <div>
                                <span class="text-gray-500 fs-8 fw-bold d-block" id="modal_summary_remaining_label">SISA TAGIHAN</span>
                                <span class="fw-bold fs-6 text-danger" id="modal_summary_remaining">Rp 0</span>
                            </div>
                        </div>

                        <!-- Live Search Input -->
                        <div class="position-relative w-100 w-md-250px">
                            <i class="ki-duotone ki-magnifier fs-4 text-gray-500 position-absolute top-50 translate-middle-y ms-3">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                            <input type="text" id="modal_submission_search" class="form-control form-control-solid form-control-sm ps-9" placeholder="Cari naskah / penulis...">
                        </div>
                    </div>
                </div>

                <div class="modal-body py-4">
                    <!-- Loading State -->
                    <div id="modal_submission_loading" class="text-center py-10">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div class="text-gray-600 fw-semibold fs-7 mt-3">Memuat data submission via API...</div>
                    </div>

                    <!-- Error State -->
                    <div id="modal_submission_error" class="alert alert-danger d-none my-4">
                        <div class="d-flex align-items-center">
                            <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4"><span class="path1"></span><span class="path2"></span></i>
                            <div class="d-flex flex-column">
                                <h5 class="mb-1 text-danger">Terjadi Kesalahan</h5>
                                <span id="modal_submission_error_text" class="fs-7">Gagal memuat data submission.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Table Content -->
                    <div id="modal_submission_content" class="table-responsive d-none">
                        <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-3 fs-7" id="modal_submission_table">
                            <thead>
                                <tr class="fw-bold text-muted bg-light">
                                    <th class="ps-4 min-w-40px text-center rounded-start">#</th>
                                    <th class="min-w-80px">ID</th>
                                    <th class="min-w-260px">Judul & Penulis</th>
                                    <th class="min-w-150px">Edisi</th>
                                    <th class="min-w-100px text-center">Status Naskah</th>
                                    <th class="min-w-100px text-end">Total Biaya</th>
                                    <th class="min-w-110px text-end col-modal-paid">Sudah Masuk</th>
                                    <th class="min-w-110px text-end">Sisa Tagihan</th>
                                    <th class="min-w-140px">Invoice</th>
                                    <th class="min-w-80px text-center pe-4 rounded-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="modal_submission_tbody">
                                <!-- Injected via JavaScript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Empty State -->
                    <div id="modal_submission_empty" class="text-center py-10 d-none">
                        <div class="symbol symbol-60px symbol-circle  mb-3 mx-auto d-flex align-items-center justify-content-center">
                            <i class="ki-duotone ki-check-circle fs-2x text-success">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </div>
                        <div class="fs-5 fw-bold text-gray-800" id="modal_submission_empty_title">Tidak Ada Submission</div>
                        <div class="text-gray-500 fs-7" id="modal_submission_empty_text">Tidak ada data submission yang cocok dengan kriteria ini.</div>
                    </div>
                </div>

                <div class="modal-footer py-3">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const journalSelect = document.getElementById('journal_select');
    const issueSelect = document.getElementById('issue_select');
    const btnRefresh = document.getElementById('btn_refresh');
    const loadingOverlay = document.getElementById('dashboard_loading_overlay');
    let isUpdatingIssueDropdown = false;

    function formatRupiah(amount) {
        return 'Rp ' + parseInt(amount || 0).toLocaleString('id-ID');
    }

    // 1. Chart: Articles Per Issue (Bar/Column)
    const chartArticlesPerIssueOptions = {
        series: [{
            name: 'Published',
            data: []
        }, {
            name: 'Belum Publish',
            data: []
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: { show: true }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                borderRadius: 5
            },
        },
        dataLabels: { enabled: false },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        colors: ['#50CD89', '#FFC700'],
        xaxis: {
            categories: [],
            labels: {
                rotate: -35,
                maxHeight: 100,
                style: { fontSize: '11px' }
            }
        },
        yaxis: {
            title: { text: 'Jumlah Artikel' },
            labels: {
                formatter: function (val) { return parseInt(val || 0); }
            }
        },
        fill: { opacity: 1 },
        legend: { position: 'top' },
        tooltip: {
            y: {
                formatter: function (val) {
                    return parseInt(val || 0) + ' Artikel';
                }
            }
        }
    };
    const chartArticlesPerIssue = new ApexCharts(document.querySelector("#chart_articles_per_issue"), chartArticlesPerIssueOptions);
    chartArticlesPerIssue.render();

    // 2. Chart: Payment Distribution (Donut)
    const chartPaymentDistributionOptions = {
        series: [0, 0, 0, 0],
        chart: {
            type: 'donut',
            height: 280
        },
        labels: ['Lunas', 'Belum Lunas', 'Belum Bayar', 'Free Charge'],
        colors: ['#50CD89', '#FFC700', '#F1416C', '#009EF7'],
        legend: { show: false },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val > 0 ? val.toFixed(0) + '%' : '';
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Artikel',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                            }
                        }
                    }
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + ' Artikel';
                }
            }
        }
    };
    const chartPaymentDistribution = new ApexCharts(document.querySelector("#chart_payment_distribution"), chartPaymentDistributionOptions);
    chartPaymentDistribution.render();

    // 3. Chart: Articles Per Year (Bar/Column)
    const chartArticlesPerYearOptions = {
        series: [{
            name: 'Published',
            data: []
        }, {
            name: 'Belum Publish',
            data: []
        }],
        chart: {
            type: 'bar',
            height: 320,
            toolbar: { show: true }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '45%',
                borderRadius: 4
            },
        },
        dataLabels: { enabled: false },
        colors: ['#50CD89', '#FFC700'],
        xaxis: {
            categories: [],
            title: { text: 'Tahun Terbit' }
        },
        yaxis: {
            title: { text: 'Jumlah Artikel' },
            labels: {
                formatter: function (val) { return parseInt(val || 0); }
            }
        },
        legend: { position: 'top' },
        tooltip: {
            y: {
                formatter: function (val) {
                    return parseInt(val || 0) + ' Artikel';
                }
            }
        }
    };
    const chartArticlesPerYear = new ApexCharts(document.querySelector("#chart_articles_per_year"), chartArticlesPerYearOptions);
    chartArticlesPerYear.render();

    // 4. Chart: Article Status (Donut)
    const chartArticleStatusOptions = {
        series: [0, 0, 0],
        chart: {
            type: 'donut',
            height: 280
        },
        labels: ['Published', 'Belum Publish', 'Naskah Menunggu'],
        colors: ['#50CD89', '#FFC700', '#7239EA'],
        legend: { show: false },
        dataLabels: {
            enabled: true,
            formatter: function (val) {
                return val > 0 ? val.toFixed(0) + '%' : '';
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total Naskah',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                            }
                        }
                    }
                }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return val + ' Naskah';
                }
            }
        }
    };
    const chartArticleStatus = new ApexCharts(document.querySelector("#chart_article_status"), chartArticleStatusOptions);
    chartArticleStatus.render();

    // Main Function to load statistics via API and update DOM
    function loadJournalStats(journalId, issueId = null, shouldUpdateIssueDropdown = false) {
        if (!journalId) return;

        loadingOverlay.classList.remove('d-none');

        let url = "{{ route('back.dashboard.journal.stat') }}?journal_id=" + encodeURIComponent(journalId);
        if (issueId && issueId !== 'all') {
            url += "&issue_id=" + encodeURIComponent(issueId);
        }

        // Keep URL in sync without reloading
        if (window.history && window.history.replaceState) {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('journal_id', journalId);
            if (issueId && issueId !== 'all') {
                currentUrl.searchParams.set('issue_id', issueId);
            } else {
                currentUrl.searchParams.delete('issue_id');
            }
            window.history.replaceState({}, '', currentUrl.toString());
        }

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal mengambil data statistik');
            }
            return response.json();
        })
        .then(res => {
            if (!res.success) {
                throw new Error(res.message || 'Error');
            }

            const journal = res.journal;
            const summary = res.summary;
            const charts = res.charts;
            const issuesTable = res.issues_table;
            const issuesOptions = res.issues_options || [];

            // Update Header & Journal Info DOM
            document.getElementById('journal_name_display').textContent = journal.name || journal.title;

            const issueBadge = document.getElementById('filtered_issue_badge');
            if (journal.selected_issue) {
                document.getElementById('journal_fee_badge').textContent = 'Author Fee Edisi: ' + formatRupiah(journal.author_fee);
                if (issueBadge) {
                    issueBadge.textContent = 'Edisi: ' + journal.selected_issue.label;
                    issueBadge.classList.remove('d-none');
                }
            } else {
                document.getElementById('journal_fee_badge').textContent = 'Author Fee Jurnal: ' + formatRupiah(journal.journal_author_fee || journal.author_fee);
                if (issueBadge) {
                    issueBadge.classList.add('d-none');
                }
            }

            // Rebuild Issue Select Options if requested (e.g. on journal switch)
            if (shouldUpdateIssueDropdown) {
                isUpdatingIssueDropdown = true;
                if (typeof jQuery !== 'undefined' && $('#issue_select').length) {
                    const $issueSelect = $('#issue_select');
                    $issueSelect.empty();
                    $issueSelect.append(new Option('Semua Issue', 'all', true, true));
                    issuesOptions.forEach(opt => {
                        $issueSelect.append(new Option(opt.label, opt.id, false, false));
                    });
                    $issueSelect.val('all').trigger('change.select2');
                } else if (issueSelect) {
                    issueSelect.innerHTML = '<option value="all" selected>Semua Issue</option>';
                    issuesOptions.forEach(opt => {
                        const option = document.createElement('option');
                        option.value = opt.id;
                        option.textContent = opt.label;
                        issueSelect.appendChild(option);
                    });
                    issueSelect.value = 'all';
                }
                isUpdatingIssueDropdown = false;
            }

            // Update Summary Card 1 (Total Artikel)
            document.getElementById('stat_total_articles').textContent = (summary.total_submissions || 0).toLocaleString('id-ID');
            document.getElementById('stat_published_articles').textContent = (summary.total_published || 0).toLocaleString('id-ID');
            document.getElementById('stat_unpublished_articles').textContent = (summary.total_unpublished || 0).toLocaleString('id-ID');
            document.getElementById('stat_published_bar').style.width = (summary.published_percentage || 0) + '%';
            document.getElementById('stat_published_percent').textContent = (summary.published_percentage || 0) + '% telah terbit';

            // Update Summary Card 2 (Lunas)
            document.getElementById('stat_lunas_count').textContent = (summary.lunas.count || 0).toLocaleString('id-ID');
            document.getElementById('stat_lunas_amount').textContent = formatRupiah(summary.lunas.amount);
            document.getElementById('stat_free_count').textContent = (summary.free.count || 0) + ' Artikel';

            // Update Summary Card 3 (Belum Lunas)
            document.getElementById('stat_belum_lunas_count').textContent = (summary.belum_lunas.count || 0).toLocaleString('id-ID');
            document.getElementById('stat_belum_lunas_paid').textContent = formatRupiah(summary.belum_lunas.paid_amount);
            document.getElementById('stat_belum_lunas_remaining').textContent = formatRupiah(summary.belum_lunas.remaining_amount);

            // Update Summary Card 4 (Belum Bayar)
            document.getElementById('stat_belum_bayar_count').textContent = (summary.belum_bayar.count || 0).toLocaleString('id-ID');
            document.getElementById('stat_belum_bayar_amount').textContent = formatRupiah(summary.belum_bayar.amount);

            // Update Row 2 (Finansial & Waiting)
            document.getElementById('stat_total_paid_received').textContent = formatRupiah(summary.total_paid_received);
            document.getElementById('stat_total_outstanding').textContent = formatRupiah(summary.total_outstanding);
            document.getElementById('stat_total_potential').textContent = formatRupiah(summary.total_potential_revenue);
            document.getElementById('stat_total_issues').textContent = (journal.filtered_issues_count ?? journal.total_issues ?? 0).toLocaleString('id-ID') + (summary.is_issue_filtered ? ' (Filter)' : '');

            if (summary.is_issue_filtered) {
                document.getElementById('stat_waiting_count').textContent = (summary.waiting_submissions.total || 0) + ' Naskah Baru';
                document.getElementById('stat_waiting_detail').textContent = 'Naskah belum masuk edisi: ' + summary.waiting_submissions.waiting + ' Menunggu | ' + summary.waiting_submissions.under_review + ' Review';
            } else {
                document.getElementById('stat_waiting_count').textContent = (summary.waiting_submissions.total || 0) + ' Naskah Baru';
                document.getElementById('stat_waiting_detail').textContent = 'Waiting: ' + summary.waiting_submissions.waiting + ' | Under Review: ' + summary.waiting_submissions.under_review;
            }

            // Update Legends
            document.getElementById('legend_lunas').textContent = summary.lunas.count + ' (' + formatRupiah(summary.lunas.amount) + ')';
            document.getElementById('legend_belum_lunas').textContent = summary.belum_lunas.count + ' (' + formatRupiah(summary.belum_lunas.paid_amount) + ')';
            document.getElementById('legend_belum_bayar').textContent = summary.belum_bayar.count + ' (' + formatRupiah(summary.belum_bayar.amount) + ')';
            document.getElementById('legend_free').textContent = summary.free.count + ' Artikel';

            document.getElementById('legend_stat_published').textContent = summary.total_published + ' Artikel';
            document.getElementById('legend_stat_unpublished').textContent = summary.total_unpublished + ' Artikel';
            document.getElementById('legend_stat_waiting').textContent = (summary.is_issue_filtered ? summary.waiting_submissions.total + ' Naskah (Jurnal)' : summary.waiting_submissions.total + ' Naskah');

            // Update Chart 1: Articles Per Issue
            if (charts.issue_chart.categories && charts.issue_chart.categories.length > 0) {
                document.getElementById('chart_articles_per_issue').classList.remove('d-none');
                document.getElementById('empty_chart_issue').classList.add('d-none');

                chartArticlesPerIssue.updateOptions({
                    xaxis: { categories: charts.issue_chart.categories },
                    series: [
                        { name: 'Published', data: charts.issue_chart.published },
                        { name: 'Belum Publish', data: charts.issue_chart.unpublished }
                    ]
                });
            } else {
                document.getElementById('chart_articles_per_issue').classList.add('d-none');
                document.getElementById('empty_chart_issue').classList.remove('d-none');
            }

            // Update Chart 2: Payment Distribution
            const paymentSeries = charts.payment_chart.series || [0, 0, 0, 0];
            chartPaymentDistribution.updateSeries(paymentSeries);

            // Update Chart 3: Articles Per Year
            if (charts.year_chart.categories && charts.year_chart.categories.length > 0) {
                document.getElementById('chart_articles_per_year').classList.remove('d-none');
                document.getElementById('empty_chart_year').classList.add('d-none');

                chartArticlesPerYear.updateOptions({
                    xaxis: { categories: charts.year_chart.categories },
                    series: [
                        { name: 'Published', data: charts.year_chart.published },
                        { name: 'Belum Publish', data: charts.year_chart.unpublished }
                    ]
                });
            } else {
                document.getElementById('chart_articles_per_year').classList.add('d-none');
                document.getElementById('empty_chart_year').classList.remove('d-none');
            }

            // Update Chart 4: Article Status
            const statusSeries = charts.article_status_chart.series || [0, 0, 0];
            chartArticleStatus.updateSeries(statusSeries);

            // Update Issues Table DOM
            const tbody = document.getElementById('table_issues_body');
            tbody.innerHTML = '';

            if (!issuesTable || issuesTable.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="13" class="text-center py-6 text-gray-500">
                            <i class="ki-duotone ki-information-2 fs-2x text-gray-400 mb-2"></i>
                            <div class="fw-bold">Belum ada edisi (issue) yang cocok untuk jurnal ini.</div>
                        </td>
                    </tr>
                `;
            } else {
                issuesTable.forEach((item, index) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td class="text-center fw-bold text-gray-700">${index + 1}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-gray-900 fw-bold fs-6">${item.issue_label}</span>
                                <span class="text-gray-500 fs-8">${item.title}</span>
                            </div>
                        </td>
                        <td class="text-center fw-semibold text-gray-700">${item.year || '-'}</td>
                        <td class="text-end fw-semibold text-gray-900">${formatRupiah(item.author_fee)}</td>
                        <td class="text-center">
                            <span class="badge badge-light-primary fw-bold">${item.total_articles}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light-success fw-bold">${item.published_count}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-light-warning fw-bold">${item.unpublished_count}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-success fw-bold">${item.lunas_count}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-warning fw-bold">${item.belum_lunas_count}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-danger fw-bold">${item.belum_bayar_count}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge badge-info fw-bold">${item.free_count}</span>
                        </td>
                        <td class="text-end fw-bold text-success">${formatRupiah(item.total_income)}</td>
                        <td class="text-center">
                            <a href="${item.action_url}" class="btn btn-icon btn-light-primary btn-sm" title="Lihat Artikel Edisi">
                                <i class="ki-duotone ki-eye fs-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </a>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        })
        .catch(err => {
            console.error('Error loading journal stats:', err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memuat Data',
                text: err.message || 'Terjadi kesalahan saat memuat data statistik jurnal.'
            });
        })
        .finally(() => {
            loadingOverlay.classList.add('d-none');
        });
    }

    // Event listener: Select2 & native change
    if (typeof jQuery !== 'undefined') {
        $('#journal_select').on('change select2:select', function () {
            const newJournalId = $(this).val();
            if (newJournalId) {
                loadJournalStats(newJournalId, '', true);
            }
        });

        $('#issue_select').on('change select2:select', function () {
            if (isUpdatingIssueDropdown) return;
            const currentJournalId = $('#journal_select').val();
            const selectedIssueId = $(this).val();
            const filterIssueId = (selectedIssueId === 'all' || !selectedIssueId) ? '' : selectedIssueId;
            loadJournalStats(currentJournalId, filterIssueId, false);
        });
    }

    if (journalSelect) {
        journalSelect.addEventListener('change', function () {
            loadJournalStats(this.value, 'all', true);
        });
    }

    if (issueSelect) {
        issueSelect.addEventListener('change', function () {
            if (isUpdatingIssueDropdown) return;
            const currentJournalId = journalSelect ? journalSelect.value : null;
            const filterIssueId = (this.value === 'all' || !this.value) ? '' : this.value;
            loadJournalStats(currentJournalId, filterIssueId, false);
        });
    }

    if (btnRefresh) {
        btnRefresh.addEventListener('click', function () {
            const currentJournalId = (typeof jQuery !== 'undefined' && $('#journal_select').val())
                ? $('#journal_select').val()
                : (journalSelect ? journalSelect.value : null);
            const selectedIssueId = (typeof jQuery !== 'undefined' && $('#issue_select').val())
                ? $('#issue_select').val()
                : (issueSelect ? issueSelect.value : null);
            const filterIssueId = (selectedIssueId === 'all' || !selectedIssueId) ? '' : selectedIssueId;
            loadJournalStats(currentJournalId, filterIssueId, false);
        });
    }

    // Modal Submissions & API
    let currentModalType = 'belum_lunas';
    let currentModalSubmissions = [];

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderModalSubmissionsTable(items, type, isSearching = false) {
        const tbody = document.getElementById('modal_submission_tbody');
        const emptyBox = document.getElementById('modal_submission_empty');
        const emptyText = document.getElementById('modal_submission_empty_text');
        const emptyTitle = document.getElementById('modal_submission_empty_title');
        const contentBox = document.getElementById('modal_submission_content');

        if (!tbody) return;

        if (!items || items.length === 0) {
            contentBox.classList.add('d-none');
            emptyBox.classList.remove('d-none');
            if (isSearching) {
                if (emptyTitle) emptyTitle.textContent = 'Tidak Ditemukan';
                if (emptyText) emptyText.textContent = 'Tidak ada naskah yang cocok dengan kata kunci pencarian.';
            } else {
                if (emptyTitle) emptyTitle.textContent = 'Tidak Ada Submission';
                if (emptyText) emptyText.textContent = (type === 'belum_lunas')
                    ? 'Tidak ada submission berstatus belum lunas (DP/Cicil).'
                    : 'Tidak ada submission berstatus belum bayar (0%).';
            }
            return;
        }

        emptyBox.classList.add('d-none');
        contentBox.classList.remove('d-none');

        const isBelumLunas = (type === 'belum_lunas');

        let html = '';
        items.forEach((sub, index) => {
            const statusBadge = sub.is_published
                ? '<span class="badge badge-light-success fw-bold fs-8">Published</span>'
                : `<span class="badge badge-light-warning fw-bold fs-8">${escapeHtml(sub.status_label || 'Belum Publish')}</span>`;

            let invoicesHtml = '<span class="text-muted fs-8 fst-italic">Belum ada invoice</span>';
            if (sub.invoices && sub.invoices.length > 0) {
                invoicesHtml = sub.invoices.map(inv => {
                    const badgeClass = inv.is_paid ? 'badge-light-success text-success' : 'badge-light-danger text-danger';
                    const statusText = inv.is_paid ? 'Lunas' : 'Belum Lunas';
                    const percentText = inv.payment_percent ? inv.payment_percent + '%' : 'Inv';
                    const dueText = inv.due_date ? ` (${escapeHtml(inv.due_date)})` : '';
                    return `<div class="mb-1"><span class="badge ${badgeClass} fs-9 fw-bold">${escapeHtml(inv.invoice_number)}: ${percentText} - ${statusText}${dueText}</span></div>`;
                }).join('');
            }

            const paidColHtml = isBelumLunas
                ? `<td class="text-end col-modal-paid">
                    <span class="text-success fw-bold">${formatRupiah(sub.paid_amount)}</span>
                    <span class="badge badge-light-success fs-9 ms-1">${sub.paid_percent}%</span>
                   </td>`
                : '';

            html += `
                <tr>
                    <td class="ps-4 text-center text-gray-600 fw-semibold">${index + 1}</td>
                    <td>
                        <span class=" text-gray-800 fw-bold">${escapeHtml(sub.submission_id)}</span>
                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="text-gray-900 fw-bold fs-7 mb-1">${escapeHtml(sub.title)}</span>
                            <span class="text-muted fs-8">
                                <i class="ki-duotone ki-user fs-8 text-gray-400 me-1"><span class="path1"></span><span class="path2"></span></i>
                                ${escapeHtml(sub.authors)}
                            </span>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-light-primary fw-semibold fs-8">${escapeHtml(sub.issue_label)}</span>
                    </td>
                    <td class="text-center">
                        ${statusBadge}
                    </td>
                    <td class="text-end fw-bold text-gray-800">
                        ${formatRupiah(sub.author_fee)}
                    </td>
                    ${paidColHtml}
                    <td class="text-end fw-bold text-danger">
                        ${formatRupiah(sub.remaining_amount)}
                    </td>
                    <td>
                        ${invoicesHtml}
                    </td>
                    <td class="text-center pe-4">
                        <a href="${sub.action_url}" target="_blank" class="btn btn-icon btn-sm btn-light-primary" title="Buka Halaman Artikel Edisi">
                            <i class="ki-duotone ki-arrow-up-right fs-4">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </a>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    function openSubmissionModal(type) {
        currentModalType = type;
        const journalId = (typeof jQuery !== 'undefined' && $('#journal_select').val())
            ? $('#journal_select').val()
            : (journalSelect ? journalSelect.value : null);

        const issueVal = (typeof jQuery !== 'undefined' && $('#issue_select').val())
            ? $('#issue_select').val()
            : (issueSelect ? issueSelect.value : null);

        const issueId = (issueVal === 'all' || !issueVal) ? '' : issueVal;

        const modalEl = document.getElementById('modal_submission_payment_detail');
        if (!modalEl) return;
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();

        // UI Reset
        document.getElementById('modal_submission_loading').classList.remove('d-none');
        document.getElementById('modal_submission_error').classList.add('d-none');
        document.getElementById('modal_submission_content').classList.add('d-none');
        document.getElementById('modal_submission_empty').classList.add('d-none');
        const searchInput = document.getElementById('modal_submission_search');
        if (searchInput) searchInput.value = '';

        const isBelumLunas = (type === 'belum_lunas');
        const modalTitle = isBelumLunas ? 'Daftar Submission Belum Lunas (DP/Cicil)' : 'Daftar Submission Belum Bayar (0%)';
        document.getElementById('modal_submission_title').textContent = modalTitle;

        const typeSymbol = document.getElementById('modal_type_symbol');
        if (isBelumLunas) {
            typeSymbol.innerHTML = `
                <span class="symbol-label bg-light-warning">
                    <i class="ki-duotone ki-bill fs-2 text-warning">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span><span class="path6"></span>
                    </i>
                </span>
            `;
            document.getElementById('modal_summary_remaining_label').textContent = 'SISA TAGIHAN';
            document.querySelectorAll('.col-modal-paid').forEach(el => el.classList.remove('d-none'));
        } else {
            typeSymbol.innerHTML = `
                <span class="symbol-label bg-light-danger">
                    <i class="ki-duotone ki-cross-circle fs-2 text-danger">
                        <span class="path1"></span><span class="path2"></span>
                    </i>
                </span>
            `;
            document.getElementById('modal_summary_remaining_label').textContent = 'TOTAL PIUTANG';
            document.querySelectorAll('.col-modal-paid').forEach(el => el.classList.add('d-none'));
        }

        let url = '{{ route("back.dashboard.journal.submissions") }}?type=' + encodeURIComponent(type);
        if (journalId) url += '&journal_id=' + encodeURIComponent(journalId);
        if (issueId) url += '&issue_id=' + encodeURIComponent(issueId);

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Status ' + res.status);
            return res.json();
        })
        .then(data => {
            document.getElementById('modal_submission_loading').classList.add('d-none');
            if (!data.success) {
                throw new Error(data.message || 'Gagal memuat data');
            }

            const meta = data.meta || {};
            const journalName = meta.journal ? meta.journal.name : '-';
            const issueText = meta.issue ? meta.issue.label : 'Semua Issue';
            document.getElementById('modal_submission_subtitle').textContent = journalName + ' • ' + issueText;

            document.getElementById('modal_summary_count').textContent = (meta.total_count || 0) + ' Naskah';
            document.getElementById('modal_summary_fee').textContent = formatRupiah(meta.total_fee || 0);
            document.getElementById('modal_summary_paid').textContent = formatRupiah(meta.total_paid || 0);
            document.getElementById('modal_summary_remaining').textContent = formatRupiah(meta.total_remaining || 0);

            currentModalSubmissions = data.submissions || [];
            renderModalSubmissionsTable(currentModalSubmissions, type, false);
        })
        .catch(err => {
            document.getElementById('modal_submission_loading').classList.add('d-none');
            document.getElementById('modal_submission_error').classList.remove('d-none');
            document.getElementById('modal_submission_error_text').textContent = err.message || 'Terjadi kesalahan saat memuat data.';
        });
    }

    // Modal Search
    const modalSearchInput = document.getElementById('modal_submission_search');
    if (modalSearchInput) {
        modalSearchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            if (!query) {
                renderModalSubmissionsTable(currentModalSubmissions, currentModalType, false);
                return;
            }

            const filtered = currentModalSubmissions.filter(sub => {
                const title = (sub.title || '').toLowerCase();
                const authors = (sub.authors || '').toLowerCase();
                const subId = String(sub.submission_id || '').toLowerCase();
                const issue = (sub.issue_label || '').toLowerCase();
                return title.includes(query) || authors.includes(query) || subId.includes(query) || issue.includes(query);
            });

            renderModalSubmissionsTable(filtered, currentModalType, true);
        });
    }

    // Event listeners for card click
    const cardBelumLunas = document.getElementById('card_belum_lunas');
    if (cardBelumLunas) {
        cardBelumLunas.addEventListener('click', function () {
            openSubmissionModal('belum_lunas');
        });
        cardBelumLunas.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openSubmissionModal('belum_lunas');
            }
        });
    }

    const cardBelumBayar = document.getElementById('card_belum_bayar');
    if (cardBelumBayar) {
        cardBelumBayar.addEventListener('click', function () {
            openSubmissionModal('belum_bayar');
        });
        cardBelumBayar.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openSubmissionModal('belum_bayar');
            }
        });
    }

    // Initial Load
    const initialJournalId = (typeof jQuery !== 'undefined' && $('#journal_select').val())
        ? $('#journal_select').val()
        : (journalSelect ? journalSelect.value : null);

    const initialIssueVal = (typeof jQuery !== 'undefined' && $('#issue_select').val())
        ? $('#issue_select').val()
        : (issueSelect ? issueSelect.value : null);
    const initialIssueId = (initialIssueVal === 'all' || !initialIssueVal) ? '' : initialIssueVal;

    if (initialJournalId) {
        loadJournalStats(initialJournalId, initialIssueId, false);
    }
});
</script>
@endsection
