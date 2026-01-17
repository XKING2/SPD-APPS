@extends('layouts.main1')

@section('pageheads')
    <h1 class="h3 mb-4 text-gray-800">Kelola Data Seleksi</h1>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Kelola Data Seleksi </h6>
                    <button class="btn btn-primary"
                            data-toggle="modal"
                            data-target="#createSeleksiModal">
                        <i class="fas fa-file-import"></i> Tambah Data Seleksi
                    </button>
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

            <div class="row">
                @foreach($formasis as $formasi)
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5>{{ $formasi->seleksi->judul }}</h5>
                                <p>Tahun {{ $formasi->tahun }}</p>

                                <a href="{{ route('formasi.show', Hashids::encode($formasi->id)) }} "
                                class="btn btn-outline-info btn-sm">
                                    Kelola Kebutuhan
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            

        </div>
    </div>
</div>

<div class="modal fade" id="createSeleksiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST"
              action="{{ route('formasi.store') }}"
              enctype="multipart/form-data"
              class="modal-content">

            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Buat Data Seleksi</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <h6 class="mb-3 text-primary">Pilih Seleksi</h6>
                <div class="form-group">
                    <label>Seleksi <span class="text-danger">*</span></label>
                    <select name="id_seleksi" class="form-control" required>
                        <option value="">-- Pilih Seleksi --</option>
                        @foreach($seleksis as $seleksi)
                            <option value="{{ $seleksi->id }}">
                                {{ $seleksi->judul }} {{ $seleksi->nama_desa }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Batal
                </button>
            </div>

        </form>
    </div>
</div>

@if ($errors->any())
    <div
        data-swal-errors="{{ implode('|', $errors->all()) }}"
        style="display:none"
    ></div>
@endif

@if (session('success'))
    <div
        data-swal-success="{{ session('success') }}"
        style="display:none"
    ></div>
@endif


@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<script>
document.addEventListener('DOMContentLoaded', function () {

    // === ERROR ALERT ===
    const swalErrors = document.querySelector('[data-swal-errors]');
    if (swalErrors) {
        Swal.fire({
            icon: 'error',
            title: 'Formasi Tidak Bisa Dibuat',
            html: swalErrors.getAttribute('data-swal-errors').split('|').join('<br>'),
            confirmButtonText: 'Mengerti'
        });
    }

    // === SUCCESS ALERT ===
    const swalSuccess = document.querySelector('[data-swal-success]');
    if (swalSuccess) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: swalSuccess.getAttribute('data-swal-success'),
            timer: 2500,
            showConfirmButton: false
        });
    }

});
</script>


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

