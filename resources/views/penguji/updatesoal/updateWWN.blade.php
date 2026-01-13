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
                        Edit Soal Wawancara
                    </h4>
                    <p class="text-muted mb-0">
                        <small>Perbarui informasi soal dan opsi jawaban dengan poin</small>
                    </p>
                </div>
                <a href="{{ route('addorb') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <form action="{{ route('WWN.store', $question->id) }}"
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
                                   placeholder="Contoh: Kepemimpinan"
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
                        <div class="form-group mb-0">
                            <label class="form-label fw-bold">
                                Gambar
                                <span class="text-muted">(opsional)</span>
                            </label>
                            
                            <div class="mb-2">
                                <img id="preview-image"
                                     src="{{ $question->image_path ? asset('storage/'.$question->image_path) : '' }}"
                                     class="img-thumbnail"
                                     style="max-width: 250px; max-height: 250px; object-fit: cover;"
                                     @if(!$question->image_path) hidden @endif>
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

                    </div>
                </div>

                {{-- CARD: OPSI JAWABAN --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-list-ol me-2"></i>
                            Opsi Jawaban (Point 5 = Paling Ideal)
                        </h6>
                    </div>
                    <div class="card-body">
                        
                        @foreach ($question->options->sortBy('label')->values() as $i => $opt)
                        <div class="card mb-3 border-start border-{{ $i == 0 ? 'primary' : ($i == 1 ? 'success' : ($i == 2 ? 'warning' : ($i == 3 ? 'info' : 'danger'))) }} border-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <strong class="text-dark">
                                    <i class="fas fa-{{ strtolower($opt->label) }}-circle me-1"></i>
                                    Opsi {{ $opt->label }}
                                </strong>
                                <span class="badge bg-{{ $i == 0 ? 'primary' : ($i == 1 ? 'success' : ($i == 2 ? 'warning' : ($i == 3 ? 'info' : 'danger'))) }}">
                                    {{ $opt->label }}
                                </span>
                            </div>
                            <div class="card-body">

                                <input type="hidden" name="options[{{ $i }}][id]" value="{{ $opt->id }}">
                                <input type="hidden" name="options[{{ $i }}][label]" value="{{ $opt->label }}">

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Teks Opsi</label>
                                    <input type="text"
                                           name="options[{{ $i }}][opsi]"
                                           class="form-control @error('options.'.$i.'.opsi') is-invalid @enderror"
                                           value="{{ old("options.$i.opsi", $opt->opsi_tulisan) }}"
                                           placeholder="Masukkan teks untuk opsi {{ $opt->label }}"
                                           required>
                                    @error('options.'.$i.'.opsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-0">
                                    <label class="form-label fw-bold">Point</label>
                                    <select name="options[{{ $i }}][point]"
                                            class="form-select @error('options.'.$i.'.point') is-invalid @enderror"
                                            required>
                                        @for($p=5;$p>=1;$p--)
                                            <option value="{{ $p }}"
                                                {{ old("options.$i.point", $opt->point) == $p ? 'selected' : '' }}>
                                                {{ $p }} {{ $p == 5 ? '(Paling Ideal)' : ($p == 1 ? '(Kurang Ideal)' : '') }}
                                            </option>
                                        @endfor
                                    </select>
                                    @error('options.'.$i.'.point')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

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

                    <a href="{{ route('addWWN') }}" class="btn btn-secondary btn-lg">
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
    const img = document.getElementById('preview-image');
    img.src = URL.createObjectURL(event.target.files[0]);
    img.hidden = false;
}
</script>
@endsection