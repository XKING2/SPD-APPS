@extends('layouts.main')

@section('pageheads')
<div class="container">
    <h4 class="mb-1 text-left">Verivikasi Data Diri </h4>
</div>
@endsection

@section('content')
<div class="container">

    @php
        $profileImg = optional($biodata)->profile_img ?? 'img/undraw_profile.svg';
        $kk = optional($biodata)->kartu_keluarga;
        $ktp = optional($biodata)->ktp;
        $ijazah = optional($biodata)->ijazah;
        $cv = optional($biodata)->cv;
        $suratPendaftaran = optional($biodata)->surat_pendaftaran;
    @endphp

    @if($biodata)
        <div class="alert alert-danger text-center fw-bold">
            Anda sudah mengupload biodata sebelumnya.
        </div>
    @endif

    <form action="{{ route('biodata.post') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- FOTO DI BAGIAN TENGAH ATAS -->
        <div class="row justify-content-center mt-2 mb-2">
            <div class="col-md-6 text-center">

                <img id="previewFoto"
                    src="{{ $profileImg == 'img/undraw_profile.svg'
                            ? asset($profileImg)
                            : asset('storage/'.$profileImg) }}"
                    class="rounded-circle border shadow-sm"
                    style="width:160px; height:160px; object-fit:cover;">

                <div class="mt-2">
                    <label class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-camera"></i> Pilih Foto
                        <input type="file" name="profile_img" id="fotoInput" class="d-none" accept="image/*">
                    </label>
                </div>

            </div>
        </div>

        <div class="card shadow-sm rounded-3 mt-2">
            <div class="card-body">
                <div class="row">
                    <!-- Kanan -->
                    <div class="col-md-6">
                        <!-- Kartu Keluarga -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kartu Keluarga</label>
                            <div class="d-flex align-items-center">
                                <input type="file" class="form-control" disabled>
                                <input type="hidden" name="kartu_keluarga" value="{{ $kk }}">
                            </div>
                            <a href="{{ $kk ? asset('storage/'.$kk) : '#' }}"
                                class="btn btn-secondary btn-sm mt-2 {{ $kk ? '' : 'disabled' }}" target="_blank">
                                Lihat Preview KK
                            </a>
                            <span class="status-label {{ optional($biodata)->kartu_keluarga ? 'text-success' : 'text-danger' }}">
                                {{ optional($biodata)->kartu_keluarga ? 'Sudah upload' : 'Belum' }}
                            </span>
                        </div>

                        <!-- KTP -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Upload KTP</label>
                            <div class="d-flex align-items-center">
                                <input type="file" class="form-control" disabled>
                                <input type="hidden" name="ktp" value="{{ $ktp }}">
                            </div>
                            <a href="{{ $ktp ? asset('storage/'.$ktp) : '#' }}"
                                class="btn btn-secondary btn-sm mt-2 {{ $ktp ? '' : 'disabled' }}" target="_blank">
                                Lihat Preview KTP
                            </a>
                            <span class="status-label {{ optional($biodata)->ktp ? 'text-success' : 'text-danger' }}">
                                {{ optional($biodata)->ktp ? 'Sudah upload' : 'Belum' }}
                            </span>
                        </div>

                        <!-- Surat Pendaftaran -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Surat Pendaftaran</label>
                            <div class="d-flex align-items-center">
                                <input type="file" class="form-control" disabled>
                                <input type="hidden" name="surat_pendaftaran" value="{{ $suratPendaftaran }}">
                            </div>
                            <a href="{{ $suratPendaftaran ? asset('storage/'.$suratPendaftaran) : '#' }}"
                                class="btn btn-secondary btn-sm mt-2 {{ $suratPendaftaran ? '' : 'disabled' }}" target="_blank">
                                Lihat Surat Pendaftaran
                            </a>
                            <span class="status-label {{ optional($biodata)->surat_pendaftaran ? 'text-success' : 'text-danger' }}">
                                {{ optional($biodata)->surat_pendaftaran ? 'Sudah upload' : 'Belum' }}
                            </span>
                        </div>
                    </div>


                    <div class="col-md-6">

                        <!-- Ijazah -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Upload Ijazah</label>
                            <div class="d-flex align-items-center">
                                <input type="file" class="form-control" disabled>
                                <input type="hidden" name="ijazah" value="{{ $ijazah }}">
                            </div>
                            <a href="{{ $ijazah ? asset('storage/'.$ijazah) : '#' }}"
                                class="btn btn-secondary btn-sm mt-2 {{ $ijazah ? '' : 'disabled' }}" target="_blank">
                                Lihat Preview Ijazah
                            </a>
                            <span class="status-label {{ optional($biodata)->ijazah ? 'text-success' : 'text-danger' }}">
                                {{ optional($biodata)->ijazah ? 'Sudah upload' : 'Belum' }}
                            </span>
                        </div>

                        <!-- CV -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Upload CV</label>
                            <div class="d-flex align-items-center">
                                <input type="file" class="form-control" disabled>
                                <input type="hidden" name="cv" value="{{ $cv }}">
                            </div>
                            <a href="{{ $cv ? asset('storage/'.$cv) : '#' }}"
                                class="btn btn-secondary btn-sm mt-2 {{ $cv ? '' : 'disabled' }}" target="_blank">
                                Lihat Preview CV
                            </a>
                            <span class="status-label {{ optional($biodata)->cv ? 'text-success' : 'text-danger' }}">
                                {{ optional($biodata)->cv ? 'Sudah upload' : 'Belum' }}
                            </span>
                        </div>
                    </div>

                </div>


                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-success px-4 py-2">
                        <i class="bi bi-save"></i> Ajukan Ke Admin
                    </button>
                </div>

            </div>
        </div>

    </form>

</div>

<script>
    document.getElementById('fotoInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (!file) return;
        document.getElementById('previewFoto').src = URL.createObjectURL(file);
    });
</script>

@endsection
