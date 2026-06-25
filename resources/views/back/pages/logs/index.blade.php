@extends('back.app')
@section('content')
    <div id="kt_content_container" class=" container-xxl ">

        {{-- Statistik Cards --}}
        <div class="row mb-5">
            <div class="col-md-3">
                <div class="card mb-5">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-45px me-4">
                                <span class="symbol-label bg-light-primary">
                                    <i class="ki-duotone ki-chart-simple fs-2x text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i>
                                </span>
                            </div>
                            <div>
                                <span class="fs-3 fw-bold text-primary" id="stat_total">0</span>
                                <div class="fs-7 fw-semibold text-gray-500">Total Aktivitas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-5">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-45px me-4">
                                <span class="symbol-label bg-light-success">
                                    <i class="ki-duotone ki-plus-circle fs-2x text-success">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div>
                                <span class="fs-3 fw-bold text-success" id="stat_created">0</span>
                                <div class="fs-7 fw-semibold text-gray-500">Created</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-5">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-45px me-4">
                                <span class="symbol-label bg-light-warning">
                                    <i class="ki-duotone ki-pencil fs-2x text-warning">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div>
                                <span class="fs-3 fw-bold text-warning" id="stat_updated">0</span>
                                <div class="fs-7 fw-semibold text-gray-500">Updated</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mb-5">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-45px me-4">
                                <span class="symbol-label bg-light-danger">
                                    <i class="ki-duotone ki-trash fs-2x text-danger">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                    </i>
                                </span>
                            </div>
                            <div>
                                <span class="fs-3 fw-bold text-danger" id="stat_deleted">0</span>
                                <div class="fs-7 fw-semibold text-gray-500">Deleted</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-flush">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title">
                    <div class="fw-bold">
                        <i class="ki-duotone ki-scroll fs-2 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        Activity Log
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">

                {{-- Filter Baris 1: Search & Pengguna --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fs-7 fw-semibold text-gray-600">Pencarian</label>
                        <div class="d-flex align-items-center position-relative">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" id="search_keyword" class="form-control form-control-solid ps-12"
                                placeholder="Cari nama pengguna, email, deskripsi, model, event, properties..." />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fs-7 fw-semibold text-gray-600">Pengguna (Causer)</label>
                        <div class="d-flex align-items-center position-relative">
                            <i class="ki-duotone ki-profile-user fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                            <select id="causer_id" class="form-select form-select-solid form-select-lg ps-12">
                                <option value="" selected>Semua Pengguna</option>
                                @foreach ($causers as $causer)
                                    <option value="{{ $causer->id }}">{{ $causer->name }} ({{ $causer->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Filter Baris 2: Log Name, Event, Subject Type --}}
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label fs-7 fw-semibold text-gray-600">Log Name</label>
                        <div class="d-flex align-items-center position-relative">
                            <i class="ki-duotone ki-category fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                            <select id="log_name" class="form-select form-select-solid form-select-lg ps-12">
                                <option value="" selected>Semua Log</option>
                                @foreach ($logNames as $name)
                                    <option value="{{ $name }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-7 fw-semibold text-gray-600">Event</label>
                        <div class="d-flex align-items-center position-relative">
                            <i class="ki-duotone ki-flag fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <select id="event" class="form-select form-select-solid form-select-lg ps-12">
                                <option value="" selected>Semua Event</option>
                                @foreach ($events as $evt)
                                    <option value="{{ $evt }}">{{ ucfirst($evt) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-7 fw-semibold text-gray-600">Subject (Model)</label>
                        <div class="d-flex align-items-center position-relative">
                            <i class="ki-duotone ki-abstract-26 fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <select id="subject_type" class="form-select form-select-solid form-select-lg ps-12">
                                <option value="" selected>Semua Model</option>
                                @foreach ($subjectTypes as $type)
                                    <option value="{{ $type['full'] }}">{{ $type['short'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-7 fw-semibold text-gray-600">&nbsp;</label>
                        <div>
                            <button type="button" id="btn_reset_filter" class="btn btn-light-danger w-100">
                                <i class="ki-duotone ki-arrows-circle fs-4">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i> Reset Filter
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Filter Baris 3: Tanggal --}}
                <div class="row mb-5">
                    <div class="col-md-3">
                        <label class="form-label fs-7 fw-semibold text-gray-600">Dari Tanggal</label>
                        <div class="d-flex align-items-center position-relative">
                            <i class="ki-duotone ki-calendar-tick fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                                <span class="path6"></span>
                            </i>
                            <input type="date" id="date_start" value="{{ now()->subMonth()->format('Y-m-d') }}"
                                class="form-control form-control-solid ps-12" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fs-7 fw-semibold text-gray-600">Sampai Tanggal</label>
                        <div class="d-flex align-items-center position-relative">
                            <i class="ki-duotone ki-calendar-remove fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                                <span class="path6"></span>
                            </i>
                            <input type="date" id="date_end" value="{{ now()->format('Y-m-d') }}"
                                class="form-control form-control-solid ps-12" />
                        </div>
                    </div>
                </div>

                <div class="separator border-gray-200 mb-5"></div>

                <table class="table align-middle table-row-dashed fs-6 gy-5" id="datatable_ajax">
                    <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="text-start min-w-40px">No</th>
                            <th class="text-start min-w-200px">Pengguna</th>
                            <th class="text-start min-w-80px">Event</th>
                            <th class="text-start min-w-100px">Log Name</th>
                            <th class="text-start min-w-250px">Deskripsi</th>
                            <th class="text-start min-w-130px">Subject (Model)</th>
                            <th class="text-start min-w-100px">Properties</th>
                            <th class="text-start min-w-130px">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Properties --}}
    <div class="modal fade" id="modal_properties" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">
                        <i class="ki-duotone ki-document fs-2 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Detail Perubahan
                    </h2>
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                class="path2"></span></i>
                    </div>
                </div>
                <div class="modal-body">

                    {{-- Info Ringkasan --}}
                    <div class="d-flex flex-wrap gap-3 mb-5">
                        <div class="border border-dashed rounded p-3 flex-grow-1">
                            <div class="fs-7 text-gray-500">Pengguna</div>
                            <div class="fw-bold text-gray-800" id="modal_causer">-</div>
                        </div>
                        <div class="border border-dashed rounded p-3 flex-grow-1">
                            <div class="fs-7 text-gray-500">Event</div>
                            <div class="fw-bold" id="modal_event">-</div>
                        </div>
                        <div class="border border-dashed rounded p-3 flex-grow-1">
                            <div class="fs-7 text-gray-500">Subject</div>
                            <div class="fw-bold text-gray-800" id="modal_subject">-</div>
                        </div>
                        <div class="border border-dashed rounded p-3 flex-grow-1">
                            <div class="fs-7 text-gray-500">Waktu</div>
                            <div class="fw-bold text-gray-800" id="modal_time">-</div>
                        </div>
                    </div>

                    <div class="separator border-gray-200 mb-5"></div>

                    <div id="properties_content">

                        {{-- Old Attributes --}}
                        <div id="old_attributes_section" class="d-none mb-5">
                            <h5 class="fw-bold text-danger mb-3">
                                <i class="ki-duotone ki-arrow-left fs-4 text-danger">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Data Lama (Old)
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped fs-7">
                                    <thead class="bg-light-danger">
                                        <tr>
                                            <th class="fw-bold" width="30%">Attribute</th>
                                            <th class="fw-bold">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody id="old_attributes_body"></tbody>
                                </table>
                            </div>
                        </div>

                        {{-- New Attributes --}}
                        <div id="new_attributes_section" class="d-none mb-5">
                            <h5 class="fw-bold text-success mb-3">
                                <i class="ki-duotone ki-arrow-right fs-4 text-success">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Data Baru (Attributes)
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped fs-7">
                                    <thead class="bg-light-success">
                                        <tr>
                                            <th class="fw-bold" width="30%">Attribute</th>
                                            <th class="fw-bold">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody id="new_attributes_body"></tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Perbandingan Side by Side untuk Update --}}
                        <div id="comparison_section" class="d-none mb-5">
                            <h5 class="fw-bold text-info mb-3">
                                <i class="ki-duotone ki-arrows-loop fs-4 text-info">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Perbandingan Perubahan
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-bordered fs-7">
                                    <thead class="bg-light-info">
                                        <tr>
                                            <th class="fw-bold" width="25%">Attribute</th>
                                            <th class="fw-bold text-danger" width="37%">Sebelum</th>
                                            <th class="fw-bold text-success" width="38%">Sesudah</th>
                                        </tr>
                                    </thead>
                                    <tbody id="comparison_body"></tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Output Sekarang --}}
                        <div id="current_output_section" class="d-none">
                            <h5 class="fw-bold text-primary mb-3">
                                <i class="ki-duotone ki-document fs-4 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Data Sekarang
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped fs-7">
                                    <thead class="bg-light-primary">
                                        <tr>
                                            <th class="fw-bold" width="30%">Attribute</th>
                                            <th class="fw-bold">Value</th>
                                        </tr>
                                    </thead>
                                    <tbody id="current_output_body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var table = $('#datatable_ajax').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('back.logs.datatable') }}',
                type: 'GET',
                data: function(d) {
                    d.search_keyword = $('#search_keyword').val();
                    d.log_name = $('#log_name').val();
                    d.event = $('#event').val();
                    d.causer_id = $('#causer_id').val();
                    d.subject_type = $('#subject_type').val();
                    d.date_start = $('#date_start').val();
                    d.date_end = $('#date_end').val();
                }
            },
            columns: [{
                    data: null,
                    name: 'no',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'causer_info',
                    name: 'causer_info',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'event_badge',
                    name: 'event_badge',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'log_name_badge',
                    name: 'log_name_badge',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'description_info',
                    name: 'description_info',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'subject_info',
                    name: 'subject_info',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'properties_info',
                    name: 'properties_info',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'created_at_formatted',
                    name: 'created_at_formatted',
                    orderable: false,
                    searchable: false
                },
            ],
            "createdRow": function(row, data, dataIndex) {
                $(row).find('td').eq(0).addClass('text-start pe-0');
                $(row).find('td').eq(1).addClass('text-start pe-0');
                $(row).find('td').eq(2).addClass('text-start pe-0');
                $(row).find('td').eq(3).addClass('text-start pe-0');
                $(row).find('td').eq(4).addClass('text-start pe-0');
                $(row).find('td').eq(5).addClass('text-start pe-0');
                $(row).find('td').eq(6).addClass('text-start pe-0');
                $(row).find('td').eq(7).addClass('text-start pe-0');
            }
        });

        // Update statistik dari response
        table.on('xhr', function() {
            var json = table.ajax.json();
            $('#stat_total').text(json.stat_total ?? 0);
            $('#stat_created').text(json.stat_created ?? 0);
            $('#stat_updated').text(json.stat_updated ?? 0);
            $('#stat_deleted').text(json.stat_deleted ?? 0);
        });

        // Debounce untuk search input
        var searchTimeout;
        $('#search_keyword').on('keyup', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                table.ajax.reload();
            }, 500);
        });

        // Filter langsung reload untuk select dan date
        $('#log_name, #event, #causer_id, #subject_type, #date_start, #date_end').on('change', function() {
            table.ajax.reload();
        });

        // Reset filter
        $('#btn_reset_filter').on('click', function() {
            $('#search_keyword').val('');
            $('#log_name').val('');
            $('#event').val('');
            $('#causer_id').val('');
            $('#subject_type').val('');
            $('#date_start').val('{{ now()->subMonth()->format('Y-m-d') }}');
            $('#date_end').val('{{ now()->format('Y-m-d') }}');
            table.ajax.reload();
        });

        // Modal properties handler
        $(document).on('click', '.btn-properties', function() {
            var properties = $(this).data('properties');
            var description = $(this).data('description');
            var event = $(this).data('event');
            var subject = $(this).data('subject');
            var subjectId = $(this).data('subject-id');
            var subjectTypeFull = $(this).data('subject-type-full');
            var causer = $(this).data('causer');
            var time = $(this).data('time');

            // Set ringkasan info
            $('#modal_causer').text(causer);
            $('#modal_subject').text(subject + ' (Data ID: ' + subjectId + ')');
            $('#modal_time').text(time);

            // Event badge di modal
            var eventBadgeClass = 'badge-light-info';
            if (event === 'created') eventBadgeClass = 'badge-light-success';
            else if (event === 'updated') eventBadgeClass = 'badge-light-warning';
            else if (event === 'deleted') eventBadgeClass = 'badge-light-danger';
            $('#modal_event').html('<span class="badge ' + eventBadgeClass + '">' + (event ? event.charAt(0)
                .toUpperCase() + event.slice(1) : '-') + '</span>');

            // Reset sections
            $('#old_attributes_section').addClass('d-none');
            $('#new_attributes_section').addClass('d-none');
            $('#comparison_section').addClass('d-none');
            $('#current_output_section').addClass('d-none');
            $('#old_attributes_body').html('');
            $('#new_attributes_body').html('');
            $('#comparison_body').html('');
            $('#current_output_body').html('');

            if (typeof properties === 'string') {
                try {
                    properties = JSON.parse(properties);
                } catch (e) {
                    return;
                }
            }

            var hasStructuredData = false;

            // Jika ada old DAN attributes (update) → tampilkan perbandingan side-by-side
            if (properties.old && properties.attributes) {
                hasStructuredData = true;
                $('#comparison_section').removeClass('d-none');

                // Gabungkan semua key dari old dan attributes
                var allKeys = [];
                $.each(properties.old, function(key) {
                    if (allKeys.indexOf(key) === -1) allKeys.push(key);
                });
                $.each(properties.attributes, function(key) {
                    if (allKeys.indexOf(key) === -1) allKeys.push(key);
                });

                $.each(allKeys, function(index, key) {
                    var oldVal = properties.old[key];
                    var newVal = properties.attributes[key];
                    var oldDisplay = (oldVal === null || oldVal === undefined || oldVal ===
                            '') ?
                        '<span class="text-muted fst-italic">kosong</span>' : escapeHtml(
                            String(oldVal));
                    var newDisplay = (newVal === null || newVal === undefined || newVal ===
                            '') ?
                        '<span class="text-muted fst-italic">kosong</span>' : escapeHtml(
                            String(newVal));

                    var isChanged = String(oldVal) !== String(newVal);
                    var rowClass = isChanged ? 'bg-light-warning' : '';

                    $('#comparison_body').append(
                        '<tr class="' + rowClass + '">' +
                        '<td class="fw-semibold">' + escapeHtml(key) + (isChanged ?
                            ' <i class="ki-duotone ki-information-3 fs-7 text-warning"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>' :
                            '') + '</td>' +
                        '<td class="text-danger">' + oldDisplay + '</td>' +
                        '<td class="text-success">' + newDisplay + '</td>' +
                        '</tr>'
                    );
                });
            } else {
                // Show old attributes saja
                if (properties.old) {
                    hasStructuredData = true;
                    $('#old_attributes_section').removeClass('d-none');
                    $.each(properties.old, function(key, value) {
                        var displayValue = (value === null || value === '') ?
                            '<span class="text-muted fst-italic">kosong</span>' :
                            escapeHtml(String(value));
                        $('#old_attributes_body').append(
                            '<tr><td class="fw-semibold">' + escapeHtml(key) +
                            '</td><td>' + displayValue + '</td></tr>'
                        );
                    });
                }

                // Show new/attributes saja
                if (properties.attributes) {
                    hasStructuredData = true;
                    $('#new_attributes_section').removeClass('d-none');
                    $.each(properties.attributes, function(key, value) {
                        var displayValue = (value === null || value === '') ?
                            '<span class="text-muted fst-italic">kosong</span>' :
                            escapeHtml(String(value));
                        $('#new_attributes_body').append(
                            '<tr><td class="fw-semibold">' + escapeHtml(key) +
                            '</td><td>' + displayValue + '</td></tr>'
                        );
                    });
                }
            }

            // Tampilkan Output Sekarang via AJAX (seluruh isi data)
            if (subjectTypeFull && subjectId && subjectId !== '-') {
                $('#current_output_section').removeClass('d-none');
                $('#current_output_body').html(
                    '<tr><td colspan="2" class="text-center text-muted py-4">' +
                    '<span class="spinner-border spinner-border-sm me-2"></span> Memuat data...' +
                    '</td></tr>'
                );

                $.ajax({
                    url: '{{ route("back.logs.subject-data") }}',
                    type: 'GET',
                    data: {
                        subject_type: subjectTypeFull,
                        subject_id: subjectId
                    },
                    success: function(response) {
                        $('#current_output_body').html('');
                        if (response.success && response.data) {
                            $.each(response.data, function(key, value) {
                                var displayValue;
                                if (value === null || value === undefined || value === '') {
                                    displayValue = '<span class="text-muted fst-italic">kosong</span>';
                                } else if (typeof value === 'object') {
                                    displayValue = '<code class="fs-8">' + escapeHtml(JSON.stringify(value, null, 2)) + '</code>';
                                } else {
                                    displayValue = escapeHtml(String(value));
                                }
                                $('#current_output_body').append(
                                    '<tr><td class="fw-semibold">' + escapeHtml(key) +
                                    '</td><td>' + displayValue + '</td></tr>'
                                );
                            });
                        } else {
                            $('#current_output_body').html(
                                '<tr><td colspan="2" class="text-center text-muted py-3">' +
                                '<i class="ki-duotone ki-information-3 fs-4 text-warning me-1"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i> ' +
                                (response.message || 'Data tidak ditemukan atau sudah dihapus') +
                                '</td></tr>'
                            );
                        }
                    },
                    error: function() {
                        $('#current_output_body').html(
                            '<tr><td colspan="2" class="text-center text-danger py-3">' +
                            'Gagal memuat data' +
                            '</td></tr>'
                        );
                    }
                });
            }
        });

        function escapeHtml(text) {
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(text));
            return div.innerHTML;
        }
    </script>
@endsection
