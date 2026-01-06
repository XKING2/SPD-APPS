@extends('layouts.main1')

@section('pageheads')
    <h1 class="h3 mb-4 text-gray-800">Validasi Data User</h1>
@endsection


@section('content')
<div class="container-fluid">
    <!-- Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Validasi Data User </h6>
        </div>
        <div class="card-body">
            <!-- Search & Print -->
            <div class="d-flex justify-content-between mb-3">
                <form action="#" method="GET" class="form-inline">
                    <input type="text" name="search" class="form-control form-control-sm mr-2" 
                        placeholder="Cari..." value="">
                    <button type="submit" class="btn btn-sm btn-secondary">Cari</button>
                </form>

            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th style="width:200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($biodatas as $index => $data)
                            @php
                                $badgeMap = [
                                    'draft'   => ['class' => 'warning', 'label' => 'Draft'],
                                    'pending' => ['class' => 'info',    'label' => 'Menunggu'],
                                    'valid'   => ['class' => 'success', 'label' => 'Disetujui'],
                                    'rejected'=> ['class' => 'danger',  'label' => 'Ditolak'],
                                ];

                                $badge = $badgeMap[$data->status] ?? ['class' => 'dark', 'label' => 'Unknown'];
                            @endphp

                            <tr id="row-{{ $data->id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $data->user->name }}</td>
                                <td>{{ $data->user->email ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $badge['class'] }}">
                                        {{ $badge['label'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('validasi.show', $data->id) }}"
                                        class="btn btn-info btn-sm">
                                            Lihat
                                        </a>

                                        @if($data->status === 'pending')
                                            <button
                                                class="btn btn-success btn-sm btn-validasi"
                                                data-id="{{ $data->id }}"
                                                data-url="{{ route('validasi.submit', $data->id) }}">
                                                Validasi
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">

            </div>
        </div>
    </div>
</div>

    <script>
    document.querySelectorAll('.btn-validasi').forEach(btn => {
        btn.addEventListener('click', function () {

            const url = this.dataset.url;
            const id  = this.dataset.id;

            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire('Berhasil', data.message, 'success');

                // 🧹 Hilangkan baris setelah validasi
                document.getElementById('row-' + id).remove();
            })
            .catch(() => {
                Swal.fire('Gagal', 'Terjadi kesalahan', 'error');
            });
        });
    });
    </script>





@endsection







