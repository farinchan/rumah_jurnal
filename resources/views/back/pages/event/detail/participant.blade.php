@extends('back.app')
@section('styles')
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
@endsection
@section('content')
    @php
        [$before, $after] = explode(' - ', $event->datetime);
        $date_before = \Carbon\Carbon::parse($before)->toDateTimeString();
        $date_after = \Carbon\Carbon::parse($after)->toDateTimeString();
        // dd($date_before, $date_after);
    @endphp
    <div id="kt_content_container" class=" container-xxl ">
        @include('back.pages.event.detail.header')
        <div class="card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <input type="text" data-kt-user-table-filter="search"
                            class="form-control form-control-solid w-250px ps-13" placeholder="Cari Pengguna" />
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
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_table_users">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">
                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                                        data-kt-check-target="#kt_table_users .form-check-input" value="1" />
                                </div>
                            </th>
                            <th class="min-w-125px">Pengguna</th>
                            <th class="min-w-125px">Nama Terdaftar</th>
                            <th class="min-w-125px">Email Terdaftar</th>
                            <th class="min-w-125px">No.telp Terdaftar</th>
                            <th class="min-w-125px">Tanggal Mendaftar</th>
                            <th class="text-end min-w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" value="1" />
                                    </div>
                                </td>
                                <td class="d-flex align-items-center">
                                    <div class="symbol symbol-circle symbol-50px overflow-hidden me-3">
                                        <a href="#">
                                            <div class="symbol-label">
                                                <img src="{{ $user->user->getPhoto() }}" alt="{{ $user->name }}"
                                                    width="50px" />
                                            </div>
                                        </a>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <a href="#"
                                            class="text-gray-800 text-hover-primary mb-1">{{ $user->name }}</a>
                                        <span>{{ $user->email ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    {{ $user->name ?? '-' }}
                                </td>
                                <td>
                                    {{ $user->email ?? '-' }}
                                </td>
                                <td>
                                    {{ $user->phone ?? '-' }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d F Y H:i') }}
                                </td>

                                <td class="text-end">
                                    <a href="{{ route("event.eticket", $user->id) }}" target="_blank"
                                         class="btn btn-icon btn-light-info btn-sm me-1"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="E-Ticket">
                                        <i class="ki-duotone ki-tablet-book fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>

                                    </a>
                                    <a href="#" class="btn btn-icon btn-light-danger btn-sm me-1"
                                        data-bs-toggle="modal" data-bs-target="#kt_modal_delete_user{{ $user->id }}">
                                        <i class="ki-duotone ki-trash fs-2" data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Hapus peserta">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach ($users as $user)
        <div class="modal fade" id="kt_modal_delete_user{{ $user->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="fw-bold">Hapus Pengguna</h2>
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                        </div>
                    </div>
                    <form class="form" method="POST"
                        action="{{ route('back.event.detail.participant.destroy', [$event->id, $user->id]) }}">
                        @method('DELETE')
                        @csrf
                        <div class="modal-body px-5">
                            <p class="">
                                Apakah Anda Yakin Ingin Menghapus peserta {{ $user->name }} ?
                            </p>
                            <p class="text-danger ">
                                <b>Warning!</b> Pengguna yang dihapus tidak dapat dikembalikan lagi, dan semua data yang
                                terkait dengan pengguna ini akan hilang.
                            </p>


                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus
                                Pengguna</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@section('scripts')
    <script src="{{ asset('back/js/custom/apps/user-management/users/list/table.js') }}"></script>
@endsection
