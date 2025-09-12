@extends('back.app')
@section('content')
    <div id="kt_content_container" class="container-fluid">

        <div class="card card-flush">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title">
                    Laporan Keuangan
                </div>
                <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                    <div class="btn-group">

                        <a href="#" id="export_excel"
                            class="btn btn-light-primary" id="export_excel">
                            <i class="ki-duotone ki-file-down fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Export Excel
                        </a>
                        <a class="btn btn-light-primary" href="">

                            <i class="ki-duotone ki-printer fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                            Print PDF
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row mb-10">
                    <div class="col-md-6">

                        <label class="form-label fs-6 fw-bold">Jurnal</label>
                        <select class="form-select form-select-solid" data-control="select2"
                            data-placeholder="Select an option" name="journal_id" id="journal_id">
                            @if (auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('keuangan'))
                                <option value="" selected>Semua Jurnal</option>
                            @endif
                            @foreach ($journals as $journal)
                                @if (auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('keuangan'))
                                    <option value="{{ $journal->id }}">{{ $journal->title }}</option>
                                @else
                                    @php
                                        $permissionNames = Auth::user()->getPermissionNames();
                                    @endphp
                                    @if ($journal->permissions->contains($journal->url_path, $permissionNames))
                                        <option value="{{ $journal->id }}">{{ $journal->title }}</option>
                                    @endif
                                @endif
                            @endforeach

                        </select>

                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-6 fw-bold">Issue</label>
                        <select class="form-select form-select-solid" data-control="select2"
                            data-placeholder="Select an option" name="issue_id" id="issue_id">
                            <option value="" selected>Semua Issue</option>

                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-6 fw-bold">Dari Tanggal</label>
                        <input type="date" name="date_start" class="form-control form-control-solid"
                            placeholder="Date Start" id="date_start"
                            value="{{ \Carbon\Carbon::createFromDate(now()->year, 1, 1)->format('Y-m-d') }}" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-6 fw-bold">Sampai Tanggal</label>
                        <input type="date" name="date_end" class="form-control form-control-solid" placeholder="Date End"
                            id="date_end"
                            value="{{ \Carbon\Carbon::createFromDate(now()->year, 12, 31)->format('Y-m-d') }}" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mb-5">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="fs-5 fw-bold text-gray-700 me-3 fs-3">Total Income:</span>
                                    <span class="fs-5 fw-bold text-success fs-2" id="total_income">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mb-5">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <span class="fs-5 fw-bold text-gray-700 me-3 fs-3">Total Expense:</span>
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
                                    <span class="fs-5 fw-bold fs-2  text-danger " id="balance">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="table_finance">
                    <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-200px">Jurnal</th>
                            <th class="min-w-300px">Penulis</th>
                            <th class="min-w-300px">Artikel</th>
                            <th class="min-w-200px">Edisi</th>
                            <th class="min-w-400px">Status Pembayaran</th>
                            <th class="min-w-100px">LoA</th>


                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                    </tbody>
                </table>
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
                    url: "{{ route('back.finance.report.datatable') }}",
                    data: function(d) {
                        d.journal_id = $('#journal_id').val();
                        d.date_start = $('#date_start').val();
                        d.date_end = $('#date_end').val();
                    }
                },
                columns: [{
                        data: 'journal',
                        name: 'journal'
                    },
                    {
                        data: 'author',
                        name: 'author'
                    },
                    {
                        data: 'submission',
                        name: 'submission'
                    },
                    {
                        data: 'edition',
                        name: 'edition'
                    },
                    {
                        data: 'payment_info',
                        name: 'payment_info'
                    },
                    {
                        data: 'loa',
                        name: 'loa'
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
                    "{{ route('back.finance.report.export') }}?journal_id=" +
                    $('#journal_id').val() + "&date_start=" + $('#date_start').val() + "&date_end=" +
                    $(
                        '#date_end').val());

            });

            $('#journal_id').on('change', function() {
                table.ajax.reload();
            });

            $('#date_start').on('change', function() {
                table.ajax.reload();
            });

            $('#date_end').on('change', function() {
                table.ajax.reload();
            });

            $('#export_excel').on('click', function(e) {
                e.preventDefault();
                let url = "{{ route('back.finance.report.export') }}";
                url += '?journal_id=' + encodeURIComponent($('#journal_id').val());
                url += '&date_start=' + encodeURIComponent($('#date_start').val());
                url += '&date_end=' + encodeURIComponent($('#date_end').val());
                window.location.href = url;
            });

        });
    </script>
@endsection
