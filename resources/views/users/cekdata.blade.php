@extends('layouts.main')

@section('pageheads')
    <h4 class="mb-1 text-left">Tambah HHHH Diri Anda</h4>
@endsection

@section('content')

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Preview SPJ</h6>
            <a href="#" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card-body">
            <div class="mb-3">
                <h6 class="text-primary mb-1">
                    Nomor SPJ: <strong></strong>
                </h6>
                <p class="mb-0">
                    Tanggal Dibuat:
                </p>
            </div>

            <div id="pdf-viewer" class="mt-3">
                <iframe id="pdf-frame" src="#toolbar=0" width="100%" height="650px"
                    style="border:1px solid #ccc; border-radius:8px;"></iframe>
            </div>

            <div class="mt-3 d-flex justify-content-end">
                <a href="" class="btn btn-danger btn-sm">
                    <i class="fas fa-times"></i> Tutup Preview
                </a>
            </div>
        </div>
    </div>

@endsection
