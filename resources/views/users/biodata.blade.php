@extends('layouts.main')

@section('pageheads')
<div class="container-fluid px-4">
    <h1 class="h3 mb-3 fw-bold text-gradient">
        <i class="fas fa-user-edit me-2"></i>
        Tambah Data Diri Anda
    </h1>
</div>
@endsection

@section('content')

<div class="container-fluid biodata-container">

    @php
        $profileImg = optional($biodata)->profile_img ?? 'img/undraw_profile.svg';
        $kk = optional($biodata)->kartu_keluarga;
        $ktp = optional($biodata)->ktp;
        $ijazah = optional($biodata)->ijazah;
        $cv = optional($biodata)->cv;
        $suratPendaftaran = optional($biodata)->surat_pendaftaran;
        $isReadonly = !empty($biodata) && !empty($biodata->id_kebutuhan);
    @endphp

    @if($biodata)
        <div class="alert-exists">
            <i class="fas fa-info-circle"></i>

            @if($biodata->status === 'valid')
                <span class="text-success fw-semibold">
                    Biodata anda sudah valid, jangan ganti data anda
                </span>
            @else
                <span>
                    Anda sudah mengupload biodata sebelumnya
                </span>
            @endif

        </div>
    @endif

    <form action="{{ route('biodata.post') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- FOTO PROFILE SECTION -->
        <div class="photo-upload-section">
            <div class="photo-preview-container">
                <img id="previewFoto"
                    src="{{ $profileImg == 'img/undraw_profile.svg'
                            ? asset($profileImg)
                            : asset('storage/'.$profileImg) }}"
                    class="photo-preview"
                    alt="Preview Foto">
                <div class="photo-overlay">
                    <i class="fas fa-camera"></i>
                </div>
            </div>
            <p class="text-muted mt-3 mb-0" style="font-size: 13px;">
                <i class="fas fa-info-circle me-1"></i>
                Format: JPG, PNG | Ukuran: 3x4 | Maksimal: 2MB
            </p>

            
            <label class="btn-photo-upload">
                <i class="fas fa-upload"></i>
                <span>Upload Foto 3x4</span>
                <input type="file" name="profile_img" id="fotoInput" class="d-none" accept="image/*">
            </label>

            
            
        </div>

        <!-- FORM CARD -->
        <div class="form-card">
            <div class="row">
                <!-- KOLOM KIRI -->
                <div class="col-md-6">
                    <div class="form-section-title">
                        <i class="fas fa-file-upload"></i>
                        Dokumen Identitas
                    </div>

                    <!-- Kartu Keluarga -->
                    <div class="upload-group">
                        <label>
                            <i class="fas fa-id-card"></i>
                            Kartu Keluarga
                        </label>
                        <input type="file" name="kartu_keluarga" class="form-control" accept="image/*">
                        
                        <div class="upload-info">
                            <button type="button"
                                class="btn-preview {{ $kk ? '' : 'disabled' }}"
                                data-url="{{ $kk ? asset('storage/'.$kk) : '' }}"
                                onclick="openPreview(this)"
                                {{ $kk ? '' : 'disabled' }}>
                                Lihat Preview KK
                            </button>

                            <span class="status-badge {{ optional($biodata)->kartu_keluarga ? 'uploaded' : 'not-uploaded' }}">
                                {{ optional($biodata)->kartu_keluarga ? '✓ Sudah upload' : '✗ Belum upload' }}
                            </span>
                        </div>
                    </div>

                    <!-- KTP -->
                    <div class="upload-group">
                        <label>
                            <i class="fas fa-address-card"></i>
                            Upload KTP
                        </label>
                        <input type="file" name="ktp" class="form-control" accept="image/*">
                        
                        <div class="upload-info">
                            <button type="button"
                                class="btn-preview {{ $ktp ? '' : 'disabled' }}"
                                data-url="{{ $ktp ? asset('storage/'.$ktp) : '' }}"
                                onclick="openPreview(this)"
                                {{ $ktp ? '' : 'disabled' }}>
                                <i class="fas fa-eye"></i>
                                Lihat Preview KTP
                            </button>
        
                            <span class="status-badge {{ optional($biodata)->ktp ? 'uploaded' : 'not-uploaded' }}">
                                {{ optional($biodata)->ktp ? '✓ Sudah upload' : '✗ Belum upload' }}
                            </span>
                        </div>
                    </div>

                    <!-- Surat Pendaftaran -->
                    <div class="upload-group">
                        <label>
                            <i class="fas fa-file-signature"></i>
                            Surat Pendaftaran
                        </label>
                        <input type="file" name="surat_pendaftaran" class="form-control" accept="application/pdf">

                        
                        
                        <div class="upload-info">
                           <button type="button"
                                class="btn-preview {{ $suratPendaftaran ? '' : 'disabled' }}"
                                data-url="{{ $suratPendaftaran ? asset('storage/'.$suratPendaftaran) : '' }}"
                                onclick="openPreview(this)"
                                {{ $suratPendaftaran ? '' : 'disabled' }}>
                                <i class="fas fa-eye"></i>
                                Lihat Preview Surat Lamaran
                            </button>
                            <span class="status-badge {{ optional($biodata)->surat_pendaftaran ? 'uploaded' : 'not-uploaded' }}">
                                {{ optional($biodata)->surat_pendaftaran ? '✓ Sudah upload' : '✗ Belum upload' }}
                            </span>
                        </div>
                    </div>

                </div> <!-- END KOLOM KIRI -->

                <!-- KOLOM KANAN -->
                <div class="col-md-6">
                    <div class="form-section-title">
                        <i class="fas fa-graduation-cap"></i>
                        Dokumen Pendidikan & CV
                    </div>

                    <!-- Ijazah -->
                    <div class="upload-group">
                        <label>
                            <i class="fas fa-certificate"></i>
                            Upload Ijazah
                        </label>
                        <input type="file" name="ijazah" class="form-control" accept="image/*">
                        
                        
                        <div class="upload-info">
                            
                            <button type="button"
                                class="btn-preview {{ $ijazah ? '' : 'disabled' }}"
                                data-url="{{ $ijazah ? asset('storage/'.$ijazah) : '' }}"
                                onclick="openPreview(this)"
                                {{ $ijazah ? '' : 'disabled' }}>
                                <i class="fas fa-eye"></i>
                                Lihat Preview Ijazah
                            </button>
      
                            <span class="status-badge {{ optional($biodata)->ijazah ? 'uploaded' : 'not-uploaded' }}">
                                {{ optional($biodata)->ijazah ? '✓ Sudah upload' : '✗ Belum upload' }}
                            </span>
                        </div>
                    </div>

                    <!-- CV -->
                    <div class="upload-group">
                        <label>
                            <i class="fas fa-file-alt"></i>
                            Upload CV
                        </label>
                        <input type="file" name="cv" class="form-control" accept="application/pdf">
                        
                        <div class="upload-info">
                            <button type="button"
                                class="btn-preview {{ $cv ? '' : 'disabled' }}"
                                data-url="{{ $cv ? asset('storage/'.$cv) : '' }}"
                                onclick="openPreview(this)"
                                {{ $cv ? '' : 'disabled' }}>
                                <i class="fas fa-eye"></i>
                                Lihat Preview CV
                            </button>
                            <span class="status-badge {{ optional($biodata)->cv ? 'uploaded' : 'not-uploaded' }}">
                                {{ optional($biodata)->cv ? '✓ Sudah upload' : '✗ Belum upload' }}
                            </span>
                        </div>
                    </div>

                    <!-- Formasi Selection -->
                    <div class="formasi-select-group">
                        @forelse($formasis as $formasi)
                            <label>
                                <i class="fas fa-briefcase"></i>
                                Formasi Tahun {{ $formasi->tahun }}
                                <span class="text-danger">*</span>
                            </label>

                            <select name="id_kebutuhan" class="form-control" {{ $isReadonly ? 'disabled' : '' }} required>
                                <option value="">-- Pilih Formasi --</option>
                                @foreach($formasi->kebutuhan as $kebutuhan)
                                    <option value="{{ $kebutuhan->id }}"
                                        {{ $biodata?->id_kebutuhan == $kebutuhan->id ? 'selected' : '' }}>
                                        {{ $kebutuhan->nama_kebutuhan }}
                                    </option>
                                @endforeach
                            </select>
                            
                            @if($isReadonly)
                                <input type="hidden" name="id_kebutuhan" value="{{ $biodata->id_kebutuhan }}">
                            @endif
                            
                            <input type="hidden" name="id_formasi" value="{{ $formasi->id }}">
                            
                            <div class="mt-2">
                                <span class="status-badge {{ $isReadonly ? 'selected' : 'not-selected' }}">
                                    {{ $isReadonly ? '✓ Sudah dipilih' : '✗ Belum dipilih' }}
                                </span>
                            </div>
                               
                        @empty
                            <div class="alert-no-formasi">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Formasi belum tersedia
                            </div>
                        @endforelse
                    </div>

                </div> <!-- END KOLOM KANAN -->
            </div>

            <!-- FORM ACTIONS -->
            <div class="form-actions">
                @if($biodata)
                    @if($biodata->status === 'valid')
                        <button class="btn-update-biodata disabled"
                                type="button"
                                style="pointer-events: none; opacity: .6;"
                                title="Biodata sudah divalidasi dan tidak bisa diubah">
                            <i class="fas fa-lock me-1"></i>
                            Biodata Sudah Valid
                        </button>
                    @else
                        <a href="{{ route('edit.biodata', Hashids::encode($biodata->id)) }}"
                        class="btn-update-biodata">
                            <i class="fas fa-edit"></i>
                            Lanjut Update Biodata
                        </a>
                    @endif

                    {{-- 🔒 SIMPAN DATA DI-DISABLE --}}
                    <button type="button"
                            class="btn-submit disabled"
                            style="pointer-events: none; opacity: .6;"
                            title="Anda sudah memiliki biodata, silakan update data">
                        <i class="fas fa-ban"></i>
                        Simpan Data
                    </button>
                @else
                    {{-- ✅ USER BELUM PUNYA BIODATA --}}
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i>
                        Simpan Data
                    </button>
                @endif
            </div>

        </div>

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
    document.getElementById('fotoInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (!file) return;
        
        // Check file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 2MB');
            this.value = '';
            return;
        }
        
        document.getElementById('previewFoto').src = URL.createObjectURL(file);
    });
</script>

@endsection