@extends('layouts.main2')

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
            <div class="d-flex justify-content-between mb-3">
                <h5 class="mb-0">Tambah Ujian</h5>

                <button class="btn btn-primary"
                        data-toggle="modal"
                        data-target="#createSeleksiModal">
                    <i class="fas fa-file-import"></i> Tambah Ujian
                </button>
            </div>
            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Judul</th>
                            <th>Tahun</th>
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
                                @if ($exam->status === 'valid')
                                    <span class="badge badge-success">Tervalidasi</span>
                                @elseif ($exam->status === 'draft')
                                    <span class="badge badge-warning">Belum divalidasi</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>

                            <td>
                                {{-- VALIDASI --}}
                                <form action="{{ route('exam.validasi', $exam->id) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Validasi ujian ini?')">
                                    @csrf
                                    <button class="btn btn-sm btn-primary"
                                            {{ $exam->status === 'valid' ? 'disabled' : '' }}>
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>

                                <a href="{{ route('exam.edit', $exam->id) }}"
                                class="btn btn-sm btn-success">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('exam.destroy', $exam->id) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Yakin hapus exam ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

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

<div class="modal fade" id="createSeleksiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST"
              action="{{ route('exams.import') }}"
              enctype="multipart/form-data"
              class="modal-content">

            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Buat Exam & Import Soal</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <div class="form-group">
                    <label>Judul Seleksi <span class="text-danger">*</span></label>
                    <input type="text"
                           name="judul"
                           class="form-control"
                           required>
                </div>

                <h6 class="mb-3 text-primary">Pilih Seleksi</h6>
                <div class="form-group">
                    <label>Seleksi <span class="text-danger">*</span></label>
                    <select name="seleksi_id" class="form-control" required>
                        <option value="">-- Pilih Seleksi --</option>
                        @foreach($seleksis as $seleksi)
                            <option value="{{ $seleksi->id }}">
                                {{ $seleksi->judul }} {{ $seleksi->nama_desa }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- =======================
                     DATA EXAM
                ======================= --}}
                <h6 class="mb-3 text-primary mt-4">Informasi Exam</h6>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Tipe Exam <span class="text-danger">*</span></label>
                        <select name="type" class="form-control" required>
                            <option value="">-- Pilih Tipe Exam --</option>
                            @foreach($types as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-6">
                        <label>Durasi (menit) <span class="text-danger">*</span></label>
                        <input type="number"
                               name="duration"
                               class="form-control"
                               min="1"
                               required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Mulai</label>
                        <input type="datetime-local"
                               name="start_at"
                               class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Selesai</label>
                        <input type="datetime-local"
                               name="end_at"
                               class="form-control">
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan & Import
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Batal
                </button>
            </div>

        </form>
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

