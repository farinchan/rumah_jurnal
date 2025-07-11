@extends('back.app')
@section('styles')
@endsection
@section('content')
    <!--begin::Container-->
    <div id="kt_content_container" class=" container-xxl ">
        @include('back.pages.whatsapp.message.header')
        <div class="card card-flush">

            <div class="card-header mt-6">
                <h2 class="mb-5">
                    Kirim Pesan Whatsapp
                </h2>
            </div>

            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-4">
                        <img src="{{ asset('img_ext/wa_massage.png') }}" style="width: 100%" alt="" />
                    </div>
                    <div class="col-md-8">
                        <form id="kt_modal_create_discipline_rule_form" class="form" method="POST" action="{{ route('back.whatsapp.message.sendImageProcess') }}" enctype="multipart/form-data">
                            @csrf
                          
                            <div class="fv-row mb-10">
                                <label class="required fw-bold fs-6 mb-2">Kepada (No Whatsapp)</label>
                                <input class="form-control form-control-solid form-control-lg fw-bold" type="number"
                                    placeholder="628xxxxxxxxxx" name="phone" />
                                <small class="text-muted">Pastikan nomor whatsapp benar, nomor diawali dengan kode
                                    negara tanpa tanda <code>+</code> atau <code>0</code>, dengan contoh
                                    <code>6281234567890</code></small>
                            </div>

                            <div class="fv-row mb-10">
                                <label class="required fw-bold fs-6 mb-2">Gambar</label>
                                <input type="file" class="form-control form-control-solid form-control-lg fw-bold" name="image" accept="image/*" required />
                                <small class="text-muted">Pastikan gambar yang diunggah berformat <code>jpg</code>, <code>jpeg</code>, atau <code>png</code>.</small>
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
    </script>
@endsection
