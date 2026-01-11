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
<style>
    .text-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .biodata-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .alert-exists {
        background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
        border: none;
        border-left: 5px solid #ff9800;
        border-radius: 12px;
        padding: 20px;
        color: #e65100;
        font-weight: 600;
        text-align: center;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        box-shadow: 0 4px 15px rgba(255, 152, 0, 0.2);
    }

    .alert-exists i {
        font-size: 24px;
    }

    .photo-upload-section {
        background: white;
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 30px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        text-align: center;
    }

    .photo-preview-container {
        position: relative;
        display: inline-block;
        margin-bottom: 20px;
    }

    .photo-preview {
        width: 180px;
        height: 240px;
        object-fit: cover;
        border-radius: 15px;
        border: 5px solid #f0f0f0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .photo-preview:hover {
        transform: scale(1.05);
        box-shadow: 0 15px 40px rgba(102, 126, 234, 0.3);
    }

    .btn-photo-upload {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-photo-upload:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-photo-upload i {
        font-size: 18px;
    }

    .form-card {
        background: white;
        border-radius: 20px;
        padding: 35px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .form-section-title {
        font-size: 20px;
        font-weight: 700;
        color: #333;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 3px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-section-title i {
        color: #667eea;
        font-size: 24px;
    }

    .upload-group {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .upload-group:hover {
        border-color: #667eea;
        background: #f5f7ff;
    }

    .upload-group label {
        color: #333;
        font-weight: 600;
        font-size: 15px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .upload-group label i {
        color: #667eea;
        font-size: 18px;
    }

    .form-control {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .btn-preview {
        background: white;
        border: 2px solid #667eea;
        color: #667eea;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        margin-top: 10px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-preview:hover:not(.disabled) {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
    }

    .btn-preview.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        border-color: #ddd;
        color: #999;
    }

    .btn-preview i {
        font-size: 14px;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 10px;
    }

    .status-badge.uploaded {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.not-uploaded {
        background: #f8d7da;
        color: #721c24;
    }

    .status-badge.selected {
        background: #d4edda;
        color: #155724;
    }

    .status-badge.not-selected {
        background: #f8d7da;
        color: #721c24;
    }

    .formasi-select-group {
        background: linear-gradient(135deg, #f5f7ff 0%, #e8ebff 100%);
        border-radius: 12px;
        padding: 20px;
        border: 2px solid #667eea;
    }

    .formasi-select-group label {
        color: #333;
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .formasi-select-group label i {
        color: #667eea;
    }

    .formasi-select-group .text-danger {
        color: #e74a3b !important;
        font-weight: 700;
    }

    .formasi-select-group select {
        background: white;
        border: 2px solid #667eea;
    }

    .formasi-select-group select:disabled {
        background: #e9ecef;
        cursor: not-allowed;
    }

    .alert-no-formasi {
        background: linear-gradient(135deg, #fff3e0 0%, #ffe0b2 100%);
        border: none;
        border-left: 5px solid #ff9800;
        border-radius: 12px;
        padding: 20px;
        color: #e65100;
        font-weight: 600;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 30px;
        padding-top: 25px;
        border-top: 2px solid #f0f0f0;
    }

    .btn-submit {
        background: linear-gradient(135deg, #23d5ab 0%, #1cc88a 100%);
        color: white;
        border: none;
        padding: 14px 40px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 16px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(28, 200, 138, 0.3);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(28, 200, 138, 0.4);
    }

    .btn-submit i {
        font-size: 18px;
    }

    .upload-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 10px;
    }

    @media (max-width: 768px) {
        .photo-upload-section {
            padding: 25px 20px;
        }

        .photo-preview {
            width: 150px;
            height: 200px;
        }

        .form-card {
            padding: 25px 20px;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-submit {
            width: 100%;
            justify-content: center;
        }
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .photo-upload-section,
    .form-card {
        animation: fadeInUp 0.5s ease;
    }

    .upload-group {
        animation: fadeInUp 0.5s ease;
    }

    .upload-group:nth-child(1) { animation-delay: 0.1s; }
    .upload-group:nth-child(2) { animation-delay: 0.2s; }
    .upload-group:nth-child(3) { animation-delay: 0.3s; }
</style>

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
            <span>Anda sudah mengupload biodata sebelumnya</span>
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
            </div>
            
            <label class="btn-photo-upload">
                <i class="fas fa-camera"></i>
                <span>Pilih Foto Profile</span>
                <input type="file" name="profile_img" id="fotoInput" class="d-none" accept="image/*">
            </label>
            
            <p class="text-muted mt-3 mb-0" style="font-size: 13px;">
                <i class="fas fa-info-circle me-1"></i>
                Format: JPG, PNG | Ukuran maksimal: 2MB
            </p>
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
                            <a href="{{ $kk ? asset('storage/'.$kk) : '#' }}"
                               class="btn-preview {{ $kk ? '' : 'disabled' }}" 
                               target="_blank">
                                <i class="fas fa-eye"></i>
                                Lihat Preview KK
                            </a>
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
                            <a href="{{ $ktp ? asset('storage/'.$ktp) : '#' }}"
                               class="btn-preview {{ $ktp ? '' : 'disabled' }}" 
                               target="_blank">
                                <i class="fas fa-eye"></i>
                                Lihat Preview KTP
                            </a>
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
                            <a href="{{ $suratPendaftaran ? asset('storage/'.$suratPendaftaran) : '#' }}"
                               class="btn-preview {{ $suratPendaftaran ? '' : 'disabled' }}" 
                               target="_blank">
                                <i class="fas fa-file-pdf"></i>
                                Lihat Surat Pendaftaran
                            </a>
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
                            <a href="{{ $ijazah ? asset('storage/'.$ijazah) : '#' }}"
                               class="btn-preview {{ $ijazah ? '' : 'disabled' }}" 
                               target="_blank">
                                <i class="fas fa-eye"></i>
                                Lihat Preview Ijazah
                            </a>
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
                            <a href="{{ $cv ? asset('storage/'.$cv) : '#' }}"
                               class="btn-preview {{ $cv ? '' : 'disabled' }}" 
                               target="_blank">
                                <i class="fas fa-file-pdf"></i>
                                Lihat Preview CV
                            </a>
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
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i>
                    Simpan Data
                </button>
            </div>

        </div>

    </form>

</div>

<script>
    document.getElementById('fotoInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (!file) return;
        document.getElementById('previewFoto').src = URL.createObjectURL(file);
    });
</script>

@endsection