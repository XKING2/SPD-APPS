@extends('layouts.main')

@section('pageheads')
<div class="container-fluid px-4">
    <h1 class="h3 mb-3"> Test seleksi Perangkat Desa </h1>
</div>
@endsection

@section('content')
    <div class="container-fluid">

        <div class="card shadow-lg border-left-primary mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-clipboard-check mr-2"></i> Test Pengetahuan Umum
            </h5>
        </div>
        @php
            use Carbon\Carbon;
        @endphp

        <div class="card-body">

            {{-- Deskripsi singkat --}}
            <div class="alert alert-info d-flex align-items-center" style="border-left: 4px solid #0d6efd;">
                <i class="fas fa-info-circle fa-lg mr-3"></i>
                <div>
                    Silakan tinjau data Administrasi sebelum memulai ujian. Pastikan data anda sudah benar.
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
                <div class="alert alert-success mt-3">
                    <i class="fas fa-check-circle"></i>
                    <strong>Anda sudah mengerjakan ujian TPU.</strong>
                </div>

                {{-- ================= UJIAN SELESAI ================= --}}
                @elseif($isFinished)
                    <p class="text-secondary mt-3 fw-bold">
                        Jadwal ujian sudah selesai
                    </p>

                {{-- ================= BISA MULAI UJIAN ================= --}}
                @elseif($canGenerate)
                    <button
                        class="btn btn-lg btn-primary btn-enroll"
                        data-action="{{ route('exam.tpu.verify', $examTPU->id) }}"
                        data-title="Ujian TPU">
                        Mulai Ujian TPU
                    </button>

                {{-- ================= BELUM WAKTUNYA ================= --}}
                @else
                    <button class="btn btn-lg btn-primary" disabled>
                        Mulai Ujian TPU
                    </button>

                    <small class="text-warning d-block mt-2">
                        Tombol akan aktif 5 menit sebelum ujian dimulai
                    </small>

                    <small class="text-muted d-block">
                        Waktu ujian: {{ $startAt->format('d M Y H:i') }}
                    </small>
                @endif

            @else
                <div class="alert alert-warning">
                    Ujian TPU belum tersedia
                </div>
            @endif


        </div>
    </div>

<div class="card shadow-lg border-left-primary mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-clipboard-check mr-2"></i> Test Wawancara
            </h5>
        </div>

    <div class="card-body">

            {{-- Deskripsi singkat --}}
            <div class="alert alert-info d-flex align-items-center" style="border-left: 4px solid #0d6efd;">
                <i class="fas fa-info-circle fa-lg mr-3"></i>
                <div>
                    Silakan tinjau data Administrasi sebelum memulai ujian. Pastikan data anda sudah benar.
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
                <div class="alert alert-success mt-3">
                    <i class="fas fa-check-circle"></i>
                    <strong>Anda sudah mengerjakan ujian Wawancara.</strong>
                </div>

                {{-- ================= UJIAN SELESAI ================= --}}
                @elseif($isFinished)
                    <p class="text-secondary mt-3 fw-bold">
                        Jadwal ujian sudah selesai
                    </p>

                {{-- ================= BISA MULAI UJIAN ================= --}}
                @elseif($canGenerate)
                    <button
                        class="btn btn-lg btn-primary btn-enroll"
                        data-action="{{ route('exam.wwn.verify', $examWWN->id) }}"
                        data-title="Ujian Wawancara">
                        Mulai Ujian Wawancara
                    </button>

                {{-- ================= BELUM WAKTUNYA ================= --}}
                @else
                    <button class="btn btn-lg btn-primary" disabled>
                        Mulai Ujian TPU
                    </button>

                    <small class="text-warning d-block mt-2">
                        Tombol akan aktif 5 menit sebelum ujian dimulai
                    </small>

                    <small class="text-muted d-block">
                        Waktu ujian: {{ $startAt->format('d M Y H:i') }}
                    </small>
                @endif

            @else
                <div class="alert alert-warning">
                    Ujian TPU belum tersedia
                </div>
            @endif


        </div>
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
                    autocorrect: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Verifikasi & Mulai',
                cancelButtonText: 'Batal',
                preConfirm: (value) => {
                    if (!value || value.length !== 6) {
                        Swal.showValidationMessage('Enrollment key harus 6 karakter');
                    }
                    return value;
                }
            });

            if (!enrollment) return;

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
                Swal.fire('Gagal', text || 'Enrollment key salah', 'error');

            } catch (err) {
                Swal.fire('Error', 'Terjadi kesalahan saat verifikasi', 'error');
            }
        });
    });
});
</script>

<script>
document.getElementById('startExamBtn')?.addEventListener('click', function () {
    // kirim niat fullscreen ke halaman ujian
    sessionStorage.setItem('exam_fullscreen_intent', '1');

    // redirect normal
    window.location.href = this.dataset.url;
});
</script>


@endsection


{{-- 🔹 SweetAlert Script --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-edit');
    editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const editUrl = this.getAttribute('data-edit-url');
            Swal.fire({
                title: "Edit data ini?",
                text: "Perubahan akan mempengaruhi data terkait.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, lanjutkan",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) window.location.href = editUrl;
            });
        });
    });

    const deleteForms = document.querySelectorAll('.form-delete');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: "Hapus data ini?",
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, hapus",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    const btn = form.querySelector('button[type="submit"]');
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
                    form.submit();
                }
            });
        });
    });
});
</script>
