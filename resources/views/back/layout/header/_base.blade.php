<!--begin::Header-->
<div id="kt_header" style="" class="header  align-items-stretch">
    <!--begin::Brand-->
    <div class="header-brand">
        <!--begin::Logo-->
        <a href="?page=index">
            <img alt="Logo" src="{{ asset('back/media/logos/default-dark.svg') }}" class="h-25px h-lg-25px" />
        </a>
        <!--end::Logo-->
        <!--begin::Aside minimize-->
        <div id="kt_aside_toggle"
            class="
                    btn btn-icon w-auto px-0 btn-active-color-primary aside-minimize
                                    "
            data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body"
            data-kt-toggle-name="aside-minimize">
            <i class="ki-duotone ki-entrance-right fs-1 me-n1 minimize-default"><span class="path1"></span><span
                    class="path2"></span></i>
            <i class="ki-duotone ki-entrance-left fs-1 minimize-active"><span class="path1"></span><span
                    class="path2"></span></i>
        </div>
        <!--end::Aside minimize-->
        <!--begin::Aside toggle-->
        <div class="d-flex align-items-center d-lg-none me-n2" title="Show aside menu">
            <div class="btn btn-icon btn-active-color-primary w-30px h-30px" id="kt_aside_mobile_toggle">
                <i class="ki-duotone ki-abstract-14 fs-1"><span class="path1"></span><span class="path2"></span></i>
            </div>
        </div>
        <!--end::Aside toggle-->
    </div>
    <!--end::Brand-->
    <div class="toolbar d-flex align-items-stretch">
        <div
            class=" container-xxl  py-6 py-lg-0 d-flex flex-column flex-lg-row align-items-lg-stretch justify-content-lg-between">
            @include('back/layout/_page-title')
            <div class="d-flex align-items-stretch overflow-auto pt-3 pt-lg-0">

                <div class="d-flex align-items-center">
                    @include('back/partials/theme-mode/_main')
                </div>
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center ps-5">
                        <div class="d-flex">
                            <div class="d-flex align-items-center">
                                <a href="#" class="btn btn-sm btn-icon btn-icon-muted btn-active-icon-primary"
                                    data-kt-menu-trigger="{default:'click', lg: 'hover'}"
                                    data-kt-menu-placement="bottom-start" data-kt-menu-overflow="true">
                                    <img src=" /storage/user/LX16JvmflrhToeInR1rsh8d70el5c6t2su4IEg8B.jpg "
                                        class="rounded-3 h-35px w-35px" alt="user">
                                </a>
                                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                                    data-kt-menu="true" style="">
                                    <div class="menu-item px-5">
                                        <a href="https://simalungun-tourism.com/akun/profil" class="menu-link px-5">
                                            <i class="ki-duotone ki-user fs-2 me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Profil
                                        </a>
                                    </div>
                                    <div class="menu-item px-5">
                                        <a href="https://simalungun-tourism.com/auth/logout" class="menu-link px-5">
                                            <i class="ki-duotone ki-entrance-right fs-2 me-3 minimize-default">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Keluar
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!--end::Header-->
