<!--begin::User-->
<div class="aside-user d-flex align-items-sm-center justify-content-center py-5">
    <!--begin::Symbol-->
    <div class="symbol symbol-50px">
        <img src="{{ auth()?->user()?->getPhoto() ?? '' }}" alt="" />
    </div>
    <!--end::Symbol-->
    <!--begin::Wrapper-->
    <div class="aside-user-info flex-row-fluid flex-wrap ms-5">
        <!--begin::Section-->
        <div class="d-flex">
            <!--begin::Info-->
            <div class="flex-grow-1 me-2">
                <!--begin::Username-->
                <a href="#" class="text-white text-hover-primary fs-6 fw-bold">{{ Auth::user()?->name ?? '-' }}</a>
                <!--end::Username-->
                <!--begin::Description-->
                <span
                    class="text-gray-600 fw-semibold d-block fs-8 mb-1">{{ auth()?->user()?->roles?->pluck('name')->join(', ') ?? '' }}</span>
                <!--end::Description-->
                <!--begin::Label-->
                <div class="d-flex align-items-center text-success fs-9">
                    <span class="bullet bullet-dot bg-success me-1"></span>online
                </div>
                <!--end::Label-->
            </div>
            <!--end::Info-->
            <!--end::User menu-->
        </div>
        <!--end::Section-->
    </div>
    <!--end::Wrapper-->
</div>
<!--end::User-->
<!--begin::Aside search-->
@role('super-admin|admin-ejournal|admin-proceeding|admin-student_research_hub|keuangan|keuangan-proceeding|keuangan-student-research-hub|editor|editor-proceeding|editor-student-research-hub')
@php
    $control_panel = Illuminate\Support\Facades\Cookie::get('control_panel');
@endphp
<div class="aside-search py-5">
    <div class="border rounded">
        <select data-control="select2" class="form-select form-select-sm form-select-solid"
            data-placeholder="Pilih Kontrol" onchange="window.location.href = this.value" data-hide-search="true">
            <option></option>
            @role('super-admin|admin-ejournal|keuangan|editor')
            <option value="{{ route('back.switch.control', 'journal') }}" {{ $control_panel == 'journal' ? 'selected' : '' }}>e-Journal</option>
            @endrole
            @role('super-admin|admin-proceeding|keuangan-proceeding|editor-proceeding')
            <option value="{{ route('back.switch.control', 'proceeding') }}" {{ $control_panel == 'proceeding' ? 'selected' : '' }}>Proceeding</option>
            @endrole
            @role('super-admin|admin-student-research-hub|keuangan-student-research-hub|editor-student-research-hub')
            <option value="{{ route('back.switch.control', 'student_research_hub') }}" {{ $control_panel == 'student_research_hub' ? 'selected' : '' }}>Student Research Hub</option>
            @endrole
        </select>
    </div>
</div>
@endrole
<!--end::Aside search-->
