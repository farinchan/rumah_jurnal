@extends('back.app')
@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <form id="kt_ecommerce_add_category_form" class="form d-flex flex-column flex-lg-row"
                action="{{ route('back.master.user.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="d-flex flex-column gap-7 gap-lg-10 w-100 w-lg-300px mb-7 me-lg-10">
                    <div class="card card-flush py-4">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Foto</h2>
                            </div>
                        </div>
                        <div class="card-body text-center pt-0">
                            <style>
                                .image-input-placeholder {
                                    background-image: url('{{ asset('back/media/svg/files/blank-image.svg') }}');
                                }

                                [data-bs-theme="dark"] .image-input-placeholder {
                                    background-image: url('{{ asset('back/media/svg/files/blank-image-dark.svg') }}');
                                }
                            </style>
                            <div class="image-input image-input-empty image-input-outline image-input-placeholder mb-3"
                                data-kt-image-input="true">
                                <div class="image-input-wrapper w-150px h-150px"></div>
                                <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Ubah Thumbnail">
                                    <i class="ki-duotone ki-pencil fs-7">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <input type="file" name="photo" accept=".png, .jpg, .jpeg" />
                                    <input type="hidden" name="avatar_remove" />
                                </label>
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Batalkan Thumbnail">
                                    <i class="ki-duotone ki-cross fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                    data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Hapus Thumbnail">
                                    <i class="ki-duotone ki-cross fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div class="text-muted fs-7">
                                Set foto anggota, hanya menerima file dengan ekstensi .png, .jpg, .jpeg
                            </div>
                        </div>
                    </div>
                    <div class="card card-flush py-4">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Role</h2>
                            </div>
                            <div class="card-toolbar">
                                <div class="rounded-circle bg-success w-15px h-15px" id="kt_ecommerce_add_category_status">
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="role_super_admin" value="1"
                                    @if (old('role_super_admin') == 1) checked @endif id="flexCheckDefault" />
                                <label class="form-check-label" for="flexCheckDefault">
                                    Super-Admin/Owner
                                </label>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="role_admin_ejournal" value="1"
                                    @if (old('role_admin_ejournal') == 1) checked @endif id="flexCheckKantor" />
                                <label class="form-check-label" for="flexCheckKantor">
                                    Admin e-Journal
                                </label>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="role_admin_proceeding" value="1"
                                    @if (old('role_admin_proceeding') == 1) checked @endif id="flexCheckKantor" />
                                <label class="form-check-label" for="flexCheckKantor">
                                    Admin Proceeding
                                </label>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="role_admin_student_research_hub" value="1"
                                    @if (old('role_admin_student_research_hub') == 1) checked @endif id="flexCheckKantor" />
                                <label class="form-check-label" for="flexCheckKantor">
                                    Admin Student Research Hub
                                </label>
                            </div>

                             <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="role_editor" value="1"
                                    @if (old('role_editor') == 1) checked @endif id="flexCheckAgen" />
                                <label class="form-check-label" for="flexCheckAgen">
                                    Editor e-Journal
                                </label>
                            </div>
                                <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="role_editor_proceeding" value="1"
                                    @if (old('role_editor_proceeding') == 1) checked @endif id="flexCheckAgen" />
                                <label class="form-check-label" for="flexCheckAgen">
                                    Editor Proceeding
                                </label>
                            </div>
                                <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="role_editor_student_research_hub" value="1"
                                    @if (old('role_editor_student_research_hub') == 1) checked @endif id="flexCheckAgen" />
                                <label class="form-check-label" for="flexCheckAgen">
                                    Editor Student Research Hub
                                </label>
                            </div>

                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="role_keuangan" value="1"
                                    @if (old('role_keuangan') == 1) checked @endif id="flexCheckKantor" />
                                <label class="form-check-label" for="flexCheckKantor">
                                    Bendahara e-Journal
                                </label>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="role_keuangan_proceeding" value="1"
                                    @if (old('role_keuangan_proceeding') == 1) checked @endif id="flexCheckKantor" />
                                <label class="form-check-label" for="flexCheckKantor">
                                    Bendahara Proceeding
                                </label>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="role_keuangan_student_research_hub" value="1"
                                    @if (old('role_keuangan_student_research_hub') == 1) checked @endif id="flexCheckKantor" />
                                <label class="form-check-label" for="flexCheckKantor">
                                    Bendahara Student Research Hub
                                </label>
                            </div>

                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="role_humas" value="1"
                                    @if (old('role_humas') == 1) checked @endif id="flexCheckAgen" />
                                <label class="form-check-label" for="flexCheckAgen">
                                    Humas
                                </label>
                            </div>


                            @error('status')
                                <div class="text-danger fs-7">{{ $message }}</div>
                            @enderror

                        </div>
                    </div>
                    <div class="card card-flush py-4">
                        <div class="card-header">
                            <div class="card-title">
                                <h3>Editor - Journal Permission</h3>
                            </div>

                        </div>
                        <div class="card-body pt-0">
                            @php
                                $permissions = Spatie\Permission\Models\Permission::where('guard_name', 'web')->get();
                            @endphp
                            @foreach ($permissions as $permission)
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" name="permissions[]"
                                        value="{{ $permission->name }}" id="flexCheckDefault" />
                                    <label class="form-check-label" for="flexCheckDefault">
                                        {{ App\Models\Journal::where('url_path', $permission->name)->first()->name ?? '' }}
                                        ({{ $permission->name }})
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-10">
                    <div class="card card-flush py-4">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Biodata</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">

                            <div class="mb-5 fv-row">
                                <label class="required form-label">Nama</label>
                                <input type="text" name="name" class="form-control mb-2" placeholder="Nama Staff"
                                    value="{{ old('name') }}" required />
                                @error('name')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-5 fv-row">
                                <label class="form-label required">Jenis Kelamin</label>
                                <select name="gender" class="form-control mb-2" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="laki-laki" @if (old('gender') == 'laki-laki') selected @endif>Laki-laki
                                    </option>
                                    <option value="perempuan" @if (old('gender') == 'perempuan') selected @endif>Perempuan
                                    </option>
                                </select>
                                @error('gender')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-5 fv-row">
                                <label class=" form-label">Username</label>
                                <input type="text" name="username" class="form-control mb-2" placeholder="Username"
                                    value="{{ old('username') }}" />
                                @error('username')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-5 fv-row">
                                <label class=" form-label required">Email</label>
                                <input type="email" name="email" class="form-control mb-2" placeholder="Email"
                                    value="{{ old('email') }}" required />
                                @error('email')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-5 fv-row">
                                <label class="form-label required">Telepon</label>
                                <input type="text" name="phone" class="form-control mb-2" placeholder="+628XXXXXXX"
                                    value="{{ old('phone') }}" required />
                                <small class="text-muted">Nomor telepon harus diawali dengan kode negara, contoh
                                    :<code>indonesia : +62</code></small>
                                @error('phone')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                    <div class="card card-flush py-4">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Informasi tambahan</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="mb-5 fv-row">
                                <div class="row">
                                    <div class="mb-5 fv-row">
                                        <label class="form-label">SINTA ID</label>
                                        <input type="text" name="sinta_id" class="form-control mb-2"
                                            placeholder="SINTA ID" value="{{ old('sinta_id') }}" />
                                        @error('sinta_id')
                                            <div class="text-danger fs-7">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-5 fv-row">
                                        <label class="form-label">Scopus ID</label>
                                        <input type="text" name="scopus_id" class="form-control mb-2"
                                            placeholder="Scopus ID" value="{{ old('scopus_id') }}" />
                                        @error('scopus_id')
                                            <div class="text-danger fs-7">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-5 fv-row">
                                        <label class="form-label">Google Scholar ID</label>
                                        <input type="text" name="google_scholar" class="form-control mb-2"
                                            placeholder="Google Scholar" value="{{ old('google_scholar') }}" />
                                        @error('google_scholar')
                                            <div class="text-danger fs-7">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-flush py-4">
                        <div class="card-header">
                            <div class="card-title">
                                <h2>Buat Password</h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="mb-5 fv-row">
                                <label class="required form-label">Password</label>
                                <input type="password" name="password" class="form-control mb-2" placeholder="Password"
                                    required />
                                <small class="text-muted">Password minimal 8 karakter</small>
                                @error('password')
                                    <div class="text-danger fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('back.master.user.index') }}" id="kt_ecommerce_add_product_cancel"
                            class="btn btn-light me-5">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Simpan Perubahan</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
