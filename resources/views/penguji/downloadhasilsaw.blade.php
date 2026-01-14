@extends('layouts.main2')

@section('content')
<div class="container">

    <h4 class="fw-bold mb-4">Daftar File PDF Hasil Generate</h4>

    @forelse($files as $seleksiId => $items)
        <div class="card mb-4 shadow-sm">

            <div class="card-body p-0">
                <table class="table table-striped table-bordered mb-0">
                    <thead class="table-secondary">
                        <tr>
                            <th width="5%">#</th>
                            <th>Nama Dokumen</th>
                            <th>Nama File</th>
                            <th width="15%">Tanggal</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $i => $file)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $file->seleksi->judul ?? 'Ranking SAW' }}</td>
                                <td class="text-muted small">
                                    {{ $file->file_name  ?? 'Ranking SAW'}}
                                </td>
                                <td>
                                    {{ $file->created_at->format('d/m/Y') }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('ranking.download', $file->id) }}"
                                       class="btn btn-sm btn-primary">
                                        Download
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    @empty
        <div class="alert alert-warning">
            Belum ada file PDF yang tersedia.
        </div>
    @endforelse

</div>
@endsection
