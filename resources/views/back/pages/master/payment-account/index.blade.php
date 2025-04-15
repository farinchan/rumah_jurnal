@extends('back.app')
@section('content')
    <div id="kt_content_container" class=" container-xxl ">

        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse"
                data-bs-target="#kt_account_profile_details" aria-expanded="true" aria-controls="kt_account_profile_details">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Rekening Pembayaran</h3>
                </div>
            </div>
            <div id="kt_account_settings_profile_details" class="collapse show">
                <form id="kt_account_profile_details_form" class="form" method="POST" enctype="multipart/form-data"
                    action="{{ route('back.master.payment-account.update') }}">
                    @method('PUT')
                    @csrf
                    <div class="card-body border-top p-9">
                        <!--begin::Repeater-->
                        <div id="kt_docs_repeater_basic">
                            <!--begin::Form group-->
                            <div class="form-group">
                                <div data-repeater-list="payment_accounts">
                                    @foreach ($payment_accounts as $account)
                                        <div data-repeater-item>
                                            <input type="hidden" name="account_id" value="{{ $account->id ?? '' }}"
                                                id="account_id">
                                            <div class="form-group row mb-10">
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label required">Nama Pemilik Rekening:</label>
                                                    <input type="text" class="form-control mb-2 mb-md-0"
                                                        name="account_name" placeholder="Rekening Atas Nama"
                                                        value="{{ $account->account_name }}" required />
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label required">Bank:</label>
                                                    <input type="text" class="form-control mb-2 mb-md-0" name="bank"
                                                        placeholder="Nama Bank" value="{{ $account->bank }}" required />
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label required">No. Rekening:</label>
                                                    <input type="text" class="form-control mb-2 mb-md-0"
                                                        name="account_number" placeholder="Nomor Rekening Bank"
                                                        value="{{ $account->account_number }}" required />
                                                </div>

                                                @if($payment_accounts->count() > 1)
                                                <div class="col-md-3">
                                                    <a href="javascript:;" data-repeater-delete
                                                        class="btn btn-sm btn-light-danger mt-3 mt-md-8">
                                                        <i class="ki-duotone ki-trash fs-5"><span
                                                                class="path1"></span><span class="path2"></span><span
                                                                class="path3"></span><span class="path4"></span><span
                                                                class="path5"></span></i>
                                                        Delete
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                            <hr class="mb-10" />
                                        </div>
                                    @endforeach
                                </div>
                                <input type="hidden" name="delete_account" id="delete_account" value="">
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
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="reset" class="btn btn-light btn-active-light-primary me-2">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ asset('back/plugins/custom/formrepeater/formrepeater.bundle.js') }}"></script>
    <script>
        $('#kt_docs_repeater_basic').repeater({
            initEmpty: false,

            defaultValues: {
                'text-input': 'foo'
            },

            show: function() {
                $(this).slideDown();
            },

            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);

                // Ambil id sertifikat yang dihapus
                var account_id = $(this).find('#account_id').val();

                console.log(account_id);
                // Tambahkan id sertifikat yang dihapus ke inputan delete_certificate
                var delete_account = $('#delete_account').val();
                if (account_id != '') {
                    if (delete_account == '') {
                        $('#delete_account').val('[' + account_id + ']');
                    } else {
                        delete_account = delete_account.slice(0, -1) + ',' + account_id + ']';
                        $('#delete_account').val(delete_account);
                    }
                }
                console.log($('#delete_account').val());
            }
        });
    </script>
@endsection
