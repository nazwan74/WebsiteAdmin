@extends('admin.layouts.app', [
    'activePage' => 'pengaturan',
    'navbarTitle' => 'Pengaturan',
    'navbarSubtitle' => 'Admin'
])

@section('title', 'Pengaturan')

@section('content')
<div class="container-fluid">
    <!-- Header Daftar Admin -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Admin</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahAdmin">
            <i class="bi bi-plus-circle me-1"></i> Tambah Admin
        </button>   
    </div>

    <!-- Pesan Flash -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <!-- Tabel Daftar Admin -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($admins as $index => $admin)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $admin['email'] ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $admin['role'] === 'super_admin' ? 'primary' : 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $admin['role'] ?? '-')) }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($admin['created_at'])->format('d M Y, H:i') ?? '-' }}</td>
                                <td>
                                    @if ($admin['role'] !== 'super_admin')
                                        <form action="{{ route('admin.hapusAdmin', ['uid' => $admin['uid']]) }}" method="POST" class="delete-admin-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger delete-admin-btn" data-email="{{ $admin['email'] }}">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">Tidak dapat dihapus</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Belum ada admin terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('modals')
<!-- Modal Tambah Admin -->
<div class="modal fade" id="modalTambahAdmin" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        
        <form method="POST" action="{{ route('admin.storeAdmin') }}">
            @csrf

            <div class="modal-header">
            <h5 class="modal-title">Tambah Admin</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
            <!-- Email -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control" minlength="6" required>
                <small class="text-muted">Minimal 6 karakter</small>
            </div>

            <!-- Role -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Role</label>
                <select name="role" class="form-select" required>
                <option value="">-- Pilih Role --</option>
                <option value="admin">Admin</option>
                <option value="super_admin">Super Admin</option>
                </select>
            </div>
            </div>

            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Simpan
            </button>
            </div>

        </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Konfirmasi hapus admin dengan SweetAlert
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-admin-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('.delete-admin-form');
                const adminEmail = this.getAttribute('data-email');

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: `Apakah Anda yakin ingin menghapus admin "${adminEmail}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#6c757d',
                    cancelButtonColor: '#28a745',
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    focusCancel: true,
                    customClass: {
                        confirmButton: 'btn btn-secondary',
                        cancelButton: 'btn btn-success'
                    }
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