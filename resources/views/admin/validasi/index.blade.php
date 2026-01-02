@extends('layouts.main1')

@section('content')

    <div class="container-fluid">

        <h4 class="mb-4">Validasi Biodata</h4>

        <div class="card shadow">
            <div class="card-body">
                <table class="table table-bordered text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama</th>
                            <th>NIM</th>
                            <th>Status</th>
                            <th style="width:200px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($biodatas as $data)
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



