@extends('layouts.main2')

@section('pageheads')
<h1 class="h3 mb-4 text-gray-800">Dashboard</h1>
@endsection

@section('content')
<div class="container-fluid">

    {{-- FILTER --}}
    <form method="GET" class="card shadow mb-4">
        <div class="card-body row">
            <div class="col-md-4">
                <label>Kecamatan</label>
                <select name="kecamatan" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Kecamatan</option>
                    @foreach ($kecamatans as $kec)
                        <option value="{{ $kec->id }}" {{ $kecamatanId == $kec->id ? 'selected' : '' }}>
                            {{ $kec->nama_kecamatan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>Desa</label>
                <select name="desa" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Desa</option>
                    @foreach ($desas as $desa)
                        <option value="{{ $desa->id }}" {{ $desaId == $desa->id ? 'selected' : '' }}>
                            {{ $desa->nama_desa }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    {{-- STAT CARD --}}
    <div class="row mb-4">
        @foreach ([
            ['Total Peserta', $totalPeserta, 'primary', 'users'],
            ['Lulus', $lulus, 'success', 'check-circle'],
            ['Belum', $belum, 'warning', 'exclamation-triangle'],
            ['Total Desa', $totalDesa, 'info', 'map'],
        ] as [$title, $value, $color, $icon])
        <div class="col-xl-3 col-md-6">
            <div class="card border-left-{{ $color }} shadow h-100 py-2">
                <div class="card-body d-flex align-items-center">
                    <div class="me-auto">
                        <div class="text-xs text-{{ $color }} font-weight-bold text-uppercase">
                            {{ $title }}
                        </div>
                        <div class="h5 font-weight-bold text-gray-800">
                            {{ $value }}
                        </div>
                    </div>
                    <i class="fas fa-{{ $icon }} fa-2x text-gray-300"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- CHART --}}
    <div class="row">
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-body">
                    {!! $areaChart->container() !!}
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ $areaChart->cdn() }}"></script>
{{ $areaChart->script() }}
@endsection
