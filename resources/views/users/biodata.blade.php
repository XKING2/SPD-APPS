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
                @if($biodata)
                        <a href="{{ route('biodata.update') }}" class="btn-update-biodata">
                            <i class="fas fa-edit"></i>
                            Lanjut Update Biodata
                        </a>
                @endif
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