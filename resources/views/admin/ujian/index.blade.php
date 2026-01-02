@extends('layouts.main1')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Mulai Ujian</h1>

    <div class="row">

        @php
            use Carbon\Carbon;
        @endphp

        {{-- ================= TPU ================= --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-body text-center">

                    <i class="fas fa-book-open fa-3x text-success mb-3"></i>
                    <h4 class="card-title">Tes Pengetahuan Umum (TPU)</h4>

                    @if(isset($examTPU))

                        @php     
                            $now = Carbon::now();
                            $startAt = Carbon::parse($examTPU->start_at);
                            $endAt = Carbon::parse($examTPU->end_at);
                            $isFinished = $now->greaterThan($endAt);
                            $canGenerate = $now->greaterThanOrEqualTo($startAt->subMinutes(30));
                        @endphp 

                        @if($isFinished)
                            <p class="text-secondary mt-3 fw-bold">
                                Jadwal ujian sudah selesai
                            </p>

                        @elseif($canGenerate)
                            <form action="{{ route('admin.tpu.generate', $examTPU->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-primary">
                                    Generate Enrollment Key
                                </button>
                            </form>

                        @else
                            <button class="btn btn-primary" disabled>
                                Generate Enrollment Key
                            </button>

                            <small class="text-warning d-block mt-2">
                                Tombol akan aktif 30 menit sebelum ujian dimulai
                            </small>

                            <small class="text-muted d-block">
                                Waktu ujian: {{ $startAt->format('d M Y H:i') }}
                            </small>
                        @endif

                    @else
                        <p class="text-danger mt-3 fw-bold">
                            Ujian belum dibuat oleh penguji
                        </p>
                    @endif

                </div>
            </div>
        </div>

        {{-- ================= WAWANCARA ================= --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-body text-center">

                    <i class="fas fa-comments fa-3x text-primary mb-3"></i>
                    <h4 class="card-title">Tes Wawancara</h4>

                    @if(isset($examWWN))

                        @php
                            $startAt = Carbon::parse($examWWN->start_at);
                            $endAt = Carbon::parse($examWWN->end_at);

                            $isFinished = $now->greaterThan($endAt);
                            $canGenerate = $now->greaterThanOrEqualTo($startAt->copy()->subMinutes(30));
                        @endphp

                        @if($isFinished)
                            <p class="text-secondary mt-3 fw-bold">
                                Jadwal ujian sudah selesai
                            </p>

                        @elseif($canGenerate)
                            <form action="{{ route('admin.wwn.generate', $examWWN->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-primary">
                                    Generate Enrollment Key
                                </button>
                            </form>

                        @else
                            <button class="btn btn-primary" disabled>
                                Generate Enrollment Key
                            </button>

                            <small class="text-warning d-block mt-2">
                                Tombol akan aktif 30 menit sebelum ujian dimulai
                            </small>

                            <small class="text-muted d-block">
                                Waktu ujian: {{ $startAt->format('d M Y H:i') }}
                            </small>
                        @endif

                    @else
                        <p class="text-danger mt-3 fw-bold">
                            Ujian belum dibuat oleh penguji
                        </p>
                    @endif

                </div>
            </div>
        </div>
    </div>

</div>


@if(session('enrollment_key'))
<!-- Modal -->
<div class="modal fade show" id="keyModal" tabindex="-1" style="display:block;background:rgba(0,0,0,.5)">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">

            <h4 class="mb-3 text-success">Enrollment Key</h4>

            <div class="display-4 fw-bold text-primary mb-3">
                {{ session('enrollment_key') }}
            </div>

            <p class="text-muted mb-2">
                Halaman ini akan tertutup otomatis dalam
            </p>

            <h5 id="countdown" class="text-danger fw-bold">
                05:00
            </h5>

            <button class="btn btn-secondary mt-3" onclick="closeModal()">
                Tutup Sekarang
            </button>

        </div>
    </div>
</div>
@endif


@if(session('enrollment_key'))
<script>
    let timeLeft = 300; // 5 menit (detik)

    const countdownEl = document.getElementById('countdown');

    const timer = setInterval(() => {
        let minutes = Math.floor(timeLeft / 60);
        let seconds = timeLeft % 60;

        countdownEl.textContent =
            `${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;

        timeLeft--;

        if (timeLeft < 0) {
            clearInterval(timer);
            closeModal();
        }
    }, 1000);

    function closeModal() {
        document.getElementById('keyModal').style.display = 'none';
        document.body.classList.remove('modal-open');
        document.querySelector('.modal-backdrop')?.remove();
    }
</script>
@endif

@endsection
