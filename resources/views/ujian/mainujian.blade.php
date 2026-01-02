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

                {{-- ================= UJIAN SELESAI ================= --}}
                @if($isFinished)
                    <p class="text-secondary mt-3 fw-bold">
                        Jadwal ujian sudah selesai
                    </p>

                {{-- ================= BISA MULAI UJIAN ================= --}}
                @elseif($canGenerate)
                    <button
                        class="btn btn-lg btn-primary"
                        data-toggle="modal"
                        data-target="#enrollModal">
                        Mulai Ujian TPU
                    </button>

                    {{-- MODAL ENROLLMENT --}}
                    <div class="modal fade" id="enrollModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title">Masukkan Enrollment Key</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>

                                <form method="POST" action="{{ route('exam.tpu.verify', $examTPU->id) }}">
                                    @csrf

                                    <div class="modal-body text-center">
                                        <input type="text"
                                            name="enrollment_key"
                                            class="form-control text-center text-uppercase"
                                            placeholder="XXXXXX"
                                            maxlength="6"
                                            required>

                                        @error('enrollment_key')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">
                                            Verifikasi & Mulai
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

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
                <i class="fas fa-clipboard-check mr-2"></i> Test Pengetahuan Umum
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

                {{-- ================= UJIAN SELESAI ================= --}}
                @if($isFinished)
                    <p class="text-secondary mt-3 fw-bold">
                        Jadwal ujian sudah selesai
                    </p>

                {{-- ================= BISA MULAI UJIAN ================= --}}
                @elseif($canGenerate)
                    <button
                        class="btn btn-lg btn-primary"
                        data-toggle="modal"
                        data-target="#enrollModal">
                        Mulai Ujian TPU
                    </button>

                    {{-- MODAL ENROLLMENT --}}
                    <div class="modal fade" id="enrollModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title">Masukkan Enrollment Key</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>

                                <form method="POST" action="{{ route('exam.wwn.verify', $examWWN->id) }}">
                                    @csrf

                                    <div class="modal-body text-center">
                                        <input type="text"
                                            name="enrollment_key"
                                            class="form-control text-center text-uppercase"
                                            placeholder="XXXXXX"
                                            maxlength="6"
                                            required>

                                        @error('enrollment_key')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">
                                            Verifikasi & Mulai
                                        </button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

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
