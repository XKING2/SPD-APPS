@extends('layouts.main1')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-edit"></i> Edit Ujian
            </h5>
        </div>

        <div class="card-body">

            {{-- ERROR --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('Adminexam.update', $exam->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- TYPE --}}

                <div class="form-group">
                    <label>Judul <span class="text-danger">*</span></label>
                    <input type="text"
                           name="judul"
                           class="form-control"
                           value="{{ old('judul', $exam->judul) }}"
                           required>
                </div>

                <div class="mb-3">
                   <label>Jenis Ujian <span class="text-danger">*</span></label>
                    <select name="type" class="form-control" required>
                        @foreach ($types as $key => $label)
                            <option value="{{ $key }}"
                                {{ $exam->type === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- SELEKSI --}}
                <div class="mb-3">
                    <label>Seleksi <span class="text-danger">*</span></label>
                    <select name="id_seleksi" class="form-control" required>
                        <option value="">-- Pilih Seleksi --</option>
                        @foreach ($seleksi as $s)
                            <option value="{{ $s->id }}"
                                {{ $exam->id_seleksi == $s->id ? 'selected' : '' }}>
                                {{ $s->judul }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- DURATION --}}
                <div class="mb-3">
                    <label>Durasi (menit) <span class="text-danger">*</span></label>
                    <input type="number"
                           name="duration"
                           id="duration"
                           class="form-control"
                           min="1"
                           value="{{ old('duration', $exam->duration) }}"
                           readonly>
                </div>

                {{-- START --}}
                <div class="mb-3">
                    <label>Waktu Mulai <span class="text-danger">*</span></label>
                    <input type="datetime-local"
                           name="start_at"
                           id="start_at"
                           class="form-control"
                           value="{{ old('start_at', \Carbon\Carbon::parse($exam->start_at)->format('Y-m-d\TH:i')) }}"
                           required>
                </div>

                {{-- END --}}
                <div class="mb-3">
                    <label>Waktu Selesai <span class="text-danger">*</span></label>
                    <input type="datetime-local"
                           name="end_at"
                           id="end_at"
                           class="form-control"
                           value="{{ old('end_at', \Carbon\Carbon::parse($exam->end_at)->format('Y-m-d\TH:i')) }}"
                           required>
                </div>

                {{-- ACTION --}}
                <div class="d-flex justify-content-between">
                    <a href="{{ route('adminexams') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
function calculateDuration() {
    const startInput = document.getElementById('start_at');
    const endInput   = document.getElementById('end_at');
    const duration   = document.getElementById('duration');

    if (!startInput.value || !endInput.value) {
        duration.value = '';
        return;
    }

    const start = new Date(startInput.value);
    const end   = new Date(endInput.value);

    // Validasi logika waktu
    if (end <= start) {
        duration.value = '';
        Swal.fire({
            icon: 'warning',
            title: 'Waktu tidak valid',
            text: 'Waktu selesai harus lebih besar dari waktu mulai'
        });
        endInput.value = '';
        return;
    }

    const diffMs = end - start;
    const diffMinutes = Math.floor(diffMs / 60000);

    duration.value = diffMinutes;
}

// Trigger saat user mengubah waktu
document.getElementById('start_at').addEventListener('change', calculateDuration);
document.getElementById('end_at').addEventListener('change', calculateDuration);
</script>
@endsection
