@extends('back.app')
@section('content')
    @php
        [$before, $after] = explode(' - ', $event->datetime);
        $date_before = \Carbon\Carbon::parse($before)->toDateTimeString();
        $date_after = \Carbon\Carbon::parse($after)->toDateTimeString();
        // dd($date_before, $date_after);
    @endphp
    <div id="kt_content_container" class=" container-xxl ">
        @include('back.pages.event.detail.header')
        <div class="card card-flush">

            <div class="card-header mt-6">
                <h3 class="card-title">Kirim Pesan</h3>
                <div class="card-toolbar">
                    <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_7">Whatsapp</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_8">Email</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body pt-0">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="kt_tab_pane_7" role="tabpanel">
                        <div class="row">
                            <div class="col-md-4">
                                <img src="{{ asset('ext_images/wa_massage.png') }}" style="width: 100%" alt="" />
                            </div>
                            <div class="col-md-8">
                                <form id="kt_modal_create_discipline_rule_form" class="form" method="POST" enctype="multipart/form-data"
                                    action="{{ route('back.event.detail.notification.whatsapp', $event->id ) }}">
                                    @csrf
                                    <div class="fv-row mb-10">
                                        <label class="required fw-bold fs-6 mb-2">Delay (milisecond)</label>
                                        <input type="number"
                                            class="form-control form-control-solid form-control-lg fw-bold" name="delay"
                                            placeholder="1000" value="2000" min="1000" required />
                                        <small class="text-muted">Jeda antara pengiriman pesan, dalam milisecond. Contoh:
                                            <code>2000</code> untuk 2 detik.</small>
                                    </div>
                                    <div class="fv-row mb-10">
                                        <label class="required fw-bold fs-6 mb-2">Peserta</label>
                                        <select class="form-select form-select-solid form-select-lg fw-bold"
                                            data-control="select2" data-placeholder="Pilih Peserta" data-hide-search="true"
                                            name="user" required>
                                            <option></option>
                                            <option value="all">Seluruh Peserta</option>
                                            <option value="100_percent" disabled>Peserta Dengan
                                                kehadiran 100%</option>
                                        </select>
                                    </div>

                                    <div class="fv-row mb-10">
                                        <label class="required fw-bold fs-6 mb-2">Pesan</label>
                                        <textarea class="form-control form-control-solid form-control-lg fw-bold" name="message" rows="10" placeholder=""></textarea>
                                    </div>

                                     <div class="fv-row mb-10">
                                        <label class=" fw-bold fs-6 mb-2">Lampiran</label>
                                        <input type="file" class="form-control form-control-solid form-control-lg fw-bold"
                                            name="attachment" accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx" />
                                        <small class="text-muted">Format file yang didukung: JPG, JPEG, PNG, PDF, DOCX, XLSX.
                                            Ukuran maksimal: 8MB.</small>
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

                    <div class="tab-pane fade" id="kt_tab_pane_8" role="tabpanel">
                        ...
                    </div>
                </div>


            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('back/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>

    <script>
        $('#phone_list').repeater({
            initEmpty: false,

            defaultValues: {
                'text-input': 'foo'
            },

            show: function() {
                $(this).slideDown();
            },

            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);
            }
        });
        $(document).ready(function() {
            $('#kt_modal_create_discipline_rule_form').submit(function(e) {
                $('#send_message_btn').attr('disabled', true);
                $('#send_message_btn').html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...'
                );
            });
        });
    </script>
@endsection
