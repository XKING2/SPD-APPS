<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem E-SPD - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/costomcss.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div class="login-box text-center">
        <img src="{{ asset('images/Logo1.png') }}" class="logo" alt="Logo">
        <h4 class="fw-bold">E-SPD</h4>

        <form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="mb-3 text-start">
        <label class="form-label">Nama</label>
        <input type="text" name="name" class="form-control" placeholder="Masukkan Nama Anda" required>
    </div>

    <div class="mb-3 text-start">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="Masukkan Email Anda" required>
    </div>

    <div class="mb-3 text-start">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required minlength="8">
    </div>

    <div class="mb-3 text-start">
        <label class="form-label">Kecamatan</label>
        <select name="kecamatan_id"
                id="kecamatan"
                class="form-control"
                required>
            <option value="">-- Pilih Kecamatan --</option>
            @foreach ($kecamatans as $kec)
                <option value="{{ $kec->id }}">{{ $kec->nama_kecamatan }}</option>
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
    
    <button type="submit" class="btn btn-primary w-100">Register</button>
</form>
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            
            <a href="{{ route('login') }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50"></i> Kembali Ke login
            </a>

        </div>

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                {{ $errors->first() }}
            </div>
        @endif
    </div>
</body>

{{-- Flash message untuk login sukses --}}
@if(session('success'))
    <div data-swal-success="{{ session('success') }}"></div>
@endif

{{-- Flash message untuk login gagal --}}
@if($errors->any())
    <div data-swal-errors="{{ implode('|', $errors->all()) }}"></div>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const successData = document.querySelector('[data-swal-success]');
    const errorData = document.querySelector('[data-swal-errors]');

    if (successData) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: successData.dataset.swalSuccess,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    }

    if (errorData) {
        const errorMessages = errorData.dataset.swalErrors.split('|');
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            html: errorMessages.join('<br>'),
            confirmButtonColor: '#d33',
            confirmButtonText: 'Coba Lagi'
        });
    }
});
</script>

<script>
document.getElementById('kecamatan').addEventListener('change', function () {
    let kecamatanId = this.value;
    let desaSelect = document.getElementById('desa');

    desaSelect.innerHTML = '<option value="">Loading...</option>';

    if (!kecamatanId) {
        desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
        return;
    }

    fetch(`/get-desa/${kecamatanId}`)
        .then(res => res.json())
        .then(data => {
            desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
            data.forEach(desa => {
                desaSelect.innerHTML += `
                    <option value="${desa.id}">
                        ${desa.nama_desa}
                    </option>`;
            });
        });
});
</script>


</html>
