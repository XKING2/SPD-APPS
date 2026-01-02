@extends('layouts.main2')

@section('pageheads')
<div class="container-fluid px-4">
    <h1 class="h3 mb-4 text-gray-800">Beri Nilai Ujian Perangkat Desa</h1>
</div>
@endsection

@section('content')

<div class="row">
    <!-- KECAMATAN -->
    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">
                Pilih Kecamatan
            </div>
            <div class="card-body d-grid gap-2" id="kecamatan-container">
                @foreach ($kecamatans as $kecamatan)
                    <button type="button"
                        class="kecamatan-btn"
                        data-id="{{ $kecamatan->id }}">
                        <div class="left">
                            <div class="icon">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <div>
                                <div class="name">{{ $kecamatan->nama_kecamatan }}</div>
                                <small>Pilih untuk melihat desa</small>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right arrow"></i>
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- DESA -->
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span id="desa-title">Pilih kecamatan terlebih dahulu</span>
                <button id="reset-btn" class="btn btn-sm btn-outline-secondary d-none">
                    Ganti Kecamatan
                </button>
            </div>

            <div class="card-body">
                <div class="row g-3" id="desa-container"></div>
            </div>
        </div>
    </div>
</div>

{{-- SweetAlert --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    const desaContainer = document.getElementById('desa-container');
    const desaTitle = document.getElementById('desa-title');
    const resetBtn = document.getElementById('reset-btn');
    const kecamatanBtns = document.querySelectorAll('.kecamatan-btn');

    kecamatanBtns.forEach(btn => {
        btn.addEventListener('click', async () => {

            kecamatanBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const id = btn.dataset.id;
            const nama = btn.querySelector('.name').innerText;

            desaTitle.innerText = `Desa di Kecamatan ${nama}`;
            resetBtn.classList.remove('d-none');

            desaContainer.innerHTML = `
                <div class="col-12 text-muted">Memuat data desa...</div>
            `;

            try {
                const res = await fetch(`/desa/by-kecamatan/${id}`);
                const data = await res.json();

                if (!data.length) {
                    desaContainer.innerHTML = `
                        <div class="col-12 alert alert-warning">
                            Tidak ada desa
                        </div>`;
                    return;
                }

                desaContainer.innerHTML = data.map(desa => `
                    <div class="col-md-4">
                        <div class="card desa-card desa-btn"
                             data-id="${desa.id}"
                             style="cursor:pointer">
                            <div class="card-body fw-semibold">
                                ${desa.nama_desa}
                            </div>
                        </div>
                    </div>
                `).join('');

                // EVENT KLIK DESA
                document.querySelectorAll('.desa-btn').forEach(card => {
                    card.addEventListener('click', async () => {
                        const desaId = card.dataset.id;

                        try {
                            const cek = await fetch(`/cek-seleksi-desa/${desaId}`);
                            const result = await cek.json();

                            if (!result.status) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Belum Ada Seleksi',
                                    text: result.message
                                });
                                return;
                            }

                            window.location.href =
                                `/Penguji/Nilai/Praktik/desa/${desaId}`;

                        } catch {
                            Swal.fire({
                                icon: 'error',
                                title: 'Waduhh !!!',
                                text: 'Data Seleksi Untuk Desa ini Belum Di isi'
                            });
                        }
                    });
                });

            } catch {
                desaContainer.innerHTML = `
                    <div class="col-12 alert alert-danger">
                        Gagal memuat desa
                    </div>`;
            }
        });
    });

    resetBtn.addEventListener('click', () => {
        kecamatanBtns.forEach(b => b.classList.remove('active'));
        desaContainer.innerHTML = '';
        desaTitle.innerText = 'Pilih kecamatan terlebih dahulu';
        resetBtn.classList.add('d-none');
    });

});
</script>

@endsection
