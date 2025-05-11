<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f4f6f9;
        }
        .sidebar {
            width: 180px;
            height: 100vh;
            background-color: white;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
            padding: 15px;
            position: fixed;
            top: 0;
            left: 0;
        }
        .sidebar-logo {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }
        .sidebar-logo img {
            max-width: 100px;
            max-height: 50px;
            object-fit: contain;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        .sidebar-menu li {
            margin-bottom: 10px;
        }
        .sidebar-menu li a {
            text-decoration: none;
            color: #6c757d;
            display: flex;
            align-items: center;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        .sidebar-menu li a:hover {
            background-color: #f1f3f9;
            color: #4361ee;
        }
        .sidebar-menu li a.active {
            background-color: #e6edff;
            color: #4361ee;
            font-weight: 600;
        }
        .sidebar-menu li a i {
            margin-right: 10px;
            color: #6c757d;
            font-size: 1rem;
        }
        .sidebar-menu li a.active i {
            color: #4361ee;
        }
        .navbar {
            margin-left: 180px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .navbar-dashboard-title {
            font-weight: 600;
            color: #333;
        }
        .navbar-dashboard-subtitle {
            font-size: 0.875rem;
            color: #6c757d;
        }
        .main-content {
            margin-left: 180px;
            margin-top: 70px;
            padding: 20px;
        }
        .sidebar-menu .dropdown-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px;
        border-radius: 8px;
        transition: all 0.3s ease;
        color: #6c757d;
        font-size: 0.9rem;
        text-decoration: none;
    }

    .sidebar-menu .dropdown-toggle:hover {
        background-color: #f1f3f9;
        color: #4361ee;
    }

    .sidebar-menu .dropdown-toggle i {
        margin-right: 10px;
        font-size: 1rem;
        color: #6c757d;
    }

    .sidebar-menu .dropdown-toggle:hover i,
    .sidebar-menu .dropdown.active .dropdown-toggle i {
        color: #4361ee;
    }

    .sidebar-menu .submenu {
        display: none;
        list-style: none;
        padding-left: 20px;
        margin-top: 5px;
    }

    .sidebar-menu .submenu li a {
        padding: 6px 8px;
        font-size: 0.85rem;
        color: #6c757d;
        border-radius: 6px;
        display: block;
    }

    .sidebar-menu .submenu li a:hover {
        background-color: #f1f3f9;
        color: #4361ee;
    }

    .sidebar-menu .dropdown.active .submenu {
        display: block;
    }

    .dropdown-icon {
        font-size: 0.8rem;
        color: #6c757d;
    }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Add SweetAlert2 CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
                <img src="{{ URL::to('Images/Gesa_Logo.png')}}" alt="Logo GESA" style="height: 80px;">
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="/admin/dashboard">
                    <i class="bi bi-grid"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="/admin/articel">
                    <i class="bi bi-journal-text"></i>
                    Artikel
                </a>
            </li>
            <li>
                <a href="/admin/laporan">
                    <i class="bi bi-file-earmark-text"></i>
                    Laporan
                </a>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" onclick="toggleDropdown(event)">
                    <span>
                        <i class="bi bi-bar-chart"></i>
                        Chart
                    </span>
                    <i ></i>
                </a>
                <ul class="submenu">
                    <li><a href="/admin/chart/kekerasan-anak">Kekerasan Anak</a></li>
                    <li><a href="/admin/chart/pernikahan-anak">Pernikahan Anak</a></li>
                    <li><a href="/admin/chart/bullying">Bullying</a></li>
                    <li><a href="/admin/chart/stunting">Stunting</a></li>
                </ul>
            </li>
            <li>
                <a href="/admin/pengaturan"  class="active">
                    <i class="bi bi-gear"></i>
                    Pengaturan
                </a>
            </li>
            <li>
                <a href="/admin/profile">
                    <i class="bi bi-person-circle"></i>
                    Profile
                </a>
            </li>
        </ul>
    </div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <div class="ms-3">
                <div class="navbar-dashboard-title">Pengaturan</div>
                <div class="navbar-dashboard-subtitle">Admin</div>
            </div>
        </div>
        <div class="ms-auto me-3">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </form>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Daftar Admin</h2>
            <a href="{{ route('admin.tambahAdmin') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Tambah Admin
            </a>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
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
                </div> <!-- table-responsive -->
            </div> <!-- card-body -->
        </div> <!-- card -->
    </div> <!-- container-fluid -->
</div> <!-- main-content -->

        <script>
            function toggleDropdown(event) {
                event.preventDefault();
                const dropdown = event.currentTarget.parentElement;
                dropdown.classList.toggle('active');
            }
        </script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // SweetAlert delete confirmation
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
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>

</body>
</html>