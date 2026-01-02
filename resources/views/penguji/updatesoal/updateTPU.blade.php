@extends('layouts.main2')

@section('content')
<div class="container">

    <h4 class="mb-4">Edit Soal</h4>

    <form action="{{ route('TPU.update', $question->id) }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf
        @method('PUT')

        {{-- ================= SUBJECT ================= --}}
        <div class="form-group">
            <label>Subject</label>
            <input type="text"
                   name="subject"
                   class="form-control"
                   value="{{ old('subject', $question->subject) }}"
                   required>
        </div>

        {{-- ================= PERTANYAAN ================= --}}
        <div class="form-group">
            <label>Pertanyaan</label>
            <textarea name="pertanyaan"
                      class="form-control"
                      rows="3"
                      required>{{ old('pertanyaan', $question->pertanyaan) }}</textarea>
        </div>

         {{-- ================= IMAGE ================= --}}
        <div class="form-group">
            <label>Gambar Soal</label><br>

            <img id="preview-image"
                src="{{ $question->image_name ? asset('storage/'.$question->image_name) : '' }}"
                class="img-thumbnail mb-2"
                style="max-width: 200px;"
                @if(!$question->image_name) hidden @endif>

            <input type="file"
                name="image"
                class="form-control-file"
                accept="image/*"
                onchange="previewImage(event)">
        </div>

        {{-- ================= JAWABAN BENAR ================= --}}
        <div class="form-group">
            <label>Jawaban Benar</label>
            <select name="jawaban_benar" class="form-control" required>
                @foreach (['A','B','C','D'] as $opt)
                    <option value="{{ $opt }}"
                        {{ old('jawaban_benar', $question->jawaban_benar) === $opt ? 'selected' : '' }}>
                        {{ $opt }}
                    </option>
                @endforeach
            </select>
        </div>

        <hr>

        {{-- ================= OPSI JAWABAN ================= --}}
        <h5 class="mb-3">Opsi Jawaban</h5>

        @foreach ($question->options as $index => $option)
            <div class="card mb-2">
                <div class="card-body">

                    <strong>Opsi {{ $option->label }}</strong>

                    {{-- ID OPSI (WAJIB UNTUK UPDATE GRANULAR) --}}
                    <input type="hidden"
                           name="options[{{ $index }}][id]"
                           value="{{ $option->id }}">

                    <input type="text"
                           name="options[{{ $index }}][text]"
                           class="form-control mt-2"
                           value="{{ old("options.$index.text", $option->opsi_tulisan) }}"
                           required>
                </div>
            </div>
        @endforeach

        {{-- ================= SUBMIT ================= --}}
        <button type="submit" class="btn btn-primary mt-3">
            Simpan Perubahan
        </button>

        <a href="{{ route('tambahtpu') }}" class="btn btn-secondary mt-3">
            Kembali
        </a>

    </form>
</div>

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
