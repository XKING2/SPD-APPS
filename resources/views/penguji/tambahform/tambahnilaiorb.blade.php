@extends('layouts.main2')

@section('pageheads')
<div class="container">
    <h4 class="mb-1">Tambah Nilai Observasi</h4>
</div>
@endsection

@section('content')
<div class="container">
    <div class="card shadow-sm rounded-3">
        <div class="card-body">

           <form method="POST" action="{{ route('nilaiorbstore', ['seleksiHash' => $seleksiHash,'userHash' => $userHash]) }}"enctype="multipart/form-data"class="modal-content">
                @csrf
                <div class="row">
                    <!-- Kiri -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nilai Kerapian</label>
                            <input type="number" name="kerapian" class="form-control @error('kerapian') is-invalid @enderror" value="{{ old('kerapian') }}" min="0" max="25" required>
                            @error('kerapian')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nilai Kecepatan</label>
                            <input type="number" name="kecepatan" class="form-control @error('kecepatan') is-invalid @enderror" value="{{ old('kecepatan') }}" min="0" max="100" required>
                            @error('kecepatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Kanan -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nilai Ketepatan</label>
                            <input type="number" name="ketepatan" class="form-control @error('ketepatan') is-invalid @enderror" value="{{ old('ketepatan') }}" min="0" max="100" required>
                            @error('ketepatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Efektivitas</label>
                            <input type="number" name="efektifitas"  class="form-control @error('efektifitas') is-invalid @enderror" value="{{ old('efektifitas') }}"  min="0" max="25"  required>
                            @error('efektifitas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Tombol -->
                <div class="d-flex justify-content-end gap-3">
                    <a href="{{ route('showobservasi', ['seleksiHash' => $seleksiHash,'desaHash' => $desaHash]) }}" class="btn btn-secondary px-4">
                        <i class="bi bi-arrow-left-circle"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
