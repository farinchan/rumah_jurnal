@extends('back.app')

@section('content')
    <!--begin::Container-->
    <div id="kt_content_container" class=" container-xxl ">
        <!--begin::Row-->
        <div class="row g-5 g-xl-8">
            <div class="col-xl-4">
                <!--begin::Statistics Widget 5-->
                <a href="#" class="card bg-body-white hoverable card-xl-stretch mb-xl-8">
                    <!--begin::Body-->
                    <div class="card-body">
                        <i class="ki-duotone ki-basket text-primary fs-2x ms-n1"><span class="path1"></span><span
                                class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5">
                            Shopping Cart
                        </div>
                        <div class="fw-semibold text-gray-400">
                            Lands, Houses, Ranchos, Farms </div>
                    </div>
                    <!--end::Body-->
                </a>
                <!--end::Statistics Widget 5-->
            </div>
            <div class="col-xl-4">
                <!--begin::Statistics Widget 5-->
                <a href="#" class="card bg-primary hoverable card-xl-stretch mb-xl-8">
                    <!--begin::Body-->
                    <div class="card-body">
                        <i class="ki-duotone ki-element-11 text-white fs-2x ms-n1"><span class="path1"></span><span
                                class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                        <div class="text-white fw-bold fs-2 mb-2 mt-5">
                            Appartments
                        </div>
                        <div class="fw-semibold text-white">
                            Flats, Shared Rooms, Duplex </div>
                    </div>
                    <!--end::Body-->
                </a>
                <!--end::Statistics Widget 5-->
            </div>
            <div class="col-xl-4">
                @include('back/partials/widgets-2/statistics/_widget-5')
            </div>
        </div>
        <!--end::Row-->
        <!--begin::Row-->
        <div class="row g-5 g-xl-8">
            <!--begin::Col-->
            <div class="col-xl-4">
                @include('back/partials/widgets-2/lists/_widget-1')
            </div>
            <!--end::Col-->
            <!--begin::Col-->
            <div class="col-xl-8">
                @include('back/partials/widgets-2/tables/_widget-5')
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->
        <!--begin::Row-->
        <div class="row g-5 g-xl-8">
            <div class="col-xl-4">
                @include('back/partials/widgets-2/lists/_widget-3')
            </div>
            <div class="col-xl-8">
                @include('back/partials/widgets-2/charts/_widget-1')
            </div>
        </div>
        <!--end::Row-->
        <!--begin::Row-->
        <div class="row g-5 g-xl-8">
            <div class="col-xl-6">
                @include('back/partials/widgets-2/lists/_widget-7')
            </div>
            <div class="col-xl-6">
                @include('back/partials/widgets-2/lists/_widget-6')
            </div>
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
@endsection
