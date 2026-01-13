@extends('layouts.main2')

@section('pageheads')
<div class="container-fluid px-4">
    <h1 class="h3 mb-3 fw-bold text-gradient">
        <i class="fas fa-plus-circle me-2"></i>
        Tambah Soal TPU Baru
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
                Pastikan Anda mengisi semua informasi soal dengan benar. Pilih jawaban yang benar dari 4 opsi yang tersedia.
            </div>
        </div>

        <form action="{{ route('TPU.store') }}" 
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
                    Subject
                    <span class="text-danger">*</span>
                </label>
                <input type="text"
                       name="subject"
                       class="form-control @error('subject') is-invalid @enderror"
                       value="{{ old('subject') }}"
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
                    Pertanyaan / Soal
                    <span class="text-danger">*</span>
                </label>
                <textarea name="pertanyaan"
                          class="form-control @error('pertanyaan') is-invalid @enderror"
                          rows="5"
                          placeholder="Tulis pertanyaan atau soal di sini..."
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

            <!-- Jawaban Benar -->
            <div class="form-section">
                <label>
                    <i class="fas fa-check-circle"></i>
                    Jawaban Benar
                    <span class="text-danger">*</span>
                </label>
                <select name="jawaban_benar" 
                        id="correctAnswerSelect"
                        class="form-select @error('jawaban_benar') is-invalid @enderror" 
                        required
                        onchange="highlightCorrectAnswer()">
                    <option value="">-- Pilih Jawaban Benar --</option>
                    @foreach (['A','B','C','D'] as $opt)
                        <option value="{{ $opt }}" {{ old('jawaban_benar') === $opt ? 'selected' : '' }}>
                            Opsi {{ $opt }}
                        </option>
                    @endforeach
                </select>
                @error('jawaban_benar')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Section: Opsi Jawaban -->
            <div class="section-header mt-4">
                <i class="fas fa-list-ul"></i>
                <h5>Opsi Jawaban</h5>
            </div>

            @foreach (['A','B','C','D'] as $index => $label)
                <div class="option-card" id="option-{{ $label }}">
                    <div class="option-header">
                        <div class="option-label">
                            <span class="option-badge" id="badge-{{ $label }}">Opsi {{ $label }}</span>
                            <span class="correct-indicator">
                                <i class="fas fa-check-circle"></i>
                                Jawaban Benar
                            </span>
                        </div>
                    </div>
                    
                    <input type="text"
                           name="options[{{ $index }}][label]"
                           value="{{ $label }}"
                           hidden>
                    
                    <input type="text"
                           name="options[{{ $index }}][text]"
                           class="form-control @error("options.$index.text") is-invalid @enderror"
                           value="{{ old("options.$index.text") }}"
                           placeholder="Masukkan teks untuk opsi {{ $label }}"
                           required>
                    
                    @error("options.$index.text")
                        <div class="invalid-feedback">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endforeach

            <!-- Action Buttons -->
            <div class="form-actions">
                <a href="{{ route('addTPU') }}" class="btn-back">
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

// Highlight correct answer
function highlightCorrectAnswer() {
    const correctAnswer = document.getElementById('correctAnswerSelect').value;
    
    // Remove all highlights
    ['A', 'B', 'C', 'D'].forEach(label => {
        const card = document.getElementById(`option-${label}`);
        const badge = document.getElementById(`badge-${label}`);
        
        card.classList.remove('correct-answer');
        badge.classList.remove('correct');
    });
    
    // Add highlight to correct answer
    if (correctAnswer) {
        const card = document.getElementById(`option-${correctAnswer}`);
        const badge = document.getElementById(`badge-${correctAnswer}`);
        
        card.classList.add('correct-answer');
        badge.classList.add('correct');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    highlightCorrectAnswer();
});

// Form validation
document.getElementById('questionForm').addEventListener('submit', function(e) {
    const correctAnswer = document.getElementById('correctAnswerSelect').value;
    
    if (!correctAnswer) {
        e.preventDefault();
        alert('Silakan pilih jawaban yang benar!');
        document.getElementById('correctAnswerSelect').focus();
        return false;
    }
    
    // Check if all options are filled
    let allFilled = true;
    document.querySelectorAll('input[name*="[text]"]').forEach(input => {
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