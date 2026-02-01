@extends('back.app')
@section('content')
    <div id="kt_content_container" class="container-xxl">
        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 cursor-pointer" role="button">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Payment Year Settings</h3>
                </div>
                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addPaymentYearModal">
                        <i class="fas fa-plus"></i> Tambah Tahun
                    </button>
                </div>
            </div>
            <div class="card-body border-top p-9">
                <div class="table-responsive">
                    <table class="table table-row-dashed table-row-gray-300 align-middle gs-0 gy-4" id="paymentYearTable">
                        <thead>
                            <tr class="fw-bold text-muted">
                                <th class="min-w-50px">No</th>
                                <th class="min-w-100px">Tahun</th>
                                <th class="min-w-150px">Tanggal Mulai</th>
                                <th class="min-w-150px">Tanggal Selesai</th>
                                <th class="min-w-100px text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paymentYearSettings as $index => $setting)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <span class="badge badge-light-primary fs-6">{{ $setting->year }}</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($setting->start_date)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($setting->end_date)->format('d M Y') }}</td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"
                                            data-bs-toggle="modal" data-bs-target="#editPaymentYearModal{{ $setting->id }}"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-icon btn-bg-light btn-active-color-danger btn-sm"
                                            onclick="deletePaymentYear({{ $setting->id }}, '{{ $setting->year }}')"
                                            title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Edit Modal for each setting -->
                                <div class="modal fade" id="editPaymentYearModal{{ $setting->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('back.setting.payment_year_settings.update', $setting->id) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Payment Year Setting</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-6">
                                                        <label class="form-label required">Tahun</label>
                                                        <input type="number" class="form-control form-control-solid" name="year"
                                                            value="{{ $setting->year }}" placeholder="Contoh: 2026" required min="2000" max="2100">
                                                    </div>
                                                    <div class="mb-6">
                                                        <label class="form-label required">Tanggal Mulai</label>
                                                        <input type="date" class="form-control form-control-solid" name="start_date"
                                                            value="{{ $setting->start_date }}" required>
                                                    </div>
                                                    <div class="mb-6">
                                                        <label class="form-label required">Tanggal Selesai</label>
                                                        <input type="date" class="form-control form-control-solid" name="end_date"
                                                            value="{{ $setting->end_date }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="fas fa-calendar-times fa-3x mb-3 d-block"></i>
                                        Belum ada data payment year setting
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Payment Year Modal -->
    <div class="modal fade" id="addPaymentYearModal" tabindex="-1" aria-labelledby="addPaymentYearModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('back.setting.payment_year_settings.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPaymentYearModalLabel">Tambah Payment Year Setting</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-6">
                            <label class="form-label required">Tahun</label>
                            <input type="number" class="form-control form-control-solid" name="year"
                                placeholder="Contoh: 2026" required min="2000" max="2100">
                            <small class="text-muted">Masukkan tahun yang ingin diatur</small>
                        </div>
                        <div class="mb-6">
                            <label class="form-label required">Tanggal Mulai</label>
                            <input type="date" class="form-control form-control-solid" name="start_date" required>
                            <small class="text-muted">Tanggal mulai periode pembayaran</small>
                        </div>
                        <div class="mb-6">
                            <label class="form-label required">Tanggal Selesai</label>
                            <input type="date" class="form-control form-control-solid" name="end_date" required>
                            <small class="text-muted">Tanggal berakhir periode pembayaran</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function deletePaymentYear(id, year) {
        Swal.fire({
            title: 'Hapus Payment Year Setting?',
            text: `Apakah Anda yakin ingin menghapus setting untuk tahun ${year}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('back.setting.payment_year_settings.delete', '') }}/${id}`;
                form.innerHTML = `
                    @csrf
                    @method('DELETE')
                `;
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection
