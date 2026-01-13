@extends('layouts.main2')

@section('pageheads')
<div class="container-fluid px-4">
    <h1 class="h3 mb-3 fw-bold text-gradient">
        <i class="fas fa-edit me-2"></i>
        Edit Ujian
    </h1>
</div>
@endsection

@section('content')


<div class="container-fluid seleksi-form-container">
    <div class="form-card">
        <form method="POST" action="{{ route('exam.update', $exam->id) }}" id="formExam">
            @csrf
            @method('PUT')

            <!-- Judul Ujian -->
            <div class="form-section">
                <label>
                    <i class="fas fa-heading"></i>
                    Judul Ujian
                    <span class="text-danger">*</span>
                </label>
                <input type="text"
                       name="judul"
                       class="form-control @error('judul') is-invalid @enderror"
                       value="{{ old('judul', $exam->judul) }}"
                       placeholder="Masukkan judul ujian"
                       required>
                @error('judul')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Type Ujian -->
            <div class="form-section">
                <label>
                    <i class="fas fa-tag"></i>
                    Tipe Ujian
                    <span class="text-danger">*</span>
                </label>
                <select name="type" 
                        class="form-select @error('type') is-invalid @enderror" 
                        required>
                    <option value="">-- Pilih Tipe --</option>
                    @foreach ($types as $key => $label)
                        <option value="{{ $key }}"
                            {{ old('type', $exam->type) === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Seleksi -->
            <div class="form-section">
                <label>
                    <i class="fas fa-clipboard-list"></i>
                    Seleksi
                    <span class="text-danger">*</span>
                </label>
                <select name="id_seleksi" 
                        class="form-select @error('id_seleksi') is-invalid @enderror" 
                        required>
                    <option value="">-- Pilih Seleksi --</option>
                    @foreach ($seleksi as $s)
                        <option value="{{ $s->id }}"
                            {{ old('id_seleksi', $exam->id_seleksi) == $s->id ? 'selected' : '' }}>
                            {{ $s->judul }}
                        </option>
                    @endforeach
                </select>
                @error('id_seleksi')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Duration -->
            <div class="form-section">
                <label>
                    <i class="fas fa-clock"></i>
                    Durasi (dalam menit)
                    <span class="text-danger">*</span>
                </label>
                <input type="number"
                       name="duration"
                       class="form-control @error('duration') is-invalid @enderror"
                       min="1"
                       value="{{ old('duration', $exam->duration) }}"
                       placeholder="Contoh: 90"
                       required>
                @error('duration')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i>
                    Masukkan durasi ujian dalam satuan menit
                </small>
            </div>

            <div class="row">
                <!-- Waktu Mulai -->
                <div class="col-md-6">
                    <div class="form-section">
                        <label>
                            <i class="fas fa-calendar-check"></i>
                            Waktu Mulai
                            <span class="text-danger">*</span>
                        </label>
                        <input type="datetime-local"
                               name="start_at"
                               class="form-control @error('start_at') is-invalid @enderror"
                               value="{{ old('start_at', \Carbon\Carbon::parse($exam->start_at)->format('Y-m-d\TH:i')) }}"
                               required>
                        @error('start_at')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Waktu Selesai -->
                <div class="col-md-6">
                    <div class="form-section">
                        <label>
                            <i class="fas fa-calendar-times"></i>
                            Waktu Selesai
                            <span class="text-danger">*</span>
                        </label>
                        <input type="datetime-local"
                               name="end_at"
                               class="form-control @error('end_at') is-invalid @enderror"
                               value="{{ old('end_at', \Carbon\Carbon::parse($exam->end_at)->format('Y-m-d\TH:i')) }}"
                               required>
                        @error('end_at')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="form-actions">
                <a href="{{ route('addexams') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Batal
                </a>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const startAtInput = document.querySelector('input[name="start_at"]');
    const endAtInput = document.querySelector('input[name="end_at"]');
    const durationInput = document.querySelector('input[name="duration"]');

    // Validasi waktu selesai harus lebih besar dari waktu mulai
    if (startAtInput && endAtInput) {
        endAtInput.addEventListener('change', function() {
            const startAt = new Date(startAtInput.value);
            const endAt = new Date(endAtInput.value);
            
            if (endAt <= startAt) {
                alert('Waktu selesai harus lebih besar dari waktu mulai!');
                endAtInput.value = '';
            }
        });

        startAtInput.addEventListener('change', function() {
            if (endAtInput.value) {
                const startAt = new Date(startAtInput.value);
                const endAt = new Date(endAtInput.value);
                
                if (endAt <= startAt) {
                    alert('Waktu selesai harus lebih besar dari waktu mulai!');
                    endAtInput.value = '';
                }
            }
        });
    }

    // Validasi durasi tidak boleh 0 atau negatif
    if (durationInput) {
        durationInput.addEventListener('input', function() {
            if (this.value < 1) {
                this.value = 1;
            }
        });
    }
});
</script>

@endsection