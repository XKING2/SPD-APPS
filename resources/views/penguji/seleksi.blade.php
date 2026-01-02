@extends('layouts.main2')

@section('pageheads')
    <h1 class="h3 mb-4 text-gray-800">Tambah Seleksi Ujian</h1>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Kelola Data Soal TPU</h6>
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
                <h5 class="mb-0">Tambah Seleksi</h5>

                <button class="btn btn-primary"
                        data-toggle="modal"
                        data-target="#createSeleksiModal">
                    <i class="fas fa-file-import"></i> Tambah Seleksi
                </button>
            </div>
            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Judul</th>
                            <th>Deskripsi</th>
                            <th>Tahun</th>
                            <th style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse ($seleksi as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->judul }}</td>
                                <td class="text-start">
                                    {{ Str::limit($item->deskripsi, 80) }}
                                </td>
                                <td>{{ $item->tahun}}</td>
                                <td>
                                    <a href="#"
                                    class="btn btn-sm btn-success">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <form action="#"method="POST"class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
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

<div class="modal fade" id="createSeleksiModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('seleksi.import') }}"
              method="POST"
              class="modal-content">
            @csrf

            <div class="modal-header">
                <h5 class="modal-title">Buat Seleksi Baru</h5>
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

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi"
                              class="form-control"
                              rows="3"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Tahun <span class="text-danger">*</span></label>
                        <input type="number"
                               name="tahun"
                               class="form-control"
                               placeholder="Tambah Tahun"
                               required>
                    </div>

                    <div class="mb-3 text-start">
                        <label class="form-label">Kecamatan</label>
                        <select name="id_kecamatans"
                                id="kecamatan"
                                class="form-control"
                                required>
                            <option value="">-- Pilih Kecamatan --</option>
                            @foreach ($kecamatans as $kec)
                                <option value="{{ $kec->id }}">
                                    {{ $kec->nama_kecamatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 text-start">
                        <label class="form-label">Desa</label>
                        <select name="id_desas"
                                id="desa"
                                class="form-control"
                                required>
                            <option value="">-- Pilih Desa --</option>
                        </select>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan Seleksi
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const kecamatanSelect = document.getElementById('kecamatan');
    const desaSelect = document.getElementById('desa');

    if (!kecamatanSelect || !desaSelect) {
        console.error('Element kecamatan atau desa tidak ditemukan');
        return;
    }

    kecamatanSelect.addEventListener('change', function () {
        const kecamatanId = this.value;

        desaSelect.innerHTML = '<option value="">Loading...</option>';

        if (!kecamatanId) {
            desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
            return;
        }

        fetch(`/desa/by-kecamatan/${kecamatanId}`)
            .then(res => res.json())
            .then(data => {
                desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';

                data.forEach(desa => {
                    const option = document.createElement('option');
                    option.value = desa.id;
                    option.textContent = desa.nama_desa;
                    desaSelect.appendChild(option);
                });
            })
            .catch(err => {
                console.error(err);
                desaSelect.innerHTML = '<option value="">Gagal memuat desa</option>';
            });
    });
});
</script>



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

