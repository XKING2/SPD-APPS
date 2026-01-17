<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Si SSD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/costom.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo1.png') }}">
</head>

<body>
    <div class="login-box text-center">
        <img src="{{ asset('images/logo1.png') }}" class="logo" alt="Logo">
        <h4 class="fw-bold">Si SSD</h4>

        <form method="POST" action="{{route('update.pass')}}">
            @csrf
            <input type="hidden" name="token" value="{{$token}}" class="form-control" required>
            
            <div class="mb-3 text-start">
                <label class="form-label">Email</label>
                <div class="input-group-custom">
                    <i class="fas fa-envelope input-icon"></i>
                    <input type="text" name="email" class="form-control" placeholder="Masukkan Email" required>
                </div>
            </div>

            <div class="mb-3 text-start">
                <label class="form-label">Password</label>
                <div class="input-group-custom">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan Password" required>
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                </div>
            </div>

            <div class="mb-3 text-start">
                <label class="form-label">Konfirmasi Password</label>
                <div class="input-group-custom">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Masukkan Password" required>
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-sign-in-alt me-2"></i>Send Email
            </button>
        </form>

        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-success mt-3">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('status') }}
            </div>
        @endif

    </div>

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

    // SweetAlert logic (tetap sama seperti kode asli)
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
                title: 'Login Gagal',
                html: errorMessages.join('<br>'),
                confirmButtonColor: '#d33',
                confirmButtonText: 'Coba Lagi'
            });
        }
    });
    </script>
</body>
</html>