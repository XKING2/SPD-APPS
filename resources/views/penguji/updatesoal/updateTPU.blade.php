@extends('layouts.main2')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Edit Soal TPU
                    </h4>
                    <p class="text-muted mb-0">
                        <small>Perbarui informasi soal dan opsi jawaban</small>
                    </p>
                </div>
                <a href="{{ route('addTPU') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <form action="{{ route('TPU.update', $question->id) }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf
                @method('PUT')

                {{-- CARD: INFORMASI SOAL --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Informasi Soal
                        </h6>
                    </div>
                    <div class="card-body">
                        
                        {{-- SUBJECT --}}
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">
                                Subject
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                   name="subject"
                                   class="form-control @error('subject') is-invalid @enderror"
                                   value="{{ old('subject', $question->subject) }}"
                                   placeholder="Contoh: Matematika"
                                   required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- PERTANYAAN --}}
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">
                                Pertanyaan
                                <span class="text-danger">*</span>
                            </label>
                            <textarea name="pertanyaan"
                                      class="form-control @error('pertanyaan') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Tulis pertanyaan di sini..."
                                      required>{{ old('pertanyaan', $question->pertanyaan) }}</textarea>
                            @error('pertanyaan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- IMAGE --}}
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">
                                Gambar Soal
                                <span class="text-muted">(opsional)</span>
                            </label>
                            
                            <div class="mb-2">
                                <img id="preview-image"
                                    src="{{ $question->image_name ? asset('storage/'.$question->image_name) : '' }}"
                                    class="img-thumbnail"
                                    style="max-width: 250px; max-height: 250px; object-fit: cover;"
                                    @if(!$question->image_name) hidden @endif>
                            </div>

                            <input type="file"
                                name="image"
                                class="form-control @error('image') is-invalid @enderror"
                                accept="image/*"
                                onchange="previewImage(event)">
                            
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-info-circle"></i>
                                Format: JPG, PNG, GIF. Maksimal 2MB
                            </small>
                            
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- JAWABAN BENAR --}}
                        @php
                            $jawabanBenar = null;
                            $map = ['A', 'B', 'C', 'D'];

                            foreach ($question->options as $i => $opt) {
                                if ($opt->id == $question->correct_option_id) {
                                    $jawabanBenar = $map[$i] ?? null;
                                    break;
                                }
                            }
                        @endphp

                        <div class="form-group mb-0">
                            <label class="form-label fw-bold">
                                Jawaban Benar
                                <span class="text-danger">*</span>
                            </label>
                            <select name="jawaban_benar" 
                                    class="form-select @error('jawaban_benar') is-invalid @enderror" 
                                    required>
                                <option value="">Pilih Jawaban Benar</option>
                                @foreach (['A','B','C','D'] as $opt)
                                    <option value="{{ $opt }}"
                                        {{ old('jawaban_benar', $jawabanBenar) === $opt ? 'selected' : '' }}>
                                        Opsi {{ $opt }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jawaban_benar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- CARD: OPSI JAWABAN --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-list-ul me-2"></i>
                            Opsi Jawaban
                        </h6>
                    </div>
                    <div class="card-body">
                        
                        @foreach ($question->options as $index => $option)
                            <div class="card mb-3 border-{{ $index == 0 ? 'primary' : ($index == 1 ? 'success' : ($index == 2 ? 'warning' : 'danger')) }}">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <strong class="text-dark">
                                        <i class="fas fa-{{ chr(97 + $index) }}-circle me-1"></i>
                                        Opsi {{ chr(65 + $index) }}
                                    </strong>
                                    <span class="badge bg-{{ $index == 0 ? 'primary' : ($index == 1 ? 'success' : ($index == 2 ? 'warning' : 'danger')) }}">
                                        {{ chr(65 + $index) }}
                                    </span>
                                </div>
                                <div class="card-body">

                                    <input type="hidden"
                                        name="options[{{ $index }}][id]"
                                        value="{{ $option->id }}">

                                    <input type="text"
                                        name="options[{{ $index }}][text]"
                                        class="form-control @error('options.'.$index.'.text') is-invalid @enderror"
                                        value="{{ old("options.$index.text", $option->opsi_tulisan) }}"
                                        placeholder="Masukkan teks untuk opsi {{ chr(65 + $index) }}"
                                        required>
                                    
                                    @error('options.'.$index.'.text')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="d-flex gap-2 mb-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>
                        Simpan Perubahan
                    </button>

                    <a href="{{ route('addTPU') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>
                        Batal
                    </a>
                </div>

            </form>

        </div>
    </div>
</div>



{{-- SCRIPTS --}}
<script>
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('preview-image');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.hidden = false;
        };

        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection