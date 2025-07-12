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
                                <form id="kt_modal_create_discipline_rule_form" class="form" method="POST"
                                    action="{{ route('back.whatsapp.message.sendBulkMessageProcess') }}">
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
                                        <!--begin::Repeater-->
                                        <div id="phone_list">
                                            <!--begin::Form group-->
                                            <div class="form-group">
                                                <div data-repeater-list="phones">
                                                    <div data-repeater-item>
                                                        <div class="form-group row mb-5">
                                                            <div class="col-md-9">
                                                                <label class="form-label">Kepada (No Whatsapp)</label>
                                                                <input type="number"
                                                                    class="form-control form-control-solid mb-2 mb-md-0"
                                                                    placeholder="628xxxxxxxxxx" name="phone" required />
                                                                <small class="text-muted">Pastikan nomor whatsapp benar,
                                                                    nomor
                                                                    diawali dengan kode
                                                                    negara tanpa tanda <code>+</code> atau <code>0</code>,
                                                                    dengan
                                                                    contoh
                                                                    <code>6281234567890</code></small>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <a href="javascript:;" data-repeater-delete
                                                                    class="btn btn-sm btn-light-danger mt-3 mt-md-8">
                                                                    <i class="ki-duotone ki-trash fs-5"><span
                                                                            class="path1"></span><span
                                                                            class="path2"></span><span
                                                                            class="path3"></span><span
                                                                            class="path4"></span><span
                                                                            class="path5"></span></i>
                                                                    Delete
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end::Form group-->

                                            <!--begin::Form group-->
                                            <div class="form-group mt-5">
                                                <a href="javascript:;" data-repeater-create class="btn btn-light-primary">
                                                    <i class="ki-duotone ki-plus fs-3"></i>
                                                    Add
                                                </a>
                                            </div>
                                            <!--end::Form group-->
                                        </div>
                                        <!--end::Repeater-->
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
