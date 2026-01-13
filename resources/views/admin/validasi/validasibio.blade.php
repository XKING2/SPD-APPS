@extends('layouts.main1')

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Header Section -->
    <div class="d-flex align-items-center mb-4">
        <div class="me-3">
            <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3">
                <i class="fas fa-user-circle text-white fs-4"></i>
            </div>
        </div>
        <div>
            <h4 class="mb-1 fw-bold">Detail Biodata Peserta</h4>
            <p class="text-muted mb-0 small">Informasi lengkap data peserta</p>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-4">

            {{-- ================= DATA USER ================= --}}
            <div class="user-info-section mb-5">
                <h5 class="fw-bold mb-4 pb-2 border-bottom">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Informasi Dasar
                </h5>
                
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="info-card p-3 rounded-3 bg-primary h-100 text-white">
                            <label class="small mb-1 d-block text-white">
                                <i class="fas fa-user me-1"></i> Nama Lengkap
                            </label>
                            <p class="mb-0 fw-semibold text-white">
                                {{ $biodata->user->name }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-card p-3 rounded-3 bg-dark h-100 text-white">
                            <label class="small mb-1 d-block text-white">
                                <i class="fas fa-envelope me-1"></i> Email
                            </label>
                            <p class="mb-0 fw-semibold text-white">
                                {{ $biodata->user->email }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-card p-3 rounded-3 bg-secondary h-100 text-white">
                            <label class="small mb-1 d-block text-white">
                                <i class="fas fa-check-circle me-1"></i> Status Validasi
                            </label>

                            @if ($biodata->status === 'valid')
                                <span class="badge bg-success px-3 py-2 rounded-pill text-white">
                                    <i class="fas fa-check-circle me-1"></i> Tervalidasi
                                </span>
                            @elseif ($biodata->status === 'draft')
                                <span class="badge bg-warning px-3 py-2 rounded-pill text-dark">
                                    <i class="fas fa-clock me-1"></i> Belum divalidasi
                                </span>
                            @else
                                <span class="badge bg-danger px-3 py-2 rounded-pill text-white">
                                    <i class="fas fa-times-circle me-1"></i> Ditolak
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= DOKUMEN ================= --}}
            <div class="documents-section">
                <h5 class="fw-bold mb-4 pb-2 border-bottom">
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    Dokumen Pendukung
                </h5>

                <div class="row g-4">

                    <!-- KTP -->
                    <div class="col-md-6 col-lg-4">
                        <div class="document-card h-100">
                            <div class="document-header p-3 bg-primary bg-opacity-10 rounded-top">
                                <h6 class="mb-0 fw-semibold text-white">
                                    <i class="fas fa-id-card text-white me-2"></i>
                                    KTP
                                </h6>
                            </div>
                            <div class="document-body p-3 border border-top-0 rounded-bottom">
                                @if($biodata->ktp)
                                    <div class="doc-preview rounded overflow-hidden position-relative">
                                        <img src="{{ asset('storage/'.$biodata->ktp) }}" 
                                             class="img-fluid w-100" 
                                             style="object-fit: cover; height: 200px;">
                                        <div class="preview-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                            <a href="{{ asset('storage/'.$biodata->ktp) }}" 
                                               target="_blank" 
                                               class="btn btn-light btn-sm rounded-pill">
                                                <i class="fas fa-eye me-1"></i> Lihat
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="doc-empty text-center p-4 bg-light rounded">
                                        <i class="fas fa-file-image text-muted fs-1 mb-2 d-block opacity-25"></i>
                                        <span class="text-muted small">Tidak ada KTP</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Keluarga -->
                    <div class="col-md-6 col-lg-4">
                        <div class="document-card h-100">
                            <div class="document-header p-3 bg-success bg-opacity-10 rounded-top">
                                <h6 class="mb-0 fw-semibold text-white">
                                    <i class="fas fa-users text-white me-2"></i>
                                    Kartu Keluarga
                                </h6>
                            </div>
                            <div class="document-body p-3 border border-top-0 rounded-bottom">
                                @if($biodata->kartu_keluarga)
                                    <div class="doc-preview rounded overflow-hidden position-relative">
                                        <img src="{{ asset('storage/'.$biodata->kartu_keluarga) }}" 
                                             class="img-fluid w-100" 
                                             style="object-fit: cover; height: 200px;">
                                        <div class="preview-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                            <a href="{{ asset('storage/'.$biodata->kartu_keluarga) }}" 
                                               target="_blank" 
                                               class="btn btn-light btn-sm rounded-pill">
                                                <i class="fas fa-eye me-1"></i> Lihat
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="doc-empty text-center p-4 bg-light rounded">
                                        <i class="fas fa-file-image text-muted fs-1 mb-2 d-block opacity-25"></i>
                                        <span class="text-muted small">Tidak ada KK</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Ijazah -->
                    <div class="col-md-6 col-lg-4">
                        <div class="document-card h-100">
                            <div class="document-header p-3 bg-warning bg-opacity-10 rounded-top">
                                <h6 class="mb-0 fw-semibold text-white">
                                    <i class="fas fa-graduation-cap text-white me-2"></i>
                                    Ijazah
                                </h6>
                            </div>
                            <div class="document-body p-3 border border-top-0 rounded-bottom">
                                @if($biodata->ijazah)
                                    <div class="doc-preview rounded overflow-hidden position-relative">
                                        <img src="{{ asset('storage/'.$biodata->ijazah) }}" 
                                             class="img-fluid w-100" 
                                             style="object-fit: cover; height: 200px;">
                                        <div class="preview-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                            <a href="{{ asset('storage/'.$biodata->ijazah) }}" 
                                               target="_blank" 
                                               class="btn btn-light btn-sm rounded-pill">
                                                <i class="fas fa-eye me-1"></i> Lihat
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="doc-empty text-center p-4 bg-light rounded">
                                        <i class="fas fa-file-image text-muted fs-1 mb-2 d-block opacity-25"></i>
                                        <span class="text-muted small">Tidak ada Ijazah</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- SURAT PENDAFTARAN --}}
                    <div class="col-md-6">
                        <div class="document-card h-100">
                            <div class="document-header p-3 bg-info bg-opacity-10 rounded-top">
                                <h6 class="mb-0 fw-semibold text-white">
                                    <i class="fas fa-file-pdf text-white me-2"></i>
                                    Surat Pendaftaran
                                </h6>
                            </div>
                            <div class="document-body p-4 border border-top-0 rounded-bottom">
                                @if($biodata->surat_pendaftaran)
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="pdf-icon bg-danger bg-opacity-10 rounded p-3 me-3">
                                                <i class="fas fa-file-pdf text-white fs-4"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-semibold">Surat Pendaftaran.pdf</p>
                                                <small class="text-muted">Dokumen PDF</small>
                                            </div>
                                        </div>
                                        <button type="button"
                                                class="btn btn-outline-primary btn-sm rounded-pill preview-pdf px-3"
                                                data-src="{{ asset('storage/'.$biodata->surat_pendaftaran) }}"
                                                data-toggle="modal"
                                                data-target="#pdfModal">
                                            <i class="fas fa-eye me-1"></i> Preview
                                        </button>
                                    </div>
                                @else
                                    <div class="doc-empty text-center p-3 bg-light rounded">
                                        <i class="fas fa-file-pdf text-muted fs-1 mb-2 d-block opacity-25"></i>
                                        <p class="text-muted mb-0 small">Tidak ada dokumen</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- CV --}}
                    <div class="col-md-6">
                        <div class="document-card h-100">
                            <div class="document-header p-3 bg-secondary bg-opacity-10 rounded-top">
                                <h6 class="mb-0 fw-semibold text-white">
                                    <i class="fas fa-file-alt text-white me-2"></i>
                                    Curriculum Vitae
                                </h6>
                            </div>
                            <div class="document-body p-4 border border-top-0 rounded-bottom">
                                @if($biodata->cv)
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="pdf-icon bg-danger bg-opacity-10 rounded p-3 me-3">
                                                <i class="fas fa-file-pdf text-white fs-4"></i>
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-semibold">Curriculum Vitae.pdf</p>
                                                <small class="text-muted">Dokumen PDF</small>
                                            </div>
                                        </div>
                                        <button type="button"
                                                class="btn btn-outline-primary btn-sm rounded-pill preview-pdf px-3"
                                                data-src="{{ asset('storage/'.$biodata->cv) }}"
                                                data-toggle="modal"
                                                data-target="#pdfModal">
                                            <i class="fas fa-eye me-1"></i> Preview
                                        </button>
                                    </div>
                                @else
                                    <div class="doc-empty text-center p-3 bg-light rounded">
                                        <i class="fas fa-file-pdf text-muted fs-1 mb-2 d-block opacity-25"></i>
                                        <p class="text-muted mb-0 small">Tidak ada dokumen</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ================= AKSI ================= --}}
            <div class="action-section mt-5 pt-4 border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('validasi.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="fas fa-arrow-left me-2"></i> Kembali
                    </a>

                    @if(!$biodata->is_validated)
                        <form action="{{ route('validasi.submit', $biodata->id) }}"
                              method="POST">
                            @csrf
                            <button class="btn btn-success rounded-pill px-4 shadow-sm btn-Validasi-bio ">
                                <i class="fas fa-check-circle me-2"></i> Validasi Biodata
                            </button>
                        </form>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

{{-- ================= MODAL PREVIEW PDF (SATU KALI SAJA) ================= --}}
<div class="modal fade" id="pdfModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header bg-light border-bottom-0 rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-file-pdf text-danger me-2"></i>
                    Preview Dokumen
                </h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.btn-Validasi-bio');

    buttons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const form = this.closest('form');

            Swal.fire({
                title: "Apakah Anda yakin ingin Validasi Biodata ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Validasi",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>


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