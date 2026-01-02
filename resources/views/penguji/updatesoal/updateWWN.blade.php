@extends('layouts.main2')

@section('content')
<div class="container">

<h4>Edit Soal Wawancara</h4>

<form action="{{ route('wawan.update', $question->id) }}"
      method="POST"
      enctype="multipart/form-data">
@csrf
@method('PUT')

{{-- SUBJECT --}}
<div class="form-group">
    <label>Subject</label>
    <input type="text" name="subject"
           class="form-control"
           value="{{ old('subject', $question->subject) }}" required>
</div>

{{-- PERTANYAAN --}}
<div class="form-group">
    <label>Pertanyaan</label>
    <textarea name="pertanyaan"
              class="form-control"
              rows="3" required>{{ old('pertanyaan', $question->pertanyaan) }}</textarea>
</div>

{{-- IMAGE --}}
<div class="form-group">
    <label>Gambar (opsional)</label><br>

    <img id="preview-image"
         src="{{ $question->image_path ? asset('storage/'.$question->image_path) : '' }}"
         class="img-thumbnail mb-2"
         style="max-width:200px;"
         @if(!$question->image_path) hidden @endif>

    <input type="file"
           name="image"
           class="form-control-file"
           accept="image/*"
           onchange="previewImage(event)">
</div>

<hr>

{{-- OPSI --}}
<h5>Opsi Jawaban (Point 5 = Paling Ideal)</h5>

@foreach ($question->options->sortBy('label')->values() as $i => $opt)
<div class="card mb-2">
    <div class="card-body">

        <strong>Opsi {{ $opt->label }}</strong>

        <input type="hidden" name="options[{{ $i }}][id]" value="{{ $opt->id }}">
        <input type="hidden" name="options[{{ $i }}][label]" value="{{ $opt->label }}">

        <input type="text"
               name="options[{{ $i }}][opsi]"
               class="form-control mt-2"
               value="{{ old("options.$i.opsi", $opt->opsi_tulisan) }}"
               required>

        <label class="mt-2">Point</label>
        <select name="options[{{ $i }}][point]"
                class="form-control"
                required>
            @for($p=5;$p>=1;$p--)
                <option value="{{ $p }}"
                    {{ old("options.$i.point", $opt->point) == $p ? 'selected' : '' }}>
                    {{ $p }}
                </option>
            @endfor
        </select>

    </div>
</div>
@endforeach

<button class="btn btn-primary mt-3">Simpan Perubahan</button>
<a href="{{ route('tambahwawan') }}" class="btn btn-secondary mt-3">Kembali</a>

</form>
</div>

<script>
function previewImage(event) {
    const img = document.getElementById('preview-image');
    img.src = URL.createObjectURL(event.target.files[0]);
    img.hidden = false;
}
</script>
@endsection
