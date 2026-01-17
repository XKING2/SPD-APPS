<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SI SSD</title>
    
    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="{{ asset('css/otp.css') }}" rel="stylesheet">
</head>

<body>
    <div class="container-wrapper">
        <div class="otp-box text-center">
            <img src="{{ asset('images/logo1.png') }}" class="logo" alt="Logo">
            <h4 class="otp-title">Verifikasi OTP</h4>
            <p class="otp-subtitle">
                Masukkan kode OTP 6 digit yang telah dikirim ke email Anda
            </p>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Success Messages -->
            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- OTP Verification Form -->
            <form method="POST" action="{{ route('otp.verify') }}" id="otpForm">
                @csrf
                
                <div class="text-start">
                    <label class="form-label">Kode OTP</label>
                    <div class="otp-input-container">
                        <i class="fas fa-shield-alt otp-icon"></i>
                        <input 
                            type="text" 
                            name="otp" 
                            id="otpInput"
                            class="otp-input"
                            required 
                            maxlength="6"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            placeholder="000000"
                            pattern="[0-9]{6}"
                        >
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-check-circle me-2"></i>Verifikasi
                </button>
            </form>

            <!-- Resend OTP Form -->
            <form method="POST" action="{{ route('otp.resend') }}" id="resendForm">
                @csrf
                <button
                    type="submit"
                    id="resendBtn"
                    class="btn-secondary"
                    disabled>
                    <i class="fas fa-redo-alt me-2"></i>
                    <span id="btnText">Kirim ulang OTP (<span id="timer">60</span>s)</span>
                </button>
            </form>

            <!-- Cancel Verification Form -->
            <form method="POST" action="{{ route('otp.cancel') }}" style="margin-top: 15px;">
                @csrf
                <button type="submit" class="btn-primary" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fas fa-times-circle me-2"></i>Ganti Email / Batalkan Verifikasi
                </button>
            </form>

            <!-- Info Box -->
            <div class="info-text">
                <i class="fas fa-info-circle"></i>
                Kode OTP berlaku selama 5 menit
            </div>
        </div>
    </div>

    <!-- Flash Message Handler -->
    @if(session('otp_resent'))
        <div data-swal-resend="{{ session('otp_resent') }}" style="display: none;"></div>
    @endif

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ==============================================
        // OTP INPUT HANDLER
        // ==============================================
        const otpInput = document.getElementById('otpInput');
        
        // Only allow numeric input
        otpInput.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Auto-focus and select on page load
        window.addEventListener('load', function() {
            otpInput.focus();
            otpInput.select();
        });

        // ==============================================
        // RESEND TIMER
        // ==============================================
        let timeLeft = 60;
        const resendBtn = document.getElementById('resendBtn');
        const timerElement = document.getElementById('timer');
        const btnTextElement = document.getElementById('btnText');

        function startTimer() {
            const interval = setInterval(() => {
                timeLeft--;
                timerElement.innerText = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(interval);
                    resendBtn.disabled = false;
                    btnTextElement.innerHTML = 'Kirim ulang OTP';
                }
            }, 1000);

            return interval;
        }

        let currentInterval = startTimer();

        // ==============================================
        // RESEND OTP HANDLER
        // ==============================================
        document.getElementById('resendForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Disable button and show loading
            resendBtn.disabled = true;
            btnTextElement.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Mengirim...';
            
            // Send request
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'OTP baru telah dikirim ke email Anda',
                        confirmButtonColor: '#667eea',
                        confirmButtonText: 'OK',
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        // Reset timer
                        timeLeft = 60;
                        timerElement.innerText = timeLeft;
                        btnTextElement.innerHTML = 'Kirim ulang OTP (<span id="timer">60</span>s)';
                        
                        // Clear old interval and start new one
                        clearInterval(currentInterval);
                        currentInterval = startTimer();
                    });
                } else {
                    throw new Error(data.message || 'Gagal mengirim OTP');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: error.message || 'Terjadi kesalahan. Silakan coba lagi.',
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'OK'
                });
                
                // Reset button
                resendBtn.disabled = false;
                btnTextElement.innerHTML = 'Kirim ulang OTP';
            });
        });

        // ==============================================
        // FLASH MESSAGE HANDLER
        // ==============================================
        document.addEventListener("DOMContentLoaded", function () {
            const resendData = document.querySelector('[data-swal-resend]');

            if (resendData) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: resendData.dataset.swalResend,
                    confirmButtonColor: '#667eea',
                    confirmButtonText: 'OK',
                    timer: 3000,
                    timerProgressBar: true
                });
            }
        });
    </script>
</body>
</html>
