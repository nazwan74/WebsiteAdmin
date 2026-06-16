@extends('admin.layouts.app', [
    'activePage' => 'pengaturan',
    'navbarTitle' => 'Pengaturan Sistem',
    'navbarSubtitle' => 'Manajemen Hak Akses Admin'
])

@section('title', 'Pengaturan')

@section('styles')
<style>
    /* Background & Glass Effects */
    body {
        background-color: #f0f2f5;
        overflow-x: hidden;
    }

    .bg-blobs {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: -1;
        overflow: hidden;
    }

    .blob {
        position: absolute;
        filter: blur(80px);
        opacity: 0.4;
        border-radius: 50%;
        animation: move 20s infinite alternate;
    }

    .blob-1 { width: 400px; height: 400px; background: #FFCB05; top: -100px; right: -100px; }
    .blob-2 { width: 300px; height: 300px; background: #E6B800; bottom: -50px; left: -50px; animation-delay: -5s; }

    @keyframes move {
        from { transform: translate(0, 0) scale(1); }
        to { transform: translate(50px, 100px) scale(1.1); }
    }

    .glass-panel {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 24px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.05);
        padding: 2rem;
    }

    /* Admin Role Badges */
    .admin-role-badge {
        padding: 6px 14px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 1.5px solid transparent;
    }

    .role-super-admin {
        background: #FFFAE6;
        color: #B45309;
        border-color: rgba(255, 203, 5, 0.3);
    }

    .role-admin {
        background: #f8fafc;
        color: #475569;
        border-color: rgba(71, 85, 105, 0.2);
    }

    /* Table Styling */
    .table thead th {
        background: transparent;
        border-bottom: 2px solid rgba(0,0,0,0.05);
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 0.05em;
        padding: 1.25rem 1rem;
    }

    .table tbody tr {
        transition: all 0.2s;
    }

    .table tbody tr:hover {
        background: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
    }

    /* Modal Glassmorphism */
    .modal-content-glass {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 28px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2);
    }

    .modal-header-glass {
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.5rem 2rem;
    }

    .form-control-premium {
        background: rgba(255, 255, 255, 0.5);
        border: 1.5px solid rgba(0,0,0,0.05);
        border-radius: 14px;
        padding: 0.75rem 1.25rem;
        transition: all 0.3s;
    }

    .form-control-premium:focus {
        background: #fff;
        border-color: #FFCB05;
        box-shadow: 0 0 0 4px rgba(255, 203, 5, 0.1);
    }

    .btn-premium {
        border-radius: 14px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 203, 5, 0.2);
    }
</style>
@endsection

@section('content')
<div class="bg-blobs">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
</div>

