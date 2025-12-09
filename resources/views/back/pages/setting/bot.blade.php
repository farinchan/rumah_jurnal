@extends('back.app')
@section('content')
    <div id="kt_content_container" class="container-xxl">
        <div class="card card-flush">
            <div class="card-header">
                <div class="card-title">
                    <h2>Pengaturan Bot AI</h2>
                </div>
            </div>
            <div class="card-body">
                <form id="kt_bot_settings_form" class="form"
                    action="{{ route('back.setting.bot.update') }}" method="POST">
                    @method('PUT')
                    @csrf

                    {{-- Nama Bot --}}
                    <div class="row fv-row mb-7">
                        <div class="col-md-3 text-md-end">
                            <label class="fs-6 fw-semibold form-label mt-3">
                                <span class="required">Nama Bot</span>
                                <span class="ms-1" data-bs-toggle="tooltip"
                                    title="Nama yang akan ditampilkan untuk bot AI">
                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control form-control-solid" name="name"
                                value="{{ $settingBot->name ?? '' }}" required placeholder="Contoh: Asisten Jurnal" />
                            @error('name')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- API Production URL --}}
                    <div class="row fv-row mb-7">
                        <div class="col-md-3 text-md-end">
                            <label class="fs-6 fw-semibold form-label mt-3">
                                <span class="required">API Production URL</span>
                                <span class="ms-1" data-bs-toggle="tooltip"
                                    title="URL endpoint API untuk mode production">
                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <input type="url" class="form-control form-control-solid" name="api_production_url"
                                value="{{ $settingBot->api_production_url ?? '' }}" required
                                placeholder="https://api.example.com/v1/chat" />
                            @error('api_production_url')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- API Sandbox URL --}}
                    <div class="row fv-row mb-7">
                        <div class="col-md-3 text-md-end">
                            <label class="fs-6 fw-semibold form-label mt-3">
                                <span class="required">API Sandbox URL</span>
                                <span class="ms-1" data-bs-toggle="tooltip"
                                    title="URL endpoint API untuk mode testing/sandbox">
                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <input type="url" class="form-control form-control-solid" name="api_sandbox_url"
                                value="{{ $settingBot->api_sandbox_url ?? '' }}" required
                                placeholder="https://sandbox.api.example.com/v1/chat" />
                            @error('api_sandbox_url')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- System Message --}}
                    <div class="row fv-row mb-7">
                        <div class="col-md-3 text-md-end">
                            <label class="fs-6 fw-semibold form-label mt-3">
                                <span>System Message</span>
                                <span class="ms-1" data-bs-toggle="tooltip"
                                    title="Pesan sistem yang akan dikirim ke AI untuk mengatur perilaku bot">
                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <textarea class="form-control form-control-solid" name="system_message" rows="5"
                                placeholder="Contoh: Kamu adalah asisten AI yang membantu pengguna dalam hal jurnal ilmiah...">{{ $settingBot->system_message ?? '' }}</textarea>
                            @error('system_message')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                Instruksi ini akan menentukan bagaimana bot AI merespons pengguna.
                            </div>
                        </div>
                    </div>

                    {{-- Additional Context --}}
                    <div class="row fv-row mb-7">
                        <div class="col-md-3 text-md-end">
                            <label class="fs-6 fw-semibold form-label mt-3">
                                <span>Additional Context</span>
                                <span class="ms-1" data-bs-toggle="tooltip"
                                    title="Konteks tambahan yang akan diberikan ke AI">
                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <textarea class="form-control form-control-solid" name="additional_context" rows="8"
                                placeholder="Informasi tambahan tentang website, layanan, atau data yang perlu diketahui bot...">{{ $settingBot->additional_context ?? '' }}</textarea>
                            @error('additional_context')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">
                                Tambahkan informasi kontekstual seperti FAQ, informasi layanan, atau data penting lainnya.
                            </div>
                        </div>
                    </div>

                    {{-- Signature --}}
                    <div class="row fv-row mb-7">
                        <div class="col-md-3 text-md-end">
                            <label class="fs-6 fw-semibold form-label mt-3">
                                <span>Signature</span>
                                <span class="ms-1" data-bs-toggle="tooltip"
                                    title="Tanda tangan atau penutup pesan dari bot">
                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </label>
                        </div>
                        <div class="col-md-9">
                            <textarea class="form-control form-control-solid" name="signature" rows="3"
                                placeholder="Contoh: Salam hangat, Tim Support Jurnal">{{ $settingBot->signature ?? '' }}</textarea>
                            @error('signature')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Aktivasi Bot --}}
                    <div class="row fv-row mb-7">
                        <div class="col-md-3 text-md-end">
                            <label class="fs-6 fw-semibold form-label mt-3">
                                <span>Aktifkan Bot AI Website</span>
                                <span class="ms-1" data-bs-toggle="tooltip"
                                    title="Aktifkan atau nonaktifkan bot AI untuk website">
                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </label>
                        </div>
                        <div class="col-md-9 d-flex align-items-center">
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1"
                                    {{ isset($settingBot) && $settingBot->is_active ? 'checked' : '' }} />
                            </div>
                        </div>
                    </div>

                    {{-- Aktivasi Bot untuk WhatsApp --}}
                    <div class="row fv-row mb-7">
                        <div class="col-md-3 text-md-end">
                            <label class="fs-6 fw-semibold form-label mt-3">
                                <span>Aktifkan Bot AI untuk WhatsApp</span>
                                <span class="ms-1" data-bs-toggle="tooltip"
                                    title="Aktifkan bot AI untuk integrasi WhatsApp">
                                    <i class="ki-duotone ki-information-5 text-gray-500 fs-6">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                    </i>
                                </span>
                            </label>
                        </div>
                        <div class="col-md-9 d-flex align-items-center">
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" name="is_whatsapp_active" value="1"
                                    {{ isset($settingBot) && $settingBot->is_whatsapp_active ? 'checked' : '' }} />
                            </div>
                        </div>
                    </div>

                    {{-- Buttons --}}
                    <div class="row py-10">
                        <div class="col-md-9 offset-md-3">
                            <div class="d-flex">
                                <button type="reset" class="btn btn-light me-3">
                                    <i class="ki-duotone ki-arrow-left fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <span class="indicator-label">
                                        <i class="ki-duotone ki-check fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Simpan Pengaturan
                                    </span>
                                    <span class="indicator-progress">Menyimpan...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script>
    // Form submission with loading indicator
    document.getElementById('kt_bot_settings_form').addEventListener('submit', function(e) {
        var submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.setAttribute('data-kt-indicator', 'on');
        submitBtn.disabled = true;
    });
</script>
@endsection
