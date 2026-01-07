@extends('back.app')

@section('content')
    <div id="kt_content_container" class=" container-xxl ">

        {{-- Welcome Widget --}}
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-xl-12">
                <div class="card card-flush">
                    <div class="card-body py-8">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-60px symbol-circle me-4">
                                    @if(Auth::user()->getPhoto())
                                        <img src="{{ Auth::user()->getPhoto() }}" alt="Avatar" />
                                    @else
                                        <div class="symbol-label fs-1 fw-bold bg-light-primary text-primary">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-gray-800 fw-bold mb-1">Halo, {{ Auth::user()->name }}!</h3>
                                    <div class="text-gray-500 fs-6">
                                        @foreach(Auth::user()->getRoleNames() as $role)
                                            <span class="badge badge-light-primary">{{ ucfirst($role) }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 mt-md-0 text-md-end">
                                <span class="text-gray-600 fs-7 d-block">{{ now()->translatedFormat('l, d F Y') }}</span>
                                <span class="text-gray-800 fw-semibold fs-4" id="live-clock">{{ now()->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Stats Cards --}}
        @role('super-admin')
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            {{-- Total Journals --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card card-flush h-lg-100">
                    <div class="card-body d-flex justify-content-between align-items-start flex-column">
                        <div class="m-0">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-45px me-3">
                                    <div class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-book-open fs-2x text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                    </div>
                                </div>
                                <div>
                                    <span class="fw-semibold fs-6 text-gray-500 d-block">Total Jurnal</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column my-5">
                            <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2" id="stat-journal">-</span>
                        </div>
                        <a href="#" class="btn btn-sm btn-light-primary">Lihat Detail</a>
                    </div>
                </div>
            </div>

            {{-- Total Submissions --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card card-flush h-lg-100">
                    <div class="card-body d-flex justify-content-between align-items-start flex-column">
                        <div class="m-0">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-45px me-3">
                                    <div class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-file-added fs-2x text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <div>
                                    <span class="fw-semibold fs-6 text-gray-500 d-block">Total Submission</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column my-5">
                            <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2" id="stat-submission">-</span>
                        </div>
                        <span class="badge badge-light-success fs-7">Artikel Masuk</span>
                    </div>
                </div>
            </div>

            {{-- Total Events --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card card-flush h-lg-100">
                    <div class="card-body d-flex justify-content-between align-items-start flex-column">
                        <div class="m-0">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-45px me-3">
                                    <div class="symbol-label bg-light-warning">
                                        <i class="ki-duotone ki-calendar fs-2x text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <div>
                                    <span class="fw-semibold fs-6 text-gray-500 d-block">Total Event</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column my-5">
                            <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2" id="stat-event">-</span>
                        </div>
                        <a href="#" class="btn btn-sm btn-light-warning">Lihat Event</a>
                    </div>
                </div>
            </div>

            {{-- Total Users --}}
            <div class="col-sm-6 col-xl-3">
                <div class="card card-flush h-lg-100">
                    <div class="card-body d-flex justify-content-between align-items-start flex-column">
                        <div class="m-0">
                            <div class="d-flex align-items-center">
                                <div class="symbol symbol-45px me-3">
                                    <div class="symbol-label bg-light-info">
                                        <i class="ki-duotone ki-people fs-2x text-info">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                        </i>
                                    </div>
                                </div>
                                <div>
                                    <span class="fw-semibold fs-6 text-gray-500 d-block">Total Pengguna</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column my-5">
                            <span class="fw-bold fs-2x text-gray-800 lh-1 ls-n2" id="stat-user">-</span>
                        </div>
                        <span class="badge badge-light-info fs-7">Pengguna Terdaftar</span>
                    </div>
                </div>
            </div>
        </div>
        @endrole

        {{-- Quick Actions & Recent Activity --}}
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            {{-- Quick Actions --}}
            {{-- <div class="col-xl-4">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Aksi Cepat</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-7">Menu yang sering digunakan</span>
                        </h3>
                    </div>
                    <div class="card-body pt-5">
                        <div class="d-flex flex-stack mb-5">
                            <div class="d-flex align-items-center me-3">
                                <div class="symbol symbol-40px me-3">
                                    <div class="symbol-label bg-light-primary">
                                        <i class="ki-duotone ki-profile-user fs-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                        </i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-gray-800 fs-6">Profil Saya</span>
                                    <span class="text-gray-500 fs-7">Kelola profil Anda</span>
                                </div>
                            </div>
                            <a href="{{ route('back.profile.index') }}" class="btn btn-sm btn-icon btn-light">
                                <i class="ki-duotone ki-arrow-right fs-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                        </div>
                        <div class="separator separator-dashed my-4"></div>
                        <div class="d-flex flex-stack mb-5">
                            <div class="d-flex align-items-center me-3">
                                <div class="symbol symbol-40px me-3">
                                    <div class="symbol-label bg-light-success">
                                        <i class="ki-duotone ki-calendar-add fs-2 text-success">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                            <span class="path4"></span>
                                            <span class="path5"></span>
                                            <span class="path6"></span>
                                        </i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-gray-800 fs-6">Event</span>
                                    <span class="text-gray-500 fs-7">Lihat daftar event</span>
                                </div>
                            </div>
                            <a href="#" class="btn btn-sm btn-icon btn-light">
                                <i class="ki-duotone ki-arrow-right fs-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                        </div>
                        <div class="separator separator-dashed my-4"></div>
                        <div class="d-flex flex-stack mb-5">
                            <div class="d-flex align-items-center me-3">
                                <div class="symbol symbol-40px me-3">
                                    <div class="symbol-label bg-light-warning">
                                        <i class="ki-duotone ki-message-text-2 fs-2 text-warning">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-gray-800 fs-6">Pengumuman</span>
                                    <span class="text-gray-500 fs-7">Lihat pengumuman</span>
                                </div>
                            </div>
                            <a href="#" class="btn btn-sm btn-icon btn-light">
                                <i class="ki-duotone ki-arrow-right fs-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                        </div>
                        <div class="separator separator-dashed my-4"></div>
                        <div class="d-flex flex-stack">
                            <div class="d-flex align-items-center me-3">
                                <div class="symbol symbol-40px me-3">
                                    <div class="symbol-label bg-light-danger">
                                        <i class="ki-duotone ki-setting-2 fs-2 text-danger">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-gray-800 fs-6">Pengaturan</span>
                                    <span class="text-gray-500 fs-7">Kelola pengaturan</span>
                                </div>
                            </div>
                            <a href="{{ route('back.profile.settings') }}" class="btn btn-sm btn-icon btn-light">
                                <i class="ki-duotone ki-arrow-right fs-3">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </a>
                        </div>
                    </div>
                </div>
            </div> --}}

            {{-- Monthly Visitors Chart --}}
            <div class="col-xl-12">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Statistik Pengunjung Website</span>
                            <span class="text-gray-500 mt-1 fw-semibold fs-7">Data sebulan terakhir</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div id="chart_1" class="px-5"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Country & Platform Stats --}}
        <div class="row g-5 gx-xl-10 mb-5 mb-xl-10">
            <div class="col-xl-12">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Statistik Pengunjung Berdasarkan Negara</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div id="chart_4" class="px-5"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-5 gx-xl-10 mb-5 mb-xl-10">
            <div class="col-xl-6">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Statistik Berdasarkan Platform OS</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div id="chart_2" class="px-5"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-gray-900">Statistik Berdasarkan Browser</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0 px-0">
                        <div id="chart_3" class="px-5"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Live Clock
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('live-clock').textContent = `${hours}:${minutes}`;
        }
        setInterval(updateClock, 1000);

        // Load Stats
        $.ajax({
            url: "{{ route('back.dashboard.stats') }}",
            type: "GET",
            success: function(response) {
                $('#stat-journal').text(response.journals || 0);
                $('#stat-submission').text(response.submissions || 0);
                $('#stat-event').text(response.events || 0);
                $('#stat-user').text(response.users || 0);
            },
            error: function() {
                $('#stat-journal').text('0');
                $('#stat-submission').text('0');
                $('#stat-event').text('0');
                $('#stat-user').text('0');
            }
        });

        var chart_1 = new ApexCharts(document.querySelector("#chart_1"), {

            series: [{
                name: 'Pengunjung',
                data: [10]
            }],
            chart: {
                height: 350,
                type: 'line',
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: true
            },
            stroke: {
                curve: 'straight'
            },
            title: {
                text: 'Pengunjung',
                align: 'left'
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: ['x'],
            }
        });
        chart_1.render();



        var chart_2 = new ApexCharts(document.querySelector("#chart_2"), {
            series: [{
                data: []
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    borderRadiusApplication: 'end',
                    horizontal: true,
                }
            },
            dataLabels: {
                enabled: true
            },
            xaxis: {
                categories: [],
            },
            legend: {
                show: true,
            }
        });
        chart_2.render();

        var chart_3 = new ApexCharts(document.querySelector("#chart_3"), {
            series: [{
                data: []
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    borderRadiusApplication: 'end',
                    horizontal: true,
                }
            },
            dataLabels: {
                enabled: true
            },
            xaxis: {
                categories: [],
            },
            legend: {
                show: true,
            }
        });
        chart_3.render();

        var chart_4 = new ApexCharts(document.querySelector("#chart_4"), {
            series: [{
                data: []
            }],
            chart: {
                height: 350,
                type: 'bar',
                events: {
                    click: function(chart, w, e) {
                        // console.log(chart, w, e)
                    }
                }
            },
            colors: [],
            plotOptions: {
                bar: {
                    columnWidth: '45%',
                    distributed: true,
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                show: false
            },
            xaxis: {
                categories: [],
                labels: {
                    style: {
                        colors: [],
                        fontSize: '12px'
                    }
                }
            }
        });
        chart_4.render();

        $.ajax({
            url: "{{ route('back.dashboard.visitor.stat') }}",
            type: "GET",
            success: function(response) {
                console.log(response);

                chart_1.updateSeries([{
                    data: response.visitor_monthly.map(function(item) {
                        return item.total;
                    }).reverse()
                }]);
                chart_1.updateOptions({
                    xaxis: {
                        categories: response.visitor_monthly.map(function(item) {
                            return item.date;
                        }).reverse()
                    }
                });

                chart_2.updateOptions({
                    xaxis: {
                        categories: response.visitor_platfrom.map(function(item) {
                            if (item.platform == '0') {
                                return 'Unknown';
                            } else {
                                return item.platform;
                            }
                        })
                    },
                    series: [{
                        name: 'Jumlah',
                        data: response.visitor_platfrom.map(function(item) {
                            return item.total;
                        })
                    }]
                });

                chart_3.updateOptions({
                    xaxis: {
                        categories: response.visitor_browser.map(function(item) {
                            if (item.browser == '0') {
                                return 'Unknown';
                            } else {
                                return item.browser;
                            }
                        })
                    },
                    series: [{
                        name: 'Jumlah',
                        data: response.visitor_browser.map(function(item) {
                            return item.total;
                        })
                    }]
                });
                chart_4.updateOptions({
                    xaxis: {
                        categories: response.visitor_country.map(function(item) {
                            if (item.country == '') {
                                return 'Unknown';
                            } else {
                                return item.country;
                            }
                        }),
                        labels: {
                            style: {
                                colors: response.visitor_country.map(function(item) {
                                    return item.color;
                                }),
                                fontSize: '14px'
                            }
                        }
                    },
                    series: [{
                        name: 'Jumlah',
                        data: response.visitor_browser.map(function(item) {
                            return item.total;
                        })
                    }],
                    colors: response.visitor_country.map(function(item) {
                        return item.color;
                    })
                });
            }
        });
    </script>
@endsection
