@extends('layouts.main2')

@section('content')
<div class="container">
    <h4 class="mb-4">Log Aktivitas</h4>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Aktivitas</th>
                        <th>Deskripsi</th>
                        <th>Pengguna</th>
                        <th>Role</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}</td>

                            <td>
                                <span class="badge bg-secondary text-uppercase text-white">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <td>
                                {{ $log->description ?? '-' }}
                            </td>

                            <td>
                                {{ $log->user_name ?? $log->user->name ?? 'System' }}
                            </td>

                            <td>
                                @if($log->user)
                                    <span class="badge bg-info text-uppercase text-white">
                                        {{ $log->user->role }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                {{ $log->created_at->format('d M Y H:i') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Tidak ada aktivitas
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="card-footer">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
