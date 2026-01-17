@extends('layouts.main2')

@section('pageheads')
<style>
    :root {
        --gradient-word: linear-gradient(135deg, #2b5876 0%, #4e4376 100%);
        --gradient-excel: linear-gradient(135deg, #1e7e34 0%, #38b000 100%);
        --shadow-card: 0 10px 40px rgba(0,0,0,0.08);
        --shadow-hover: 0 15px 50px rgba(0,0,0,0.12);
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-card);
    }

    .page-header h4 {
        color: white;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-header i {
        font-size: 2rem;
        opacity: 0.9;
    }

    .main-card {
        border-radius: 24px;
        border: none;
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }

    .section-divider {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        margin: 0 -2rem 2rem;
        position: relative;
    }

    .section-divider::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .section-icon {
        width: 50px;
        height: 50px;
        background: rgba(255,255,255,0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .word-section .section-divider {
        background: var(--gradient-word);
    }

    .excel-section .section-divider {
        background: var(--gradient-excel);
    }

    .form-group-modern {
        margin-bottom: 2rem;
        animation: fadeInUp 0.5s ease backwards;
    }

    .form-group-modern:nth-child(1) { animation-delay: 0.1s; }
    .form-group-modern:nth-child(2) { animation-delay: 0.2s; }
    .form-group-modern:nth-child(3) { animation-delay: 0.3s; }
    .form-group-modern:nth-child(4) { animation-delay: 0.4s; }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .input-label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1rem;
    }

    .input-label i {
        font-size: 1.25rem;
    }

    .badge-max {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-left: auto;
    }

    .form-control-modern {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.875rem 1.25rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control-modern:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .form-control-modern:hover {
        border-color: #cbd5e0;
    }

    .form-text-modern {
        color: #718096;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .form-text-modern i {
        font-size: 0.75rem;
        opacity: 0.7;
    }

    .alert-info-modern {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        border: 2px solid rgba(102, 126, 234, 0.2);
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 2rem;
        color: #4c51bf;
    }

    .alert-info-modern i {
        font-size: 1.25rem;
        margin-right: 0.5rem;
    }

    .btn-submit {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        border: none;
        color: white;
        padding: 1rem 3rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(72, 187, 120, 0.4);
        background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
        color: white;
    }

    .btn-back {
        background: white;
        border: 2px solid #e2e8f0;
        color: #4a5568;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: #f7fafc;
        border-color: #cbd5e0;
        transform: translateX(-5px);
        color: #2d3748;
    }

    .action-buttons {
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 2px solid #e2e8f0;
    }

    .invalid-feedback {
        color: #e53e3e;
        font-weight: 500;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .is-invalid {
        border-color: #fc8181 !important;
        background-color: #fff5f5;
    }

    .is-invalid:focus {
        border-color: #e53e3e !important;
        box-shadow: 0 0 0 4px rgba(229, 62, 62, 0.1) !important;
    }

    @media (max-width: 768px) {
        .section-divider {
            margin: 0 -1rem 2rem;
            padding: 1.25rem;
        }

        .section-title {
            font-size: 1.25rem;
        }

        .section-icon {
            width: 40px;
            height: 40px;
            font-size: 1.25rem;
        }

        .btn-submit, .btn-back {
            width: 100%;
            margin-bottom: 1rem;
        }

        .action-buttons {
            flex-direction: column-reverse;
        }
    }
</style>

<div class="container">
    <div class="page-header">
        <h4>
            <i class="bi bi-clipboard-check"></i>
            Tambah Nilai Praktik
        </h4>
    </div>
</div>
@endsection

@section('content')
<div class="container">
    <div class="card main-card">
        <div class="card-body p-4 p-md-5">
            <form method="POST" 
                  action="{{ route('nilaiprakstore', ['seleksiHash' => $seleksiHash, 'userHash' => $userHash]) }}" 
                  enctype="multipart/form-data" 
                  id="formNilaiPraktik">
                @csrf
                
                <!-- Info Alert -->
                <div class="alert-info-modern">
                    <i class="bi bi-info-circle"></i>
                    <strong>Petunjuk:</strong> Silakan masukkan nilai praktik untuk aspek Word dan Excel sesuai dengan kriteria penilaian yang telah ditentukan.
                </div>

                <!-- SECTION WORD -->
                <div class="word-section">
                    <div class="section-divider">
                        <div class="section-title">
                            <div class="section-icon">
                                <i class="bi bi-file-word text-primary"></i>
                            </div>
                            <span>Penilaian Microsoft Word</span>
                        </div>
                    </div>

                    <div class="row g-4">
                        <!-- Kop Surat -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="input-label">
                                    <i class="bi bi-card-heading text-primary"></i>
                                    Kop Surat
                                    <span class="badge-max">Max: 10</span>
                                </label>
                                <input type="number" 
                                       name="kop_surat" 
                                       class="form-control form-control-modern @error('kop_surat') is-invalid @enderror" 
                                       value="{{ old('kop_surat') }}" 
                                       min="0" 
                                       max="10" 
                                       placeholder="Masukkan nilai (0-10)"
                                       required>
                                @error('kop_surat')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text-modern">
                                    <i class="bi bi-info-circle-fill"></i>
                                    Penilaian pembuatan kop surat
                                </div>
                            </div>
                        </div>

                        <!-- Format Dokumen -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="input-label">
                                    <i class="bi bi-file-earmark-text text-success"></i>
                                    Format Dokumen
                                    <span class="badge-max">Max: 10</span>
                                </label>
                                <input type="number" 
                                       name="format_dokumen" 
                                       class="form-control form-control-modern @error('format_dokumen') is-invalid @enderror" 
                                       value="{{ old('format_dokumen') }}" 
                                       min="0" 
                                       max="10" 
                                       placeholder="Masukkan nilai (0-10)"
                                       required>
                                @error('format_dokumen')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text-modern">
                                    <i class="bi bi-info-circle-fill"></i>
                                    Penilaian format dan struktur dokumen
                                </div>
                            </div>
                        </div>

                        <!-- Layout TTD -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="input-label">
                                    <i class="bi bi-pen text-warning"></i>
                                    Layout Tanda Tangan
                                    <span class="badge-max">Max: 10</span>
                                </label>
                                <input type="number" 
                                       name="layout_ttd" 
                                       class="form-control form-control-modern @error('layout_ttd') is-invalid @enderror" 
                                       value="{{ old('layout_ttd') }}" 
                                       min="0" 
                                       max="10" 
                                       placeholder="Masukkan nilai (0-10)"
                                       required>
                                @error('layout_ttd')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text-modern">
                                    <i class="bi bi-info-circle-fill"></i>
                                    Penilaian tata letak tanda tangan
                                </div>
                            </div>
                        </div>

                        <!-- Manajemen File & Waktu -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="input-label">
                                    <i class="bi bi-clock-history text-info"></i>
                                    Manajemen File & Waktu
                                    <span class="badge-max">Max: 10</span>
                                </label>
                                <input type="number" 
                                       name="manajemen_file_waktu" 
                                       class="form-control form-control-modern @error('manajemen_file_waktu') is-invalid @enderror" 
                                       value="{{ old('manajemen_file_waktu') }}" 
                                       min="0" 
                                       max="10" 
                                       placeholder="Masukkan nilai (0-10)"
                                       required>
                                @error('manajemen_file_waktu')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text-modern">
                                    <i class="bi bi-info-circle-fill"></i>
                                    Penilaian manajemen file dan ketepatan waktu
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION EXCEL -->
                <div class="excel-section mt-5">
                    <div class="section-divider">
                        <div class="section-title">
                            <div class="section-icon">
                                <i class="bi bi-file-earmark-excel"></i>
                            </div>
                            <span>Penilaian Microsoft Excel</span>
                        </div>
                    </div>

                    <div class="row g-4">
                        <!-- Format Visualisasi Tabel -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="input-label">
                                    <i class="bi bi-table text-primary"></i>
                                    Format Visualisasi Tabel
                                    <span class="badge-max">Max: 10</span>
                                </label>
                                <input type="number" 
                                       name="format_visualisasi_tabel" 
                                       class="form-control form-control-modern @error('format_visualisasi_tabel') is-invalid @enderror" 
                                       value="{{ old('format_visualisasi_tabel') }}" 
                                       min="0" 
                                       max="10" 
                                       placeholder="Masukkan nilai (0-10)"
                                       required>
                                @error('format_visualisasi_tabel')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text-modern">
                                    <i class="bi bi-info-circle-fill"></i>
                                    Penilaian format dan visualisasi tabel
                                </div>
                            </div>
                        </div>

                        <!-- Fungsi Logika -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="input-label">
                                    <i class="bi bi-diagram-3 text-success"></i>
                                    Fungsi Logika
                                    <span class="badge-max">Max: 10</span>
                                </label>
                                <input type="number" 
                                       name="fungsi_logika" 
                                       class="form-control form-control-modern @error('fungsi_logika') is-invalid @enderror" 
                                       value="{{ old('fungsi_logika') }}" 
                                       min="0" 
                                       max="10" 
                                       placeholder="Masukkan nilai (0-10)"
                                       required>
                                @error('fungsi_logika')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text-modern">
                                    <i class="bi bi-info-circle-fill"></i>
                                    Penilaian penggunaan fungsi logika (IF, AND, OR)
                                </div>
                            </div>
                        </div>

                        <!-- Fungsi Lanjutan -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="input-label">
                                    <i class="bi bi-lightning-charge text-warning"></i>
                                    Fungsi Lanjutan
                                    <span class="badge-max">Max: 15</span>
                                </label>
                                <input type="number" 
                                       name="fungsi_lanjutan" 
                                       class="form-control form-control-modern @error('fungsi_lanjutan') is-invalid @enderror" 
                                       value="{{ old('fungsi_lanjutan') }}" 
                                       min="0" 
                                       max="15" 
                                       placeholder="Masukkan nilai (0-15)"
                                       required>
                                @error('fungsi_lanjutan')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text-modern">
                                    <i class="bi bi-info-circle-fill"></i>
                                    Penilaian fungsi lanjutan (VLOOKUP, HLOOKUP, dll)
                                </div>
                            </div>
                        </div>

                        <!-- Format Data -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="input-label">
                                    <i class="bi bi-bar-chart text-info"></i>
                                    Format Data
                                    <span class="badge-max">Max: 10</span>
                                </label>
                                <input type="number" 
                                       name="format_data" 
                                       class="form-control form-control-modern @error('format_data') is-invalid @enderror" 
                                       value="{{ old('format_data') }}" 
                                       min="0" 
                                       max="10" 
                                       placeholder="Masukkan nilai (0-10)"
                                       required>
                                @error('format_data')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text-modern">
                                    <i class="bi bi-info-circle-fill"></i>
                                    Penilaian format dan pengolahan data
                                </div>
                            </div>
                        </div>

                        <!-- Output TTD -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="input-label">
                                    <i class="bi bi-printer text-danger"></i>
                                    Output & Tanda Tangan
                                    <span class="badge-max">Max: 5</span>
                                </label>
                                <input type="number" 
                                       name="output_ttd" 
                                       class="form-control form-control-modern @error('output_ttd') is-invalid @enderror" 
                                       value="{{ old('output_ttd') }}" 
                                       min="0" 
                                       max="5" 
                                       placeholder="Masukkan nilai (0-5)"
                                       required>
                                @error('output_ttd')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text-modern">
                                    <i class="bi bi-info-circle-fill"></i>
                                    Penilaian output dan penempatan tanda tangan
                                </div>
                            </div>
                        </div>

                        <!-- Manajemen File Excel -->
                        <div class="col-md-6">
                            <div class="form-group-modern">
                                <label class="input-label">
                                    <i class="bi bi-folder-check text-primary"></i>
                                    Manajemen File Excel
                                    <span class="badge-max">Max: 10</span>
                                </label>
                                <input type="number" 
                                       name="manajemen_file_excel" 
                                       class="form-control form-control-modern @error('manajemen_file_excel') is-invalid @enderror" 
                                       value="{{ old('manajemen_file_excel') }}" 
                                       min="0" 
                                       max="10" 
                                       placeholder="Masukkan nilai (0-10)"
                                       required>
                                @error('manajemen_file_excel')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle"></i>{{ $message }}
                                    </div>
                                @enderror
                                <div class="form-text-modern">
                                    <i class="bi bi-info-circle-fill"></i>
                                    Penilaian manajemen dan organisasi file
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons d-flex justify-content-between align-items-center">
                    <a href="{{ route('showpraktik', ['seleksiHash' => $seleksiHash, 'desaHash' => $desaHash]) }}" 
                       class="btn btn-back">
                        <i class="bi bi-arrow-left-circle me-2"></i>Kembali
                    </a>

                    <button type="submit" class="btn btn-submit">
                        <i class="bi bi-check-circle me-2"></i>Simpan Nilai
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection