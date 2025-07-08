@extends('back.app')
@section('content')
    @php
        [$before, $after] = explode(' - ', $event->datetime);
        $date_before = \Carbon\Carbon::parse($before)->toDateTimeString();
        $date_after = \Carbon\Carbon::parse($after)->toDateTimeString();
        // dd($date_before, $date_after);
    @endphp
    <div id="kt_content_container" class=" container-xxl ">
        @include('back.pages.event.detail.header')
        <div class="card mb-5 " id="kt_profile_details_view">
            <div class="card-header cursor-pointer">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Details</h3>
                </div>
                <a href="#" class="btn btn-sm btn-primary align-self-center" data-bs-toggle="modal"
                    data-bs-target="#editDaftarKehadiran">Edit Daftar Kehadiran</a>
            </div>
            <div class="card-body p-9">
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Nama Daftar Kehadiran</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $attendance->name }}</span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">waktu Mulai</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">
                            {{ \Carbon\Carbon::parse($attendance->start_datetime)->format('d M Y H:i') }}</span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Waktu Selesai</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">
                            {{ \Carbon\Carbon::parse($attendance->end_datetime)->format('d M Y H:i') }}</span>
                    </div>
                </div>
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold text-muted">Deskripsi</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $attendance->description ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-5 ">

            <div class="card-body p-9">
                <div class="w-100">
                    <h4 class="fs-5 fw-semibold text-gray-800">Link Absensi mandiri</h4>
                    <div class="d-flex">
                        <input id="kt_share_earn_link_input" type="text"
                            class="form-control form-control-solid me-3 flex-grow-1"
                            value="https://keenthemes.com/?ref=keenthemes_user" readonly />

                        <button id="kt_share_earn_link_copy_button" class="btn btn-light fw-bold flex-shrink-0"
                            data-clipboard-target="#kt_share_earn_link_input">Copy Link</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <input type="text" id="search" class="form-control form-control-solid w-250px ps-13"
                            placeholder="Nama Peserta" />
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">


                        {{-- <a href="{{ route('back.master.user.create') }}" class="btn btn-primary me-3">
                            <i class="ki-duotone ki-plus fs-2"></i>Tambah Pengguna</a> --}}
                        <div class="btn-group">

                            {{-- <a href="#" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#import">
                                <i class="ki-duotone ki-file-down fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Import</a> --}}
                            <a class="btn btn-secondary" href="#">
                                <i class="ki-duotone ki-file-up fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                Export
                            </a>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end align-items-center d-none" {{-- data-kt-user-table-toolbar="selected" --}}>
                        <div class="fw-bold me-5">
                            <span class="me-2" data-kt-user-table-select="selected_count"></span>Selected
                        </div>
                        <button type="button" class="btn btn-danger" data-kt-user-table-select="delete_selected">Delete
                            Selected</button>
                    </div>

                </div>
            </div>
            <div class="card-body py-4">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="datatable_ajax">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">

                            <th class="min-w-125px">Pengguna</th>
                            <th class="min-w-125px">Nama Terdaftar</th>
                            <th class="min-w-125px">Email Terdaftar</th>
                            <th class="min-w-125px">No.telp Terdaftar</th>
                            <th class="min-w-200px">Kehadiran</th>
                            <th class="text-end min-w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="editDaftarKehadiran">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">
                        Edit Daftar Kehadiran
                    </h3>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>
                <form action="{{ route('back.event.detail.attendance.update', [$event->id, $attendance->id]) }}"
                    method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-7">
                            <label for="name" class="form-label required">Nama Daftar Kehadiran</label>
                            <input type="text" class="form-control form-control-solid" name="name" id="name"
                                value="{{ old('name', $attendance->name) }}" placeholder="Masukkan nama daftar kehadiran"
                                required />
                        </div>
                        <div class="mb-7">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control form-control-solid" name="description" id="description" placeholder="deskripsi"
                                rows="3">{{ old('description', $attendance->description) }}</textarea>
                        </div>
                        <div class="mb-7">
                            <label for="start_datetime" class="form-label required">Waktu Mulai</label>
                            <input type="datetime-local" class="form-control form-control-solid" name="start_datetime"
                                id="start_datetime"
                                value="{{ old('start_datetime', \Carbon\Carbon::parse($attendance->start_datetime)->format('Y-m-d\TH:i')) }}"
                                placeholder="Masukkan tanggal mulai" required />
                        </div>
                        <div class="mb-7">
                            <label for="end_datetime" class="form-label required">Waktu Selesai</label>
                            <input type="datetime-local" class="form-control form-control-solid" name="end_datetime"
                                id="end_datetime"
                                value="{{ old('end_datetime', \Carbon\Carbon::parse($attendance->end_datetime)->format('Y-m-d\TH:i')) }}"
                                placeholder="Masukkan tanggal selesai" required />
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
        var table = $('#datatable_ajax').DataTable({
            // processing: true, // Menampilkan indikator loading
            serverSide: true, // Menggunakan server-side processing
            ajax: {
                url: '{{ route('back.event.detail.attendance.detail.datatable', [$event->id, $attendance->id]) }}',
                type: 'GET',
                data: function(d) {
                    d.name = $('#search').val();
                }
            },
            columns: [{
                    data: 'user',
                    name: 'user',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name',
                },
                {
                    data: 'email',
                    name: 'email',
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'attendance',
                    name: 'attendance'
                },
                {
                    data: 'action',
                    name: 'action'
                },

            ],
            "createdRow": function(row, data, dataIndex) {
                $(row).find('td').eq(0).addClass('d-flex align-items-center ');
                $(row).find('td').eq(1).addClass('text-start pe-0');
                $(row).find('td').eq(2).addClass('text-start pe-0');
                $(row).find('td').eq(3).addClass('text-start pe-0');
                $(row).find('td').eq(4).addClass('text-start pe-0');
                $(row).find('td').eq(5).addClass('text-end ');

            }
        });
        // Update the summary counts
        table.on('xhr', function() {
            var json = table.ajax.json();
            $('#payment_pending').text(json.payment_pending);
            $('#payment_accepted').text(json.payment_accepted);
            $('#payment_rejected').text(json.payment_rejected);
            $('#payment_total').text(json.payment_total);
        });
        $('#search').on('change keyup',
            function() {
                $('#datatable_ajax').DataTable().ajax.reload();
            });
        $(document).on('click', '[id^="checkin-button-"]', function() {
            var button = $(this);
            var link = button.attr('link');
            Swal.fire({
                title: 'Konfirmasi Kehadiran',
                text: "Apakah Anda yakin ingin membuat kehadiran untuk peserta ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Buat Kehadiran',
                cancelButtonText: 'Tidak, Batalkan'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: link,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Kehadiran Dibuat',
                                text: 'Kehadiran untuk peserta ini telah berhasil dibuat.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                // Reload the DataTable to reflect the changes
                                $('#datatable_ajax').DataTable().ajax.reload();
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'Error',
                                text: 'Terjadi kesalahan saat membuat kehadiran. Silakan coba lagi.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });

        var button = document.querySelector('#kt_share_earn_link_copy_button');
        var input = document.querySelector('#kt_share_earn_link_input');
        var clipboard = new ClipboardJS(button);

        if (!clipboard) {
            console.error('ClipboardJS is not defined. Please ensure you have included the ClipboardJS library.');
        } else {
            console.log('ClipboardJS initialized successfully.');
        }

        //  Copy text to clipboard. For more info check the plugin's documentation: https://clipboardjs.com/
        clipboard.on('success', function(e) {
            var buttonCaption = button.innerHTML;
            //Add bgcolor
            input.classList.add('bg-success');
            input.classList.add('text-inverse-success');

            button.innerHTML = 'Copied!';

            setTimeout(function() {
                button.innerHTML = buttonCaption;

                // Remove bgcolor
                input.classList.remove('bg-success');
                input.classList.remove('text-inverse-success');
            }, 3000); // 3seconds

            e.clearSelection();
        });
    </script>
@endsection
