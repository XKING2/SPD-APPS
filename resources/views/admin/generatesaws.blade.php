@extends('layouts.main1')

@section('pageheads')
<div class="container-fluid px-4">
    <h1 class="h3 mb-3 fw-bold text-gradient">
        <i class="fas fa-chart-line me-2"></i>
        Generate Ranking SAW
    </h1>
</div>
@endsection

@section('content')


<div class="container-fluid">
    
    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- PILIH SELEKSI --}}
    <div class="selection-card">
        <form method="GET" action="{{ route('generate.admin') }}">
            <label>
                <i class="fas fa-filter me-2"></i>
                Pilih Seleksi
            </label>
            <select name="seleksi_id" class="form-control" onchange="this.form.submit()">
                <option value="">-- Pilih Seleksi --</option>
                @foreach ($seleksis as $seleksi)
                    <option value="{{ $seleksi->id }}"
                        {{ request('seleksi_id') == $seleksi->id ? 'selected' : '' }}>
                        {{ $seleksi->judul }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- INFO SELEKSI & GENERATE BUTTON --}}
    @if ($selectedSeleksi)
        <div class="info-card">
            <h5>
                <i class="fas fa-info-circle me-2"></i>
                {{ $selectedSeleksi->judul }}
            </h5>

            <form action="{{ route('saw.admin.generate', $selectedSeleksi->id) }}"
                method="POST"
                class="form-generate-saw">
                @csrf
                <button type="submit" class="btn-generate btn-generate-Saw">
                    <i class="fas fa-sync-alt me-2"></i>
                    Generate Ranking SAW
                </button>
            </form>
        </div>

        {{-- TABEL 1: KRITERIA --}}
        <div class="table-section">
            <h6>
                <i class="fas fa-list"></i>
                Tabel 1: Kriteria
            </h6>
            <div class="table-responsive">
                <table class="custom-table table">
                    <thead>
                        <tr>
                            <th>Kriteria</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>C1</strong></td>
                            <td>Tes Pengetahuan Umum (TPU)</td>
                        </tr>
                        <tr>
                            <td><strong>C2</strong></td>
                            <td>Tes Praktik Komputer Tingkat Dasar (PRAK)</td>
                        </tr>
                        <tr>
                            <td><strong>C3</strong></td>
                            <td>Wawancara (WWN)</td>
                        </tr>
                        <tr>
                            <td><strong>C4</strong></td>
                            <td>Observasi (ORB)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL 2: BOBOT KRITERIA --}}
        <div class="table-section">
            <h6>
                <i class="fas fa-weight-hanging"></i>
                Tabel 2: Nilai Bobot Kriteria
            </h6>
            <div class="table-responsive">
                <table class="custom-table table">
                    <thead>
                        <tr>
                            <th>Kriteria</th>
                            <th>Nilai Bobot (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>C1 (TPU)</strong></td>
                            <td class="score-value">35</td>
                        </tr>
                        <tr>
                            <td><strong>C2 (PRAK)</strong></td>
                            <td class="score-value">35</td>
                        </tr>
                        <tr>
                            <td><strong>C3 (WWN)</strong></td>
                            <td class="score-value">20</td>
                        </tr>
                        <tr>
                            <td><strong>C4 (ORB)</strong></td>
                            <td class="score-value">10</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL 3: NILAI CRISP --}}
        <div class="table-section">
            <h6>
                <i class="fas fa-table"></i>
                Tabel 3: Penentuan Nilai Crisp
            </h6>
            <div class="table-responsive">
                <table class="custom-table table">
                    <thead>
                        <tr>
                            <th>Rentang Nilai</th>
                            <th>Nilai Crisp</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>0 - 19</td>
                            <td><strong>1</strong></td>
                        </tr>
                        <tr>
                            <td>20 - 39</td>
                            <td><strong>2</strong></td>
                        </tr>
                        <tr>
                            <td>40 - 59</td>
                            <td><strong>3</strong></td>
                        </tr>
                        <tr>
                            <td>60 - 79</td>
                            <td><strong>4</strong></td>
                        </tr>
                        <tr>
                            <td>80 - 100</td>
                            <td><strong>5</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABEL 5: KONVERSI NILAI CRISP --}}
        @if($fuzzyScores->isNotEmpty())
        <div class="table-section">
            <h6>
                <i class="fas fa-exchange-alt"></i>
                Tabel 4: Konversi Nilai Fuzzy ke Nilai Crisp
            </h6>
            <div class="table-responsive">
                <table class="custom-table table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Alternatif (Ai)</th>
                            <th>C1 (TPU)</th>
                            <th>C2 (PRAK)</th>
                            <th>C3 (WWN)</th>
                            <th>C4 (ORB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fuzzyScores as $userId => $scores)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td style="text-align: left; font-weight: 600;">
                                    A{{ $loop->iteration }} ({{ $scores->first()->name }})
                                </td>
                                <td>{{ $scores->firstWhere('type', 'TPU')->score_crisp ?? 0 }}</td>
                                <td>{{ $scores->firstWhere('type', 'PRAK')->score_crisp ?? 0 }}</td>
                                <td>{{ $scores->firstWhere('type', 'WWN')->score_crisp ?? 0 }}</td>
                                <td>{{ $scores->firstWhere('type', 'ORB')->score_crisp ?? 0 }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- NILAI MAX --}}
            @if(!empty($maxValues))
            <div class="max-value-box">
                <p><i class="fas fa-arrow-up me-2"></i><strong>Nilai Maksimum:</strong></p>
                <p>C1 (TPU) = {{ $maxValues['TPU'] }} | C2 (PRAK) = {{ $maxValues['PRAK'] }} | C3 (WWN) = {{ $maxValues['WWN'] }} | C4 (ORB) = {{ $maxValues['ORB'] }}</p>
            </div>
            @endif
        </div>

        {{-- TABEL 6: MATRIKS TERNORMALISASI --}}
        <div class="table-section">
            <h6>
                <i class="fas fa-calculator"></i>
                Tabel 5: Hasil Perhitungan Matriks Ternormalisasi
            </h6>
            <div class="table-responsive">
                <table class="custom-table table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Alternatif (Ai)</th>
                            <th>C1 (TPU)</th>
                            <th>C2 (PRAK)</th>
                            <th>C3 (WWN)</th>
                            <th>C4 (ORB)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fuzzyScores as $userId => $scores)
                            @php
                                $tpuCrisp = $scores->firstWhere('type', 'TPU')->score_crisp ?? 0;
                                $prakCrisp = $scores->firstWhere('type', 'PRAK')->score_crisp ?? 0;
                                $wwnCrisp = $scores->firstWhere('type', 'WWN')->score_crisp ?? 0;
                                $orbCrisp = $scores->firstWhere('type', 'ORB')->score_crisp ?? 0;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td style="text-align: left; font-weight: 600;">
                                    A{{ $loop->iteration }}
                                </td>
                                <td>{{ number_format($maxValues['TPU'] > 0 ? $tpuCrisp / $maxValues['TPU'] : 0, 2) }}</td>
                                <td>{{ number_format($maxValues['PRAK'] > 0 ? $prakCrisp / $maxValues['PRAK'] : 0, 2) }}</td>
                                <td>{{ number_format($maxValues['WWN'] > 0 ? $wwnCrisp / $maxValues['WWN'] : 0, 2) }}</td>
                                <td>{{ number_format($maxValues['ORB'] > 0 ? $orbCrisp / $maxValues['ORB'] : 0, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- TABEL 7: HASIL AKHIR & RANKING --}}
        @if($rankings->isNotEmpty())
        <div class="table-section">
            <h6>
                <i class="fas fa-trophy"></i>
                Tabel 6: Hasil Bobot dan Perankingan (HASIL AKHIR)
            </h6>
            <div class="table-responsive">
                <table class="custom-table table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Alternatif (Ai)</th>
                            <th>Nilai SAW</th>
                            <th>Ranking</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rankings as $rank)
                            @php
                                $badgeClass = 'rank-other';
                                if($rank->peringkat == 1) $badgeClass = 'rank-1';
                                elseif($rank->peringkat == 2) $badgeClass = 'rank-2';
                                elseif($rank->peringkat == 3) $badgeClass = 'rank-3';
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td style="text-align: left; font-weight: 600;">
                                    A{{ $loop->iteration }} ({{ $rank->name }})
                                </td>
                                <td>
                                    <span class="final-score">
                                        {{ number_format($rank->nilai_saw, 2) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="rank-badge {{ $badgeClass }}">
                                        {{ $rank->peringkat }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.form-generate-saw').forEach(form => {

        form.addEventListener('submit', function (e) {
            e.preventDefault(); // ⛔ STOP submit otomatis

            Swal.fire({
                title: 'Generate Ranking SAW?',
                text: 'Proses ini akan menghitung ulang ranking peserta.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Generate',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // ✅ BARU submit di sini
                }
            });
        });

    });

});
</script>

@endsection