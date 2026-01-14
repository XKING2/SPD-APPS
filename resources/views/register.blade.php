<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Si SSD - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/register.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo1.png') }}">
</head>

<body>
    <div class="container-wrapper">
        <div class="login-box text-center">
        <img src="{{ asset('images/logo1.png') }}" class="logo" alt="Logo">
        <h4 class="fw-bold">Register Si SSD</h4>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3 text-start">
                <label class="form-label">Nama</label>
                <div class="input-group-custom">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="name" class="form-control" placeholder="Masukkan Nama Anda" value="{{ old('name') }}" required>
                </div>
            </div>

            <div class="mb-3 text-start">
                <label class="form-label">Email</label>
                <div class="input-group-custom">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="email" name="email" class="form-control" placeholder="Masukkan Email Anda" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="mb-3 text-start">
                <label class="form-label">Password</label>
                <div class="input-group-custom">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan Password" required minlength="8">
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                </div>
            </div>

            <div class="mb-3 text-start">
                <label class="form-label">Kecamatan</label>
                <div class="input-group-custom">
                    <i class="fas fa-map-marker-alt input-icon"></i>
                    <select name="kecamatan_id"
                            id="kecamatan"
                            class="form-control"
                            required>
                        <option value="">-- Pilih Kecamatan --</option>
                        @foreach ($kecamatans as $kec)
                            <option value="{{ $kec->id }}" {{ old('kecamatan_id') == $kec->id ? 'selected' : '' }}>
                                {{ $kec->nama_kecamatan }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mb-3 text-start">
                <label class="form-label">Desa</label>
                <div class="input-group-custom">
                    <i class="fas fa-home input-icon"></i>
                    <select name="id_desas"
                            id="desa"
                            class="form-control"
                            required>
                        <option value="">-- Pilih Desa --</option>
                    </select>
                </div>
            </div>
        
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-user-plus me-2"></i>Register
            </button>
        </form>
        
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <a href="{{ route('login') }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-arrow-left fa-sm me-1"></i> Kembali Ke Login
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif
    </div>
    </div>

    {{-- Flash message untuk register sukses --}}
    @if(session('success'))
        <div data-swal-success="{{ session('success') }}"></div>
    @endif

    {{-- Flash message untuk register gagal --}}
    @if($errors->any())
        <div data-swal-errors="{{ implode('|', $errors->all()) }}"></div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    if (togglePassword && password) {
        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    // SweetAlert logic
    document.addEventListener("DOMContentLoaded", function () {
        const successData = document.querySelector('[data-swal-success]');
        const errorData = document.querySelector('[data-swal-errors]');

        if (successData) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: successData.dataset.swalSuccess,
                confirmButtonColor: '#667eea',
                confirmButtonText: 'OK'
            });
        }

        if (errorData) {
            const errorMessages = errorData.dataset.swalErrors.split('|');
            Swal.fire({
                icon: 'error',
                title: 'Registrasi Gagal',
                html: errorMessages.join('<br>'),
                confirmButtonColor: '#d33',
                confirmButtonText: 'Coba Lagi'
            });
        }
    });

        document.addEventListener('DOMContentLoaded', function () {
        const kecamatanEl = document.getElementById('kecamatan');
        const desaSelect = document.getElementById('desa');

        if (!kecamatanEl || !desaSelect) return;

        kecamatanEl.addEventListener('change', function () {
            const kecamatanId = this.value;

            desaSelect.disabled = true;
            desaSelect.innerHTML = '<option>Loading...</option>';

            if (!kecamatanId) {
                desaSelect.disabled = false;
                desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
                return;
            }

            fetch(`/regis/get-desa/${kecamatanId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (!res.ok) throw res;
                return res.json();
            })
            .then(data => {
                desaSelect.disabled = false;
                desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';

                data.forEach(desa => {
                    const opt = document.createElement('option');
                    opt.value = desa.id;
                    opt.textContent = desa.nama_desa;
                    desaSelect.appendChild(opt);
                });
            })
            .catch(err => {
                desaSelect.disabled = false;
                desaSelect.innerHTML = '<option>Gagal memuat desa</option>';
                console.error(err);
            });
        });
    });
    </script>
</body>
</html>