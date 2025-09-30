@extends('back.app')
@section('content')
    <div id="kt_content_container" class=" container-xxl ">
        <div class="card card-flush">
            <div class="card-header align-items-center py-5 gap-2 gap-md-5">
                <div class="card-title">
                    <h2 class="fw-bolder">Journal</h2>
                </div>
                <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_journal">
                        <i class="ki-duotone ki-plus fs-2"></i>
                        Tambah Jurnal
                    </a>
                </div>
            </div>
        </div>
        <div class="row g-6 mb-6 g-xl-9 mb-xl-9 mt-3">
            @forelse ($journals as $journal)
                <div class="col-md-6 col-xxl-4">
                    <div class="card ">
                        <div class="card-body d-flex flex-center flex-column py-9 px-5">
                            <div class="symbol symbol-100px  mb-5">
                                <img src="{{ $journal->getJournalThumbnail() }}" alt="image">
                            </div>
                            <a href="{{ $journal->url }}" target="_blank"
                                class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0 text-center">{{ $journal->title }}</a>
                            <div class="fw-semibold text-gray-500  ">Name: {{ $journal->name }}</div>
                            <div class="fw-semibold text-gray-500  ">Path: {{ $journal->url_path }}</div>
                            <div class="fw-semibold text-gray-500  ">e-ISSN: {{ $journal->onlineIssn }} | p-ISSN:
                                {{ $journal->printIssn }}</div>
                            <div class="fw-semibold text-gray-500  ">Akreditasi:
                                @foreach ($journal->indexing ?? [] as $akreditasi_item)
                                    <span class="badge badge-light-primary">{{ $akreditasi_item }}</span>
                                @endforeach
                            </div>
                            <div class="fw-semibold text-gray-500 mb-3 ">
                                Editor Chief: {{ $journal->editor_chief_name ?? '-' }}
                            </div>
                            <div class="d-flex flex-center flex-wrap mb-3">
                                <div class="border border-dashed rounded min-w-90px py-3 px-4 mx-2 mb-3">
                                    <div class="fs-6 fw-bold text-gray-700 text-center">
                                        @money($journal->author_fee ?? 0)</div>
                                    <div class="fw-semibold text-gray-500">Biaya Publikasi</div>
                                </div>

                            </div>
                            <div>
                                <button class="btn btn-light-info btn-sm" onclick="syncJournal('{{ $journal->url_path }}')">
                                    <i class="ki-duotone ki-fasten fs-2" data-bs-toggle="tooltip" data-bs-placement="bottom"
                                        title="Sinkronisasi Ulang Jurnal">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i> Sync
                                </button>
                                <a href="#" class="btn  btn-light-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#edit_journal_{{ $journal->id }}">
                                    <i class="ki-duotone ki-message-edit fs-2" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title="edit Informasi Jurnal">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i> Edit
                                </a>
                                <a href="#" class="btn  btn-light-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#delete_journal_{{ $journal->id }}">
                                    <i class="ki-duotone ki-trash-square fs-2" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title="Hapus Jurnal">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                    </i> Hapus
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12">
                    <div class="alert alert-info">Belum ada jurnal yang diimport</div>
                </div>
            @endforelse

        </div>
    </div>
    <div class="modal fade" tabindex="-1" id="add_journal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Import Jurnal</h3>
                    <!--begin::Close-->
                    <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                        aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                    </div>
                    <!--end::Close-->
                </div>
                <form method="post" id="form_import_journal">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12 mb-10">
                                <label class="form-label">Nama Jurnal</label>
                                <input type="text" name="name" class="form-control"
                                    placeholder="Short Name Journal" />
                                <span class="form-text text-muted">Masukkan Nama jurnal yang ingin diimport,
                                    direkomendasikan
                                    menggunakan nama jurnal yang singkat dan jelas</span>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Link Jurnal</label>
                                <input type="url" name="url" class="form-control"
                                    placeholder="https://your-journal.org/journal-path" />
                                <span class="form-text text-muted">Masukkan URL jurnal yang ingin diimport pastikan SSL
                                    (<code>https</code>) sudah aktif</span>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Path Jurnal</label>
                                <input type="text" name="url_path" class="form-control" placeholder="journal-path" />
                            </div>

                            <div class="col-md-12 mt-10">
                                <label class="form-label">Versi OJS</label>
                                <select name="ojs_version" class="form-select">
                                    <option value="3.4" disabled>OJS 3.4 - coming soon</option>
                                    <option value="3.3">OJS 3.3</option>
                                    <option value="3.2" disabled>OJS 3.2</option>
                                </select>
                                <span class="form-text text-muted">Pilih versi OJS yang digunakan, Pastikan versi OJS yang
                                    dipilih sesuai dengan versi OJS jurnal yang ingin diimport
                                </span>
                            </div>

                            <div class="col-md-12 mt-10">
                                <label class="form-label">API KEY</label>
                                <textarea name="api_key" class="form-control" rows="3" placeholder="APIKEY jurnal"></textarea>
                                <span class="form-text text-muted">Masukkan API KEY jurnal yang ingin diimport, disarankan
                                    menggunakan apikey administrator jurnal</span><br>
                                <span class="form-text text-muted">cara mendapatkan API KEY: Klik pada <b>Nama Pengguna</b>
                                    > Pilih <b>View Profile</b> > Klik tab <b>API Key</b> > Klik <b>Generate API
                                        Key</b></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import & Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach ($journals as $journal)
        <div class="modal fade" tabindex="-1" id="edit_journal_{{ $journal->id }}">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Edit informasi Jurnal</h3>
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                    class="path2"></span></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form method="post" action="{{ route('back.master.journal.update', $journal->id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-5">
                                <label class="form-label">Nama Jurnal</label>
                                <input type="text" name="name" class="form-control"
                                    placeholder="Short Name Journal" value="{{ $journal->name }}" />
                                <span class="form-text text-muted">Masukkan Nama jurnal yang ingin diimport,
                                    direkomendasikan
                                    menggunakan nama jurnal yang singkat dan jelas</span>
                            </div>
                            <div class="mb-5">
                                <label class="form-label">Biaya Publikasi</label>
                                <div class="input-group mb-5">
                                    <span class="input-group-text" id="basic-addon3">Rp.</span>
                                    <input type="number" name="author_fee" class="form-control"
                                        value="{{ $journal->author_fee }}" placeholder="100XXX" />
                                </div>
                            </div>
                            <div class="mb-5">
                                <label class="form-label">Akreditasi Jurnal</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="Scopus Q1" id="scopus_q1"
                                                @if (in_array('Scopus Q1', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="scopus_q1">
                                                Scopus Q1
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="Scopus Q2" id="scopus_q2"
                                                @if (in_array('Scopus Q2', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="scopus_q2">
                                                Scopus Q2
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="Scopus Q3" id="scopus_q3"
                                                @if (in_array('Scopus Q3', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="scopus_q3">
                                                Scopus Q3
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="Scopus Q4" id="scopus_q4"
                                                @if (in_array('Scopus Q4', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="scopus_q4">
                                                Scopus Q4
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="Scopus Non-Q" id="scopus_non_q"
                                                @if (in_array('Scopus Non-Q', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="scopus_non_q">
                                                Scopus Non-Q
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="Sinta 1" id="sinta_1"
                                                @if (in_array('Sinta 1', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="sinta_1">
                                                Sinta 1
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="Sinta 2" id="sinta_2"
                                                @if (in_array('Sinta 2', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="sinta_2">
                                                Sinta 2
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="Sinta 3" id="sinta_3"
                                                @if (in_array('Sinta 3', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="sinta_3">
                                                Sinta 3
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="Sinta 4" id="sinta_4"
                                                @if (in_array('Sinta 4', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="sinta_4">
                                                Sinta 4
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="Sinta 5" id="sinta_5"
                                                @if (in_array('Sinta 5', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="sinta_5">
                                                Sinta 5
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="Sinta 6" id="sinta_6"
                                                @if (in_array('Sinta 6', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="sinta_6">
                                                Sinta 6
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="DOAJ" id="doaj"
                                                @if (in_array('DOAJ', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="doaj">
                                                DOAJ
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="Moraref" id="moraref"
                                                @if (in_array('Moraref', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="moraref">
                                                Moraref
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="Copernicus" id="copernicus"
                                                @if (in_array('Copernicus', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="copernicus">
                                                Copernicus
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="Garuda" id="garuda"
                                                @if (in_array('Garuda', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="garuda">
                                                Garuda
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="Crossref" id="crossref"
                                                @if (in_array('Crossref', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="crossref">
                                                Crossref
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="Scholar" id="scholar"
                                                @if (in_array('Scholar', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="scholar">
                                                Scholar
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="WOS" id="wos"
                                                @if (in_array('WOS', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="wos">
                                                WOS
                                            </label>
                                        </div>
                                        <div class="form-check form-check-custom form-check-solid form-check-lg mb-2">
                                            <input class="form-check-input" type="checkbox" name="akreditasi[]"
                                                value="EBSCO" id="ebsco"
                                                @if (in_array('EBSCO', $journal->indexing ?? [])) checked @endif />
                                            <label class="form-check-label" for="ebsco">
                                                EBSCO
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-5">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Faculty</label>
                                        <select name="faculty" class="form-control">
                                            <option value="">-- Pilih Fakultas --</option>
                                            <option value="pascasarjana" @if ($journal->faculty == 'pasca') selected @endif>Pascasarjana</option>
                                            <option value="syariah" @if ($journal->faculty == 'fsyar') selected @endif>Syariah</option>
                                            <option value="ftik" @if ($journal->faculty == 'ftik') selected @endif>Tarbiyah dan Ilmu Keguruan</option>
                                            <option value="febi" @if ($journal->faculty == 'febi') selected @endif>Ekonomi dan Bisnis Islam</option>
                                            <option value="fuad" @if ($journal->faculty == 'fuad') selected @endif>Ushuluddin Adab dan Dakwah</option>
                                        </select>
                                    </div>
                                     <div class="col-md-6">
                                        <label class="form-label">Program Studi</label>
                                        <select name="subject" class="form-control">
                                            <option value="">-- Pilih Prodi --</option>
                                            <option value="101" @if ($journal->subject == '101') selected @endif>Hukum Islam - S2</option>
                                            <option value="102" @if ($journal->subject == '102') selected @endif>Manajemen Pendidikan Islam - S2</option>
                                            <option value="103" @if ($journal->subject == '103') selected @endif>Ilmu Syariah - S3</option>
                                            <option value="11" @if ($journal->subject == '11') selected @endif>Hukum Keluarga Islam (Ahwal Syakhshiyyah) - S1</option>
                                            <option value="12" @if ($journal->subject == '12') selected @endif>Hukum Ekonomi Syariah (Muamalah) - S1</option>
                                            <option value="13" @if ($journal->subject == '13') selected @endif>Hukum Tatanegara (siyasah) - S1</option>
                                            <option value="14" @if ($journal->subject == '14') selected @endif>Hukum Pidana Islam (Jinayah) - S1</option>
                                            <option value="201" @if ($journal->subject == '201') selected @endif>Pendidikan Agama Islam - S2</option>
                                            <option value="202" @if ($journal->subject == '202') selected @endif>Tadris Bahasa Inggris - S2</option>
                                            <option value="21" @if ($journal->subject == '21') selected @endif>Pendidikan Agama Islam - S1</option>
                                            <option value="211" @if ($journal->subject == '211') selected @endif>Pendidikan Profesi Guru (PPG) - PROFESI</option>
                                            <option value="22" @if ($journal->subject == '22') selected @endif>Pendidikan Bahasa Arab - S1</option>
                                            <option value="23" @if ($journal->subject == '23') selected @endif>Pendidikan Bahasa Inggris - S1</option>
                                            <option value="24" @if ($journal->subject == '24') selected @endif>Pendidikan Matematika - S1</option>
                                            <option value="25" @if ($journal->subject == '25') selected @endif>Pendidikan Teknik Informatika Dan Komputer - S1</option>
                                            <option value="26" @if ($journal->subject == '26') selected @endif>Bimbingan dan Konseling - S1</option>
                                            <option value="27" @if ($journal->subject == '27') selected @endif>Matematika - S1</option>
                                            <option value="28" @if ($journal->subject == '28') selected @endif>Statistika - S1</option>
                                            <option value="29" @if ($journal->subject == '29') selected @endif>Informatika - S1</option>
                                            <option value="301" @if ($journal->subject == '301') selected @endif>Ekonomi Syariah - S2</option>
                                            <option value="31" @if ($journal->subject == '31') selected @endif>Perbankan Syariah D3 - D3</option>
                                            <option value="32" @if ($journal->subject == '32') selected @endif>Ekonomi Islam - S1</option>
                                            <option value="321" @if ($journal->subject == '321') selected @endif>Pendidikan Agama Islam - S3</option>
                                            <option value="33" @if ($journal->subject == '33') selected @endif>Perbankan Syariah - S1</option>
                                            <option value="34" @if ($journal->subject == '34') selected @endif>Akuntansi Syariah - S1</option>
                                            <option value="35" @if ($journal->subject == '35') selected @endif>Pariwisata Syariah - S1</option>
                                            <option value="36" @if ($journal->subject == '36') selected @endif>Manajemen Haji dan Umrah - S1</option>
                                            <option value="37" @if ($journal->subject == '37') selected @endif>Manajemen Bisnis Syariah - S1</option>
                                            <option value="38" @if ($journal->subject == '38') selected @endif>Bisnis Digital - S1</option>
                                            <option value="401" @if ($journal->subject == '401') selected @endif>Aqidah dan Filsafat Islam - S2</option>
                                            <option value="41" @if ($journal->subject == '41') selected @endif>Ilmu Al Qur'an dan Tafsir - S1</option>
                                            <option value="42" @if ($journal->subject == '42') selected @endif>Ilmu Hadis - S1</option>
                                            <option value="43" @if ($journal->subject == '43') selected @endif>Komunikasi dan Penyiaran Islam - S1</option>
                                            <option value="44" @if ($journal->subject == '44') selected @endif>Sejarah Peradaban Islam - S1</option>
                                            <option value="45" @if ($journal->subject == '45') selected @endif>Aqidah dan Filsafat Islam - S1</option>
                                            <option value="46" @if ($journal->subject == '46') selected @endif>Sosiologi Agama - S1</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-5">
                                <label class="form-label required">Editor In Chief</label>
                                <input type="text" name="editor_chief_name" class="form-control"
                                    value="{{ $journal->editor_chief_name }}" placeholder="Name of Editor Chief"
                                    required />
                            </div>
                            <div class="mb-5">
                                <label class="form-label mb-3 required">Tanda Tangan Editor In Chief</label><br>
                                <!--begin::Image input placeholder-->
                                <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3 ms-5" style="background-image: url('{{ $journal->getEditorChiefSignature() }}');"
                                    data-kt-image-input="true">
                                    <div class="image-input-wrapper w-150px h-150px"></div>
                                    <label
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                        title="Ubah Thumbnail">
                                        <i class="ki-duotone ki-pencil fs-7">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        <input type="file" name="editor_chief_signature" accept=".png, .jpg, .jpeg" />
                                        <input type="hidden" name="avatar_remove" />
                                    </label>
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                        title="Batalkan Thumbnail">
                                        <i class="ki-duotone ki-cross fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <span
                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                        data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                        title="Hapus Thumbnail">
                                        <i class="ki-duotone ki-cross fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-warning">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" id="delete_journal_{{ $journal->id }}">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Hapus Jurnal</h3>
                        <!--begin::Close-->
                        <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span
                                    class="path2"></span></i>
                        </div>
                        <!--end::Close-->
                    </div>
                    <form action="{{ route('back.master.journal.destroy', $journal->id) }}" method="post">
                        @csrf
                        @method('delete')
                        <div class="modal-body">
                            <p>Apakah anda yakin ingin menghapus jurnal <b>{{ $journal->title }}</b> ?</p>

                            <span class="text-danger">
                                <b>Perhatian : </b> jika jurnal dihapus maka semua data yang berkaitan dengan jurnal ini
                                akan
                                ikut
                                terhapus seperti artikel, reviewer, dan lain-lain.
                            </span>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endforeach
@endsection
@section('scripts')
    <script>
        $('#form_import_journal').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var data = form.serialize();
            Swal.fire({
                title: 'Loading',
                text: 'Please wait...',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                }
            });
            $.ajax({
                type: "POST",
                url: "{{ route('api.v1.journal.store') }}",
                data: data,
                success: function(response) {
                    console.log(response);
                    Swal.fire({
                        title: 'Berhasil',
                        text: 'Jurnal berhasil diimport',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        title: 'Gagal',
                        text: 'Jurnal gagal diimport : ' + xhr.responseJSON.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        function syncJournal(url_path) {

            Swal.fire({
                title: 'Loading',
                text: 'Please wait...',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                type: "POST",
                url: "{{ route('api.v1.journal.sync') }}",
                data: {
                    url_path: url_path
                },
                success: function(response) {
                    console.log(response);
                    Swal.fire({
                        title: 'Berhasil',
                        text: 'Jurnal berhasil disinkronisasi',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        title: 'Gagal',
                        text: 'Jurnal gagal disinkronisasi : ' + xhr.responseJSON.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });


        }
    </script>
@endsection
