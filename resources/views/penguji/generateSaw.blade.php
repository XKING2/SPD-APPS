@extends('layouts.main2')

@section('pageheads')
<div class="container-fluid px-4">
    <h1 class="h3 mb-3 text-gray-800">Generate Ranking SAW</h1>
</div>
@endsection

@section('content')
<div class="container-fluid">

    {{-- PILIH SELEKSI --}}
    <form method="GET" action="{{ route('generate.page') }}" class="mb-4">
        <div class="row">
            <div class="col-md-6">
                <select name="seleksi_id" class="form-control" onchange="this.form.submit()">
                    <option value="">-- Pilih Seleksi --</option>
                    @foreach ($seleksis as $seleksi)
                        <option value="{{ $seleksi->id }}"
                            {{ request('seleksi_id') == $seleksi->id ? 'selected' : '' }}>
                            {{ $seleksi->judul }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    {{-- INFO SELEKSI --}}
    @if ($selectedSeleksi)
        <div class="card shadow">
            <div class="card-body">
                <h5>{{ $selectedSeleksi->nama_seleksi }}</h5>
                <p class="text-muted mb-3">
                    Desa ID: {{ $selectedSeleksi->id_desas }}
                </p>

                <form action="{{ route('saw.generate', $selectedSeleksi->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-danger"
                        onclick="return confirm('Generate ulang ranking SAW untuk seleksi ini?')">
                        Generate Ranking SAW
                    </button>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection
