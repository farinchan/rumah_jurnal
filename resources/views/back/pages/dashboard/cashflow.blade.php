@extends('back.app')
@section('content')
    <div id="kt_content_container" class=" container-xxl ">
        <!-- Summary Cards -->
        <div class="row g-5 gx-xl-10 mb-5 mb-xl-10">
            <div class="col-xl-3">
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100"
                    style="background-color: #F1416C;background-image:url('/metronic8/demo1/assets/media/patterns/vector-1.png')">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white me-2 lh-1" id="total_income_card">Rp 0</span>
                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total Pemasukan</span>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                            <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                <span class="fw-bolder fs-6 text-white opacity-75">Manual</span>
                                <span class="fw-bold fs-6 text-white" id="manual_income">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                <span class="fw-bolder fs-6 text-white opacity-75">Pembayaran</span>
                                <span class="fw-bold fs-6 text-white" id="payment_income">Rp 0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100"
                    style="background-color: #7239EA;background-image:url('/metronic8/demo1/assets/media/patterns/vector-2.png')">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white me-2 lh-1" id="total_expense_card">Rp 0</span>
                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Total Pengeluaran</span>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                            <div class="h-8px mx-3 w-100 bg-white bg-opacity-50 rounded">
                                <div class="bg-white rounded h-8px" role="progressbar" style="width: 100%" id="expense_progress">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card card-flush bgi-no-repeat bgi-size-contain bgi-position-x-end h-xl-100"
                    style="background-color: #17C653;background-image:url('/metronic8/demo1/assets/media/patterns/vector-3.png')">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-white me-2 lh-1" id="total_balance_card">Rp 0</span>
                            <span class="text-white opacity-75 pt-1 fw-semibold fs-6">Saldo Bersih</span>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                            <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                <span class="fw-bolder fs-6 text-white opacity-75">Status</span>
                                <span class="fw-bold fs-6 text-white" id="balance_status">Sehat</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card card-flush h-xl-100">
                    <div class="card-header pt-5">
                        <div class="card-title d-flex flex-column">
                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1" id="transaction_count">0</span>
                            <span class="text-gray-500 pt-1 fw-semibold fs-6">Total Transaksi</span>
                        </div>
                    </div>
                    <div class="card-body d-flex align-items-end pt-0">
                        <div class="d-flex align-items-center flex-column mt-3 w-100">
                            <div class="d-flex justify-content-between w-100 mt-auto mb-2">
                                <span class="fw-bolder fs-6 text-gray-500">Bulan Ini</span>
                                <span class="fw-bold fs-6 text-gray-900" id="monthly_transactions">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-5 gx-xl-10 mb-5 mb-xl-10">
            <div class="col-xl-8">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Trend Cashflow Bulanan</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">Pemasukan vs Pengeluaran</span>
                        </h3>
                        <div class="card-toolbar">
                            <div class="d-flex align-items-center position-relative my-1">
                                <select class="form-select form-select-sm form-select-solid w-125px" id="chart_period">
                                    <option value="30">30 Hari</option>
                                    <option value="60">60 Hari</option>
                                    <option value="90">90 Hari</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div id="cashflow_chart" class="px-5"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Distribusi Tipe Transaksi</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">Pemasukan vs Pengeluaran</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div id="transaction_type_chart" class="px-5"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Finance Year Overview and Recent Transactions -->
        <div class="row g-5 gx-xl-10 mb-5 mb-xl-10">
            <div class="col-xl-6">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Overview Finance Year</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">Perbandingan Income, Outcome & Balance per tahun keuangan</span>
                        </h3>
                        <div class="card-toolbar">
                            <div class="badge badge-light-success">5 Tahun Terakhir</div>
                        </div>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div id="finance_year_chart" class="px-5"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Transaksi Terbaru</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">10 transaksi terakhir</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0 px-5">
                        <div class="table-responsive">
                            <table class="table table-row-dashed gs-7 gy-4" id="recent_transactions_table">
                                <thead>
                                    <tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
                                        <th>Transaksi</th>
                                        <th>Tanggal</th>
                                        <th class="text-end">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    <!-- Data will be populated via JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row g-5 gx-xl-10">
            <div class="col-xl-12">
                <div class="card card-flush">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Aksi Cepat</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-6">Navigasi ke halaman terkait</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-5">
                            <div class="col-xxl-3 col-md-6">
                                <a href="{{ route('back.finance.cashflow.index') }}" class="card bg-light-primary hoverable card-xl-stretch mb-xl-8">
                                    <div class="card-body">
                                        <i class="ki-duotone ki-chart-simple fs-2x text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                        <div class="text-gray-900 fw-bold fs-6 mt-5">Kelola Cashflow</div>
                                        <div class="text-gray-500 fw-semibold fs-7">Lihat detail transaksi</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <a href="{{ route('back.finance.verification.index') }}" class="card bg-light-warning hoverable card-xl-stretch mb-xl-8">
                                    <div class="card-body">
                                        <i class="ki-duotone ki-verification fs-2x text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="text-gray-900 fw-bold fs-6 mt-5">Verifikasi Pembayaran</div>
                                        <div class="text-gray-500 fw-semibold fs-7">Konfirmasi pembayaran masuk</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <a href="{{ route('back.finance.report.index') }}" class="card bg-light-info hoverable card-xl-stretch mb-xl-8">
                                    <div class="card-body">
                                        <i class="ki-duotone ki-chart-pie-2 fs-2x text-info">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="text-gray-900 fw-bold fs-6 mt-5">Laporan Keuangan</div>
                                        <div class="text-gray-500 fw-semibold fs-7">Generate laporan detail</div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-xxl-3 col-md-6">
                                <div class="card bg-light-success hoverable card-xl-stretch mb-xl-8" data-bs-toggle="modal" data-bs-target="#export_modal">
                                    <div class="card-body">
                                        <i class="ki-duotone ki-file-down fs-2x text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <div class="text-gray-900 fw-bold fs-6 mt-5">Export Data</div>
                                        <div class="text-gray-500 fw-semibold fs-7">Download laporan Excel/PDF</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <div class="modal fade" tabindex="-1" id="export_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Export Laporan Cashflow</h3>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body">
                    <form action="{{ route('back.finance.cashflow.export') }}" method="GET">
                        <div class="mb-5">
                            <label class="form-label">Tipe Transaksi</label>
                            <select class="form-select" name="type">
                                <option value="all">Semua</option>
                                <option value="income">Pemasukan</option>
                                <option value="expense">Pengeluaran</option>
                            </select>
                        </div>
                        <div class="row mb-5">
                            <div class="col-6">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" class="form-control" name="date_start" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" class="form-control" name="date_end" value="{{ now()->format('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Export Excel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<style>
    .hoverable {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .hoverable:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .card-xl-stretch {
        min-height: 120px;
    }
</style>
<script>
    $(document).ready(function() {
        loadCashflowData();

        // Refresh data every 5 minutes
        setInterval(loadCashflowData, 300000);

        $('#chart_period').on('change', function() {
            loadCashflowData();
        });
    });

    function loadCashflowData() {
        // Show loading state
        $('#total_income_card, #total_expense_card, #total_balance_card').text('Loading...');

        $.ajax({
            url: "{{ route('back.dashboard.cashflow.stat') }}",
            type: "GET",
            success: function(response) {
                console.log('Cashflow data:', response);
                if (response && response.summary) {
                    updateSummaryCards(response.summary);
                    updateCashflowChart(response.monthly_cashflow || []);
                    updateTransactionTypeChart(response.transaction_types || []);
                    updateFinanceYearChart(response.finance_years || []);
                    updateRecentTransactionsTable(response.recent_transactions || []);
                } else {
                    console.error('Invalid response format:', response);
                }
            },
            error: function(xhr, status, error) {
                handleAjaxError(xhr, status, error);
                // Reset to default values on error
                updateSummaryCards({
                    total_income: 0,
                    total_expense: 0,
                    total_balance: 0,
                    finance_income: 0,
                    payment_income: 0,
                    transaction_count: 0,
                    monthly_transactions: 0
                });
            }
        });
    }

    function updateSummaryCards(summary) {
        // Format currency function
        function formatRupiah(amount) {
            return 'Rp ' + parseInt(amount || 0).toLocaleString('id-ID');
        }

        $('#total_income_card').text(formatRupiah(summary.total_income));
        $('#total_expense_card').text(formatRupiah(summary.total_expense));
        $('#total_balance_card').text(formatRupiah(summary.total_balance));
        $('#manual_income').text(formatRupiah(summary.finance_income));
        $('#payment_income').text(formatRupiah(summary.payment_income));

        // Update transaction count
        $('#transaction_count').text((summary.transaction_count || 0).toLocaleString('id-ID'));
        $('#monthly_transactions').text((summary.monthly_transactions || 0).toLocaleString('id-ID'));

        // Update balance status and color
        if (summary.total_balance >= 0) {
            $('#balance_status').text('Sehat');
            $('#total_balance_card').removeClass('text-danger').addClass('text-white');
        } else {
            $('#balance_status').text('Defisit');
            $('#total_balance_card').removeClass('text-white').addClass('text-danger');
        }
    }

    // Initialize Charts
    var cashflowChart = new ApexCharts(document.querySelector("#cashflow_chart"), {
        series: [{
            name: 'Pemasukan',
            data: []
        }, {
            name: 'Pengeluaran',
            data: []
        }, {
            name: 'Saldo',
            data: []
        }],
        chart: {
            height: 350,
            type: 'line',
            toolbar: {
                show: true
            },
            zoom: {
                enabled: true
            }
        },
        colors: ['#50cd89', '#f1416c', '#009ef7'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        title: {
            text: 'Trend Cashflow',
            align: 'left'
        },
        grid: {
            borderColor: '#e7e7e7',
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            },
        },
        markers: {
            size: 1
        },
        xaxis: {
            categories: [],
            title: {
                text: 'Tanggal'
            }
        },
        yaxis: {
            title: {
                text: 'Jumlah (Rp)'
            },
            labels: {
                formatter: function (val) {
                    return 'Rp ' + parseInt(val || 0).toLocaleString('id-ID');
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            floating: true,
            offsetY: -25,
            offsetX: -5
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return 'Rp ' + parseInt(val || 0).toLocaleString('id-ID');
                }
            }
        }
    });

    var transactionTypeChart = new ApexCharts(document.querySelector("#transaction_type_chart"), {
        series: [],
        chart: {
            width: 380,
            type: 'pie',
        },
        labels: [],
        colors: ['#50cd89', '#f1416c'],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }],
        legend: {
            position: 'bottom'
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return 'Rp ' + parseInt(val || 0).toLocaleString('id-ID');
                }
            }
        }
    });

    var financeYearChart = new ApexCharts(document.querySelector("#finance_year_chart"), {
        series: [{
            name: 'Income',
            data: []
        }, {
            name: 'Outcome',
            data: []
        }, {
            name: 'Balance',
            data: []
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: true
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: false,
                columnWidth: '55%',
            }
        },
        colors: ['#50cd89', '#f1416c', '#009ef7'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: [],
            title: {
                text: 'Finance Year'
            },
            labels: {
                rotate: -45,
                maxHeight: 120
            }
        },
        yaxis: {
            title: {
                text: 'Amount (Rp)'
            },
            labels: {
                formatter: function (val) {
                    return 'Rp ' + parseInt(val || 0).toLocaleString('id-ID');
                }
            }
        },
        fill: {
            opacity: 1
        },
        legend: {
            position: 'top',
            horizontalAlign: 'left'
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return 'Rp ' + parseInt(val || 0).toLocaleString('id-ID');
                }
            }
        }
    });

    // Render charts
    cashflowChart.render();
    transactionTypeChart.render();
    financeYearChart.render();

    function updateCashflowChart(data) {
        if (!data || data.length === 0) {
            data = []; // Empty chart if no data
        }

        const categories = data.map(item => item.date).reverse();
        const incomeData = data.map(item => parseInt(item.income || 0)).reverse();
        const expenseData = data.map(item => parseInt(item.expense || 0)).reverse();
        const balanceData = data.map(item => parseInt(item.balance || 0)).reverse();

        cashflowChart.updateSeries([{
            name: 'Pemasukan',
            data: incomeData
        }, {
            name: 'Pengeluaran',
            data: expenseData
        }, {
            name: 'Saldo',
            data: balanceData
        }]);

        cashflowChart.updateOptions({
            xaxis: {
                categories: categories
            }
        });
    }

    function updateTransactionTypeChart(data) {
        if (!data || data.length === 0) {
            transactionTypeChart.updateSeries([]);
            transactionTypeChart.updateOptions({ labels: [] });
            return;
        }

        const series = data.map(item => parseInt(item.total || 0));
        const labels = data.map(item => item.type === 'income' ? 'Pemasukan' : 'Pengeluaran');

        transactionTypeChart.updateSeries(series);
        transactionTypeChart.updateOptions({
            labels: labels
        });
    }

    function updateFinanceYearChart(data) {
        if (!data || data.length === 0) {
            financeYearChart.updateSeries([
                { name: 'Income', data: [] },
                { name: 'Outcome', data: [] },
                { name: 'Balance', data: [] }
            ]);
            financeYearChart.updateOptions({ xaxis: { categories: [] } });
            return;
        }

        const categories = data.map(item => {
            const name = item.name || 'Unknown';
            return item.is_active ? name + ' (Active)' : name;
        });
        const incomeData = data.map(item => parseInt(item.income || 0));
        const outcomeData = data.map(item => parseInt(item.outcome || 0));
        const balanceData = data.map(item => parseInt(item.balance || 0));

        financeYearChart.updateSeries([{
            name: 'Income',
            data: incomeData
        }, {
            name: 'Outcome',
            data: outcomeData
        }, {
            name: 'Balance',
            data: balanceData
        }]);

        financeYearChart.updateOptions({
            xaxis: {
                categories: categories,
                labels: {
                    rotate: -45,
                    maxHeight: 120
                }
            }
        });
    }

    function updateRecentTransactionsTable(data) {
        const tbody = $('#recent_transactions_table tbody');
        tbody.empty();

        if (data && data.length > 0) {
            data.forEach(function(transaction) {
                const typeClass = transaction.type === 'income' ? 'text-success' : 'text-danger';
                const typeSymbol = transaction.type === 'income' ? '+' : '-';
                const date = new Date(transaction.date).toLocaleDateString('id-ID');
                const amount = parseInt(transaction.amount || 0);

                const row = `
                    <tr>
                        <td>
                            <div class="d-flex flex-column">
                                <a href="#" class="text-gray-800 text-hover-primary fw-bold">${transaction.name || 'Transaksi'}</a>
                                <span class="text-gray-500 fw-semibold fs-7">${transaction.description || '-'}</span>
                            </div>
                        </td>
                        <td class="text-gray-600 fw-bold">${date}</td>
                        <td class="text-end">
                            <span class="fw-bold ${typeClass}">${typeSymbol}Rp ${amount.toLocaleString('id-ID')}</span>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        } else {
            tbody.append('<tr><td colspan="3" class="text-center text-gray-500">Tidak ada data transaksi</td></tr>');
        }
    }

    function formatCurrency(input) {
        let value = input.value.replace(/\D/g, '');
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        input.value = value;
    }

    // Global currency formatter function
    function formatRupiah(amount) {
        return 'Rp ' + parseInt(amount || 0).toLocaleString('id-ID');
    }

    // Error handling for AJAX calls
    function handleAjaxError(xhr, status, error) {
        console.error('AJAX Error:', {xhr, status, error});
        let message = 'Terjadi kesalahan saat memuat data';

        if (xhr.responseJSON && xhr.responseJSON.message) {
            message = xhr.responseJSON.message;
        }

        // Show user-friendly error message
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Error',
                text: message,
                icon: 'error',
                timer: 3000
            });
        } else {
            alert(message);
        }
    }
</script>
@endsection
