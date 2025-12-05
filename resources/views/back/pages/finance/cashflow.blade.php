@extends('back.app')
@section('content')
    <div id="kt_content_container" class="container-fluid">
        <div class="card mb-10">
            <div class="card-body pt-9 pb-0">
                <div class="d-flex flex-wrap flex-sm-nowrap mb-6">

                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                            <div class="d-flex flex-column">
                                <div class="d-flex align-items-center mb-1">
                                    <a href="#" class="text-gray-800 text-hover-primary fs-2 fw-bold me-3">
                                        {{ $finance_year ? $finance_year->name : 'Tidak ada buku aktif' }}
                                    </a>
                                    @if ($finance_year->is_active)
                                        <span class="badge badge-light-success me-auto">Aktif</span>
                                    @else
                                        <span class="badge badge-light-danger me-auto">Tutup</span>
                                    @endif
                                </div>
                                {{-- <div class="d-flex flex-wrap fw-semibold mb-4 fs-5 text-gray-500">#1 Tool to get started
                                    with Web Apps any Kind & size</div> --}}
                            </div>
                            <div class="d-flex mb-4">
                                @if ($finance_year->is_active)
                                    <div id="btn_status">

                                        <a href="#" class="btn btn-sm btn-danger me-3" data-bs-toggle="modal"
                                            data-bs-target="#tutup_buku">Tutup Buku</a>
                                        <a href="#" class="btn btn-sm btn-warning me-3" data-bs-toggle="modal"
                                            data-bs-target="#edit_buku">Edit</a>

                                        <div class="modal fade" tabindex="-1" id="tutup_buku">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title">Tutup Buku</h3>

                                                        <!--begin::Close-->
                                                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                                                            data-bs-dismiss="modal" aria-label="Close">
                                                            <i class="ki-duotone ki-cross fs-1"><span
                                                                    class="path1"></span><span class="path2"></span></i>
                                                        </div>
                                                        <!--end::Close-->
                                                    </div>
                                                    <form action="{{ route('back.finance.cashflow-year.store') }}"
                                                        method="post">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>
                                                                Dengan menutup buku, Anda menyatakan bahwa semua transaksi
                                                                keuangan
                                                                untuk periode ini telah dicatat dengan benar dan lengkap.
                                                                Proses
                                                                ini
                                                                akan <span class="fw-bold text-danger">mengunci data
                                                                    keuangan</span>
                                                                untuk periode ini, sehingga <span class="fw-bold">tidak
                                                                    dapat
                                                                    diubah
                                                                    atau dihapus</span>. Pastikan untuk melakukan tinjauan
                                                                akhir
                                                                sebelum melanjutkan.
                                                            </p>
                                                            <div class="alert alert-warning mt-4 mb-4">
                                                                <i class="ki-duotone ki-information fs-2 me-2"></i>
                                                                <span>Pastikan seluruh transaksi sudah benar sebelum menutup
                                                                    buku.</span>
                                                            </div>
                                                            <hr>

                                                            <h4 class="mb-4">Buat Buku Baru</h4>
                                                            <div class="mb-4">
                                                                <label class="form-label fw-semibold"
                                                                    for="name">Nama</label>
                                                                <input type="text" class="form-control" id="name"
                                                                    name="name" placeholder="Nama Buku">
                                                                @error('name')
                                                                    <small class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                                <div class="form-text">Contoh: Anggaran
                                                                    {{ \Carbon\Carbon::now()->year }}.</div>
                                                            </div>
                                                            <div class="mb-4">
                                                                <label class="form-label fw-semibold"
                                                                    for="start_date">Tanggal
                                                                    Mulai</label>
                                                                <input type="date" class="form-control" id="start_date"
                                                                    name="start_date"
                                                                    value="{{ \Carbon\Carbon::now()->addDay()->format('Y-m-d') }}">
                                                                @error('start_date')
                                                                    <small class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                            </div>
                                                            <div class="mb-4">
                                                                <label class="form-label fw-semibold"
                                                                    for="distribution_percentage">Persentase
                                                                    Distribusi ke Rumah Jurnal</label>
                                                                <div class="input-group mb-5">
                                                                    <input type="number" class="form-control"
                                                                        placeholder="Persentase Distribusi ke Rumah Jurnal"
                                                                        name="distribution_percentage" id="distribution_percentage"
                                                                        value="{{ old('distribution_percentage', 50) }}"
                                                                        min="0" max="100" required />
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                                @error('distribution_percentage')
                                                                    <small class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                                <div class="form-text">Masukkan nilai antara 0 hingga
                                                                    100. persentase ini menentukan bagian dari total
                                                                    pendapatan yang akan dialokasikan ke Rumah Jurnal.
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light"
                                                                data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-danger">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal fade" tabindex="-1" id="edit_buku">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h3 class="modal-title">Edit Buku Keuangan</h3>

                                                        <!--begin::Close-->
                                                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                                                            data-bs-dismiss="modal" aria-label="Close">
                                                            <i class="ki-duotone ki-cross fs-1"><span
                                                                    class="path1"></span><span class="path2"></span></i>
                                                        </div>
                                                        <!--end::Close-->
                                                    </div>
                                                    <form action="{{ route('back.finance.cashflow-year.edit') }}"
                                                        method="post">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-5">
                                                                <label class="form-label required">Nama Tahun
                                                                    Keuangan</label>
                                                                <input type="text" class="form-control"
                                                                    placeholder="Nama Tahun Keuangan" name="name"
                                                                    value="{{ $finance_year ? $finance_year->name : old('name') }}"
                                                                    required />
                                                                @error('name')
                                                                    <small class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                            </div>
                                                            <div class="mb-5">
                                                                <label class="form-label required">Tanggal Mulai</label>
                                                                <input type="date" class="form-control"
                                                                    placeholder="Tanggal Mulai" name="start_date"
                                                                    value="{{ $finance_year ? $finance_year->start_date : old('start_date') }}"
                                                                    required />
                                                                @error('start_date')
                                                                    <small class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                            </div>
                                                            <div class="mb-5">
                                                                <label class="form-label required">Persentase
                                                                    Distribusi
                                                                    ke Rumah Jurnal</label>
                                                                <div class="input-group mb-5">
                                                                    <input type="number" class="form-control"
                                                                        placeholder="Persentase Distribusi ke Rumah Jurnal"
                                                                        name="distribution_percentage"
                                                                        value="{{ $finance_year ? $finance_year->distribution_percentage : old('distribution_percentage') }}"
                                                                        min="0" max="100" required />
                                                                    <span class="input-group-text">%</span>
                                                                </div>
                                                                @error('distribution_percentage')
                                                                    <small class="text-danger">{{ $message }}</small>
                                                                @enderror
                                                                <div class="form-text">Masukkan nilai antara 0 hingga
                                                                    100. persentase ini menentukan bagian dari total
                                                                    pendapatan yang akan dialokasikan ke Rumah Jurnal.
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light"
                                                                data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <a href="#" class="btn btn-sm btn-bg-light btn-active-color-primary me-3"
                                    data-bs-toggle="modal" data-bs-target="#archive">Archive</a>
                                <div class="modal fade" tabindex="-1" id="archive">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h3 class="modal-title">Arsip Buku Keuangan</h3>

                                                <!--begin::Close-->
                                                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2"
                                                    data-bs-dismiss="modal" aria-label="Close">
                                                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                                            class="path2"></span></i>
                                                </div>
                                                <!--end::Close-->
                                            </div>

                                            <div class="modal-body">
                                                @foreach ($list_finance_year as $fin_year)
                                                    <div
                                                        class="d-flex flex-stack py-4 border-bottom border-gray-300 border-bottom-dashed">
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol-50px me-5">
                                                                @if ($fin_year->is_active)
                                                                    <span
                                                                        class="symbol-label bg-light-success text-success fw-bold">
                                                                        <i
                                                                            class="ki-duotone ki-two-credit-cart fs-2 text-success">
                                                                            <span class="path1"></span>
                                                                            <span class="path2"></span>
                                                                            <span class="path3"></span>
                                                                            <span class="path4"></span>
                                                                            <span class="path5"></span>
                                                                        </i>
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="symbol-label bg-light-warning text-warning fw-bold">
                                                                        <i
                                                                            class="ki-duotone ki-two-credit-cart fs-2 text-warning">
                                                                            <span class="path1"></span>
                                                                            <span class="path2"></span>
                                                                            <span class="path3"></span>
                                                                            <span class="path4"></span>
                                                                            <span class="path5"></span>
                                                                        </i>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <div class="me-3">
                                                                <a href="?id={{ $fin_year->id }}"
                                                                    class="text-gray-800 text-hover-primary fs-5 fw-bold">{{ $fin_year->name }}</a>
                                                                <div class="text-gray-400 fw-semibold fs-7">Periode:
                                                                    {{ \Carbon\Carbon::parse($fin_year->start_date)->format('d M Y') }}
                                                                    -
                                                                    {{ $fin_year->end_date ? \Carbon\Carbon::parse($fin_year->end_date)->format('d M Y') : 'Sekarang' }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex">

                                                            <a href="?id={{ $fin_year->id }}"
                                                                class="btn btn-sm btn-light btn-active-color-primary me-3">Lihat</a>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>


                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                        <div class="d-flex flex-wrap justify-content-start">
                            <div class="d-flex flex-wrap">

                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-arrow-down fs-3 text-success me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="fs-4 fw-bold" id="total_income_all">
                                            {{ $finance_year ? 'Rp ' . number_format($total_income_now, 0, ',', '.') : 'Rp 0' }}
                                        </div>
                                    </div>
                                    <div class="fw-semibold fs-6 text-gray-500">Total Income</div>
                                </div>
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-arrow-up fs-3 text-danger me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="fs-4 fw-bold" id="total_income_all">
                                            {{ $finance_year ? 'Rp ' . number_format($total_outcome_now, 0, ',', '.') : 'Rp 0' }}
                                        </div>
                                    </div>
                                    <div class="fw-semibold fs-6 text-gray-500">Total Outcome</div>
                                </div>
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <i class="ki-duotone ki-abstract-16 fs-3 text-warning me-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="fs-4 fw-bold">
                                            {{ $finance_year ? 'Rp ' . number_format($total_balance_now, 0, ',', '.') : 'Rp 0' }}
                                        </div>
                                    </div>
                                    <div class="fw-semibold fs-6 text-gray-500">Balance</div>
                                </div>
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="fs-4 fw-bold">
                                            {{ $finance_year ? 'Rp ' . number_format($total_distribution, 0, ',', '.') : 'Rp 0' }}
                                        </div>
                                        @if($finance_year)
                                            <span class="ms-2 fs-6 text-muted">
                                                ({{ $finance_year->distribution_percentage ?? 0 }}%)
                                            </span>
                                        @endif
                                    </div>
                                    <div class="fw-semibold fs-6 text-gray-500">Total Distribusi ke Rumah Jurnal</div>
                                </div>
                                <div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="fs-4 fw-bold">
                                            {{ $finance_year ? 'Rp ' . number_format($total_distributtion_other, 0, ',', '.') : 'Rp 0' }}
                                        </div>
                                        @if($finance_year)
                                            <span class="ms-2 fs-6 text-muted">
                                                ({{ 100 - ($finance_year->distribution_percentage ?? 0) }}%)
                                            </span>
                                        @endif
                                    </div>
                                    <div class="fw-semibold fs-6 text-gray-500">Total Distribusi ke BLU UIN Bukittinggi</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-flush">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title">
                    Cash Flow
                </div>
                <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                    <div class="btn-group">
                        <a href="#" class="btn btn-light-primary" id="export_excel">
                            <i class="ki-duotone ki-file-down fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Export Excel
                        </a>
                        @if ($finance_year->is_active)
                            <a href="#" class="btn btn-light-info" data-bs-toggle="modal"
                                data-bs-target="#add_finance">
                                <i class="ki-duotone ki-plus fs-2"></i>
                                Tambah Transaksi
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row mb-10">
                    <div class="col-md-4">
                        <label class="form-label fs-6 fw-bold">Tipe</label>
                        <select class="form-select form-select-solid" data-control="select2"
                            data-placeholder="Select an option" name="type" id="type">
                            <option value="all">Semua</option>
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-6 fw-bold">Dari Tanggal</label>
                        <input type="date" name="date_start" class="form-control form-control-solid"
                            placeholder="Date Start" id="date_start"
                            min="{{ $finance_year ? \Carbon\Carbon::parse($finance_year->start_date)->format('Y-m-d') : \Carbon\Carbon::now()->format('Y-m-d') }}"
                            max="{{ \Carbon\Carbon::now()->subDay()->format('Y-m-d') }}"
                            value="{{ $finance_year ? \Carbon\Carbon::parse($finance_year->start_date)->format('Y-m-d') : \Carbon\Carbon::now()->format('Y-m-d') }}"
                            oninput="
                                if (this.value < this.min) this.value = this.min;
                                if (this.value > this.max) this.value = this.max;
                            " />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-6 fw-bold">Sampai Tanggal</label>
                        <input type="date" name="date_end" class="form-control form-control-solid"
                            placeholder="Date End" id="date_end"
                            min="{{ $finance_year ? \Carbon\Carbon::parse($finance_year->start_date)->format('Y-m-d') : \Carbon\Carbon::now()->format('Y-m-d') }}"
                            max="{{ $finance_year && $finance_year->end_date ? \Carbon\Carbon::parse($finance_year->end_date)->format('Y-m-d') : \Carbon\Carbon::now()->addDay()->format('Y-m-d') }}"
                            value="{{ $finance_year && $finance_year->end_date ? \Carbon\Carbon::parse($finance_year->end_date)->format('Y-m-d') : \Carbon\Carbon::now()->addDay()->format('Y-m-d') }}"
                            oninput="
                                if (this.value < this.min) this.value = this.min;
                                if (this.value > this.max) this.value = this.max;
                            " />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-5">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="fs-5 fw-bold text-gray-700 me-3 fs-3">Income:</span>
                                    <span class="fs-5 fw-bold text-success fs-2" id="total_income">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-5">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="fs-5 fw-bold text-gray-700 me-3 fs-3">Expense:</span>
                                    <span class="fs-5 fw-bold text-danger fs-2" id="total_expense">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <div class="d-flex  align-items-center">
                                    <span class="fs-5 fw-bold text-gray-700 me-3 fs-3">Balance:</span>
                                    <span class="fs-5 fw-bold fs-2  text-warning " id="balance">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="table_finance">
                    <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-200px">Transaksi</th>
                            <th class="min-w-125px">Tanggal</th>
                            <th class="min-w-150px">Jumlah</th>
                            <th class="min-w-50px">Type</th>
                            <th class="min-w-200px">Payment Info</th>
                            <th class="min-w-100px">Lampiran</th>
                            <th class="min-w-300px">Log</th>
                            <th class="min-w-100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="add_finance">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Tambah Data Keuangan</h3>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>
                <form action="{{ route('back.finance.cashflow.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-5">
                            <label class="form-label required">Nama Transaksi</label>
                            <input type="text" class="form-control" placeholder="Nama transaksi keuangan"
                                name="name" value="{{ old('name') }}" required />
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-5">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" placeholder="Deskripsi transaksi keuangan" name="description">{{ old('description') }}</textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-5">
                            <label class="form-label required">Jumlah</label>
                            <div class="input-group mb-5">
                                <span class="input-group-text">Rp.</span>
                                <input type="text" class="form-control" placeholder="Jumlah transaksi keuangan"
                                    value="{{ old('amount') }}" oninput="formatRupiah(this)" required />
                            </div>
                            <input type="hidden" id="rupiah_value" name="amount" value="{{ old('amount') }}">
                            @error('amount')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-5">
                            <label class="form-label required">Tanggal</label>
                            <input type="datetime-local" class="form-control" placeholder="Tanggal transaksi keuangan"
                                name="date" value="{{ old('date') }}"
                                min="{{ $finance_year ? \Carbon\Carbon::parse($finance_year->start_date)->format('Y-m-d') . 'T00:00' : \Carbon\Carbon::now()->format('Y-m-d') . 'T00:00' }}"
                                required />
                            @error('date')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-5">
                            <label class="form-label required">Type</label>
                            <select class="form-select" name="type" required>
                                <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Income</option>
                                <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                            </select>
                            @error('type')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="row mb-5">
                            <div class="col-md-6">
                                <label class="form-label ">Metode Pembayaran</label>
                                <input type="text" class="form-control" placeholder="Metode Pembayaran"
                                    name="payment_method" value="{{ old('payment_method') }}" />
                                @error('payment_method')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label ">No Ref</label>
                                <input type="text" class="form-control" placeholder="No Ref" name="payment_reference"
                                    value="{{ old('payment_reference') }}" />
                                @error('payment_reference')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-5">
                            <label class="form-label ">Note</label>
                            <textarea class="form-control" placeholder="Note" name="payment_note">{{ old('payment_note') }}</textarea>
                            @error('payment_note')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-5">
                            <label class="form-label">Lampiran</label>
                            <input type="file" class="form-control" name="attachment" />
                            <small class="text-muted">Format: jpg, jpeg, png, pdf. Maksimal 10 MB.</small>
                            @error('attachment')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
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
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#table_finance').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('back.finance.cashflow.datatable') }}",
                    data: function(d) {
                        d.type = $('#type').val();
                        d.date_start = $('#date_start').val();
                        d.date_end = $('#date_end').val();
                    }
                },
                columns: [{
                        data: 'transaction',
                        name: 'transaction'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'payment_info',
                        name: 'payment_info'
                    },
                    {
                        data: 'attachment',
                        name: 'attachment'
                    },
                    {
                        data: 'log',
                        name: 'log'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            let summary = {
                total_income: 0,
                total_expense: 0,
                total_balance: 0
            };
            table.on('xhr', function() {
                let json = table.ajax.json();
                summary.total_income = json.total_income;
                summary.total_expense = json.total_expense;
                summary.total_balance = json.total_balance;
                console.log(summary);
                $('#total_income').text('Rp ' + summary.total_income.toLocaleString());
                $('#total_expense').text('Rp ' + summary.total_expense.toLocaleString());
                if (summary.total_balance >= 0) {
                    $('#balance').removeClass('text-danger').addClass('text-success');
                } else {
                    $('#balance').removeClass('text-success').addClass('text-danger');
                }
                $('#balance').text('Rp ' + summary.total_balance.toLocaleString());
                $('#export_excel').attr('href',
                    "{{ route('back.finance.cashflow.export') }}?type=" +
                    $('#type').val() + "&date_start=" + $('#date_start').val() + "&date_end=" +
                    $('#date_end').val());
            });
            $('#type').on('change', function() {
                table.ajax.reload();
            });
            $('#date_start').on('change', function() {
                table.ajax.reload();
            });
            $('#date_end').on('change', function() {
                table.ajax.reload();
            });
        });

        function formatRupiah(element) {
            let angka = element.value.replace(/\D/g, '');
            let formatted = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            element.value = formatted;
            document.getElementById('rupiah_value').value = angka;
        }
    </script>
@endsection
