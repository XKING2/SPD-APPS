@extends('layouts.main2')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header">
            <h5>Edit Seleksi</h5>
        </div>

        <form method="POST" action="{{ route('seleksi.update', $seleksi->id) }}">
            @csrf
            @method('PUT')

            <div class="card-body">

                <div class="form-group">
                    <label>Judul</label>
                    <input type="text"
                           name="judul"
                           class="form-control"
                           value="{{ old('judul', $seleksi->judul) }}"
                           required>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi"
                              class="form-control"
                              rows="3">{{ old('deskripsi', $seleksi->deskripsi) }}</textarea>
                </div>

                <div class="form-group">
                    <label>Tahun</label>
                    <input type="number"
                           name="tahun"
                           class="form-control"
                           value="{{ old('tahun', $seleksi->tahun) }}"
                           required>
                </div>

                <div class="form-group">
                    <label>Desa</label>
                    <select name="id_desas" class="form-control" required>
                        @foreach($desas as $desa)
                            <option value="{{ $desa->id }}"
                                {{ $seleksi->id_desas == $desa->id ? 'selected' : '' }}>
                                {{ $desa->nama_desa }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Kecamatan</label>
                    <select name="id_kecamatans" class="form-control" required>
                        @foreach($kecamatans as $kec)
                            <option value="{{ $kec->id }}"
                                {{ $seleksi->id_kecamatans == $kec->id ? 'selected' : '' }}>
                                {{ $kec->nama_kecamatan }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="card-footer text-right">
                <a href="{{ route('addseleksi') }}" class="btn btn-secondary">
                    Batal
                </a>
                <button class="btn btn-primary">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
