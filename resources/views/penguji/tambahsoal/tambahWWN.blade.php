@extends('layouts.main2')

@section('pageheads')
<div class="container-fluid px-4">
    <h1 class="h3 mb-3 fw-bold text-gradient">
        <i class="fas fa-plus-circle me-2"></i>
        Tambah Soal Wawancara Baru
    </h1>
</div>
@endsection

@section('content')
<div class="container-fluid question-form-container">
    <div class="form-card">
        <!-- Info Alert -->
        <div class="info-alert">
            <i class="fas fa-lightbulb text-white"></i>
            <div class="text-white">
                Buat soal wawancara dengan 5 opsi jawaban. Setiap opsi memiliki poin otomatis dari 5 (paling ideal) hingga 1 (kurang ideal).
            </div>
        </div>

        <form action="{{ route('WWN.store') }}" 
              method="POST" 
              enctype="multipart/form-data"
              id="questionForm">
            @csrf

            <!-- Section: Informasi Soal -->
            <div class="section-header">
                <i class="fas fa-file-alt"></i>
                <h5>Informasi Soal</h5>
            </div>

            <!-- Subject -->
            <div class="form-section">
                <label>
                    <i class="fas fa-book"></i>
                    Subject / Kategori
                    <span class="text-danger">*</span>
                </label>
                <input type="text"
                       name="subject"
                       class="form-control @error('subject') is-invalid @enderror"
                       value="{{ old('subject') }}"
                       placeholder="Contoh: Kepemimpinan, Komunikasi, Teamwork, dll"
                       required>
                @error('subject')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Pertanyaan -->
            <div class="form-section">
                <label>
                    <i class="fas fa-question-circle"></i>
                    Pertanyaan / Soal Wawancara
                    <span class="text-danger">*</span>
                </label>
                <textarea name="pertanyaan"
                          class="form-control @error('pertanyaan') is-invalid @enderror"
                          rows="5"
                          placeholder="Tulis pertanyaan wawancara di sini..."
                          required>{{ old('pertanyaan') }}</textarea>
                @error('pertanyaan')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Image Upload -->
            <div class="form-section">
                <label>
                    <i class="fas fa-image"></i>
                    Gambar Soal (Opsional)
                </label>
                <div class="image-upload-wrapper" id="imageUploadWrapper" onclick="document.getElementById('imageInput').click()">
                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                    <div class="upload-text">Klik untuk upload gambar</div>
                    <div class="upload-hint">Format: JPG, PNG, GIF (Max 2MB)</div>
                    <img id="preview-image" style="display: none;">
                </div>
                <input type="file"
                       id="imageInput"
                       name="image"
                       class="d-none"
                       accept="image/*"
                       onchange="previewImage(event)">
                <div class="image-actions" id="imageActions">
                    <button type="button" class="btn-remove-image" onclick="removeImage()">
                        <i class="fas fa-trash-alt"></i> Hapus Gambar
                    </button>
                </div>
                @error('image')
                    <div class="invalid-feedback d-block">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Section: Opsi Jawaban -->
            <div class="section-header mt-4">
                <i class="fas fa-list-ol"></i>
                <h5>Opsi Jawaban dengan Poin Otomatis</h5>
            </div>

            <!-- Point Info -->
            <div class="point-info-box">
                <i class="fas fa-info-circle"></i>
                <div>
                    <strong>Sistem Poin Otomatis:</strong>
                    <div class="point-breakdown">
                        <span class="point-item"><span class="point-badge point-5">A</span> = 5 Poin (Paling Ideal)</span>
                        <span class="point-item"><span class="point-badge point-4">B</span> = 4 Poin</span>
                        <span class="point-item"><span class="point-badge point-3">C</span> = 3 Poin</span>
                        <span class="point-item"><span class="point-badge point-2">D</span> = 2 Poin</span>
                        <span class="point-item"><span class="point-badge point-1">E</span> = 1 Poin (Kurang Ideal)</span>
                    </div>
                </div>
            </div>

            @php
                $points = [5, 4, 3, 2, 1];
                $colors = ['primary', 'success', 'warning', 'info', 'danger'];
                $colorHex = ['#4e73df', '#1cc88a', '#f6c23e', '#36b9cc', '#e74a3b'];
                $icons = ['star', 'check-circle', 'exclamation-circle', 'info-circle', 'times-circle'];
                $descriptions = ['Paling Ideal', 'Sangat Baik', 'Cukup Baik', 'Kurang Baik', 'Kurang Ideal'];
            @endphp

            @foreach(['A', 'B', 'C', 'D', 'E'] as $i => $label)
                <div class="option-card option-{{ strtolower($label) }}" data-color="{{ $colorHex[$i] }}">
                    <div class="option-header option-color-{{ $i }}">
                        <div class="option-title">
                            <i class="fas fa-{{ $icons[$i] }} option-icon-{{ $i }}"></i>
                            <span class="option-label">Opsi {{ $label }}</span>
                            <span class="option-point-badge option-badge-{{ $i }}">
                                {{ $points[$i] }} Poin
                            </span>
                        </div>
                        <div class="option-description">
                            {{ $descriptions[$i] }}
                        </div>
                    </div>
                    
                    <div class="option-body">
                        <label>
                            <i class="fas fa-edit"></i>
                            Teks Opsi {{ $label }}
                            <span class="text-danger">*</span>
                        </label>
                        <textarea
                            name="options[{{ $i }}][opsi_tulisan]"
                            class="form-control @error('options.'.$i.'.opsi_tulisan') is-invalid @enderror"
                            rows="2"
                            placeholder="Masukkan teks untuk opsi {{ $label }} ({{ $descriptions[$i] }})"
                            required>{{ old('options.'.$i.'.opsi_tulisan') }}</textarea>
                        
                        @error('options.'.$i.'.opsi_tulisan')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror

                        <input type="hidden" name="options[{{ $i }}][label]" value="{{ $label }}">
                        <input type="hidden" name="options[{{ $i }}][point]" value="{{ $points[$i] }}">
                    </div>
                </div>
            @endforeach

            <!-- Action Buttons -->
            <div class="form-actions">
                <a href="{{ route('addWWN') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i>
                    Simpan Soal
                </button>
            </div>
        </form>
    </div>
