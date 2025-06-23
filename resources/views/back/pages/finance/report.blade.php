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

                        <a href="#" class="btn btn-light-primary" id="export_excel">
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
                    <div class="col-md-4">

                        <label class="form-label fs-6 fw-bold">Jurnal</label>
                        <select class="form-select form-select-solid" data-control="select2"
                            data-placeholder="Select an option" name="schedule_id" id="schedule_id">
                            <option value="1">Semua Jurnal</option>
                            @foreach ($journals as $journal)
                                <option value="{{ $journal->id }}">{{ $journal->title }}</option>
                            @endforeach

                        </select>

                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-6 fw-bold">Dari Tanggal</label>
                        <input type="date" name="date_start" class="form-control form-control-solid"
                            placeholder="Date Start" id="date_start"
                            value="{{ \Carbon\Carbon::createFromDate(now()->year, 1, 1)->format('Y-m-d') }}" />
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-6 fw-bold">Sampai Tanggal</label>
                        <input type="date" name="date_end" class="form-control form-control-solid" placeholder="Date End"
                            id="date_end" value="{{ \Carbon\Carbon::createFromDate(now()->year, 12, 31)->format('Y-m-d') }}" />
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
                            <th class="min-w-200px">Invoice</th>
                            <th class="min-w-300px">Journal</th>
                            <th class="min-w-125px">Tanggal</th>
                            <th class="min-w-150px">Jumlah</th>
                            <th class="min-w-50px">Type</th>
                            <th class="min-w-200px">Payment Info</th>
                            <th class="min-w-100px">Lampiran</th>


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
                        d.schedule_id = $('#schedule_id').val();
                        d.date_start = $('#date_start').val();
                        d.date_end = $('#date_end').val();
                    }
                },
                columns: [{
                        data: 'invoice',
                        name: 'invoice'
                    },
                    {
                        data: 'journal',
                        name: 'journal'
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
                    "{{ route('back.finance.report.export') }}?schedule_id=" +
                    $('#schedule_id').val() + "&date_start=" + $('#date_start').val() + "&date_end=" +
                    $(
                        '#date_end').val());

            });

            $('#schedule_id').on('change', function() {
                table.ajax.reload();
            });

            $('#date_start').on('change', function() {
                table.ajax.reload();
            });

            $('#date_end').on('change', function() {
                table.ajax.reload();
            });




        });
    </script>
@endsection
