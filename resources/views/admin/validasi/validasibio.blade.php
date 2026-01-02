@extends('layouts.main1')

@section('content')
<div class="container-fluid">

    <h4 class="mb-4">Detail Biodata Peserta</h4>

    <div class="card shadow mb-4">
        <div class="card-body">

            {{-- ================= DATA USER ================= --}}
            <table class="table table-bordered mb-4">
                <tr>
                    <th width="200">Nama</th>
                    <td>{{ $biodata->user->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $biodata->user->email }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        @if ($biodata->status === 'valid')
                            <span class="badge badge-success">Tervalidasi</span>
                        @elseif ($biodata->status === 'draft')
                            <span class="badge badge-warning">Belum divalidasi</span>
                        @else
                            <span class="badge badge-danger">Ditolak</span>
                        @endif
                    </td>
                </tr>
            </table>

            {{-- ================= DOKUMEN ================= --}}
            <h5 class="mb-3">Dokumen</h5>

            <div class="row">

                {{-- KTP --}}
                <div class="col-md-6 mb-4">
                    <label>KTP</label>
                    @if($biodata->ktp)
                        <img src="{{ asset('storage/'.$biodata->ktp) }}"
                             class="img-fluid img-thumbnail">
                    @else
                        <p class="text-muted">Tidak ada</p>
                    @endif
                </div>

                {{-- KARTU KELUARGA --}}
                <div class="col-md-6 mb-4">
                    <label>Kartu Keluarga</label>
                    @if($biodata->kartu_keluarga)
                        <img src="{{ asset('storage/'.$biodata->kartu_keluarga) }}"
                             class="img-fluid img-thumbnail">
                    @else
                        <p class="text-muted">Tidak ada</p>
                    @endif
                </div>

                {{-- IJAZAH --}}
                <div class="col-md-6 mb-4">
                    <label>Ijazah</label>
                    @if($biodata->ijazah)
                        <img src="{{ asset('storage/'.$biodata->ijazah) }}"
                             class="img-fluid img-thumbnail">
                    @else
                        <p class="text-muted">Tidak ada</p>
                    @endif
                </div>

                {{-- SURAT PENDAFTARAN --}}
                <div class="col-md-12 mb-3">
                    <label>Surat Pendaftaran</label><br>
                    @if($biodata->surat_pendaftaran)
                        <button type="button"
                                class="btn btn-outline-primary btn-sm preview-pdf"
                                data-src="{{ asset('storage/'.$biodata->surat_pendaftaran) }}"
                                data-toggle="modal"
                                data-target="#pdfModal">
                            Preview Surat Pendaftaran
                        </button>
                    @else
                        <p class="text-muted">Tidak ada</p>
                    @endif
                </div>

                {{-- CV --}}
                <div class="col-md-12 mb-3">
                    <label>CV</label><br>
                    @if($biodata->cv)
                        <button type="button"
                                class="btn btn-outline-primary btn-sm preview-pdf"
                                data-src="{{ asset('storage/'.$biodata->cv) }}"
                                data-toggle="modal"
                                data-target="#pdfModal">
                            Preview CV
                        </button>
                    @else
                        <p class="text-muted">Tidak ada</p>
                    @endif
                </div>

            </div>

            {{-- ================= AKSI ================= --}}
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('validasi.index') }}" class="btn btn-secondary">
                    Kembali
                </a>

                @if(!$biodata->is_validated)
                    <form action="{{ route('validasi.submit', $biodata->id) }}"
                          method="POST"
                          onsubmit="return confirm('Yakin ingin memvalidasi biodata ini?')">
                        @csrf
                        <button class="btn btn-success">
                            Validasi Biodata
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>
</div>

{{-- ================= MODAL PREVIEW PDF (SATU KALI SAJA) ================= --}}
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Preview Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body p-0">
                <iframe id="pdfFrame"
                        src=""
                        width="100%"
                        height="600"
                        style="border:none;"></iframe>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // event berbasis CLICK (PALING STABIL)
    $(document).on('click', '.preview-pdf', function () {
        const src = $(this).data('src');
        $('#pdfFrame').attr('src', src);
    });

    $('#pdfModal').on('hidden.bs.modal', function () {
        $('#pdfFrame').attr('src', '');
    });
</script>
@endpush
