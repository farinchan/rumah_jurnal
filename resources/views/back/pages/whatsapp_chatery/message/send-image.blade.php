@extends('back.app')
@section('styles')
@endsection
@section('content')
    <!--begin::Container-->
    <div id="kt_content_container" class=" container-xxl ">
        @include('back.pages.whatsapp_chatery.message.header')
        <div class="card card-flush">


            <div class="card-header ">
                <h2 class="card-title">
                    Kirim Pesan Whatsapp
                </h2>

                <div class="card-toolbar">
                    <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_8">Pengguna</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " data-bs-toggle="tab" href="#kt_tab_pane_7">No Whatsapp</a>
                        </li>

                    </ul>
                </div>
            </div>


            <div class="card-body ">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="kt_tab_pane_8" role="tabpanel">

                        <div class="row">
                            <div class="col-md-4">
                                <img src="{{ asset('ext_images/wa_massage.png') }}" style="width: 100%" alt="" />
                            </div>
                            <div class="col-md-8">
                                <form id="kt_modal_create_discipline_rule_form" class="form" method="POST"
                                    action="{{ route('back.chatery-whatsapp.message.sendImageProcess') }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="fv-row mb-10">
                                        <label class="required fw-bold fs-6 mb-2">Pengguna</label>
                                        <select id="kt_docs_select2_rich_content" class="form-select form-select-solid"
                                            name="phone" data-placeholder="Pilih Pengguna" required>
                                            @foreach ($users as $user)
                                                <option></option>
                                                <option value="{{ $user->phone }}"
                                                    data-kt-rich-content-subcontent="Telp. {{ $user->phone }}"
                                                    data-kt-rich-content-icon="{{ $user->getPhoto() }}">
                                                    {{ $user->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="fv-row mb-10">
                                        <label class="required fw-bold fs-6 mb-2">Gambar</label>
                                        <input type="file"
                                            class="form-control form-control-solid form-control-lg fw-bold" name="image"
                                            accept="image/*" required />
                                        <small class="text-muted">Pastikan gambar yang diunggah berformat <code>jpg</code>,
                                            <code>jpeg</code>, atau <code>png</code>.</small>
                                    </div>

                                    <div class="fv-row mb-10">
                                        <label class="required fw-bold fs-6 mb-2">Pesan</label>
                                        <textarea class="form-control form-control-solid form-control-lg fw-bold" name="message" rows="10" placeholder=""></textarea>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary" id="send_message_btn">
                                            <span class="indicator-label">Kirim Pesan</span>
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade " id="kt_tab_pane_7" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4">
                                <img src="{{ asset('ext_images/wa_massage.png') }}" style="width: 100%" alt="" />
                            </div>
                            <div class="col-md-8">
                                <form id="kt_modal_create_discipline_rule_form" class="form" method="POST"
                                    action="{{ route('back.chatery-whatsapp.message.sendImageProcess') }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="fv-row mb-10">
                                        <label class="required fw-bold fs-6 mb-2">Kepada (No Whatsapp)</label>
                                        <input class="form-control form-control-solid form-control-lg fw-bold"
                                            type="number" placeholder="628xxxxxxxxxx" name="phone" />
                                        <small class="text-muted">Pastikan nomor whatsapp benar, nomor diawali dengan kode
                                            negara tanpa tanda <code>+</code> atau <code>0</code>, dengan contoh
                                            <code>6281234567890</code></small>
                                    </div>

                                    <div class="fv-row mb-10">
                                        <label class="required fw-bold fs-6 mb-2">Gambar</label>
                                        <input type="file"
                                            class="form-control form-control-solid form-control-lg fw-bold" name="image"
                                            accept="image/*" required />
                                        <small class="text-muted">Pastikan gambar yang diunggah berformat <code>jpg</code>,
                                            <code>jpeg</code>, atau <code>png</code>.</small>
                                    </div>

                                    <div class="fv-row mb-10">
                                        <label class="required fw-bold fs-6 mb-2">Pesan</label>
                                        <textarea class="form-control form-control-solid form-control-lg fw-bold" name="message" rows="10" placeholder=""></textarea>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary" id="send_message_btn">
                                            <span class="indicator-label">Kirim Pesan</span>
                                        </button>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>


                </div>

            </div>
        </div>
    </div>

    <!--end::Container-->
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#kt_modal_create_discipline_rule_form').submit(function(e) {
                $('#send_message_btn').attr('disabled', true);
                $('#send_message_btn').html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );
            });
        });
        const optionFormat = (item) => {
            if (!item.id) {
                return item.text;
            }

            var span = document.createElement('span');
            var template = '';

            template += '<div class="d-flex align-items-center">';
            template += '<img src="' + item.element.getAttribute('data-kt-rich-content-icon') +
                '" class="rounded-circle h-40px w-40px me-3" alt="' + item.text + '"/>';
            template += '<div class="d-flex flex-column">'
            template += '<span class="fs-4 fw-bold lh-1">' + item.text + '</span>';
            template += '<span class="text-muted fs-5">' + item.element.getAttribute(
                'data-kt-rich-content-subcontent') + '</span>';
            template += '</div>';
            template += '</div>';

            span.innerHTML = template;

            return $(span);
        }
        // Init Select2 --- more info: https://select2.org/
        $('#kt_docs_select2_rich_content').select2({
            placeholder: "Select an option",
            minimumResultsForSearch: 0, // Enable search
            templateSelection: optionFormat,
            templateResult: optionFormat
        });
    </script>
@endsection