</div>


<script>
// Preview image upload
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('preview-image');
    const wrapper = document.getElementById('imageUploadWrapper');
    const actions = document.getElementById('imageActions');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Check file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 2MB');
            input.value = '';
            return;
        }

        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            wrapper.classList.add('has-image');
            actions.classList.add('show');
            
            // Hide upload text
            wrapper.querySelector('.upload-icon').style.display = 'none';
            wrapper.querySelector('.upload-text').style.display = 'none';
            wrapper.querySelector('.upload-hint').style.display = 'none';
        };

        reader.readAsDataURL(file);
    }
}

// Remove image
function removeImage() {
    const input = document.getElementById('imageInput');
    const preview = document.getElementById('preview-image');
    const wrapper = document.getElementById('imageUploadWrapper');
    const actions = document.getElementById('imageActions');
    
    input.value = '';
    preview.src = '';
    preview.style.display = 'none';
    wrapper.classList.remove('has-image');
    actions.classList.remove('show');
    
    // Show upload text again
    wrapper.querySelector('.upload-icon').style.display = 'block';
    wrapper.querySelector('.upload-text').style.display = 'block';
    wrapper.querySelector('.upload-hint').style.display = 'block';
}

// Form validation
document.getElementById('questionForm').addEventListener('submit', function(e) {
    // Check if all options are filled
    let allFilled = true;
    document.querySelectorAll('textarea[name*="[opsi_tulisan]"]').forEach(input => {
        if (!input.value.trim()) {
            allFilled = false;
        }
    });
    
    if (!allFilled) {
        e.preventDefault();
        alert('Silakan isi semua opsi jawaban!');
        return false;
    }
});
</script>

@endsection