@extends('layouts.main2')

@section('pageheads')
    <h1 class="h3 mb-4 text-gray-800">Kelola Soal TPU</h1>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Data Soal TPU</h6>
            <div class="d-flex justify-content-between mb-3">
                <button class="btn btn-primary"
                        data-toggle="modal"
                        data-target="#importSoalModal">
                    <i class="fas fa-file-import"></i> Tambah Ujian
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Subject</th>
                            <th>Pertanyaan</th>
                            <th width="180">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($questions as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->subject }}</td>
                                <td class="text-start">
                                    {{ Str::limit($item->pertanyaan, 80) }}
                                </td>
                                <td>
                                    <a href="{{ route('TPU.edit', $item->id) }}"
                                       class="btn btn-sm btn-success">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('TPU.destroy', $item->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin hapus soal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    Data soal belum tersedia
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="importSoalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST"
              action="{{route('exam-questions.import')}}"
              enctype="multipart/form-data"
              class="modal-content">

            @csrf

            <div class="modal-header">
                <h5 class="modal-title">Import Soal TPU</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <h6 class="mb-3 text-primary">Import Soal</h6>

                <div class="form-group">
                    <label>File Excel Soal <span class="text-danger">*</span></label>
                    <input type="file"
                           name="excel"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>ZIP Gambar (Opsional)</label>
                    <input type="file"
                           name="zip"
                           class="form-control">
                </div>

                <small class="text-muted">
                    Nama file gambar di ZIP harus sama dengan kolom
                    <code>image_name</code> di Excel.
                </small>

            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-file-import"></i> Import Soal
                </button>
                <button type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">
                    Batal
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
