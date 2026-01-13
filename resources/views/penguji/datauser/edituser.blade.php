@extends('layouts.main2')

@section('pageheads')
<div class="container-fluid px-4">
    <h1 class="h3 mb-3 fw-bold text-gradient">
        <i class="fas fa-user-edit me-2"></i>
        Edit Data User
    </h1>
</div>
@endsection

@section('content')
<style>
    .text-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .user-form-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .form-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
    }

    .info-alert {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border: none;
        border-left: 5px solid #2196f3;
        border-radius: 12px;
        padding: 18px 20px;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .info-alert i {
        font-size: 24px;
        color: #1976d2;
    }

    .info-alert div {
        color: #0d47a1;
        font-size: 15px;
        font-weight: 500;
    }

    .form-section {
        margin-bottom: 30px;
    }

    .form-section-title {
        font-size: 18px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 3px solid #f0f0f0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-section-title i {
        color: #667eea;
        font-size: 20px;
    }

    .input-group-custom {
        margin-bottom: 25px;
    }

    .input-group-custom label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .input-group-custom label i {
        font-size: 16px;
    }

    .form-control,
    .form-select {
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: #e74a3b;
    }

    .invalid-feedback {
        color: #e74a3b;
        font-size: 13px;
        margin-top: 5px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .password-input-wrapper {
        position: relative;
    }

    .password-toggle {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #999;
        transition: color 0.3s ease;
    }

    .password-toggle:hover {
        color: #667eea;
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px solid #f0f0f0;
    }

    .btn-back {
        background: white;
        border: 2px solid #667eea;
        color: #667eea;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-back:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
    }

    .btn-submit {
        background: linear-gradient(135deg, #23d5ab 0%, #1cc88a 100%);
        color: white;
        border: none;
        padding: 12px 40px;
        border-radius: 10px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(28, 200, 138, 0.3);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(28, 200, 138, 0.4);
    }

    .conditional-field {
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .conditional-field.show {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .select-loading {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 50 50'%3E%3Cpath fill='%23667eea' d='M25,5A20.14,20.14,0,0,1,45,22.88a2.51,2.51,0,0,0,2.49,2.26h0A2.52,2.52,0,0,0,50,22.33a25.14,25.14,0,0,0-50,0,2.52,2.52,0,0,0,2.5,2.81h0A2.51,2.51,0,0,0,5,22.88,20.14,20.14,0,0,1,25,5Z'%3E%3CanimateTransform attributeName='transform' type='rotate' from='0 25 25' to='360 25 25' dur='0.6s' repeatCount='indefinite'/%3E%3C/path%3E%3C/svg%3E");
        background-size: 16px;
        background-position: right 15px center;
        background-repeat: no-repeat;
    }

    @media (max-width: 768px) {
        .form-card {
            padding: 25px 20px;
        }

        .form-actions {
            flex-direction: column;
            gap: 15px;
        }

        .btn-back,
        .btn-submit {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="container-fluid user-form-container">
    <div class="form-card">
        <!-- Info Alert -->
        <div class="info-alert">
            <i class="fas fa-info-circle"></i>
            <div>
                Silakan masukkan data user dengan lengkap dan benar. Pastikan semua field yang wajib diisi sudah terisi.
            </div>
        </div>

        <form method="POST" action="{{ route('user.update', $user->id) }}" enctype="multipart/form-data" id="formUser">
            @csrf
            @method('PUT')
            
            <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-md-6">
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-user-circle"></i>
                            Informasi Akun
                        </div>

                        <!-- Nama -->
                        <div class="input-group-custom">
                            <label>
                                <i class="fas fa-user text-primary"></i>
                                Nama Lengkap
                                <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="name" 
                                class="form-control @error('name') is-invalid @enderror" 
                                value="{{ old('name', $user->name) }}"
                                placeholder="Masukkan nama lengkap"
                                required>
                            @error('name')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="input-group-custom">
                            <label>
                                <i class="fas fa-envelope text-info"></i>
                                Email
                                <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                value="{{ old('email', $user->email) }}" 
                                placeholder="contoh@email.com"
                                required>
                            @error('email')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="input-group-custom">
                            <label>
                                <i class="fas fa-lock text-warning"></i>
                                Password Baru (Kosongkan jika tidak diubah)
                            </label>
                            <div class="password-input-wrapper">
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="password"
                                    class="form-control @error('password') is-invalid @enderror" 
                                    value="{{ old('password') }}" 
                                    placeholder="Minimal 8 karakter"
                                    minlength="8">
                                <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i>
                                Kosongkan jika tidak ingin mengubah password. Minimal 8 karakter jika diisi.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="col-md-6">
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-shield-alt"></i>
                            Role & Lokasi
                        </div>

                        <!-- Role -->
                        <div class="input-group-custom">
                            <label>
                                <i class="fas fa-user-tag text-success"></i>
                                Role / Hak Akses
                                <span class="text-danger">*</span>
                            </label>
                            <select name="role" id="roleSelect" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                                    Admin
                                </option>
                                <option value="penguji" {{ old('role', $user->role) == 'penguji' ? 'selected' : '' }}>
                                    Penguji
                                </option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Kecamatan (Conditional) -->
                        <div class="input-group-custom conditional-field" id="kecamatanWrapper">
                            <label>
                                <i class="fas fa-map-marked-alt text-warning"></i>
                                Kecamatan
                                <span class="text-danger">*</span>
                            </label>
                            <select id="kecamatanSelect" class="form-select @error('kecamatan_id') is-invalid @enderror">
                                <option value="">-- Pilih Kecamatan --</option>
                                @foreach ($kecamatans as $kecamatan)
                                    <option value="{{ $kecamatan->id }}"
                                        {{ old('kecamatan_id', optional($user->desas?->kecamatan)->id) == $kecamatan->id ? 'selected' : '' }}>
                                        {{ $kecamatan->nama_kecamatan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kecamatan_id')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Desa (Conditional) -->
                        <div class="input-group-custom conditional-field" id="desaWrapper">
                            <label>
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                Desa
                                <span class="text-danger">*</span>
                            </label>
                            <select name="id_desas" id="desaSelect" class="form-select @error('id_desas') is-invalid @enderror">
                                <option value="">-- Pilih Desa --</option>
                                @if($user->desas)
                                    <option value="{{ $user->desas->id }}" selected>
                                        {{ $user->desas->nama_desa }}
                                    </option>
                                @endif
                            </select>
                            @error('id_desas')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="form-actions">
                <a href="{{ route('datauser') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Elements
    const roleSelect = document.getElementById('roleSelect');
    const kecamatanWrapper = document.getElementById('kecamatanWrapper');
    const desaWrapper = document.getElementById('desaWrapper');
    const kecamatanSelect = document.getElementById('kecamatanSelect');
    const desaSelect = document.getElementById('desaSelect');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    // Toggle Password Visibility
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }

    // Function to toggle location fields visibility
    function toggleLokasiFields() {
        const selectedRole = roleSelect.value;
        
        // Hanya tampilkan kecamatan dan desa jika role adalah 'admin'
        if (selectedRole === 'admin') {
            kecamatanWrapper.classList.add('show');
            desaWrapper.classList.add('show');
        } else {
            // Sembunyikan dan reset fields untuk role selain admin (termasuk penguji)
            kecamatanWrapper.classList.remove('show');
            desaWrapper.classList.remove('show');
            kecamatanSelect.value = '';
            desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
        }
    }

    // Initialize on page load
    toggleLokasiFields();

    // Role Change Handler
    roleSelect.addEventListener('change', function () {
        toggleLokasiFields();
    });

    // Kecamatan Change Handler
    kecamatanSelect.addEventListener('change', function () {
        const kecamatanId = this.value;
        
        if (!kecamatanId) {
            desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
            return;
        }

        // Add loading state
        desaSelect.classList.add('select-loading');
        desaSelect.disabled = true;
        desaSelect.innerHTML = '<option value="">Loading...</option>';

        // Fetch desa data
        fetch(`/desa/by-kecamatan/${kecamatanId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                desaSelect.classList.remove('select-loading');
                desaSelect.disabled = false;
                desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
                
                if (data.length === 0) {
                    desaSelect.innerHTML += '<option value="" disabled>Tidak ada desa tersedia</option>';
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
                desaSelect.classList.remove('select-loading');
                desaSelect.disabled = false;
                desaSelect.innerHTML = '<option value="">Gagal memuat desa</option>';
                
                alert('Gagal memuat data desa. Silakan coba lagi.');
            });
    });
});
</script>

@endsection