<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Manajemen Admin</h3>
            <p class="text-muted mb-0">Kelola kredensial dan tingkat otoritas sistem</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary btn-premium shadow-sm border-0" data-bs-toggle="modal" data-bs-target="#modalTambahAdmin" style="background: linear-gradient(135deg, #FFCB05 0%, #E6B800 100%); color: #333;">
                <i class="bi bi-person-plus-fill me-2"></i>Tambah Akun Baru
            </button>
        </div>
    </div>

    <!-- Main Table Container -->
    <div class="glass-panel">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Administrator</th>
                        <th>Level Akses</th>
                        <th>Waktu Registrasi</th>
                        <th class="pe-4 text-end">Opsi Pengelolaan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admins as $admin)
                        <tr>
                            <td class="ps-4 py-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 48px; height: 48px; font-size: 1.1rem; border: 2px solid #fff; background: #FFFAE6 !important; color: #FFCB05 !important;">
                                        {{ strtoupper(substr($admin['email'] ?? 'A', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-6">{{ $admin['email'] ?? '-' }}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">UID: {{ substr($admin['uid'] ?? 'N/A', 0, 12) }}...</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if(($admin['role'] ?? '') === 'super_admin')
                                    <span class="admin-role-badge role-super-admin">
                                        <i class="bi bi-shield-check"></i> Super Administrator
                                    </span>
                                @elseif(($admin['role'] ?? '') === 'admin_artikel')
                                    <span class="admin-role-badge role-admin-artikel" style="background: #E0F2FE; color: #0369A1; border-color: rgba(3, 105, 161, 0.3);">
                                        <i class="bi bi-journal-text"></i> Admin Artikel
                                    </span>
                                @elseif(($admin['role'] ?? '') === 'admin_pengaduan')
                                    <span class="admin-role-badge role-admin-pengaduan" style="background: #FEE2E2; color: #991B1B; border-color: rgba(153, 27, 27, 0.3);">
                                        <i class="bi bi-chat-left-text-fill"></i> Admin Pengaduan
                                    </span>
                                @else
                                    <span class="admin-role-badge role-admin">
                                        <i class="bi bi-person-gear"></i> Standard Admin
                                    </span>
                                @endif
                            </td>
                            <td class="text-muted">
                                <div class="small fw-medium">{{ \Carbon\Carbon::parse($admin['created_at'])->locale('id')->translatedFormat('d M Y') }}</div>
                                <div style="font-size: 0.75rem;">Pukul {{ \Carbon\Carbon::parse($admin['created_at'])->format('H:i') }} WIB</div>
                            </td>
                            <td class="pe-4 text-end">
                                @if (($admin['role'] ?? '') !== 'super_admin')
                                    <form action="{{ route('admin.hapusAdmin', ['uid' => $admin['uid']]) }}" method="POST" class="delete-admin-form d-inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-link text-danger text-decoration-none fw-bold small delete-admin-btn" data-email="{{ $admin['email'] }}">
                                            <i class="bi bi-trash3-fill me-1"></i> Hapus Akses
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-light text-muted p-2 fw-semibold border" style="border-radius: 8px; font-size: 0.7rem;">Sistem Utama</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-people text-muted opacity-25" style="font-size: 4rem;"></i>
                                    <h6 class="mt-3 text-muted">Belum ada akun administrator tambahan.</h6>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('modals')
<!-- Modal Tambah Admin -->
<div class="modal fade" id="modalTambahAdmin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-glass">
            <form method="POST" action="{{ route('admin.storeAdmin') }}">
                @csrf
                <div class="modal-header modal-header-glass">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0">Tambah Administrator</h5>
                        <p class="text-muted small mb-0">Daftarkan akun admin baru untuk sistem</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small ms-1">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 rounded-start-4"><i class="bi bi-envelope" style="color: #FFCB05;"></i></span>
                            <input type="email" name="email" class="form-control form-control-premium border-start-0 ps-0" placeholder="admin@example.com" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small ms-1">Security Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 rounded-start-4"><i class="bi bi-key-fill" style="color: #FFCB05;"></i></span>
                            <input type="password" name="password" class="form-control form-control-premium border-start-0 ps-0" placeholder="Minimal 6 karakter" minlength="6" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small ms-1">Level Otoritas</label>
                        <div class="row g-2">
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="role" id="roleArtikel" value="admin_artikel" checked>
                                <label class="btn btn-outline-warning w-100 py-3 px-1 text-center" for="roleArtikel" style="border-radius: 16px; font-size: 0.8rem;">
                                    <i class="bi bi-journal-text mb-1 d-block fs-4"></i>
                                    Admin Artikel
                                </label>
                            </div>
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="role" id="rolePengaduan" value="admin_pengaduan">
                                <label class="btn btn-outline-warning w-100 py-3 px-1 text-center" for="rolePengaduan" style="border-radius: 16px; font-size: 0.8rem;">
                                    <i class="bi bi-chat-left-text mb-1 d-block fs-4"></i>
                                    Admin Pengaduan
                                </label>
                            </div>
                            <div class="col-4">
                                <input type="radio" class="btn-check" name="role" id="roleSuper" value="super_admin">
                                <label class="btn btn-outline-warning w-100 py-3 px-1 text-center" for="roleSuper" style="border-radius: 16px; font-size: 0.8rem;">
                                    <i class="bi bi-shield-lock mb-1 d-block fs-4"></i>
                                    Super Admin
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light btn-premium flex-grow-1" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-premium flex-grow-1 border-0" style="background: linear-gradient(135deg, #FFCB05 0%, #E6B800 100%); color: #333;">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-outline-warning {
        color: #B45309;
        border-color: #FFCB05;
    }
    .btn-outline-warning:hover, .btn-check:checked + .btn-outline-warning {
        background-color: #FFCB05;
        color: #333;
        border-color: #FFCB05;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- SweetAlert2 Session Notifications ---
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: { popup: 'rounded-4' }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                customClass: { popup: 'rounded-4' }
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan Input',
                text: '{{ $errors->first() }}',
                customClass: { popup: 'rounded-4' }
            });
        @endif

        // --- Delete Confirmation ---
        const deleteButtons = document.querySelectorAll('.delete-admin-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const adminEmail = this.getAttribute('data-email');
                const form = this.closest('.delete-admin-form');

                Swal.fire({
                    title: 'Hapus Akses?',
                    text: `Anda akan menghapus akun "${adminEmail}". Admin ini tidak akan bisa login lagi ke dashboard.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#FFCB05',
                    cancelButtonColor: '#f3f4f6',
                    confirmButtonText: 'Ya, Hapus Akses',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-5 px-3 pb-4',
                        confirmButton: 'btn btn-dark text-white btn-premium mx-2 px-4',
                        cancelButton: 'btn btn-light border btn-premium mx-2 px-4'
                    },
                    buttonsStyling: false
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