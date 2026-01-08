@extends('layouts.main')

@section('content')


<div class="container-fluid">
    <!-- Welcome Card -->
    <div class="welcome-card">
        <h1>👋 Selamat Datang, {{ Auth::user()->name ?? 'User' }}!</h1>
        <p>Semangat untuk mengikuti seleksi Perangkat Desa. Pastikan semua tahapan sudah diselesaikan dengan baik.</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon blue">📝</div>
            <h3>Status Biodata</h3>
            <div class="value">
                @if(auth()->user()->biodata)
                    @if(auth()->user()->biodata->status === 'valid')
                        Tervalidasi
                    @elseif(auth()->user()->biodata->status === 'pending')
                        Pending
                    @else
                        Belum Lengkap
                    @endif
                @else
                    Belum Diisi
                @endif
            </div>
        </div>
        
        <div class="stat-card green">
            <div class="stat-icon green">✅</div>
            <h3>Ujian Tersedia</h3>
            <div class="value">
                {{ ($examTPU ? 1 : 0) + ($examWawancara ? 1 : 0) }}
            </div>
        </div>
        
        <div class="stat-card orange">
            <div class="stat-icon orange">⏱️</div>
            <h3>Status TPU</h3>
            <div class="value" style="font-size: 18px;">
                @if($examTPU)
                    {{ ucfirst($examTPU->status) }}
                @else
                    Tidak Tersedia
                @endif
            </div>
        </div>
        
        <div class="stat-card red">
            <div class="stat-icon red">🎯</div>
            <h3>Status Wawancara</h3>
            <div class="value" style="font-size: 18px;">
                @if($examWawancara)
                    {{ ucfirst($examWawancara->status) }}
                @else
                    Belum Tersedia
                @endif
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Progress Timeline -->
        <div class="card">
            <div class="card-header">
                <h2>📋 Progress Seleksi Anda</h2>
                <span class="badge warning">Dalam Progress</span>
            </div>
            
            <div class="timeline">
                <div class="timeline-item completed">
                    <h4>✓ Registrasi Akun</h4>
                    <p>Selesai pada {{ Auth::user()->created_at->format('d F Y') }}</p>
                </div>
                
                <div class="timeline-item {{ auth()->user()->biodata && auth()->user()->biodata->status === 'valid' ? 'completed' : 'pending' }}">
                    <h4>
                        @if(auth()->user()->biodata && auth()->user()->biodata->status === 'valid')
                            ✓ Pengisian Biodata
                        @else
                            ⏳ Pengisian Biodata
                        @endif
                    </h4>
                    <p>
                        @if(auth()->user()->biodata && auth()->user()->biodata->status === 'valid')
                            Tervalidasi pada {{ auth()->user()->biodata->updated_at->format('d F Y') }}
                        @elseif(auth()->user()->biodata)
                            Menunggu validasi
                        @else
                            Belum diisi - <a href="{{ route('showbiodata') }}" class="text-primary">Isi sekarang</a>
                        @endif
                    </p>
                </div>
                
                <div class="timeline-item {{ auth()->user()->biodata && auth()->user()->biodata->status === 'valid' ? 'pending' : '' }}">
                    <h4>
                        @if(auth()->user()->biodata && auth()->user()->biodata->status === 'valid')
                            ⏳ Ujian Seleksi
                        @else
                            ○ Ujian Seleksi
                        @endif
                    </h4>
                    <p>
                        @if(auth()->user()->biodata && auth()->user()->biodata->status === 'valid')
                            @if($examTPU || $examWawancara)
                                Ujian tersedia - Lihat detail di samping
                            @else
                                Ujian belum tersedia
                            @endif
                        @else
                            Lengkapi biodata terlebih dahulu
                        @endif
                    </p>
                </div>
                
                <div class="timeline-item">
                    <h4>○ Pengumuman Hasil</h4>
                    <p>Menunggu - Akan diumumkan setelah semua ujian selesai</p>
                </div>
            </div>

            <div class="tips-card">
                <h4>💡 Tips Mengerjakan Ujian:</h4>
                <ul>
                    <li>Pastikan koneksi internet stabil</li>
                    <li>Kerjakan di tempat yang tenang</li>
                    <li>Baca soal dengan teliti sebelum menjawab</li>
                    <li>Kelola waktu dengan baik</li>
                </ul>
            </div>
        </div>

        <!-- Exam Selection Cards (Right Column) -->
        <div>
            <div class="exam-selection-container">
                {{-- Tab Navigation --}}
                <div class="exam-tabs">
                    <button class="exam-tab active" data-exam-type="TPU" onclick="switchExamTab('TPU')">
                        <i class="fas fa-book-open"></i>
                        <span>TPU</span>
                    </button>
                    <button class="exam-tab" data-exam-type="Wawancara" onclick="switchExamTab('Wawancara')">
                        <i class="fas fa-comments"></i>
                        <span>Wawancara</span>
                    </button>
                </div>

                {{-- TPU Card --}}
                @if($examTPU)
                <div class="exam-card" id="card-TPU" style="display: block;">
                    <div class="card">
                        <div class="card-header">
                            <h2>
                                <i class="fas fa-book-open"></i>
                                {{ $examTPU->judul }}
                            </h2>
                            <span class="exam-status status-{{ $examTPU->status }}">
                                {{ ucfirst($examTPU->status) }}
                            </span>
                        </div>
                        
                        <ul class="info-list">
                            <li>
                                <span class="label">
                                    <i class="fas fa-tag"></i>
                                    Jenis Ujian
                                </span>
                                <span class="value">TPU</span>
                            </li>
                            <li>
                                <span class="label">
                                    <i class="fas fa-list-ol"></i>
                                    Jumlah Soal
                                </span>
                                <span class="value">40 Soal</span>
                            </li>
                            <li>
                                <span class="label">
                                    <i class="fas fa-clock"></i>
                                    Durasi
                                </span>
                                <span class="value">{{ $examTPU->duration }} Menit</span>
                            </li>
                            <li>
                                <span class="label">
                                    <i class="fas fa-calendar-alt"></i>
                                    Waktu Mulai
                                </span>
                                <span class="value">{{ \Carbon\Carbon::parse($examTPU->start_at)->format('d M Y, H:i') }}</span>
                            </li>
                            <li>
                                <span class="label">
                                    <i class="fas fa-calendar-times"></i>
                                    Batas Waktu
                                </span>
                                <span class="value">{{ \Carbon\Carbon::parse($examTPU->end_at)->format('d M Y, H:i') }}</span>
                            </li>
                            @if($examTPU->enrollment_key)
                            <li>
                                <span class="label">
                                    <i class="fas fa-key"></i>
                                    Kode Akses
                                </span>
                                <span class="value enrollment-key">{{ $examTPU->enrollment_key }}</span>
                            </li>
                            @endif
                        </ul>

                        @php
                            $now = \Carbon\Carbon::now();
                            $isOpen = $examTPU->status === 'active' && 
                                      $now->between($examTPU->start_at, $examTPU->end_at);
                            $isBiodataValid = auth()->user()->biodata && 
                                              auth()->user()->biodata->status === 'valid';
                        @endphp

                        @if($isBiodataValid)
                            @if($isOpen)
                                <a href="{{ route('showmainujian') }}"
                                class="action-button action-button-primary">
                                    <i class="fas fa-play"></i>
                                    Mulai Ujian TPU
                                </a>
                            @else
                                <button class="action-button action-button-disabled" disabled>
                                    <i class="fas fa-clock"></i>
                                    Ujian Belum Dibuka / Sudah Ditutup
                                </button>
                            @endif
                        @else
                            <button class="action-button action-button-warning" disabled>
                                <i class="fas fa-exclamation-triangle"></i>
                                Lengkapi Biodata Terlebih Dahulu
                            </button>
                        @endif
                    </div>
                </div>
                @else
                <div class="exam-card" id="card-TPU" style="display: block;">
                    <div class="card card-empty">
                        <i class="fas fa-inbox empty-icon"></i>
                        <h3>Tidak Ada Ujian TPU</h3>
                        <p>Ujian Tes Potensi Umum belum tersedia saat ini</p>
                    </div>
                </div>
                @endif

                {{-- Wawancara Card --}}
                @if($examWawancara)
                <div class="exam-card" id="card-Wawancara" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h2>
                                <i class="fas fa-comments"></i>
                                {{ $examWawancara->judul }}
                            </h2>
                            <span class="exam-status status-{{ $examWawancara->status }}">
                                {{ ucfirst($examWawancara->status) }}
                            </span>
                        </div>
                        
                        <ul class="info-list">
                            <li>
                                <span class="label">
                                    <i class="fas fa-tag"></i>
                                    Jenis Ujian
                                </span>
                                <span class="value">{{ $examWawancara->type }}</span>
                            </li>
                            <li>
                                <span class="label">
                                    <i class="fas fa-list-ol"></i>
                                    Jumlah Soal
                                </span>
                                <span class="value">40 Soal</span>
                            </li>
                            <li>
                                <span class="label">
                                    <i class="fas fa-clock"></i>
                                    Durasi
                                </span>
                                <span class="value">{{ $examWawancara->duration }} Menit</span>
                            </li>
                            <li>
                                <span class="label">
                                    <i class="fas fa-calendar-alt"></i>
                                    Waktu Mulai
                                </span>
                                <span class="value">{{ \Carbon\Carbon::parse($examWawancara->start_at)->format('d M Y, H:i') }}</span>
                            </li>
                            <li>
                                <span class="label">
                                    <i class="fas fa-calendar-times"></i>
                                    Batas Waktu
                                </span>
                                <span class="value">{{ \Carbon\Carbon::parse($examWawancara->end_at)->format('d M Y, H:i') }}</span>
                            </li>
                            @if($examWawancara->enrollment_key)
                            <li>
                                <span class="label">
                                    <i class="fas fa-key"></i>
                                    Kode Akses
                                </span>
                                <span class="value enrollment-key">{{ $examWawancara->enrollment_key }}</span>
                            </li>
                            @endif
                        </ul>

                        @php
                            $now = \Carbon\Carbon::now();
                            $isOpen = $examWawancara->status === 'active' && 
                                      $now->between($examWawancara->start_at, $examWawancara->end_at);
                            $isBiodataValid = auth()->user()->biodata && 
                                              auth()->user()->biodata->status === 'valid';
                        @endphp

                        @if($isBiodataValid)
                            @if($isOpen)
                                <a href="{{ route('showmainujian') }}"
                                class="action-button action-button-primary">
                                    <i class="fas fa-play"></i>
                                    Mulai Ujian {{ $examWawancara->type }}
                                </a>
                            @else
                                <button class="action-button action-button-disabled" disabled>
                                    <i class="fas fa-clock"></i>
                                    Ujian Belum Dibuka / Sudah Ditutup
                                </button>
                            @endif
                        @else
                            <button class="action-button action-button-warning" disabled>
                                <i class="fas fa-exclamation-triangle"></i>
                                Lengkapi Biodata Terlebih Dahulu
                            </button>
                        @endif
                    </div>
                </div>
                @else
                <div class="exam-card" id="card-Wawancara" style="display: none;">
                    <div class="card card-empty">
                        <i class="fas fa-inbox empty-icon"></i>
                        <h3>Tidak Ada Ujian Wawancara</h3>
                        <p>Ujian Wawancara belum tersedia saat ini</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function switchExamTab(type) {
    // Update tab buttons
    document.querySelectorAll('.exam-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelector(`.exam-tab[data-exam-type="${type}"]`).classList.add('active');
    
    // Show/hide cards
    document.querySelectorAll('.exam-card').forEach(card => {
        card.style.display = 'none';
    });
    document.getElementById(`card-${type}`).style.display = 'block';
}
</script>

@endsection