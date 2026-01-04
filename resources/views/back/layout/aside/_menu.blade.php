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
                    <a class="menu-link @if (request()->routeIs('back.dashboard')) active @endif"
                        href="{{ route('back.dashboard') }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Default</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link @if (request()->routeIs('back.dashboard.news')) active @endif"
                        href="{{ route('back.dashboard.news') }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Berita</span>
                    </a>
                    @role('super-admin|keuangan')
                        <a class="menu-link @if (request()->routeIs('back.dashboard.cashflow')) active @endif"
                            href="{{ route('back.dashboard.cashflow') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Cashflow</span>
                        </a>
                    @endrole
                </div>
            </div>
        </div>

        @role('humas|super-admin')
            <div class="menu-item pt-5">
                <div class="menu-content">
                    <span class="menu-heading fw-bold text-uppercase fs-7">Post</span>
                </div>
            </div>

            <div class="menu-item">
                <a class="menu-link @if (request()->routeIs('back.announcement.index')) active @endif"
                    href="{{ route('back.announcement.index') }}">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-information fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                    </span>
                    <span class="menu-title">Pengumuman</span>
                </a>
            </div>

            <div class= "menu-item">
                <a class="menu-link @if (request()->routeIs('back.event.*')) active @endif"
                    href=" {{ route('back.event.index') }} ">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-pin fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </span>
                    <span class="menu-title">Event</span>
                </a>
            </div>
            <div data-kt-menu-trigger="click"
                class="menu-item menu-accordion @if (request()->routeIs('back.news.*')) here show @endif">
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-document fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
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

            <div class= "menu-item">
                <a class="menu-link @if (request()->routeIs('back.welcomeSpeech.index')) active @endif"
                    href="{{ route('back.welcomeSpeech.index') }}">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-star fs-2"></i>
                    </span>
                    <span class="menu-title">Tentang kami</span>
                </a>
            </div>

            <div class= "menu-item">
                <a class="menu-link @if (request()->routeIs('back.menu.profil.*')) active @endif"
                    href="{{ route('back.menu.profil.index') }}">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-burger-menu-5 fs-2"></i>
                    </span>
                    <span class="menu-title">Menu Profil</span>
                </a>
            </div>
        @endrole

        @role('editor|super-admin')


            @php
                $journal_all = App\Models\Journal::where('type', 'journal')->get();
                $journal_permission_exists = false;
                foreach ($journal_all as $journal) {
                    if (auth()->user()->can($journal->url_path)) {
                        $journal_permission_exists = true;
                        break;
                    }
                }
            @endphp

            @if ($journal_permission_exists)
                <div class="menu-item pt-5">
                    <div class="menu-content"><span class="menu-heading fw-bold text-uppercase fs-7">Jurnal</span>
                    </div>
                </div>
            @endif

            @foreach ($journal_all as $journal)
                @can($journal->url_path)
                    <div class="menu-item">
                        <a class="menu-link @if (request()->segment(3) == $journal->url_path) active @endif"
                            href="{{ route('back.journal.index', $journal->url_path) }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-book fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ $journal->name }}</span>
                        </a>
                    </div>
                @endcan
            @endforeach


            @php
                $proceeding_all = App\Models\Journal::where('type', 'proceeding')->get();
                $proceeding_permission_exists = false;
                foreach ($proceeding_all as $proceeding) {
                    if (auth()->user()->can($proceeding->url_path)) {
                        $proceeding_permission_exists = true;
                        break;
                    }
                }
            @endphp

            @if ($proceeding_permission_exists)
                <div class="menu-item pt-5">
                    <div class="menu-content"><span class="menu-heading fw-bold text-uppercase fs-7">Proceeding</span>
                    </div>
                </div>
            @endif

            @foreach ($proceeding_all as $proceeding)
                @can($proceeding->url_path)
                    <div class="menu-item">
                        <a class="menu-link @if (request()->segment(3) == $proceeding->url_path) active @endif"
                            href="{{ route('back.journal.index', $proceeding->url_path) }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-book fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ $proceeding->name }}</span>
                        </a>
                    </div>
                @endcan
            @endforeach


            @php
                $student_research_hub_all = App\Models\Journal::where('type', 'student_research_hub')->get();
                $student_research_hub_permission_exists = false;
                foreach ($student_research_hub_all as $student_research_hub) {
                    if (auth()->user()->can($student_research_hub->url_path)) {
                        $student_research_hub_permission_exists = true;
                        break;
                    }
                }
            @endphp

            @if ($student_research_hub_permission_exists)
                <div class="menu-item pt-5">
                    <div class="menu-content"><span class="menu-heading fw-bold text-uppercase fs-7">Student Research
                            Hub</span>
                    </div>
                </div>
            @endif

            @foreach ($student_research_hub_all as $student_research_hub)
                @can($student_research_hub->url_path)
                    <div class="menu-item">
                        <a class="menu-link @if (request()->segment(3) == $student_research_hub->url_path) active @endif"
                            href="{{ route('back.journal.index', $student_research_hub->url_path) }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-book fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ $student_research_hub->name }}</span>
                        </a>
                    </div>
                @endcan
            @endforeach
        @endrole

        @role('keuangan|super-admin')
            <div class="menu-item pt-5">
                <div class="menu-content"><span class="menu-heading fw-bold text-uppercase fs-7">Keuangan</span>
                </div>
            </div>

            <div class= "menu-item">
                <a class="menu-link @if (request()->routeIs('back.finance.verification.index')) active @endif"
                    href="{{ route('back.finance.verification.index') }}">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-two-credit-cart fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                            <span class="path5"></span>
                        </i>
                    </span>
                    <span class="menu-title">Verifikasi</span>
                    @php
                        $payment_count = App\Models\Payment::where('payment_status', 'pending')->count();
                    @endphp
                    @if ($payment_count > 0)
                        <span class="menu-badge">
                            <span class="badge badge-warning"> {{ $payment_count }} </span>
                        </span>
                    @endif
                </a>
            </div>
            <div class="menu-item">
                <a class="menu-link @if (request()->routeIs('back.finance.cashflow.index')) active @endif"
                    href="{{ route('back.finance.cashflow.index') }}">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-wallet fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                    </span>
                    <span class="menu-title">CashFlow</span>
                </a>
            </div>

            <div class= "menu-item">
                <a class="menu-link @if (request()->routeIs('back.finance.report.index')) active @endif"
                    href="{{ route('back.finance.report.index') }}">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-financial-schedule fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                            <span class="path4"></span>
                        </i>
                    </span>
                    <span class="menu-title">Laporan</span>
                </a>
            </div>
        @endrole


        @role('super-admin')
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
                class="menu-item menu-accordion @if (request()->routeIs('back.master.*')) here show @endif">
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-abstract-24 fs-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </span>
                    <span class="menu-title">Master Data</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion">
                    <div class="menu-item">
                        <a class="menu-link @if (request()->routeIs('back.master.journal.*')) active @endif"
                            href="{{ route('back.master.journal.index') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Journal</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link @if (request()->routeIs('back.master.user.*')) active @endif"
                            href="{{ route('back.master.user.index') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Pengguna</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link @if (request()->routeIs('back.master.editor.*')) active @endif"
                            href="{{ route('back.master.editor.index') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Editor</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link @if (request()->routeIs('back.master.reviewer.*')) active @endif"
                            href="{{ route('back.master.reviewer.index') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Reviewer</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link @if (request()->routeIs('back.master.payment-account.index')) active @endif"
                            href="{{ route('back.master.payment-account.index') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Rekening Pembayaran</span>
                        </a>
                    </div>
                </div>
            </div>

            <div data-kt-menu-trigger="click"
                class="menu-item menu-accordion @if (request()->routeIs('back.chatery-whatsapp.*')) here show @endif">
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-setting-4 fs-2"></i>
                    </span>
                    <span class="menu-title">Whatsapp API</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion">
                    <div class="menu-item">
                        <a class="menu-link @if (request()->routeIs('back.chatery-whatsapp.setting')) active @endif"
                            href="{{ route('back.chatery-whatsapp.setting') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Pengaturan</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link @if (request()->routeIs('back.chatery-whatsapp.message.*')) active @endif"
                            href="{{ route('back.chatery-whatsapp.message.index') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Kirim Pesan</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- <div data-kt-menu-trigger="click"
                class="menu-item menu-accordion @if (request()->routeIs('back.whatsapp.*')) here show @endif">
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-setting-4 fs-2"></i>
                    </span>
                    <span class="menu-title">Whatsapp API</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion">
                    <div class="menu-item">
                        <a class="menu-link @if (request()->routeIs('back.whatsapp.setting')) active @endif"
                            href="{{ route('back.whatsapp.setting') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Pengaturan</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link @if (request()->routeIs('back.whatsapp.message.*')) active @endif"
                            href="{{ route('back.whatsapp.message.index') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Kirim Pesan</span>
                        </a>
                    </div>
                </div>
            </div> --}}

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
                        <a class="menu-link @if (request()->routeIs('back.setting.banner')) active @endif"
                            href="{{ route('back.setting.banner') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Banner</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link @if (request()->routeIs('back.setting.bot')) active @endif"
                            href="{{ route('back.setting.bot') }}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">Bot AI</span>
                        </a>
                    </div>
                </div>
            </div>
        @endrole

    </div>

</div>
