@extends('layouts.main2')

@section('pageheads')
<div class="container-fluid px-4">
    <h1 class="h3 mb-3 fw-bold text-gradient">
        <i class="fas fa-edit me-2"></i>
        Edit Seleksi
    </h1>
</div>
@endsection

@section('content')


<div class="container-fluid seleksi-form-container">
    <div class="form-card">
        <form method="POST" action="{{ route('seleksi.update', $seleksi->id) }}" id="formSeleksi">
            @csrf
            @method('PUT')

            <!-- Judul -->
            <div class="form-section">
                <label>
                    <i class="fas fa-heading"></i>
                    Judul Seleksi
                    <span class="text-danger">*</span>
                </label>
                <input type="text"
                       name="judul"
                       class="form-control @error('judul') is-invalid @enderror"
                       value="{{ old('judul', $seleksi->judul) }}"
                       placeholder="Masukkan judul seleksi"
                       required>
                @error('judul')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div class="form-section">
                <label>
                    <i class="fas fa-align-left"></i>
                    Deskripsi
                </label>
                <textarea name="deskripsi"
                          class="form-control @error('deskripsi') is-invalid @enderror"
                          rows="4"
                          placeholder="Masukkan deskripsi seleksi (opsional)">{{ old('deskripsi', $seleksi->deskripsi) }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Tahun -->
            <div class="form-section">
                <label>
                    <i class="fas fa-calendar-alt"></i>
                    Tahun
                    <span class="text-danger">*</span>
                </label>
                <input type="number"
                       name="tahun"
                       class="form-control @error('tahun') is-invalid @enderror"
                       value="{{ old('tahun', $seleksi->tahun) }}"
                       placeholder="Contoh: 2025"
                       min="2000"
                       max="2100"
                       required>
                @error('tahun')
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="row">
                <!-- Kecamatan -->
                <div class="col-md-6">
                    <div class="form-section">
                        <label>
                            <i class="fas fa-map-marked-alt"></i>
                            Kecamatan
                            <span class="text-danger">*</span>
                        </label>
                        <select name="id_kecamatans" 
                                id="kecamatanSelect" 
                                class="form-select @error('id_kecamatans') is-invalid @enderror" 
                                required>
                            <option value="">-- Pilih Kecamatan --</option>
                            @foreach($kecamatans as $kec)
                                <option value="{{ $kec->id }}"
                                    {{ old('id_kecamatans', $seleksi->id_kecamatans) == $kec->id ? 'selected' : '' }}>
                                    {{ $kec->nama_kecamatan }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kecamatans')
                            <div class="invalid-feedback">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <!-- Desa -->
                <div class="col-md-6">
                    <div class="form-section">
                        <label>
                            <i class="fas fa-map-marker-alt"></i>
                            Desa
                            <span class="text-danger">*</span>
                        </label>
                        <select name="id_desas" 
                                id="desaSelect" 
                                class="form-select @error('id_desas') is-invalid @enderror" 
                                required>
                            <option value="">-- Pilih Desa --</option>
                            @foreach($desas as $desa)
                                <option value="{{ $desa->id }}"
                                    {{ old('id_desas', $seleksi->id_desas) == $desa->id ? 'selected' : '' }}>
                                    {{ $desa->nama_desa }}
                                </option>
                            @endforeach
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

            <!-- Action Buttons -->
            <div class="form-actions">
                <a href="{{ route('addseleksi') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i>
                    Batal
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
    const kecamatanSelect = document.getElementById('kecamatanSelect');
    const desaSelect = document.getElementById('desaSelect');
    
    // Simpan ID desa yang sudah dipilih sebelumnya (untuk edit mode)
    const selectedDesaId = "{{ old('id_desas', $seleksi->id_desas) }}";
    const selectedKecamatanId = "{{ old('id_kecamatans', $seleksi->id_kecamatans) }}";

    // Function untuk load desa berdasarkan kecamatan
    function loadDesaByKecamatan(kecamatanId, selectDesaId = null) {
        if (!kecamatanId) {
            desaSelect.innerHTML = '<option value="">-- Pilih Desa --</option>';
            desaSelect.disabled = false;
            desaSelect.classList.remove('select-loading');
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
                        
                        // Set selected jika ini adalah desa yang dipilih sebelumnya
                        if (selectDesaId && desa.id == selectDesaId) {
                            option.selected = true;
                        }
                        
                        desaSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                desaSelect.classList.remove('select-loading');
                desaSelect.disabled = false;
                desaSelect.innerHTML = '<option value="">Gagal memuat desa</option>';
                
                // SweetAlert2 untuk error (pastikan SweetAlert2 sudah ter-load)
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memuat Data',
                        text: 'Tidak dapat memuat data desa. Silakan coba lagi.',
                        confirmButtonColor: '#667eea',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('Gagal memuat data desa. Silakan coba lagi.');
                }
            });
    }

    // Load desa saat pertama kali halaman dibuka (untuk edit mode)
    if (selectedKecamatanId) {
        loadDesaByKecamatan(selectedKecamatanId, selectedDesaId);
    }

    // Event listener saat kecamatan berubah
    kecamatanSelect.addEventListener('change', function () {
        const kecamatanId = this.value;
        loadDesaByKecamatan(kecamatanId);
    });
});
</script>

@endsection