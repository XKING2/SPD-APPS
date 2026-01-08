@extends('layouts.main1')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0">
                <i class="fas fa-edit"></i> Edit Exam
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
                    <label>Judul</label>
                    <input type="text"
                           name="judul"
                           class="form-control"
                           value="{{ old('judul', $exam->judul) }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis Exam</label>
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
                    <label class="form-label">Seleksi</label>
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
                    <label class="form-label">Durasi (menit)</label>
                    <input type="number"
                           name="duration"
                           class="form-control"
                           min="1"
                           value="{{ old('duration', $exam->duration) }}"
                           required>
                </div>

                {{-- START --}}
                <div class="mb-3">
                    <label class="form-label">Waktu Mulai</label>
                    <input type="datetime-local"
                           name="start_at"
                           class="form-control"
                           value="{{ old('start_at', \Carbon\Carbon::parse($exam->start_at)->format('Y-m-d\TH:i')) }}"
                           required>
                </div>

                {{-- END --}}
                <div class="mb-3">
                    <label class="form-label">Waktu Selesai</label>
                    <input type="datetime-local"
                           name="end_at"
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
@endsection
