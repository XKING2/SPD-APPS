@extends('layouts.main2')

@section('pageheads')
    <h1 class="h3 mb-4 text-gray-800">Tambah Soal Wawancara</h1>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Kelola Data Soal Wawancara</h6>
        </div>
        <div class="card-body">
            <!-- Search & Print -->
            <div class="d-flex justify-content-between mb-3">
                <form action="#" method="GET" class="form-inline">
                    <input type="text" name="search" class="form-control form-control-sm mr-2" 
                        placeholder="Cari..." value="#">
                    <button type="submit" class="btn btn-sm btn-secondary">Cari</button>
                </form>

                <a href="#" class="btn btn-info btn-sm px-3 py-1 d-flex align-items-center">
                    <i class="fas fa-plus mr-1"></i> 
                </a>
            </div>

            <div class="d-flex justify-content-between mb-3">
                <h5 class="mb-0">Tambah Soal</h5>
                @if($exam)
                    <form action="{{ route('exam.validasi', [$exam->id, 'WWN']) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('Yakin soal sudah benar dan ingin divalidasi?')">

                        @csrf

                        <button type="submit"
                                class="btn btn-primary"
                                {{ $exam->status !== 'draft' ? 'disabled' : '' }}>
                            <i class="fas fa-check-circle"></i>
                            Validasi Soal
                        </button>
                    </form>
                @else
                    <div class="alert alert-warning mb-0">
                        Soal belum di-import. Silakan import soal terlebih dahulu.
                    </div>
                @endif
                <button class="btn btn-primary"
                        data-toggle="modal"
                        data-target="#importSoalModal">
                    <i class="fas fa-file-import"></i> Import Soal
                </button>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Subject</th>
                            <th>Pertanyaan</th>
                            <th style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($questions as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->subject }}</td>
                                <td class="text-start">
                                    {{ Str::limit($item->pertanyaan, 80) }}
                                </td>
                                <td>
                                    <a href="{{ route('wawan.edit', $item->id) }}"
                                    class="btn btn-sm btn-success">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <form action="#"method="POST"class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
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
<div class="modal fade" id="importSoalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('exam-wawancara.import',['seleksi' => $seleksi->id,'desa'=> $desa->id]) }}"
              method="POST"
              enctype="multipart/form-data"
              class="modal-content">

            @csrf
            <input type="hidden" name="id_seleksi" value="{{ $seleksi->id }}">
            <input type="hidden" name="id_desas" value="{{ $desa->id }}">

            <div class="modal-header">
                <h5 class="modal-title">Buat Exam & Import Soal</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                {{-- =======================
                    DATA EXAM
                ======================= --}}
                <h6 class="mb-3 text-primary">Informasi Exam</h6>

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

                <hr>

                {{-- =======================
                    IMPORT SOAL
                ======================= --}}
                <h6 class="mb-3 text-primary">Import Soal</h6>

                <div class="form-group">
                    <label>File Excel Soal <span class="text-danger">*</span></label>
                    <input type="file"
                           name="excel"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>ZIP Gambar (Opsional)</label>
                    <input type="file"
                           name="zip"
                           class="form-control">
                </div>

                <small class="text-muted">
                    Nama file gambar di ZIP harus sama dengan kolom
                    <code>image_name</code> di Excel.
                </small>

            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan & Import
                </button>
                <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                    Batal
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

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

