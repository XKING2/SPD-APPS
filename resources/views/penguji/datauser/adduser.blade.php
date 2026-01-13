@extends('layouts.main2')

@section('pageheads')
<div class="container-fluid px-4">
    <h1 class="h3 mb-3 fw-bold text-gradient">
        <i class="fas fa-user-plus me-2"></i>
        Tambah Data User
    </h1>
</div>
@endsection

@section('content')


<div class="container-fluid user-form-container">
    <div class="form-card">
        <!-- Info Alert -->
        <div class="info-alert">
            <i class="fas fa-info-circle"></i>
            <div>
                Silakan masukkan data user dengan lengkap dan benar. Pastikan semua field yang wajib diisi sudah terisi.
            </div>
        </div>

        <form method="POST" action="{{ route('user.store') }}" enctype="multipart/form-data" id="formUser">
            @csrf
            
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
                                value="{{ old('name') }}" 
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
                                value="{{ old('email') }}" 
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
                                Password
                                <span class="text-danger">*</span>
                            </label>
                            <div class="password-input-wrapper">
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="password"
                                    class="form-control @error('password') is-invalid @enderror" 
                                    value="{{ old('password') }}" 
                                    placeholder="Minimal 8 karakter"
                                    required
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
                                Password minimal 8 karakter
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
                            <select 
                                name="role"
                                id="roleSelect"
                                class="form-select @error('role') is-invalid @enderror"
                                required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                    Admin
                                </option>
                                <option value="penguji" {{ old('role') == 'penguji' ? 'selected' : '' }}>
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
                            <select 
                                id="kecamatanSelect"
                                class="form-select">
                                <option value="">-- Pilih Kecamatan --</option>
                                @foreach ($kecamatans as $kecamatan)
                                    <option value="{{ $kecamatan->id }}">
                                        {{ $kecamatan->nama_kecamatan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Desa (Conditional) -->
                        <div class="input-group-custom conditional-field" id="desaWrapper">
                            <label>
                                <i class="fas fa-map-marker-alt text-danger"></i>
                                Desa
                                <span class="text-danger">*</span>
                            </label>
                            <select 
                                name="id_desas"
                                id="desaSelect"
                                class="form-select @error('id_desas') is-invalid @enderror">
                                <option value="">-- Pilih Desa --</option>
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
                    Simpan Data User
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

    // Role Change Handler
    roleSelect.addEventListener('change', function () {
        const selectedRole = this.value;
        
        // Hanya tampilkan field Kecamatan & Desa untuk role ADMIN saja
        if (selectedRole === 'admin') {
            kecamatanWrapper.classList.add('show');
        } else {
            kecamatanWrapper.classList.remove('show');
            desaWrapper.classList.remove('show');
            kecamatanSelect.value = '';
            desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
        }
    });

    // Kecamatan Change Handler
    kecamatanSelect.addEventListener('change', function () {
        const kecamatanId = this.value;
        
        if (!kecamatanId) {
            desaWrapper.classList.remove('show');
            desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
            return;
        }

        // Add loading state
        desaSelect.classList.add('select-loading');
        desaSelect.disabled = true;
        desaSelect.innerHTML = '<option value="">Loading...</option>';

        // Fetch desa data
        fetch(`/ajax/desa/${kecamatanId}`)
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
                
                desaWrapper.classList.add('show');
            })
            .catch(error => {
                console.error('Error:', error);
                desaSelect.classList.remove('select-loading');
                desaSelect.disabled = false;
                desaSelect.innerHTML = '<option value="">Gagal memuat desa</option>';
                
                alert('Gagal memuat data desa. Silakan coba lagi.');
            });
    });

    // Trigger role change on page load (untuk old input)
    if (roleSelect.value) {
        roleSelect.dispatchEvent(new Event('change'));
    }
});
</script>

@endsection