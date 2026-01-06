@extends('layouts.main1')

@section('pageheads')
    <h1 class="h3 mb-4 text-gray-800">Tambah Data Formasi</h1>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Kelola Data Formasi </h6>
            <button class="btn btn-primary"
                        data-toggle="modal"
                        data-target="#createFormasiModal">
                    <i class="fas fa-plus"></i> Tambah Formasi
            </button>

        </div>
        <div class="card-body">
            

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th>Nama Kebutuhan</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            @foreach($formasi->kebutuhan as $k)
                                <tr>
                                    <td>{{ $k->nama_kebutuhan }}</td>
                                    <td>{{ $k->jumlah }}</td>
                                </tr>
                            @endforeach
                        </tr>

                        <tr>
                            <td colspan="5" class="text-center">
                                Data tidak tersedia
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="createFormasiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <form action="{{ route('formasi.kebutuhan.store', $formasi->id) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Nama Kebutuhan</label>
                    <input type="text" name="nama_kebutuhan" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah" class="form-control" min="1" required>
                </div>

                <button class="btn btn-primary mt-2">
                    Tambah Kebutuhan
                </button>
            </form>


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

