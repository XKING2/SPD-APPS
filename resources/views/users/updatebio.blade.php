@extends('layouts.main')

@section('pageheads')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            <i class="fas fa-user-edit text-primary"></i>
            Update Biodata
        </h1>
        <p class="text-muted mb-0 mt-1">Perbarui informasi biodata Anda</p>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid px-0">

    @php
        $isReadonly = $biodata->status === 'valid';
        $profileImg = optional($biodata)->profile_img ?? 'img/undraw_profile.svg';
        $kk = optional($biodata)->kartu_keluarga;
        $ktp = optional($biodata)->ktp;
        $ijazah = optional($biodata)->ijazah;
        $cv = optional($biodata)->cv;
        $suratPendaftaran = optional($biodata)->surat_pendaftaran;
    @endphp

    {{-- ================= STATUS ALERT ================= --}}
    @if($isReadonly)
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fa-2x me-3"></i>
                <div>
                    <h5 class="alert-heading mb-1">Biodata Terverifikasi</h5>
                    <p class="mb-0">Biodata Anda sudah divalidasi. Data tidak dapat diubah kembali.</p>
                </div>
            </div>
           
        </div>
    @else
        <div class="alert alert-info alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x me-3"></i>
                <div>
                    <h5 class="alert-heading mb-1">Perhatian</h5>
                    <p class="mb-0">Pastikan semua data yang Anda masukkan sudah benar sebelum menyimpan.</p>
                </div>
            </div>
            
        </div>
    @endif

    {{-- ================= SESSION MESSAGES ================= --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <h5 class="alert-heading">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Terdapat kesalahan pada form
            </h5>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ================= FORM ================= --}}
    <form action="{{ route('biodata.update') }}" method="POST" enctype="multipart/form-data" id="biodataForm">
        @csrf
        @method('PUT')

        {{-- ================= FORMASI JABATAN ================= --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white py-3">
                <div class="d-flex align-items-center">
                    <div class="header-icon bg-opacity-25 rounded me-10">
                        <i class="fas fa-briefcase  me-1"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Formasi Jabatan</h5>
                        <small class="opacity-75">Pilih formasi yang Anda inginkan</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                
                @if(count($formasis) > 0)
                    @foreach($formasis as $formasi)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-calendar-alt text-primary me-1"></i>
                                Formasi Tahun {{ $formasi->tahun }}
                                <span class="text-danger">*</span>
                            </label>

                            <select name="id_kebutuhan" 
                                    class="form-select form-select-lg @error('id_kebutuhan') is-invalid @enderror"
                                    @if($isReadonly) disabled @endif
                                    required>
                                <option value="">-- Pilih Formasi Jabatan --</option>
                                @foreach($formasi->kebutuhan as $kebutuhan)
                                    <option value="{{ $kebutuhan->id }}"
                                        @if(old('id_kebutuhan', $biodata->id_kebutuhan) == $kebutuhan->id) selected @endif>
                                        {{ $kebutuhan->nama_kebutuhan }}
                                    </option>
                                @endforeach
                            </select>

                            @error('id_kebutuhan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            <input type="hidden" name="id_formasi" value="{{ old('id_formasi', $biodata->id_formasi) }}">

                            @if($isReadonly)
                                <input type="hidden" name="id_kebutuhan" value="{{ $biodata->id_kebutuhan }}">
                            @endif
                        </div>

                        @if(!$isReadonly)
                            <div class="alert alert-warning border-start border-3 border-warning bg-warning bg-opacity-10 mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <small>Pastikan Anda memilih formasi dengan benar. Formasi tidak dapat diubah setelah terverifikasi.</small>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="alert alert-warning border-0 shadow-sm">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-circle fa-2x me-3"></i>
                            <div>
                                <h6 class="mb-0">Formasi Belum Tersedia</h6>
                                <small>Silakan hubungi administrator untuk informasi lebih lanjut.</small>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        {{-- ================= DOKUMEN PERSYARATAN ================= --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white py-3">
                <div class="d-flex align-items-center">
                    <div class="header-icon  bg-opacity-25 rounded me-3">
                        <i class="fas fa-file-upload"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Dokumen Persyaratan</h5>
                        <small class="opacity-75">Upload dokumen pendukung Anda</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">

                <div class="row g-4">
                    
                    {{-- Foto Profil --}}
                    <div class="col-md-6">
                        <div class="document-box border rounded-3 p-3 h-100 @if($isReadonly) bg-light @endif">
                            <div class="d-flex align-items-start mb-3">
                                <div class="doc-icon  bg-opacity-10 rounded me-3">
                                    <i class="fas fa-user-circle text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-label fw-semibold mb-1">
                                        Foto Profil
                                        @if(!$biodata->profile_img) <span class="text-danger">*</span> @endif
                                    </label>
                                    <p class="text-muted small mb-0">Format: JPG, PNG (Max 2MB)</p>
                                </div>
                            </div>

                            @if($biodata->profile_img)
                                <div class="alert alert-success border-start border-3 border-success  bg-opacity-10 mb-3 py-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <small class="fw-semibold">File sudah diupload</small>
                                        </div>
                                        <button type="button"
                                            class="btn-preview {{ $profileImg ? '' : 'disabled' }}"
                                            data-url="{{ $profileImg ? asset('storage/'.$profileImg) : '' }}"
                                            onclick="openPreview(this)"
                                            {{ $profileImg ? '' : 'disabled' }}>
                                            <i class="fas fa-eye"></i>
                                            Lihat Preview 
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning border-start border-3 border-warning bg-warning bg-opacity-10 mb-3 py-2">
                                    <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                    <small>Belum ada file terupload</small>
                                </div>
                            @endif

                            @if(!$isReadonly)
                                <input type="file" name="profile_img" class="form-control @error('profile_img') is-invalid @enderror" accept="image/*">
                                @error('profile_img')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>

                    {{-- Kartu Keluarga --}}
                    <div class="col-md-6">
                        <div class="document-box border rounded-3 p-3 h-100 @if($isReadonly) bg-light @endif">
                            <div class="d-flex align-items-start mb-3">
                                <div class="doc-icon bg-opacity-10 rounded me-3">
                                    <i class="fas fa-users text-info"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-label fw-semibold mb-1">
                                        Kartu Keluarga
                                        @if(!$biodata->kartu_keluarga) <span class="text-danger">*</span> @endif
                                    </label>
                                    <p class="text-muted small mb-0">Format: JPG, PNG, PDF (Max 2MB)</p>
                                </div>
                            </div>

                            @if($biodata->kartu_keluarga)
                                <div class="alert alert-success border-start border-3 border-success  bg-opacity-10 mb-3 py-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <small class="fw-semibold">File sudah diupload</small>
                                        </div>
                                        <button type="button"
                                            class="btn-preview {{ $kk ? '' : 'disabled' }}"
                                            data-url="{{ $kk ? asset('storage/'.$kk) : '' }}"
                                            onclick="openPreview(this)"
                                            {{ $kk ? '' : 'disabled' }}>
                                            <i class="fas fa-eye"></i>
                                            Lihat Preview 
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning border-start border-3 border-warning bg-warning bg-opacity-10 mb-3 py-2">
                                    <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                    <small>Belum ada file terupload</small>
                                </div>
                            @endif

                            @if(!$isReadonly)
                                <input type="file" name="kartu_keluarga" class="form-control @error('kartu_keluarga') is-invalid @enderror" accept="image/*,application/pdf">
                                @error('kartu_keluarga')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>

                    {{-- KTP --}}
                    <div class="col-md-6">
                        <div class="document-box border rounded-3 p-3 h-100 @if($isReadonly) bg-light @endif">
                            <div class="d-flex align-items-start mb-3">
                                <div class="doc-icon  bg-opacity-10 rounded me-3">
                                    <i class="fas fa-id-card text-warning"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-label fw-semibold mb-1">
                                        KTP
                                        @if(!$biodata->ktp) <span class="text-danger">*</span> @endif
                                    </label>
                                    <p class="text-muted small mb-0">Format: JPG, PNG, PDF (Max 2MB)</p>
                                </div>
                            </div>

                            @if($biodata->ktp)
                                <div class="alert alert-success border-start border-3 border-success  bg-opacity-10 mb-3 py-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <small class="fw-semibold">File sudah diupload</small>
                                        </div>
                                        <button type="button"
                                            class="btn-preview {{ $ktp ? '' : 'disabled' }}"
                                            data-url="{{ $ktp ? asset('storage/'.$ktp) : '' }}"
                                            onclick="openPreview(this)"
                                            {{ $ktp ? '' : 'disabled' }}>
                                            <i class="fas fa-eye"></i>
                                            Lihat Preview 
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning border-start border-3 border-warning bg-warning bg-opacity-10 mb-3 py-2">
                                    <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                    <small>Belum ada file terupload</small>
                                </div>
                            @endif

                            @if(!$isReadonly)
                                <input type="file" name="ktp" class="form-control @error('ktp') is-invalid @enderror" accept="image/*,application/pdf">
                                @error('ktp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>

                    {{-- Ijazah --}}
                    <div class="col-md-6">
                        <div class="document-box border rounded-3 p-3 h-100 @if($isReadonly) bg-light @endif">
                            <div class="d-flex align-items-start mb-3">
                                <div class="doc-icon  bg-opacity-10 rounded me-3">
                                    <i class="fas fa-graduation-cap text-White"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-label fw-semibold mb-1">
                                        Ijazah
                                        @if(!$biodata->ijazah) <span class="text-danger">*</span> @endif
                                    </label>
                                    <p class="text-muted small mb-0">Format: JPG, PNG, PDF (Max 2MB)</p>
                                </div>
                            </div>

                            @if($biodata->ijazah)
                                <div class="alert alert-success border-start border-3 border-success  bg-opacity-10 mb-3 py-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <small class="fw-semibold">File sudah diupload</small>
                                        </div>
                                        <button type="button"
                                            class="btn-preview {{ $ijazah ? '' : 'disabled' }}"
                                            data-url="{{ $ijazah ? asset('storage/'.$ijazah) : '' }}"
                                            onclick="openPreview(this)"
                                            {{ $ijazah ? '' : 'disabled' }}>
                                            <i class="fas fa-eye"></i>
                                            Lihat Preview
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning border-start border-3 border-warning bg-warning bg-opacity-10 mb-3 py-2">
                                    <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                    <small>Belum ada file terupload</small>
                                </div>
                            @endif

                            @if(!$isReadonly)
                                <input type="file" name="ijazah" class="form-control @error('ijazah') is-invalid @enderror" accept="image/*,application/pdf">
                                @error('ijazah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>

                    {{-- CV --}}
                    <div class="col-md-6">
                        <div class="document-box border rounded-3 p-3 h-100 @if($isReadonly) bg-light @endif">
                            <div class="d-flex align-items-start mb-3">
                                <div class="doc-icon bg-opacity-10 rounded me-3">
                                    <i class="fas fa-file-alt "></i>
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-label fw-semibold mb-1">
                                        Curriculum Vitae (CV)
                                        @if(!$biodata->cv) <span class="text-danger">*</span> @endif
                                    </label>
                                    <p class="text-muted small mb-0">Format: PDF (Max 2MB)</p>
                                </div>
                            </div>

                            @if($biodata->cv)
                                <div class="alert alert-success border-start border-3 border-success  bg-opacity-10 mb-3 py-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <small class="fw-semibold">File sudah diupload</small>
                                        </div>
                                        <button type="button"
                                            class="btn-preview {{ $cv ? '' : 'disabled' }}"
                                            data-url="{{ $cv ? asset('storage/'.$cv) : '' }}"
                                            onclick="openPreview(this)"
                                            {{ $cv ? '' : 'disabled' }}>
                                            <i class="fas fa-eye"></i>
                                            Lihat Preview
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning border-start border-3 border-warning bg-warning bg-opacity-10 mb-3 py-2">
                                    <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                    <small>Belum ada file terupload</small>
                                </div>
                            @endif

                            @if(!$isReadonly)
                                <input type="file" name="cv" class="form-control @error('cv') is-invalid @enderror" accept="application/pdf">
                                @error('cv')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>

                    {{-- Surat Pendaftaran --}}
                    <div class="col-md-6">
                        <div class="document-box border rounded-3 p-3 h-100 @if($isReadonly) bg-light @endif">
                            <div class="d-flex align-items-start mb-3">
                                <div class="doc-icon  bg-opacity-10 rounded me-3">
                                    <i class="fas fa-envelope "></i>
                                </div>
                                <div class="flex-grow-1">
                                    <label class="form-label fw-semibold mb-1">
                                        Surat Pendaftaran
                                        @if(!$biodata->surat_pendaftaran) <span class="text-danger">*</span> @endif
                                    </label>
                                    <p class="text-muted small mb-0">Format: JPG, PNG, PDF (Max 2MB)</p>
                                </div>
                            </div>

                            @if($biodata->surat_pendaftaran)
                                <div class="alert alert-success border-start border-3 border-success  bg-opacity-10 mb-3 py-2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="fas fa-check-circle text-success me-2"></i>
                                            <small class="fw-semibold">File sudah diupload</small>
                                        </div>
                                        <button type="button"
                                            class="btn-preview {{ $suratPendaftaran ? '' : 'disabled' }}"
                                            data-url="{{ $suratPendaftaran ? asset('storage/'.$suratPendaftaran) : '' }}"
                                            onclick="openPreview(this)"
                                            {{ $suratPendaftaran ? '' : 'disabled' }}>
                                            <i class="fas fa-eye"></i>
                                            Lihat Preview
                                        </button>


                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning border-start border-3 border-warning bg-warning bg-opacity-10 mb-3 py-2">
                                    <i class="fas fa-exclamation-circle text-warning me-2"></i>
                                    <small>Belum ada file terupload</small>
                                </div>
                            @endif

                            @if(!$isReadonly)
                                <input type="file" name="surat_pendaftaran" class="form-control @error('surat_pendaftaran') is-invalid @enderror" accept="image/*,application/pdf">
                                @error('surat_pendaftaran')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            @endif
                        </div>
                    </div>

                </div>

                {{-- Informasi Upload --}}
                @if(!$isReadonly)
                    <div class="alert alert-info border-start border-3 border-info bg-info bg-opacity-10 mt-4 mb-0">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-info-circle text-info me-3 mt-1"></i>
                            <div>
                                <h6 class="fw-semibold mb-2">Informasi Penting:</h6>
                                <ul class="mb-0 ps-3 small">
                                    <li>Pastikan semua dokumen yang diupload jelas dan dapat dibaca</li>
                                    <li>Ukuran maksimal file adalah 2MB per dokumen</li>
                                    <li>Dokumen yang sudah diupload dapat diganti dengan upload file baru</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        {{-- ================= TOMBOL AKSI ================= --}}
        @if(!$isReadonly)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('showbiodata') }}" class="btn btn-outline-secondary btn-lg w-100">
                                <i class="fas fa-times me-2"></i>
                                Batal
                            </a>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-save me-2"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                <h5 class="text-muted mb-3">Form telah dikunci karena data sudah terverifikasi</h5>
                <a href="{{ route('showbiodata') }}" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>
                    Kembali ke Halaman Utama
                </a>
            </div>
        @endif

    </form>

</div>

<!-- MODAL PREVIEW -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i>Preview Dokumen
                </h5>
                 <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                </button>
            </div>

            <div class="modal-body text-center">
                <img id="modalImage" class="img-fluid d-none" style="max-height:80vh;">
                <iframe id="modalPdf" class="d-none" width="100%" height="600"></iframe>

                <div id="modalEmpty" class="d-none text-muted">
                    <i class="fas fa-file-times fa-3x mb-3"></i>
                    <p>File tidak tersedia</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openPreview(button) {
    const url = button.getAttribute('data-url');

    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    const img = document.getElementById('modalImage');
    const pdf = document.getElementById('modalPdf');
    const empty = document.getElementById('modalEmpty');

    img.classList.add('d-none');
    pdf.classList.add('d-none');
    empty.classList.add('d-none');

    if (!url) {
        empty.classList.remove('d-none');
        modal.show();
        return;
    }

    const ext = url.split('.').pop().toLowerCase();

    if (['jpg','jpeg','png','webp'].includes(ext)) {
        img.src = url;
        img.classList.remove('d-none');
    } else if (ext === 'pdf') {
        pdf.src = url;
        pdf.classList.remove('d-none');
    }

    modal.show();
}
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Form Submission Loading State
        const form = document.getElementById('biodataForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
                }
            });
        }

        // File Input Preview
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const fileName = e.target.files[0]?.name;
                if (fileName) {
                    const alertBox = this.parentElement.querySelector('.alert-warning');
                    if (alertBox) {
                        alertBox.innerHTML = '<i class="fas fa-file text-info me-2"></i><small>File dipilih: <strong>' + fileName + '</strong></small>';
                        alertBox.classList.remove('alert-warning', 'bg-warning', 'border-warning');
                        alertBox.classList.add('alert-info', 'bg-info', 'border-info');
                    }
                }
            });
        });

        // Auto Dismiss Alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(alert => {
                const bsAlert = bootstrap.Alert.getInstance(alert) || new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

    });
</script>

@endsection

