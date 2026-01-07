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
    .progress-bar-custom {
        background: #e3e6f0;
        border-radius: 10px;
        height: 10px;
        margin: 15px 0;
        overflow: hidden;
    }
    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #4e73df 0%, #224abe 100%);
        border-radius: 10px;
        transition: width 0.3s ease;
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
    }
    .info-list .value {
        color: #5a5c69;
        font-weight: 600;
        font-size: 14px;
    }
    .action-button {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 14px 25px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        font-size: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .action-button:hover {
        transform: scale(1.02);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    .action-button:disabled {
        background: #e3e6f0;
        color: #858796;
        cursor: not-allowed;
        transform: none;
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
    .dashboard-cards-bottom {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }
    .dashboard-card-bottom {
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
    }
    .dashboard-card-bottom:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.12);
    }
    .dashboard-card-bottom .icon-wrapper {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
    }
    .dashboard-card-bottom .icon-wrapper.blue { background: #e3f2fd; color: #1976d2; }
    .dashboard-card-bottom .icon-wrapper.green { background: #e8f5e9; color: #388e3c; }
    .dashboard-card-bottom .icon-wrapper.orange { background: #fff3e0; color: #f57c00; }
    .dashboard-card-bottom .icon-wrapper.purple { background: #f3e5f5; color: #7b1fa2; }
    .dashboard-card-bottom h3 {
        font-size: 14px;
        color: #858796;
        margin-bottom: 10px;
        font-weight: 600;
    }
    .dashboard-card-bottom .number {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .dashboard-card-bottom .number.blue { color: #4e73df; }
    .dashboard-card-bottom .number.green { color: #1cc88a; }
    .dashboard-card-bottom .number.orange { color: #f6c23e; }
    .dashboard-card-bottom .number.purple { color: #36b9cc; }
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
    }
</style>

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
            <h3>Ujian Diselesaikan</h3>
            <div class="value">0 / 1</div>
        </div>
        
        <div class="stat-card orange">
            <div class="stat-icon orange">⏱️</div>
            <h3>Waktu Tersisa</h3>
            <div class="value">3 Hari</div>
        </div>
        
        <div class="stat-card red">
            <div class="stat-icon red">🎯</div>
            <h3>Nilai Terakhir</h3>
            <div class="value">-</div>
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
                            ⏳ Ujian Tertulis
                        @else
                            ○ Ujian Tertulis
                        @endif
                    </h4>
                    <p>
                        @if(auth()->user()->biodata && auth()->user()->biodata->status === 'valid')
                            Belum dimulai - Deadline: 9 Januari 2025
                        @else
                            Lengkapi biodata terlebih dahulu
                        @endif
                    </p>
                </div>
                
                <div class="timeline-item">
                    <h4>○ Pengumuman Hasil</h4>
                    <p>Menunggu - 15 Januari 2025</p>
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

        <!-- Info Card -->
        <div class="card">
            <div class="card-header">
                <h2>ℹ️ Informasi Ujian</h2>
            </div>
            
            <ul class="info-list">
                <li>
                    <span class="label">Jenis Ujian</span>
                    <span class="value">Pilihan Ganda</span>
                </li>
                <li>
                    <span class="label">Jumlah Soal</span>
                    <span class="value">50 Soal</span>
                </li>
                <li>
                    <span class="label">Durasi</span>
                    <span class="value">90 Menit</span>
                </li>
                <li>
                    <span class="label">Batas Waktu</span>
                    <span class="value">9 Jan 2025</span>
                </li>
                <li>
                    <span class="label">Passing Grade</span>
                    <span class="value">70</span>
                </li>
            </ul>

            @if(auth()->user()->biodata && auth()->user()->biodata->status === 'valid')
                <a href="{{ route('showmainujian') }}" class="action-button" style="margin-top: 20px; text-decoration: none;">
                    <i class="fas fa-play"></i>
                    Mulai Ujian Sekarang
                </a>
            @else
                <button class="action-button" disabled style="margin-top: 20px;">
                    <i class="fas fa-lock"></i>
                    Lengkapi Biodata Terlebih Dahulu
                </button>
            @endif
        </div>
    </div>
    </div>
</div>

@endsection