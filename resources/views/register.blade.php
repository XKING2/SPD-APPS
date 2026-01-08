<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem E-SPD - Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            width: 100%;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 0;
            margin: 0;
            position: relative;
            overflow-x: hidden;
        }

        .container-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 50px 20px;
        }

        body::before {
            content: '';
            position: fixed;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            top: -200px;
            left: -200px;
            animation: float 6s ease-in-out infinite;
            z-index: 0;
        }

        body::after {
            content: '';
            position: fixed;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            bottom: -150px;
            right: -150px;
            animation: float 8s ease-in-out infinite;
            z-index: 0;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(20px);
            }
        }

        .login-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
            position: relative;
            z-index: 1;
            animation: slideUp 0.5s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            width: 65px;
            height: 65px;
            object-fit: contain;
            margin-bottom: 12px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .fw-bold {
            color: #333;
            font-weight: 700;
            font-size: 26px;
            margin-bottom: 25px;
        }

        .form-label {
            color: #444;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .input-group-custom {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            font-size: 16px;
            z-index: 2;
            pointer-events: none;
        }

        .form-control {
            padding: 11px 15px 11px 42px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background-color: white;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.1);
            outline: none;
        }

        select.form-control {
            padding: 11px 15px 11px 42px;
            padding-right: 40px;
            background-color: white;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23667eea' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
        }

        select.form-control:hover {
            border-color: #667eea;
            background-color: #f8f9ff;
        }

        select.form-control:disabled {
            background-color: #f5f5f5;
            cursor: not-allowed;
            opacity: 0.6;
        }

        select.form-control.loading {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 50 50'%3E%3Cpath fill='%23667eea' d='M25,5A20.14,20.14,0,0,1,45,22.88a2.51,2.51,0,0,0,2.49,2.26h0A2.52,2.52,0,0,0,50,22.33a25.14,25.14,0,0,0-50,0,2.52,2.52,0,0,0,2.5,2.81h0A2.51,2.51,0,0,0,5,22.88,20.14,20.14,0,0,1,25,5Z'%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 25 25' to='360 25 25' dur='0.6s' repeatCount='indefinite'/%3E%3C/path%3E%3C/svg%3E");
            background-size: 16px;
            background-position: right 15px center;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
            transition: color 0.3s ease;
            z-index: 2;
        }

        .password-toggle:hover {
            color: #667eea;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .card-header {
            background: transparent;
            border: none;
            padding: 20px 0 0 0;
            display: flex;
            justify-content: center;
        }

        .btn-sm {
            padding: 10px 18px;
            background: white;
            border: 2px solid #667eea;
            color: #667eea;
            border-radius: 10px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-sm:hover {
            background: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }

        .alert {
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
            border: none;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .alert-danger {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        @media (max-width: 576px) {
            .container-wrapper {
                padding: 30px 15px;
            }

            .login-box {
                padding: 30px 25px;
            }

            .fw-bold {
                font-size: 24px;
            }

            .logo {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>

<body>
    <div class="container-wrapper">
        <div class="login-box text-center">
        <img src="{{ asset('images/Logo1.png') }}" class="logo" alt="Logo">
        <h4 class="fw-bold">Register E-SPD</h4>

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

    // Dynamic Desa dropdown based on Kecamatan
    document.getElementById('kecamatan').addEventListener('change', function () {
        const kecamatanId = this.value;
        const desaSelect = document.getElementById('desa');

        // Add loading state
        desaSelect.classList.add('loading');
        desaSelect.disabled = true;
        desaSelect.innerHTML = '<option value="">Loading...</option>';

        if (!kecamatanId) {
            desaSelect.classList.remove('loading');
            desaSelect.disabled = false;
            desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
            return;
        }

        fetch(`/ajax/desa/${kecamatanId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                desaSelect.classList.remove('loading');
                desaSelect.disabled = false;
                desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';

                if (data.length === 0) {
                    const emptyOption = document.createElement('option');
                    emptyOption.value = '';
                    emptyOption.disabled = true;
                    emptyOption.textContent = 'Tidak ada desa tersedia';
                    desaSelect.appendChild(emptyOption);
                } else {
                    data.forEach(desa => {
                        const option = document.createElement('option');
                        option.value = desa.id;
                        option.textContent = desa.nama_desa;
                        desaSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                desaSelect.classList.remove('loading');
                desaSelect.disabled = false;
                desaSelect.innerHTML = '<option value="">Gagal memuat desa</option>';
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memuat Data',
                    text: 'Tidak dapat memuat data desa. Silakan coba lagi.',
                    confirmButtonColor: '#667eea',
                    confirmButtonText: 'OK'
                });
            });
    });
    </script>
</body>
</html>