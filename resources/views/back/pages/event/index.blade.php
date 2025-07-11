@extends('back.app')
@section('content')
    <div id="kt_content_container" class=" container-xxl ">

        <div class="card card-flush">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title">
                    <div class="d-flex align-items-center position-relative my-1">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <input type="text" data-kt-ecommerce-category-filter="search"
                            class="form-control form-control-solid w-250px ps-12" placeholder="Cari agenda" />
                    </div>
                </div>
                <div class="card-toolbar">
                    <a href="{{ route('back.event.create') }}" class="btn btn-primary">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Buat agenda
                    </a>
                </div>
            </div>
            <div class="card-body pt-0">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_ecommerce_category_table">
                    <thead>
                        <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-10px pe-2">
                                <div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                    <input class="form-check-input" type="checkbox" data-kt-check="true"
                                        data-kt-check-target="#kt_ecommerce_category_table .form-check-input"
                                        value="1" />
                                </div>
                            </th>
                            <th class="min-w-250px">Event</th>
                            <th class="min-w-100px">Tipe</th>
                            <th class="min-w-50px">Status Event</th>
                            <th class="min-w-50px">Aktif?</th>
                            <th class="min-w-150px">Dibuat Oleh</th>
                            <th class="text-end min-w-70px">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                        @foreach ($list_event as $event)
                            @php
                                [$before, $after] = explode(' - ', $event->datetime);
                                $date_before = \Carbon\Carbon::parse($before)->toDateTimeString();
                                $date_after = \Carbon\Carbon::parse($after)->toDateTimeString();
                                // dd($date_before, $date_after);
                            @endphp
                            <tr>
                                <td>
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" value="1" />
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <a href="#" class="symbol symbol-50px">
                                            <span class="symbol-label"
                                                style="background-image:url({{ $event->getThumbnail() }});"></span>
                                        </a>
                                        <div class="ms-5">
                                            <a href="#" class="text-gray-800 text-hover-primary fs-5 fw-bold mb-1"
                                                data-kt-ecommerce-category-filter="category_name">{{ $event->name }}</a>
                                            <div class="text-muted fs-7 fw-bold">
                                                Waktu: {{ $event->datetime }}
                                            </div>
                                            <div class="text-muted fs-7 fw-bold">
                                                @if ($event->status == 'online')
                                                    Link Meet : {{ $event->location }}
                                                @elseif ($event->status == 'offline')
                                                    Lokasi : {{ $event->location }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $event->type }}
                                </td>
                                <td>
                                    {{ $event->status }}
                                </td>
                                <td>
                                    @if ($date_before > now() && $date_after < now())
                                        <span class="badge badge-light-warning">Berlangsung</span>
                                    @elseif($date_before > now())
                                        <span class="badge badge-light-info">Akan Datang</span>
                                    @elseif($date_after < now())
                                        <span class="badge badge-light-danger">Selesai</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex justify-content-start flex-column">
                                            <a href="#" class="text-gray-800 text-hover-primary fw-bolder fs-6">
                                                {{ $event->user->name }}</a>
                                            <span
                                                class="text-muted fw-bold">{{ $event->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('back.event.detail.overview', $event->id) }}"
                                        class="btn btn-sm  btn-light-primary">
                                        <i class="ki-duotone ki-eye fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>



@endsection


@section('scripts')
    <script src="{{ asset('back/js/custom/apps/ecommerce/catalog/categories.js') }}"></script>
@endsection
