@extends('layouts.main2')

@section('pageheads')
<div class="container">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-clipboard-check fs-4 text-primary"></i>
        <h4 class="mb-0">Tambah Nilai Praktik</h4>
    </div>
</div>
@endsection

@section('content')
<div class="container">
    <div class="card shadow-sm rounded-3 border-0">
        <div class="card-body p-4">
            <form method="POST" 
                  action="{{ route('nilaiprakstore', ['seleksiHash' => $seleksiHash, 'userHash' => $userHash]) }}" 
                  enctype="multipart/form-data" 
                  id="formNilaiPraktik">
                @csrf
                
                <!-- Info Section -->
                <div class="alert alert-info border-0 mb-4" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    Silakan masukkan nilai praktik sesuai dengan kriteria penilaian yang telah ditentukan.
                </div>

                <div class="row g-4">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <!-- Nilai Kerapian -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <i class="bi bi-star text-warning me-1"></i>
                                Nilai Kerapian
                                <span class="badge bg-secondary text-white ms-1">Max: 25</span>
                            </label>
                            <input type="number" 
                                   name="kerapian" 
                                   class="form-control form-control-lg @error('kerapian') is-invalid @enderror" 
                                   value="{{ old('kerapian') }}" 
                                   min="0" 
                                   max="25" 
                                   placeholder="0 - 25"
                                   required>
                            @error('kerapian')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">Penilaian tingkat kerapian dalam praktik</div>
                        </div>

                        <!-- Nilai Kecepatan -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <i class="bi bi-speedometer2 text-info me-1"></i>
                                Nilai Kecepatan
                                <span class="badge bg-secondary text-white ms-1">Max: 25</span>
                            </label>
                            <input type="number" 
                                   name="kecepatan" 
                                   class="form-control form-control-lg @error('kecepatan') is-invalid @enderror" 
                                   value="{{ old('kecepatan') }}" 
                                   min="0" 
                                   max="100" 
                                   placeholder="0 - 25"
                                   required>
                            @error('kecepatan')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">Penilaian kecepatan dalam menyelesaikan praktik</div>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <!-- Nilai Ketepatan -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <i class="bi bi-bullseye text-success me-1"></i>
                                Nilai Ketepatan
                                <span class="badge bg-secondary text-white ms-1">Max: 25</span>
                            </label>
                            <input type="number" 
                                   name="ketepatan" 
                                   class="form-control form-control-lg @error('ketepatan') is-invalid @enderror" 
                                   value="{{ old('ketepatan') }}" 
                                   min="0" 
                                   max="100" 
                                   placeholder="0 - 25"
                                   required>
                            @error('ketepatan')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">Penilaian ketepatan dalam praktik</div>
                        </div>

                        <!-- Nilai Efektivitas -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark mb-2">
                                <i class="bi bi-graph-up text-primary me-1"></i>
                                Nilai Efektivitas
                                <span class="badge bg-secondary text-white ms-1">Max: 25</span>
                            </label>
                            <input type="number" 
                                   name="efektifitas" 
                                   class="form-control form-control-lg @error('efektifitas') is-invalid @enderror" 
                                   value="{{ old('efektifitas') }}" 
                                   min="0" 
                                   max="25" 
                                   placeholder="0 - 25"
                                   required>
                            @error('efektifitas')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                            <div class="form-text">Penilaian efektivitas kerja dalam praktik</div>
                        </div>
                    </div>
                </div>

                
                <!-- Action Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <a href="{{ route('showpraktik', ['seleksiHash' => $seleksiHash, 'desaHash' => $desaHash]) }}" 
                       class="btn btn-outline-secondary btn-lg px-4">
                        <i class="bi bi-arrow-left-circle me-2"></i>Kembali
                    </a>

                    <button type="submit" class="btn btn-success btn-lg px-5 shadow-sm">
                        <i class="bi bi-check-circle me-2"></i>Simpan Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



@endsection