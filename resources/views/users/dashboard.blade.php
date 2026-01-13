@extends('layouts.main')

@section('content')
<style>
    .welcome-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 30px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    .welcome-card h1 {
        font-size: 28px;
        margin-bottom: 10px;
        font-weight: 700;
    }
    .welcome-card p {
        opacity: 0.95;
        font-size: 16px;
        margin: 0;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-left: 4px solid #4e73df;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.12);
    }
    .stat-card.green { border-left-color: #1cc88a; }
    .stat-card.orange { border-left-color: #f6c23e; }
    .stat-card.red { border-left-color: #e74a3b; }
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
    }
    .stat-icon.blue { background: #e3f2fd; color: #1976d2; }
    .stat-icon.green { background: #e8f5e9; color: #388e3c; }
    .stat-icon.orange { background: #fff3e0; color: #f57c00; }
    .stat-icon.red { background: #ffebee; color: #d32f2f; }
    .stat-icon.cyan { background: #e0f7fa; color: #00acc1; }
    .stat-card h3 {
        font-size: 14px;
        color: #858796;
        margin-bottom: 8px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .stat-card .value {
        font-size: 32px;
        font-weight: 700;
        color: #5a5c69;
        line-height: 1;
    }
    .stat-card small {
        display: block;
        margin-top: 8px;
        font-size: 12px;
        color: #858796;
    }
    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }
    .card {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f1f1;
    }
    .card-header h2 {
        font-size: 18px;
        color: #5a5c69;
        font-weight: 700;
        margin: 0;
    }
    .badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge.success { background: #d4edda; color: #155724; }
    .badge.warning { background: #fff3cd; color: #856404; }
    .badge.danger { background: #f8d7da; color: #721c24; }
    .badge.info { background: #d1ecf1; color: #0c5460; }
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e3e6f0;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 25px;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -24px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: white;
        border: 3px solid #858796;
    }
    .timeline-item.completed::before {
        background: #1cc88a;
        border-color: #1cc88a;
    }
    .timeline-item.pending::before {
        background: #f6c23e;
        border-color: #f6c23e;
    }
    .timeline-item h4 {
        font-size: 15px;
        color: #5a5c69;
        margin-bottom: 5px;
        font-weight: 600;
    }
    .timeline-item p {
        font-size: 13px;
        color: #858796;
        margin: 0;
    }
    .tips-card {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 15px;
        border-radius: 8px;
        margin-top: 20px;
    }
    .tips-card h4 {
        color: #856404;
        font-size: 14px;
        margin-bottom: 10px;
        font-weight: 600;
    }
    .tips-card ul {
        margin: 0;
        padding-left: 20px;
    }
    .tips-card li {
        color: #856404;
        font-size: 13px;
        line-height: 1.8;
    }

    /* Exam Selection Styles */
    .exam-selection-container {
        margin-bottom: 30px;
    }
    .exam-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        background: white;
        padding: 10px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        flex-wrap: wrap;
    }
    .exam-tab {
        flex: 1;
        min-width: 100px;
        padding: 12px 15px;
        background: #f8f9fa;
        border: 2px solid transparent;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #666;
    }
    .exam-tab i {
        font-size: 16px;
    }
    .exam-tab:hover {
        background: #e9ecef;
        transform: translateY(-2px);
    }
    .exam-tab.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }
    .exam-card {
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .exam-status {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .status-active {
        background: #d4edda;
        color: #155724;
    }
    .status-inactive {
        background: #f8d7da;
        color: #721c24;
    }
    .status-draft {
        background: #fff3cd;
        color: #856404;
    }
    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .info-list li {
        padding: 12px 0;
        border-bottom: 1px solid #f1f1f1;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .info-list li:last-child {
        border-bottom: none;
    }
    .info-list .label {
        color: #858796;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .info-list .label i {
        color: #667eea;
        width: 20px;
    }
    .info-list .value {
        color: #5a5c69;
        font-weight: 600;
        font-size: 14px;
    }
    .action-button {
        display: block;
        width: 100%;
        padding: 16px;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin-top: 20px;
        text-decoration: none;
    }
    .action-button-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .action-button-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        color: white;
    }
    .action-button-disabled {
        background: #e9ecef;
        color: #6c757d;
        cursor: not-allowed;
    }
    .action-button-warning {
        background: #fff3cd;
        color: #856404;
        cursor: not-allowed;
    }
    .card-empty {
        text-align: center;
        padding: 60px 30px;
        color: #999;
    }
    .empty-icon {
        font-size: 60px;
        color: #ddd;
        margin-bottom: 20px;
    }
    .card-empty h3 {
        color: #666;
        margin-bottom: 10px;
    }
    .card-empty p {
        color: #999;
        font-size: 14px;
    }
    
    @media (max-width: 768px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
        .welcome-card h1 {
            font-size: 22px;
        }
        .welcome-card p {
            font-size: 14px;
        }
        .exam-tabs {
            flex-direction: column;
        }
        .exam-tab {
            width: 100%;
        }
        .info-list li {
            flex-direction: column;
            gap: 8px;
            align-items: flex-start;
        }
    }
</style>

<div class="container-fluid">
    <!-- Welcome Card -->
    <div class="welcome-card">
        <h1>👋 Selamat Datang, {{ Auth::user()->name ?? 'User' }}!</h1>
        <p>Semangat untuk mengikuti seleksi Perangkat Desa. Pastikan semua tahapan sudah diselesaikan dengan baik.</p>
    </div>

    <!-- Stats Grid Atas (4 Cards) -->
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
                {{ ($examTPU ? 1 : 0) + ($examWawancara ? 1 : 0) + ($examORB ? 1 : 0) }}
            </div>
            <small>Dari 3 jenis ujian</small>
        </div>
        
        <div class="stat-card orange">
            <div class="stat-icon orange">📊</div>
            <h3>Ujian Dikerjakan</h3>
            <div class="value">
                {{ ($ExamResultTPU && $ExamResultTPU->is_submitted ? 1 : 0) + 
                   ($ExamResultWWN && $ExamResultWWN->is_submitted ? 1 : 0) + 
                   ($ExamResultORB && $ExamResultORB->is_submitted ? 1 : 0) }}
            </div>
            <small>Dari {{ ($examTPU ? 1 : 0) + ($examWawancara ? 1 : 0) + ($examORB ? 1 : 0) }} ujian tersedia</small>
        </div>
        
        <div class="stat-card red">
            <div class="stat-icon red">⏱️</div>
            <h3>Ujian Tersisa</h3>
            <div class="value">
                {{ (($examTPU && (!$ExamResultTPU || !$ExamResultTPU->is_submitted)) ? 1 : 0) + 
                   (($examWawancara && (!$ExamResultWWN || !$ExamResultWWN->is_submitted)) ? 1 : 0) + 
                   (($examORB && (!$ExamResultORB || !$ExamResultORB->is_submitted)) ? 1 : 0) }}
            </div>
            <small>Belum dikerjakan</small>
        </div>
    </div>

    <!-- Nilai Cards Grid (4 Cards Bawah) -->
    <div class="stats-grid">
        <div class="stat-card" style="border-left-color: #4e73df;">
            <div class="stat-icon blue">📖</div>
            <h3>Nilai TPU</h3>
            <div class="value" style="font-size: 28px;">
                @if($ExamResultTPU && $ExamResultTPU->is_submitted)
                    {{ number_format($ExamResultTPU->score ?? 0, 0) }}
                @else
                    -
                @endif
            </div>
            <small>
                @if($ExamResultTPU && $ExamResultTPU->is_submitted)
                    Sudah dikerjakan
                @else
                    Belum dikerjakan
                @endif
            </small>
        </div>
        
        <div class="stat-card" style="border-left-color: #1cc88a;">
            <div class="stat-icon green">💬</div>
            <h3>Nilai Wawancara</h3>
            <div class="value" style="font-size: 28px;">
                @if($ExamResultWWN && $ExamResultWWN->is_submitted)
                    {{ number_format($ExamResultWWN->score ?? 0, 0) }}
                @else
                    -
                @endif
            </div>
            <small>
                @if($ExamResultWWN && $ExamResultWWN->is_submitted)
                    Sudah dikerjakan
                @else
                    Belum dikerjakan
                @endif
            </small>
        </div>
        
        <div class="stat-card" style="border-left-color: #36b9cc;">
            <div class="stat-icon cyan">👁️</div>
            <h3>Nilai Observasi</h3>
            <div class="value" style="font-size: 28px;">
                @if($ExamResultORB && $ExamResultORB->is_submitted)
                    {{ number_format($ExamResultORB->score ?? 0, 0) }}
                @else
                    -
                @endif
            </div>
            <small>
                @if($ExamResultORB && $ExamResultORB->is_submitted)
                    Sudah dikerjakan
                @else
                    Belum dikerjakan
                @endif
            </small>
        </div>

        <div class="stat-card" style="border-left-color: #f6c23e;">
            <div class="stat-icon orange">🏆</div>
            <h3>Rata-rata Nilai</h3>
            <div class="value" style="font-size: 28px;">
                @php
                    $totalScore = 0;
                    $count = 0;
                    if($ExamResultTPU && $ExamResultTPU->is_submitted) {
                        $totalScore += $ExamResultTPU->score ?? 0;
                        $count++;
                    }
                    if($ExamResultWWN && $ExamResultWWN->is_submitted) {
                        $totalScore += $ExamResultWWN->score ?? 0;
                        $count++;
                    }
                    if($ExamResultORB && $ExamResultORB->is_submitted) {
                        $totalScore += $ExamResultORB->score ?? 0;
                        $count++;
                    }
                    $average = $count > 0 ? $totalScore / $count : 0;
                @endphp
                {{ $count > 0 ? number_format($average, 1) : '-' }}
            </div>
            <small>
                {{ $count > 0 ? "Dari $count ujian" : 'Belum ada nilai' }}
            </small>
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
                            @if($examTPU || $examWawancara || $examORB)
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
                {{-- Tab Navigation dengan 3 tabs --}}
                <div class="exam-tabs">
                    <button class="exam-tab active" data-exam-type="TPU" onclick="switchExamTab('TPU')">
                        <i class="fas fa-book-open"></i>
                        <span>TPU</span>
                    </button>
                    <button class="exam-tab" data-exam-type="Wawancara" onclick="switchExamTab('Wawancara')">
                        <i class="fas fa-comments"></i>
                        <span>Wawancara</span>
                    </button>
                    <button class="exam-tab" data-exam-type="Observasi" onclick="switchExamTab('Observasi')">
                        <i class="fas fa-user-tie"></i>
                        <span>Observasi</span>
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
                                <a href="{{ route('showmainujian') }}" class="action-button action-button-primary">
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
                                <span class="value">Wawancara</span>
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
                                <a href="{{ route('showmainujian') }}" class="action-button action-button-primary">
                                    <i class="fas fa-play"></i>
                                    Mulai Ujian Wawancara
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

                {{-- Observasi Card --}}
                @if($examORB)
                <div class="exam-card" id="card-Observasi" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h2>
                                <i class="fas fa-user-tie"></i>
                                {{ $examORB->judul }}
                            </h2>
                            <span class="exam-status status-{{ $examORB->status }}">
                                {{ ucfirst($examORB->status) }}
                            </span>
                        </div>
                        
                        <ul class="info-list">
                            <li>
                                <span class="label">
                                    <i class="fas fa-tag"></i>
                                    Jenis Ujian
                                </span>
                                <span class="value">Observasi</span>
                            </li>
                            <li>
                                <span class="label">
                                    <i class="fas fa-clock"></i>
                                    Durasi
                                </span>
                                <span class="value">{{ $examORB->duration }} Menit</span>
                            </li>
                            <li>
                                <span class="label">
                                    <i class="fas fa-calendar-alt"></i>
                                    Waktu Mulai
                                </span>
                                <span class="value">{{ \Carbon\Carbon::parse($examORB->start_at)->format('d M Y, H:i') }}</span>
                            </li>
                            <li>
                                <span class="label">
                                    <i class="fas fa-calendar-times"></i>
                                    Batas Waktu
                                </span>
                                <span class="value">{{ \Carbon\Carbon::parse($examORB->end_at)->format('d M Y, H:i') }}</span>
                            </li>
                        </ul>

                        @php
                            $now = \Carbon\Carbon::now();
                            $isOpen = $examORB->status === 'active' && 
                                      $now->between($examORB->start_at, $examORB->end_at);
                            $isBiodataValid = auth()->user()->biodata && 
                                              auth()->user()->biodata->status === 'valid';
                        @endphp

                        @if($isBiodataValid)
                            @if($isOpen)
                                <a href="{{ route('showmainujian') }}" class="action-button action-button-primary">
                                    <i class="fas fa-play"></i>
                                    Mulai Ujian Observasi
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
                <div class="exam-card" id="card-Observasi" style="display: none;">
                    <div class="card card-empty">
                        <i class="fas fa-inbox empty-icon"></i>
                        <h3>Tidak Ada Ujian Observasi</h3>
                        <p>Ujian Observasi belum tersedia saat ini</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function switchExamTab(examType) {
    // Hide all exam cards
    document.querySelectorAll('.exam-card').forEach(card => {
        card.style.display = 'none';
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.exam-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Show selected exam card
    const selectedCard = document.getElementById('card-' + examType);
    if (selectedCard) {
        selectedCard.style.display = 'block';
    }
    
    // Add active class to clicked tab
    const selectedTab = document.querySelector('.exam-tab[data-exam-type="' + examType + '"]');
    if (selectedTab) {
        selectedTab.classList.add('active');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Show TPU card by default
    switchExamTab('TPU');
});
</script>


@if(session('login_notifications'))
<script id="login-notifications-data" type="application/json">
{!! json_encode(session('login_notifications')) !!}
</script>
@endif

@if(session('user_notifications'))
<script id="user-notifications" type="application/json">
{!! json_encode(session('user_notifications')) !!}
</script>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const notif = JSON.parse(
        document.getElementById('user-notifications').textContent
    );

    await Swal.fire({
        title: "Login Berhasil",
        icon: "success",
        confirmButtonText: "Lanjutkan"
    });

    if (notif.status) {
        await Swal.fire({
            title: "Status Biodata",
            text: notif.status === 'valid'
                ? "Biodata Anda telah divalidasi"
                : "Biodata Anda ditolak",
            icon: notif.status === 'valid' ? 'success' : 'warning',
            confirmButtonText: "OK"
        });
    }
});
</script>
@endif


@endsection