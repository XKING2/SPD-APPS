@extends('layouts.main1')

@section('pageheads')
    <h1 class="h3 mb-4 text-gray-800">Tambah Data Ujian</h1>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Kelola Data Ujian </h6>
        </div>
        <div class="card-body">
            <!-- Search & Print -->
            <div class="d-flex justify-content-between mb-3">
                <form action="#" method="GET" class="form-inline">
                    <input type="text" name="search" class="form-control form-control-sm mr-2" 
                        placeholder="Cari..." value="#">
                    <button type="submit" class="btn btn-sm btn-secondary">Cari</button>
                </form>

            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Judul</th>
                            <th>Tahun</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Status</th>
                            <th style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $index => $exam)
                        <tr>
                            <td>{{ $index + 1 }}</td>

                            <td>
                                {{ $exam->judul ?? '-' }}
                            </td>

                            <td>
                                {{ $exam->seleksi->tahun ?? '-' }}
                            </td>

                            <td>
                                {{ $exam->start_at ?? '-' }}
                            </td>

                            <td>
                                {{ $exam->end_at ?? '-' }}
                            </td>

                                <td>
                                    @if ($exam->status === 'valid')
                                        <span class="badge badge-success">Tervalidasi</span>
                                    @elseif ($exam->status === 'draft')
                                        <span class="badge badge-warning">Belum divalidasi</span>
                                    @else
                                        <span class="badge badge-danger">Ditolak</span>
                                    @endif
                                </td>

                            <td>

                                <a href="{{ route('adminexam.edit', Hashids::encode($exam->id)) }}"
                                class="btn btn-sm btn-success">
                                    <i class="fas fa-edit"></i>
                                </a>

                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                Data tidak tersedia
                            </td>
                        </tr>
                        @endforelse
                        </tbody>

                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">

            </div>
        </div>
    </div>
</div>





@endsection





<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tangkap semua tombol Edit
    const editButtons = document.querySelectorAll('.btn-edit');

    editButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Cegah langsung pindah halaman
            const editUrl = this.getAttribute('data-edit-url');

            Swal.fire({
                title: "Apakah Anda yakin ingin mengedit data ini?",
                text: "Perubahan akan mempengaruhi data kwitansi terkait.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, lanjutkan",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect ke halaman edit
                    window.location.href = editUrl;
                }
            });
        });
    });

    // Jika ada notifikasi sukses
    const swalSuccess = document.querySelector('[data-swal-success]');
    if (swalSuccess) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: swalSuccess.getAttribute('data-swal-success'),
            timer: 2500,
            showConfirmButton: false
        });
    }

    // Jika ada error
    const swalErrors = document.querySelector('[data-swal-errors]');
    if (swalErrors) {
        const messages = swalErrors.getAttribute('data-swal-errors').split('|');
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan!',
            html: messages.join('<br>'),
        });
    }
});
</script>

