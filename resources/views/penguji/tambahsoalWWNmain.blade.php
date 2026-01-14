@extends('layouts.main2')

@section('pageheads')
    <h1 class="h3 mb-4 text-gray-800">Kelola Soal Wawancara</h1>
@endsection

@section('content')
<div class="container-fluid">
    <form id="multiDeleteForm"
          action="{{ route('wwn.multiDelete') }}"
          method="POST">
        @csrf
        @method('DELETE')
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Data Soal Wawancara</h6>

                <div class="d-flex gap-2">
                    <button type="button"
                            class="btn btn-danger"
                            id="btnMultiDelete"
                            disabled>
                        <i class="fas fa-trash"></i> Hapus Terpilih
                    </button>

                    <button class="btn btn-primary"
                            type="button"
                            data-toggle="modal"
                            data-target="#importSoalModal">
                        <i class="fas fa-file-import"></i> Tambah Soal Via Excel
                    </button>

                    <a href="{{ route('createWWN') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Tambah Soal
                    </a>

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center align-middle">
                        <thead class="thead-light">
                            <tr>
                                <th width="40">
                                    <input type="checkbox" id="checkAll">
                                </th>
                                <th width="50">No</th>
                                <th>Subject</th>
                                <th>Pertanyaan</th>
                                <th width="180">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($questions as $index => $item)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox"
                                            name="ids[]"
                                            value="{{ $item->id }}"
                                            class="checkItem">
                                    </td>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->subject }}</td>
                                    <td class="text-start">
                                        {{ Str::limit($item->pertanyaan, 80) }}
                                    </td>
                                    <td>
                                        <a href="{{ route('wawan.edit', Hashids::encode($item->id)) }}"
                                            class="btn btn-sm btn-success btn-edit-orb"
                                            data-url="{{ route('wawan.edit', Hashids::encode($item->id)) }}">
                                                <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        Data soal belum tersedia
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="importSoalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST"
              action="{{route('exam-wawancara.import')}}"
              enctype="multipart/form-data"
              class="modal-content">

            @csrf

            <div class="modal-header">
                <h5 class="modal-title">Import Soal Wawancara</h5>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.querySelectorAll('.btn-edit-orb').forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.preventDefault();

        const url = this.dataset.url;

        Swal.fire({
            title: "Edit soal ini?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Ya, Edit",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url; // ✅ REDIRECT
            }
        });
    });
});
</script>

<script>
    const checkAll = document.getElementById('checkAll');
    const btnDelete = document.getElementById('btnMultiDelete');

    checkAll.addEventListener('change', function () {
        document.querySelectorAll('.checkItem').forEach(cb => {
            cb.checked = this.checked;
        });
        toggleButton();
    });

    document.querySelectorAll('.checkItem').forEach(cb => {
        cb.addEventListener('change', toggleButton);
    });

    function toggleButton() {
        const checked = document.querySelectorAll('.checkItem:checked').length;
        btnDelete.disabled = checked === 0;
    }

    btnDelete.addEventListener('click', function () {
        Swal.fire({
            title: 'Hapus soal terpilih?',
            text: 'Data yang dihapus tidak bisa dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('multiDeleteForm').submit();
            }
        });
    });
</script>
@endsection
