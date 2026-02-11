<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Artikel Baru</title>
    
    <!-- CSS Eksternal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    
    <!-- JavaScript Eksternal -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Gaya Kustom -->
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f4f6f9;
        }

        /* --- LAYOUT STYLES (Sidebar, Navbar, Main Content) --- */
        
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
            z-index: 1050;
            transition: transform 0.3s ease;
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1040;
        }

        .sidebar-overlay.active { display: block; }
        
        /* Hamburger Button */
        .hamburger-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #333;
            cursor: pointer;
            padding: 0.5rem;
            margin-right: 1rem;
        }
        .hamburger-btn:hover { color: #4361ee; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .navbar { margin-left: 0 !important; }
            .main-content { margin-left: 0 !important; }
            .hamburger-btn { display: block; }
        }
        
        .sidebar-logo {
            display: flex; justify-content: center; align-items: center; margin-bottom: 20px;
        }
        .sidebar-logo img {
            max-width: 100px; max-height: 50px; object-fit: contain;
        }
        
        .sidebar-menu { list-style: none; padding: 0; }
        .sidebar-menu li { margin-bottom: 10px; }
        .sidebar-menu li a {
            text-decoration: none; color: #6c757d; display: flex; align-items: center; padding: 8px;
            border-radius: 8px; transition: all 0.3s ease; font-size: 0.9rem;
        }
        .sidebar-menu li a:hover { background-color: #f1f3f9; color: #4361ee; }
        .sidebar-menu li a.active { background-color: #e6edff; color: #4361ee; font-weight: 600; }
        .sidebar-menu li a i { margin-right: 10px; color: #6c757d; font-size: 1rem; }
        .sidebar-menu li a.active i { color: #4361ee; }

        /* Navbar */
        .navbar {
            margin-left: 180px; background-color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: margin-left 0.3s ease;
        }
        .navbar-dashboard-title { font-weight: 600; color: #333; }
        .navbar-dashboard-subtitle { font-size: 0.875rem; color: #6c757d; }

        /* Main Content */
        .main-content {
            margin-left: 180px; margin-top: 70px; padding: 20px;
            transition: margin-left 0.3s ease; position: relative; z-index: 1;
        }

        /* --- EXISTING STYLES --- */
        
        /* Gaya Card */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .card-title {
            color: #333;
            font-weight: 600;
        }
        
        /* Gaya Form */
        .form-label {
            color: #495057;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        /* Gaya Tombol */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background-color: #4361ee;
            border-color: #4361ee;
        }
        
        .btn-primary:hover {
            background-color: #3a56d4;
            border-color: #3a56d4;
        }
        
        .btn-danger {
            background-color: #e74c3c;
            border-color: #e74c3c;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }
        
        /* Gaya Text Muted */
        .text-muted {
            font-size: 0.875rem;
            color: #6c757d;
        }
        
        /* Gaya Invalid Feedback */
        .invalid-feedback {
            font-size: 0.875rem;
            color: #dc3545;
        }
        
        /* Gaya Summernote */
        .note-editor {
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        
        .note-editor.note-frame {
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <img src="{{ URL::to('Images/Gesa_Logo.png')}}" alt="Logo GESA" style="height: 80px;">
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="/admin/dashboard">
                    <i class="bi bi-grid"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="/admin/articel" class="active">
                    <i class="bi bi-journal-text"></i> Artikel
                </a>
            </li>
            <li>
                <a href="/admin/laporan">
                    <i class="bi bi-file-earmark-text"></i> Laporan
                </a>
            </li>
            
            @if(Session::get('admin.role') === 'super_admin')
            <li>
                <a href="/admin/pengaturan">
                    <i class="bi bi-gear"></i> Pengaturan
                </a>
            </li>
            @endif
            <li>
                <a href="/admin/profile">
                    <i class="bi bi-person-circle"></i> Profile
                </a>
            </li>
        </ul>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container-fluid">
            <button class="hamburger-btn" id="hamburgerBtn" type="button">
                <i class="bi bi-list"></i>
            </button>
            <div class="d-flex align-items-center">
                <div class="ms-3">
                    <div class="navbar-dashboard-title">Tambah Artikel</div>
                    <div class="navbar-dashboard-subtitle">Manajemen Artikel</div>
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
        <div class="container-fluid">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Tambah Artikel Baru</h3>
                    
                    <!-- Form Tambah Artikel -->
                    <form action="{{ route('admin.articel.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
    
                        <!-- Thumbnail -->
                        <div class="mb-3">
                            <label for="photoUrl" class="form-label fw-semibold">Tambah Thumbnail Artikel</label>
                            <input class="form-control @error('photoUrl') is-invalid @enderror" type="file" id="photoUrl" name="photoUrl" accept="image/*" required>
                            <small class="text-muted">Ukuran Maximum File: 2MB</small>
                            @error('photoUrl')
                                <div class="invalid-feedback">
                                    @if($message == 'The photo url field is required.')
                                        Thumbnail artikel harus diisi
                                    @elseif($message == 'The photo url must be an image.')
                                        File yang diunggah harus berupa gambar
                                    @elseif($message == 'The photo url must not be greater than 2048 kilobytes.')
                                        Ukuran file tidak boleh lebih dari 2MB
                                    @else
                                        {{ $message }}
                                    @endif
                                </div>
                            @enderror
                        </div>
    
                        <!-- Kategori -->
                        <div class="mb-3">
                            <label for="articleType" class="form-label fw-semibold">Pilih Kategori Artikel</label>
                            <select class="form-select @error('articleType') is-invalid @enderror" id="articleType" name="articleType" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="stunting">Stunting</option>
                                <option value="bullying">Bullying</option>
                                <option value="pernikahan dini">Pernikahan Anak</option>
                                <option value="kekerasan anak">Kekerasan Anak</option>
                            </select>
                            @error('articleType')
                                <div class="invalid-feedback">
                                    @if($message == 'The article type field is required.')
                                        Kategori artikel harus dipilih
                                    @else
                                        {{ $message }}
                                    @endif
                                </div>
                            @enderror
                        </div>
    
                        <!-- Judul -->
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Tambah Judul Artikel</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" placeholder="Tulis Judul Artikel Disini" required>
                            @error('title')
                                <div class="invalid-feedback">
                                    @if($message == 'The title field is required.')
                                        Judul artikel harus diisi
                                    @else
                                        {{ $message }}
                                    @endif
                                </div>
                            @enderror
                        </div>
    
    
    
                        <!-- Konten -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">Tambah Konten Artikel</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" placeholder="Tulis Isi Artikel Disini..." required></textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    @if($message == 'The description field is required.')
                                        Konten artikel harus diisi
                                    @else
                                        {{ $message }}
                                    @endif
                                </div>
                            @enderror
                        </div>
    
                        <!-- Tombol Aksi -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.articel.index') }}" class="btn btn-danger">
                                <i class="bi bi-arrow-left"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Artikel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Eksternal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    <!-- Script Kustom -->
    <script>
        // --- SIDEBAR & NAVBAR SCRIPTS ---
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }
            function closeSidebar() {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }

            if (hamburgerBtn) hamburgerBtn.addEventListener('click', toggleSidebar);
            if (overlay) overlay.addEventListener('click', closeSidebar);
            
            // Close sidebar when clicking menu links on mobile
            document.querySelectorAll('.sidebar-menu a').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) closeSidebar();
                });
            });
        });

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
                if (result.isConfirmed) document.getElementById('logoutForm').submit();
            });
        }

        // Inisialisasi Summernote Editor
        $(document).ready(function() {
            $('#description').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });


        });
    </script>
</body>
</html>
