<div class="hover-scroll-overlay-y mx-3 my-5 my-lg-5" id="kt_aside_menu_wrapper" data-kt-scroll="true"
    data-kt-scroll-height="auto"
    data-kt-scroll-dependencies="{default: '#kt_aside_toolbar, #kt_aside_footer', lg: '#kt_header, #kt_aside_toolbar, #kt_aside_footer'}"
    data-kt-scroll-wrappers="#kt_aside_menu" data-kt-scroll-offset="5px">

    <div class="menu menu-column menu-title-gray-800 menu-state-title-primary menu-state-icon-primary menu-state-bullet-primary menu-arrow-gray-500"
        id="#kt_aside_menu" data-kt-menu="true">

        <div data-kt-menu-trigger="click" class="menu-item here show menu-accordion">
            <span class="menu-link">
                <span class="menu-icon">
                    <i class="ki-duotone ki-element-11 fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i></span>
                <span class="menu-title">Dashboards</span>
                <span class="menu-arrow"></span>
            </span>
            <div class="menu-sub menu-sub-accordion">
                <div class="menu-item">
                    <a class="menu-link active" href="?page=index">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Default</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="menu-item pt-5">
            <div class="menu-content">
                <span class="menu-heading fw-bold text-uppercase fs-7">Post</span>
            </div>
        </div>
        <div data-kt-menu-trigger="click"
            class="menu-item menu-accordion @if (request()->routeIs('back.news.*')) here show @endif">
            <span class="menu-link">
                <span class="menu-icon">
                    <i class="ki-duotone ki-book fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                </span>
                <span class="menu-title">Berita</span>
                <span class="menu-arrow"></span>
            </span>
            <div class="menu-sub menu-sub-accordion">
                <div class="menu-item">
                    <a class="menu-link @if (request()->routeIs('back.news.category')) active @endif"
                        href="{{ route('back.news.category') }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Kategori</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link @if (request()->routeIs('back.news.index')) active @endif"
                        href="{{ route('back.news.index') }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">List Berita</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link @if (request()->routeIs('back.news.comment')) active @endif"
                        href="{{ route('back.news.comment') }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Komentar</span>
                    </a>
                </div>
            </div>
        </div>
        <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
            <span class="menu-link">
                <span class="menu-icon">
                    <i class="ki-duotone ki-address-book fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                </span>
                <span class="menu-title">User Profile</span>
                <span class="menu-arrow"></span>
            </span>
            <div class="menu-sub menu-sub-accordion">
                <div class="menu-item">
                    <a class="menu-link" href="?page=pages/user-profile/overview">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Overview</span>
                    </a>
                </div>

                <div class="menu-item">
                    <a class="menu-link" href="?page=pages/user-profile/projects">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Projects</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="menu-item pt-5">
            <div class="menu-content"><span class="menu-heading fw-bold text-uppercase fs-7">Help</span></div>

        </div>
        <div class="menu-item">
            <a class="menu-link" href="https://preview.keenthemes.com/html/metronic/docs/base/utilities"
                target="_blank">
                <span class="menu-icon">
                    <i class="ki-duotone ki-rocket fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </span>
                <span class="menu-title">Components</span>
            </a>
        </div>

        <div class="menu-item">
            <a class="menu-link" href="?page=layout-builder">
                <span class="menu-icon"><i class="ki-duotone ki-abstract-13 fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span></i>
                </span>
                <span class="menu-title">Layout Builder</span>
            </a>
        </div>

        <div class="menu-item pt-5">
            <div class="menu-content"><span class="menu-heading fw-bold text-uppercase fs-7">Administrator</span>
            </div>
        </div>

        <div class="menu-item">
            <a class="menu-link @if (request()->routeIs('back.message.index')) active @endif"
                href="{{ route('back.message.index') }}">
                <span class="menu-icon">
                    <i class="ki-duotone ki-sms fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </span>
                <span class="menu-title">Inbox</span>
            </a>
        </div>

        <div data-kt-menu-trigger="click"
            class="menu-item menu-accordion @if (request()->routeIs('back.setting.*')) here show @endif">
            <span class="menu-link">
                <span class="menu-icon">
                    <i class="ki-duotone ki-setting-2 fs-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </span>
                <span class="menu-title">Pengaturan</span>
                <span class="menu-arrow"></span>
            </span>
            <div class="menu-sub menu-sub-accordion">
                <div class="menu-item">
                    <a class="menu-link @if (request()->routeIs('back.setting.website')) active @endif"
                        href="{{ route('back.setting.website') }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Website</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Banner</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>
