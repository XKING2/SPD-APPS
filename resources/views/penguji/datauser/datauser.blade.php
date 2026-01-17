@extends('layouts.main2')

@section('pageheads')
    <h1 class="h3 mb-4 text-gray-800"> Data User</h1>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Kelola Data User </h6>
        </div>
        <div class="card-body">
            <!-- Search & Print -->
            <div class="d-flex justify-content-between mb-3">
                <form action="#" method="GET" class="form-inline">
                    <input type="text" name="search" class="form-control form-control-sm mr-2" 
                        placeholder="Cari..." value="">
                    <button type="submit" class="btn btn-sm btn-secondary">Cari</button>
                </form>

                <a href="{{ route('createuser') }}" class="btn btn-info btn-sm px-3 py-1 d-flex align-items-center">
                    <i class="fas fa-plus mr-1"></i> Tambah User
                </a>

                

            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Nama User</th>
                            <th>Desa</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>{{ $user->name ?? '-' }}</td>

                                <td>{{ $user->desas->nama_desa ?? ' Penguji DPMD' }}</td>

                                <td>{{ $user->role ?? '-' }}</td>

                                <td>
                                    @if ($user->status === 'actived')
                                        <span class="badge bg-success text-white">Tervalidasi</span>
                                    @elseif ($user->status === 'verify')
                                        <span class="badge bg-warning text-white">Belum divalidasi</span>
                                    @endif
                                </td>

                                <td class="text-nowrap">
                                    {{-- VALIDASI --}}
                                <form action="{{ route('user.validasi', $user->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button
                                        class="btn btn-sm btn-primary btn-valid-exam"
                                        {{ $user->status !== 'verify' ? 'disabled' : '' }}>
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>

                                    <button type="button"
                                        class="btn btn-sm btn-success btn-edit-user"
                                        data-url="{{ route('user.edit', Hashids::encode($user->id)) }}">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <form action="{{route('user.destroy', $user->id)}}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger btn-delete-user">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Data tidak tersedia
                                </td>
                            </tr>
                            @endforelse
                        </tbody>

                </table>
            </div>

            <div class="d-flex justify-content-center mt-3">

            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        document.querySelectorAll('.btn-edit-user').forEach(button => {
            button.addEventListener('click', function () {

                const url = this.dataset.url;

                Swal.fire({
                    title: 'Edit User?',
                    text: 'Pastikan Anda yakin ingin mengubah data User ini.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Edit',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });

            });
        });

    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.btn-valid-exam');

    buttons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const form = this.closest('form');

            Swal.fire({
                title: "Apakah Anda yakin ingin memvalidasi User ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, validasi",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // 🔥 INI KUNCINYA
                }
            });
        });
    });
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.btn-delete-user');

    buttons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const form = this.closest('form');

            Swal.fire({
                title: "Apakah Anda yakin ingin menghapus User ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Hapus",
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




