@extends('back.app')
@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="card">
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-5">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" data-kt-user-table-filter="search"
                                class="form-control form-control-solid w-250px ps-13" placeholder="Cari Editor" />
                        </div>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex justify-content-end" data-kt-user-table-toolbar="base">
                            <button type="button" class="btn btn-light-primary me-3" data-kt-menu-trigger="click"
                                data-kt-menu-placement="bottom-end">
                                <i class="ki-duotone ki-filter fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>Filter</button>
                            <div class="menu menu-sub menu-sub-dropdown w-300px w-md-325px" data-kt-menu="true">
                                <div class="px-7 py-5">
                                    <div class="fs-5 text-gray-900 fw-bold">Filter Options</div>
                                </div>
                                <div class="separator border-gray-200"></div>
                                <div class="px-7 py-5" data-kt-user-table-filter="form">
                                    <div class="mb-5">
                                        <label class="form-label fs-6 fw-semibold">Role</label>
                                        <select class="form-select form-select-solid fw-bold" data-kt-select2="true"
                                            data-placeholder="Select option" data-allow-clear="true"
                                            data-kt-user-table-filter="role" data-hide-search="true">
                                            <option></option>
                                            <option value="super-admin">super-admin</option>
                                            <option value="keuangan">keuangan</option>
                                            <option value="editor">editor</option>
                                        </select>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="reset"
                                            class="btn btn-light btn-active-light-primary fw-semibold me-2 px-6"
                                            data-kt-menu-dismiss="true" data-kt-user-table-filter="reset">Reset</button>
                                        <button type="submit" class="btn btn-primary fw-semibold px-6"
                                            data-kt-menu-dismiss="true" data-kt-user-table-filter="filter">Apply</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end align-items-center d-none">
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
                                <th class="min-w-250px">Editor</th>
                                <th class="min-w-300px text-start">Journal yang dikelola</th>
                                <th class="min-w-150px text-start">Email</th>
                                <th class="min-w-100px text-start">No. Telp</th>
                                <th class="min-w-150px text-start">Rekening</th>
                                <th class="min-w-150px text-start">NPWP</th>
                                <th class="text-end min-w-100px">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-600 fw-semibold">
                            @foreach ($editors as $editor)
                                <tr>
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="1" />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <div class="d-flex align-items-center">
                                                <a href="#" target="_blank"
                                                    class="text-gray-800 text-hover-primary mb-1 me-2">{{ $editor->name }}
                                                </a>
                                                @if ($editor->number)
                                                    <a href="#" data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="Sertifikat Sudah Dikirim"><i
                                                            class="ki-outline ki-file-added fs-2 text-primary"></i></a>
                                                @endif
                                            </div>
                                            <span>
                                                {{ $editor->affiliation }}
                                            </span>
                                            <span class="">
                                                NIK. {{ $editor->data?->nik ?? '-' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-start">
                                        <ul>
                                            @foreach ($editor->journal as $journal)
                                                <li>
                                                    <a href="{{ route('back.journal.index', $journal->url_path) }}"
                                                        class="text-gray-800 text-hover-primary">
                                                        {{ $journal->title }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="text-start">
                                        <span class="text-bold text-gray-800">
                                            {{ $editor->email }}
                                        </span>

                                        <span class="badge badge-light-warning cursor-pointer my-1" data-bs-toggle="modal"
                                            data-bs-target="#modal_send_email_{{ $editor->editor_id }}">
                                            <i class="ki-duotone ki-sms fs-5 text-warning me-3">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            Kirim Email
                                        </span>

                                    </td>
                                    <td class="text-start">
                                        {{ $editor->phone }}
                                    </td>
                                    <td class="text-start">
                                        <span class="fw-bold">{{ $editor->data?->account_bank }}</span><br>
                                        @if ($editor->data?->account_number)
                                            No. Rek: {{ $editor->data?->account_number }}
                                        @endif
                                    </td>
                                    <td class="text-start">
                                        @if ($editor->data?->npwp)
                                            <span class="fw-bold">{{ $editor->data?->npwp }}</span>
                                        @else
                                            <span class="text-muted">Tidak ada NPWP</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="#" class="btn btn-sm btn-light-primary my-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modal_view_article_{{ $editor->editor_id }}">
                                            <i class="ki-duotone ki-eye fs-2">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                                <span class="path3"></span>
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
    </div>
    @foreach ($editors as $editor)
        <div class="modal fade" tabindex="-1" id="modal_view_article_{{ $editor->editor_id }}">
            <div class="modal-dialog mw-650px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Data Editor</h3>
                        <div>
                            <!--begin::synchronize-->
                            <div class="btn btn-icon btn-sm btn-active-light-warning ms-2" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Sinkronisasi Data"
                                onclick="selectEditor({{ $editor->editor_id }})">
                                <i class="ki-duotone ki-arrows-circle fs-1">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </div>
                            <!--begin::Close-->
                            <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                                aria-label="Close">
                                <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                        class="path2"></span></i>
                            </div>
                            <!--end::Close-->
                        </div>
                    </div>
                    <form
                        action="{{ route('back.master.editor.update', [$editor->editor_id]) }}"
                        method="post">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <table class="table table-row-dashed table-row-gray-300 align-top gs-0 gy-4 my-0 fs-6">
                                <tr>
                                    <td>Nama</td>
                                    <td>:</td>
                                    <td>
                                        {{ $editor->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Affiliasi</td>
                                    <td>:</td>
                                    <td>
                                        {{ $editor->affiliation }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>username</td>
                                    <td>:</td>
                                    <td>
                                        {{ $editor->username }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>:</td>
                                    <td>
                                        {{ $editor->email }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>No. Telp</td>
                                    <td>:</td>
                                    <td>
                                        {{ $editor->phone }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="required">NIK</td>
                                    <td>:</td>
                                    <td>
                                        <input type="text" class="form-control" name="nik"
                                            value="{{ $editor->data?->nik }}" placeholder="Nomor Induk Kependudukan"
                                            required />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="required">Rekening</td>
                                    <td>:</td>
                                    <td>
                                        <select class="form-select" data-control="select2" data-placeholder="Pilih Bank"
                                            data-dropdown-parent="#modal_view_article_{{ $editor->editor_id }}"
                                            name="account_bank" data-allow-clear="false" required>
                                            <option></option>
                                            @php
                                                $banks = [
                                                    'Bank Mandiri',
                                                    'Bank Rakyat Indonesia (BRI)',
                                                    'Bank Central Asia (BCA)',
                                                    'Bank Negara Indonesia (BNI)',
                                                    'Bank Tabungan Negara (BTN)',
                                                    'Bank CIMB Niaga',
                                                    'Bank Danamon',
                                                    'Bank Permata',
                                                    'Bank Panin',
                                                    'Bank Maybank Indonesia',
                                                    'Bank Mega',
                                                    'Bank Sinarmas',
                                                    'Bank Bukopin',
                                                    'Bank OCBC NISP',
                                                    'Bank BTPN',
                                                    'Bank JTrust Indonesia',
                                                    'Bank UOB Indonesia',
                                                    'Bank Commonwealth',
                                                    'Bank BJB',
                                                    'Bank DKI',
                                                    'Bank Jateng',
                                                    'Bank Jatim',
                                                    'Bank Sumut',
                                                    'Bank Nagari',
                                                    'Bank Sumsel Babel',
                                                    'Bank Lampung',
                                                    'Bank Kalsel',
                                                    'Bank Kaltimtara',
                                                    'Bank Kalteng',
                                                    'Bank Sulselbar',
                                                    'Bank SulutGo',
                                                    'Bank NTB Syariah',
                                                    'Bank NTT',
                                                    'Bank Maluku Malut',
                                                    'Bank Papua',
                                                    'Bank Bengkulu',
                                                    'Bank Sulteng',
                                                    'Bank Sultra',
                                                    'Bank Aceh Syariah',
                                                    'Bank Banten',
                                                    'Bank Muamalat',
                                                    'Bank Syariah Indonesia (BSI)',
                                                    'Bank Victoria',
                                                    'Bank MNC',
                                                    'Bank Artos Indonesia',
                                                    'Bank QNB Indonesia',
                                                    'Bank INA Perdana',
                                                    'Bank Amar Indonesia',
                                                    'Bank Capital Indonesia',
                                                    'Bank Harda Internasional',
                                                    'Bank Index Selindo',
                                                    'Bank Mestika Dharma',
                                                    'Bank Mayapada',
                                                    'Bank Mayora',
                                                    'Bank Multiarta Sentosa',
                                                    'Bank Nationalnobu',
                                                    'Bank Prima Master',
                                                    'Bank Sahabat Sampoerna',
                                                    'Bank SBI Indonesia',
                                                    'Bank Seabank Indonesia',
                                                    'Bank Shinhan Indonesia',
                                                    'Bank Woori Saudara',
                                                    'Bank HSBC Indonesia',
                                                    'Bank Standard Chartered',
                                                    'Bank Citibank',
                                                    'Bank ANZ Indonesia',
                                                    'Bank DBS Indonesia',
                                                    'Bank Resona Perdania',
                                                    'Bank Mizuho Indonesia',
                                                    'Bank Sumitomo Mitsui Indonesia',
                                                    'Bank BNP Paribas Indonesia',
                                                    'Bank Rabobank International Indonesia',
                                                    'Bank of China Indonesia',
                                                    'Bank of India Indonesia',
                                                    'Bank ICBC Indonesia',
                                                    'Bank CTBC Indonesia',
                                                    'Bank Maybank Syariah Indonesia',
                                                    'Bank BCA Syariah',
                                                    'Bank BRI Syariah',
                                                    'Bank BNI Syariah',
                                                    'Bank Panin Dubai Syariah',
                                                    'Bank Victoria Syariah',
                                                    'Bank Aladin Syariah',
                                                    'Bank Jago',
                                                    'Bank Neo Commerce',
                                                    'Bank Digital BCA (blu)',
                                                    'Bank SeaBank',
                                                    'Bank Allo Bank',
                                                    'Bank Raya Indonesia',
                                                    'Bank KEB Hana Indonesia',
                                                    'Bank Mandiri Taspen',
                                                    'Bank Fama International',
                                                    'Bank Bisnis Internasional',
                                                    'Bank Oke Indonesia',
                                                    'Bank KB Bukopin Syariah',
                                                    'Bank BSI (Bank Syariah Indonesia)',
                                                ];
                                            @endphp
                                            @foreach ($banks as $bank)
                                                <option value="{{ $bank }}"
                                                    {{ isset($editor->data?->account_bank) && $editor->data?->account_bank == $bank ? 'selected' : '' }}>
                                                    {{ $bank }}</option>
                                            @endforeach
                                        </select>
                                        <input type="text" class="form-control mt-2" placeholder="No. Rekening"
                                            name="account_number" value="{{ $editor->data?->account_number }}" required />

                                    </td>
                                </tr>
                                <tr>
                                    <td class="">NPWP</td>
                                    <td>:</td>
                                    <td>
                                        <input type="text" class="form-control" name="npwp"
                                            value="{{ $editor->data?->npwp }}" placeholder="Nomor Pokok Wajib Pajak" />
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
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
