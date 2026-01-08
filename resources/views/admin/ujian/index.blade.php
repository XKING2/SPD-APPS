@extends('layouts.main1')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Mulai Ujian</h1>

    @php
        use Carbon\Carbon;

        // default biar aman dipakai di bawah
        $tpuHasKey = false;
        $wwnHasKey = false;
    @endphp

    <div class="row">

        {{-- ================= TPU ================= --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-body text-center">

                    <i class="fas fa-book-open fa-3x text-success mb-3"></i>
                    <h4 class="card-title">Tes Pengetahuan Umum (TPU)</h4>

                    @if(isset($examTPU))

                        @php
                            $now       = Carbon::now();
                            $tpuStart  = Carbon::parse($examTPU->start_at);
                            $tpuEnd    = Carbon::parse($examTPU->end_at);

                            $tpuFinished = $now->greaterThan($tpuEnd);
                            $tpuDraft    = $examTPU->status === 'draft';

                            $tpuHasKey = !empty($examTPU->enrollment_key)
                                && $examTPU->key_expired_at
                                && Carbon::parse($examTPU->key_expired_at)->isFuture();

                            $tpuCanGenerate =
                                !$tpuDraft &&
                                !$tpuHasKey &&
                                $now->greaterThanOrEqualTo($tpuStart->copy()->subMinutes(30));
                        @endphp

                        @if($tpuFinished)
                            <p class="text-secondary fw-bold">
                                Jadwal ujian sudah selesai
                            </p>

                        @elseif($tpuDraft)
                            <button class="btn btn-secondary" disabled>
                                Generate Enrollment Key
                            </button>

                        @elseif($tpuHasKey)
                            <button class="btn btn-outline-primary"
                                    onclick="openKeyModal('tpu')">
                                Lihat Enrollment Key Aktif
                            </button>

                        @elseif($tpuCanGenerate)
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
                                Aktif 30 menit sebelum ujian
                            </small>
                        @endif

                    @else
                        <p class="text-danger fw-bold">
                            Ujian belum dibuat
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
                            $wwnStart  = Carbon::parse($examWWN->start_at);
                            $wwnEnd    = Carbon::parse($examWWN->end_at);

                            $wwnFinished = $now->greaterThan($wwnEnd);
                            $wwnDraft    = $examWWN->status === 'draft';

                            $wwnHasKey = !empty($examWWN->enrollment_key)
                                && $examWWN->key_expired_at
                                && Carbon::parse($examWWN->key_expired_at)->isFuture();

                            $wwnCanGenerate =
                                !$wwnDraft &&
                                !$wwnHasKey &&
                                $now->greaterThanOrEqualTo($wwnStart->copy()->subMinutes(30));
                        @endphp

                        @if($wwnFinished)
                            <p class="text-secondary fw-bold">
                                Jadwal ujian sudah selesai
                            </p>

                        @elseif($wwnDraft)
                            <button class="btn btn-secondary" disabled>
                                Generate Enrollment Key
                            </button>

                        @elseif($wwnHasKey)
                            <button class="btn btn-outline-primary"
                                    onclick="openKeyModal('wwn')">
                                Lihat Enrollment Key Aktif
                            </button>

                        @elseif($wwnCanGenerate)
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
                                Aktif 30 menit sebelum ujian
                            </small>
                        @endif

                    @else
                        <p class="text-danger fw-bold">
                            Ujian belum dibuat
                        </p>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>


{{-- ================= MODAL TPU ================= --}}
@if($tpuHasKey)
<div class="modal fade" id="keyModal-tpu" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <h4 class="text-success">Enrollment Key TPU</h4>

            <div class="display-4 fw-bold text-primary mb-3">
                {{ $examTPU->enrollment_key }}
            </div>

            <p class="text-muted mb-1">Berlaku hingga</p>
            <h5 class="text-danger fw-bold" id="countdown-tpu"></h5>

        </div>
    </div>
</div>
@endif


{{-- ================= MODAL WWN ================= --}}
@if($wwnHasKey)
<div class="modal fade" id="keyModal-wwn" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
            <h4 class="text-success">Enrollment Key WWN</h4>

            <div class="display-4 fw-bold text-primary mb-3">
                {{ $examWWN->enrollment_key }}
            </div>

            <p class="text-muted mb-1">Berlaku hingga</p>
            <h5 class="text-danger fw-bold" id="countdown-wwn"></h5>
        </div>
    </div>
</div>
@endif

<script>
function openKeyModal(type) {
    const modalId = type === 'tpu'
        ? 'keyModal-tpu'
        : 'keyModal-wwn';

    const modalEl = document.getElementById(modalId);

    if (!modalEl) {
        console.warn('Modal tidak ditemukan:', modalId);
        return;
    }

    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}
</script>

<div id="exam-data"
     data-tpu="{{ $tpuHasKey ? $examTPU->key_expired_at : '' }}"
     data-wwn="{{ $wwnHasKey ? $examWWN->key_expired_at : '' }}">
</div>

<script>
const examData = document.getElementById('exam-data');

const examKeys = {
    tpu: examData.dataset.tpu || null,
    wwn: examData.dataset.wwn || null,
};

function startCountdown(expiredAt, elementId) {
    const expired = new Date(expiredAt).getTime();

    setInterval(() => {
        const diff = expired - Date.now();
        if (diff <= 0) return;

        const m = Math.floor(diff / 60000);
        const s = Math.floor((diff % 60000) / 1000);

        const el = document.getElementById(elementId);
        if (el) {
            el.innerText =
                `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        }
    }, 1000);
}

if (examKeys.tpu) {
    startCountdown(examKeys.tpu, 'countdown-tpu');
}

if (examKeys.wwn) {
    startCountdown(examKeys.wwn, 'countdown-wwn');
}
</script>


@endsection
