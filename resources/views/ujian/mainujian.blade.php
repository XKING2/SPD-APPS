@extends('layouts.main')

@section('pageheads')
<div class="container-fluid px-4">
    <h1 class="h3 mb-3 fw-bold text-gradient">
        <i class="fas fa-clipboard-list me-2"></i>
        Tes Seleksi Perangkat Desa
    </h1>
</div>
@endsection

@section('content')

<div class="container-fluid exam-container">

    {{-- TPU EXAM CARD --}}
    <div class="exam-card">
        <div class="exam-card-header">
            <h5>
                <i class="fas fa-book-open"></i>
                Tes Potensi Umum (TPU)
            </h5>
            <span class="exam-type-badge">Pilihan Ganda</span>
        </div>

        @php
            use Carbon\Carbon;
        @endphp

        <div class="exam-card-body">
            <div class="info-alert">
                <i class="fas fa-info-circle"></i>
                <div>
                    Silakan tinjau data Administrasi sebelum memulai ujian. Pastikan data Anda sudah benar.
                </div>
            </div>

            @if($examTPU)
                @php     
                    $now        = Carbon::now();
                    $startAt    = Carbon::parse($examTPU->start_at);
                    $endAt      = Carbon::parse($examTPU->end_at);

                    $isFinished  = $now->greaterThan($endAt);
                    $canGenerate = $now->greaterThanOrEqualTo($startAt->copy()->subMinutes(5));
                @endphp 

                {{-- ================= SUDAH DIKERJAKAN ================= --}}
                @if($ExamResultTPU && $ExamResultTPU->is_submitted)
                    <div class="success-badge">
                        <i class="fas fa-check-circle"></i>
                        <span>Anda sudah mengerjakan ujian TPU</span>
                    </div>

                {{-- ================= UJIAN SELESAI ================= --}}
                @elseif($isFinished)
                    <div class="exam-finished">
                        <i class="fas fa-clock me-2"></i>
                        Jadwal ujian sudah selesai
                    </div>

                {{-- ================= BISA MULAI UJIAN ================= --}}
                @elseif($canGenerate)
                    <button
                        class="btn-start-exam btn-enroll"
                        data-action="{{ route('exam.tpu.verify', $examTPU->id) }}"
                        data-title="Ujian TPU">
                        <i class="fas fa-play-circle"></i>
                        Mulai Ujian TPU
                    </button>

                {{-- ================= BELUM WAKTUNYA ================= --}}
                @else
                    <button class="btn-disabled" disabled>
                        <i class="fas fa-lock"></i>
                        Mulai Ujian TPU
                    </button>

                    <div class="warning-text">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Tombol akan aktif 5 menit sebelum ujian dimulai
                    </div>

                    <div class="time-info">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Waktu ujian: {{ $startAt->format('d M Y H:i') }}</span>
                    </div>
                @endif

            @else
                <div class="alert-no-exam">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Ujian TPU belum tersedia</span>
                </div>
            @endif
        </div>
    </div>

    {{-- WAWANCARA EXAM CARD --}}
    <div class="exam-card">
        <div class="exam-card-header">
            <h5>
                <i class="fas fa-comments"></i>
                Tes Wawancara
            </h5>
            <span class="exam-type-badge">Pilihan Ganda</span>
        </div>

        <div class="exam-card-body">
            <div class="info-alert">
                <i class="fas fa-info-circle"></i>
                <div>
                    Silakan tinjau data Administrasi sebelum memulai ujian. Pastikan data Anda sudah benar.
                </div>
            </div>

            @if($examWWN)
                @php     
                    $now        = Carbon::now();
                    $startAt    = Carbon::parse($examWWN->start_at);
                    $endAt      = Carbon::parse($examWWN->end_at);

                    $isFinished  = $now->greaterThan($endAt);
                    $canGenerate = $now->greaterThanOrEqualTo($startAt->copy()->subMinutes(5));
                @endphp 

                {{-- ================= SUDAH DIKERJAKAN ================= --}}
                @if($ExamResultWWN && $ExamResultWWN->is_submitted)
                    <div class="success-badge">
                        <i class="fas fa-check-circle"></i>
                        <span>Anda sudah mengerjakan ujian Wawancara</span>
                    </div>

                {{-- ================= UJIAN SELESAI ================= --}}
                @elseif($isFinished)
                    <div class="exam-finished">
                        <i class="fas fa-clock me-2"></i>
                        Jadwal ujian sudah selesai
                    </div>

                {{-- ================= BISA MULAI UJIAN ================= --}}
                @elseif($canGenerate)
                    <button
                        class="btn-start-exam btn-enroll"
                        data-action="{{ route('exam.wwn.verify', $examWWN->id) }}"
                        data-title="Ujian Wawancara">
                        <i class="fas fa-play-circle"></i>
                        Mulai Ujian Wawancara
                    </button>

                {{-- ================= BELUM WAKTUNYA ================= --}}
                @else
                    <button class="btn-disabled" disabled>
                        <i class="fas fa-lock"></i>
                        Mulai Ujian Wawancara
                    </button>

                    <div class="warning-text">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Tombol akan aktif 5 menit sebelum ujian dimulai
                    </div>

                    <div class="time-info">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Waktu ujian: {{ $startAt->format('d M Y H:i') }}</span>
                    </div>
                @endif

            @else
                <div class="alert-no-exam">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Ujian Wawancara belum tersedia</span>
                </div>
            @endif
        </div>
    </div>

    {{-- ORB EXAM CARD --}}
    <div class="exam-card">
        <div class="exam-card-header">
            <h5>
                <i class="fas fa-user-tie"></i>
                Tes Observasi
            </h5>
            <span class="exam-type-badge">Penilaian</span>
        </div>

        <div class="exam-card-body">
            <div class="info-alert">
                <i class="fas fa-info-circle"></i>
                <div>
                    Silakan tinjau data Administrasi sebelum memulai ujian. Pastikan data Anda sudah benar.
                </div>
            </div>

            @if($examORB)
                @php     
                    $now        = Carbon::now();
                    $startAt    = Carbon::parse($examORB->start_at);
                    $endAt      = Carbon::parse($examORB->end_at);

                    $isFinished  = $now->greaterThan($endAt);
                    $canGenerate = $now->greaterThanOrEqualTo($startAt->copy()->subMinutes(5));
                @endphp 

                {{-- ================= SUDAH DIKERJAKAN ================= --}}
                @if($ExamResultORB && $ExamResultORB->is_submitted)
                    <div class="success-badge">
                        <i class="fas fa-check-circle"></i>
                        <span>Anda sudah mengerjakan ujian Observasi</span>
                    </div>

                {{-- ================= UJIAN SELESAI ================= --}}
                @elseif($isFinished)
                    <div class="exam-finished">
                        <i class="fas fa-clock me-2"></i>
                        Jadwal ujian sudah selesai
                    </div>

                {{-- ================= BISA MULAI UJIAN ================= --}}
                @elseif($canGenerate)
                    <button
                        class="btn-start-exam btn-enroll"
                        data-action="{{ route('exam.orb.verify', $examORB->id) }}"
                        data-title="Ujian Observasi">
                        <i class="fas fa-play-circle"></i>
                        Mulai Ujian Observasi
                    </button>

                {{-- ================= BELUM WAKTUNYA ================= --}}
                @else
                    <button class="btn-disabled" disabled>
                        <i class="fas fa-lock"></i>
                        Mulai Ujian Observasi
                    </button>

                    <div class="warning-text">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Tombol akan aktif 5 menit sebelum ujian dimulai
                    </div>

                    <div class="time-info">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Waktu ujian: {{ $startAt->format('d M Y H:i') }}</span>
                    </div>
                @endif

            @else
                <div class="alert-no-exam">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>Ujian Observasi belum tersedia</span>
                </div>
            @endif
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-enroll').forEach(btn => {
        btn.addEventListener('click', async function () {
            const action = this.dataset.action;
            const title  = this.dataset.title;
            const csrf   = document.querySelector('meta[name="csrf-token"]').content;

            const { value: enrollment } = await Swal.fire({
                title: title,
                text: 'Masukkan Enrollment Key',
                input: 'text',
                inputPlaceholder: 'XXXXXX',
                inputAttributes: {
                    maxlength: 6,
                    autocapitalize: 'characters',
                    autocorrect: 'off',
                    style: 'text-align: center; font-size: 24px; letter-spacing: 8px; font-weight: bold;'
                },
                showCancelButton: true,
                confirmButtonText: 'Verifikasi & Mulai',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#667eea',
                cancelButtonColor: '#6c757d',
                preConfirm: (value) => {
                    if (!value || value.length !== 6) {
                        Swal.showValidationMessage('Enrollment key harus 6 karakter');
                    }
                    return value;
                }
            });

            if (!enrollment) return;

            // Show loading
            Swal.fire({
                title: 'Memverifikasi...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Kirim POST via fetch
            try {
                const res = await fetch(action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: new URLSearchParams({
                        enrollment_key: enrollment
                    })
                });

                if (res.redirected) {
                    window.location.href = res.url;
                    return;
                }

                const text = await res.text();
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: text || 'Enrollment key salah',
                    confirmButtonColor: '#667eea'
                });

            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan saat verifikasi',
                    confirmButtonColor: '#667eea'
                });
            }
        });
    });
});
</script>

@endsection