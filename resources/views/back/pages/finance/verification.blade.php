@extends('back.app')
@section('content')
    <div id="kt_content_container" class=" container-xxl ">
        <div class="card card-flush">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title">
                    <div class="fw-bold">Verifikasi Pembayaran</div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row mb-3">
                    <div class="col-md-12">

                        <div class="d-flex align-items-center position-relative my-1">

                            <i class="ki-duotone ki-book fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                            <select id="journal_id" class="form-select form-select-solid form-select-lg  ps-12">
                                <option value="0" selected>Semua Jurnal</option>
                                @foreach ($journals as $journal)
                                    <option value="{{ $journal->id }}">{{ $journal->title }}</option>
                                @endforeach

                            </select>
                        </div>
                    </div>

                    <div class="col-md-9">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" id="search" class="form-control form-control-solid ps-12"
                                placeholder="Cari ID Submission/Judul artikel/Penulis" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-flag fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <select id="payment_status" class="form-select form-select-solid form-select-lg ps-12">
                                <option value="0" selected>Semua</option>
                                <option value="pending">Pending</option>
                                <option value="accepted">Accepted</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-calendar-tick fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                                <span class="path6"></span>
                            </i>
                            <input type="datetime-local" id="payment_timestamp_start" value="{{ now()->subYear()->format('Y-m-d\TH:i') }}"
                                class="form-control form-control-solid ps-12" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-calendar-remove fs-3 position-absolute ms-4">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                                <span class="path6"></span>
                            </i>
                            <input type="datetime-local" id="payment_timestamp_end" value="{{ now()->format('Y-m-d\TH:i') }}"
                                class="form-control form-control-solid ps-12" />
                        </div>
                    </div>
                </div>

                <table class="table align-middle table-row-dashed fs-6 gy-5" id="datatable_ajax">
                    <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">

                            <th class="text-start min-w-250px">Pembayaran</th>
                            <th class="text-start min-w-350px">Submission</th>
                            <th class="text-start min-w-200px">journal</th>
                            <th class="text-start">Status</th>
                            <th class="text-end min-w-100px">Action</th>
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
    $('#datatable_ajax').DataTable({
            processing: true, // Menampilkan indikator loading
            serverSide: true, // Menggunakan server-side processing
            ajax: {
                url: '{{ route('back.finance.verification.datatable') }}',
                type: 'GET',
                data: function(d) {
                    d.journal_id = $('#journal_id').val();
                    d.submission_search = $('#search').val();
                    d.payment_status = $('#payment_status').val();
                    d.payment_timestamp_start = $('#payment_timestamp_start').val();
                    d.payment_timestamp_end = $('#payment_timestamp_end').val();
                }
            },
            columns: [{
                    data: 'payment',
                    name: 'payment',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'submission',
                    name: 'submission',
                },
                {
                    data: 'journal',
                    name: 'journal'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action'
                },

            ],
            "createdRow": function(row, data, dataIndex) {
                $(row).find('td').eq(0).addClass('text-start pe-0');
                $(row).find('td').eq(1).addClass('text-start pe-0');
                $(row).find('td').eq(2).addClass('text-start pe-0');
                $(row).find('td').eq(3).addClass('text-start pe-0');
                $(row).find('td').eq(4).addClass('text-end ');

            }
        });
        $('#journal_id, #search, #payment_status, #payment_timestamp_start, #payment_timestamp_end').on('change keyup', function() {
                $('#datatable_ajax').DataTable().ajax.reload();
            });
</script>
@endsection
