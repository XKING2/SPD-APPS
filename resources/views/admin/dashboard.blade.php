@extends('layouts.main1')

@section('pageheads')
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>
@endsection

@section('content')

<div class="container-fluid">

    {{-- STATISTIK --}}
    <div class="row mb-4">
        @foreach ([
            ['TOTAL PESERTA', $totalPeserta, 'primary', 'users'],
            ['LULUS', $pesertaValid, 'success', 'check-circle'],
            ['BELUM', $pesertaDraft, 'warning', 'exclamation-triangle'],
        ] as [$title, $value, $color, $icon])
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-{{ $color }} shadow h-100 py-3">
                <div class="card-body d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs font-weight-bold text-{{ $color }} text-uppercase mb-1">
                            {{ $title }}
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">
                            {{ $value }}
                        </div>
                    </div>
                    <div>
                        <i class="fas fa-{{ $icon }} fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- CHART --}}
    <div class="row">
        {{-- BAR CHART --}}
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Jumlah Pendaftar per Desa
                    </h6>
                    <small class="text-muted">
                        Berdasarkan filter yang dipilih
                    </small>
                </div>
                <div class="card-body">
                    {!! $weeklyPendaftarChart->container() !!}
                    
                </div>
            </div>
        </div>

        {{-- PIE CHART --}}
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Status Biodata Peserta
                    </h6>
                </div>
                <div class="card-body">
                    {!! $statusBiodataChart->container() !!}
                </div>
            </div>
        </div>

        @if(empty($weeklyPendaftarChart))
            <div class="alert alert-danger">Weekly chart kosong</div>
        @endif

        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Jumlah Pendaftar per Desa
                    </h6>
                    <small class="text-muted">
                        Berdasarkan filter yang dipilih
                    </small>
                </div>
                <div class="card-body">
                    {!! $pendaftarDesaChart->container() !!}
                    
                </div>
            </div>
        </div>
    </div>

</div>

@if(session('admin_notifications'))
<script id="admin-notifications" type="application/json">
{!! json_encode(session('admin_notifications')) !!}
</script>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const notif = JSON.parse(
        document.getElementById('admin-notifications').textContent
    );

    await Swal.fire({
        title: "Login Berhasil",
        icon: "success",
        confirmButtonText: "Lanjutkan"
    });

    if (notif.draft_count > 0) {
        const result = await Swal.fire({
            title: "Biodata Menunggu Validasi",
            html: `<b>${notif.draft_count}</b> biodata menunggu validasi`,
            icon: "info",
            confirmButtonText: "Periksa"
        });

        if (result.isConfirmed) {
            window.location.href = "{{ route('validasi.index') }}";
        }
    }
});
</script>
@endif


@endsection



@push('scripts')
    <script src="{{ $weeklyPendaftarChart->cdn() }}"></script>

    {{ $pendaftarDesaChart->script() }}
    {{ $statusBiodataChart->script() }}
    {{ $weeklyPendaftarChart->script() }}
@endpush
