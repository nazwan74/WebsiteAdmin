<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Admin</title>
    
    <!-- CSS Eksternal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- JavaScript Eksternal -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Gaya Kustom -->
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f4f6f9;
        }
        
        /* Gaya Sidebar */
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
        
        /* Gaya Dropdown */
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
        
        /* Gaya Navbar */
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
        
        /* Gaya Konten Utama */
        .main-content {
            margin-left: 180px;
            margin-top: 70px;
            padding: 20px;
        }
        
        /* Gaya Container Profile */
        .profile-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 20px;
        }
        
        /* Gaya Header Profile */
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: #e6edff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }
        
        .profile-avatar i {
            font-size: 40px;
            color: #4361ee;
        }
        
        /* Gaya Informasi Profile */
        .profile-info {
            margin-bottom: 30px;
        }
        
        .profile-info-item {
            margin-bottom: 15px;
        }
        
        .profile-info-label {
            font-weight: 600;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .profile-info-value {
            color: #333;
        }
        
        /* Gaya Form Password */
        .password-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
    </style>
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
                    <i></i>
                </a>
                <ul class="submenu">
                    <li><a href="/admin/chart/kekerasan-anak">Kekerasan Anak</a></li>
                    <li><a href="/admin/chart/pernikahan-anak">Pernikahan Anak</a></li>
                    <li><a href="/admin/chart/bullying">Bullying</a></li>
                    <li><a href="/admin/chart/stunting">Stunting</a></li>
                </ul>
            </li>

            @if(Session::get('admin.role') === 'super_admin')
            <li>
                <a href="/admin/pengaturan">
                    <i class="bi bi-gear"></i>
                    Pengaturan
                </a>
            </li>
            @endif
            <li>
                <a href="/admin/profile" class="active">
                    <i class="bi bi-person-circle"></i>
                    Profile
                </a>
            </li>
        </ul>
    </div>

    <!-- Bar Navigasi -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <div class="ms-3">
                    <div class="navbar-dashboard-title">Profile Admin</div>
                    <div class="navbar-dashboard-subtitle">Pengaturan Akun</div>
                </div>
            </div>
            <div class="ms-auto me-3">
                <form method="POST" action="{{ route('admin.logout') }}" id="logoutForm">
                    @csrf
                    <button type="button" class="btn btn-outline-danger" onclick="confirmLogout()">
                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Konten Utama -->
    <div class="main-content">
        <div class="profile-container">
            <!-- Header Profile -->
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="bi bi-person-circle"></i>
                </div>
                <h3>{{ $admin['name'] ?? 'Admin' }}</h3>
                <span class="badge bg-primary">{{ ucfirst($admin['role'] ?? 'admin') }}</span>
            </div>

            <!-- Informasi Profile -->
            <div class="profile-info">
                <div class="profile-info-item">
                    <div class="profile-info-label">Email</div>
                    <div class="profile-info-value">{{ $admin['email'] }}</div>
                </div>
                <div class="profile-info-item">
                    <div class="profile-info-label">Role</div>
                    <div class="profile-info-value">{{ ucfirst($admin['role'] ?? 'admin') }}</div>
                </div>
            </div>

            <!-- Form Ubah Password -->
            <div class="password-form">
                <h4 class="mb-4">Ubah Password</h4>
                
                <!-- Pesan Sukses -->
                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Pesan Error -->
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form Password -->
                <form action="{{ route('admin.profile.update-password') }}" method="POST">
                    @csrf
                    
                    <!-- Field Password Saat Ini -->
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Password Saat Ini</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Field Password Baru -->
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Field Konfirmasi Password -->
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" id="new_password_confirmation" name="new_password_confirmation" required>
                        @error('new_password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Tombol Submit -->
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-key me-2"></i>Ubah Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript Eksternal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script Kustom -->
    <script>
        // Fungsi toggle dropdown
        function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = event.currentTarget.parentElement;
            const allDropdowns = document.querySelectorAll('.dropdown');
            
            // Tutup semua dropdown lainnya
            allDropdowns.forEach(d => {
                if (d !== dropdown) {
                    d.classList.remove('active');
                }
            });
            
            // Toggle dropdown saat ini
            dropdown.classList.toggle('active');
        }

        // Tutup dropdown saat klik di luar
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                if (!dropdown.contains(event.target)) {
                    dropdown.classList.remove('active');
                }
            });
        });

        // Fungsi konfirmasi logout
        function confirmLogout() {
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: 'Apakah Anda yakin ingin keluar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Logout',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logoutForm').submit();
                }
            });
        }

        // Cek akses untuk link pengaturan
        document.addEventListener('DOMContentLoaded', function() {
            const pengaturanLink = document.querySelector('a[href="/admin/pengaturan"]');
            if (pengaturanLink) {
                pengaturanLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Cek apakah user adalah super_admin
                    fetch('/admin/pengaturan', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'error') {
                            Swal.fire({
                                title: 'Akses Ditolak',
                                text: data.message,
                                icon: 'error',
                                confirmButtonColor: '#3085d6'
                            });
                        } else {
                            window.location.href = '/admin/pengaturan';
                        }
                    })
                    .catch(error => {
                        window.location.href = '/admin/pengaturan';
                    });
                });
            }
        });
    </script>
</body>
</html> 