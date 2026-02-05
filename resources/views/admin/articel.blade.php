<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Artikel</title>
    
    <!-- CSS Eksternal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
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
            z-index: 1000;
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
            z-index: 999;
        }

        .sidebar-overlay.active {
            display: block;
        }

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

        .hamburger-btn:hover {
            color: #4361ee;
        }

        /* Responsive: Mobile */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .navbar {
                margin-left: 0 !important;
            }

            .main-content {
                margin-left: 0 !important;
            }

            .hamburger-btn {
                display: block;
            }
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
            transition: margin-left 0.3s ease;
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
            transition: margin-left 0.3s ease;
        }
        
        /* Warna Latar Belakang */
        .bg-light-pink {
            background-color: #ffeef2;
        }
        
        .bg-light-red {
            background-color: #fff0f0;
        }
        
        .bg-light-orange {
            background-color: #fff9e6;
        }
        
        .bg-light-green {
            background-color: #eafaf2;
        }
        
        /* Gaya Badge */
        .badge.bg-pernikahan {
            background-color: #ffeef2;
            color: #212529;
        }
        
        .badge.bg-kekerasan {
            background-color: #fff0f0;
            color: #212529;
        }
        
        .badge.bg-bullying {
            background-color: #fff9e6;
            color: #212529;
        }
        
        .badge.bg-stunting {
            background-color: #eafaf2;
            color: #212529;
        }
        
        /* Gaya Tombol */
        .btn-outline-primary {
            color: #3498db;
            border-color: #3498db;
        }
        
        .btn-outline-danger {
            color: #e74c3c;
            border-color: #e74c3c;
        }

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 1rem;
            padding: 0.75rem 0;
        }
        .pagination-info {
            color: #6c757d;
            font-size: 0.9rem;
        }
        .pagination-wrapper .pagination {
            margin: 0;
        }
        .pagination-wrapper .page-link {
            padding: 0.4rem 0.75rem;
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
                    <i class="bi bi-grid"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="/admin/articel" class="active">
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
            @if(Session::get('admin.role') === 'super_admin')
            <li>
                <a href="/admin/pengaturan">
                    <i class="bi bi-gear"></i>
                    Pengaturan
                </a>
            </li>
            @endif
            <li>
                <a href="/admin/profile">
                    <i class="bi bi-person-circle"></i>
                    Profile
                </a>
            </li>
        </ul>
    </div>

    <!-- Bar Navigasi -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container-fluid">
            <button class="hamburger-btn" id="hamburgerBtn" type="button">
                <i class="bi bi-list"></i>
            </button>
            <div class="d-flex align-items-center">
                <div class="ms-3">
                    <div class="navbar-dashboard-title">Manajemen Artikel</div>
                    <div class="navbar-dashboard-subtitle">Admin</div>
                </div>
            </div>
            <div class="ms-auto d-flex align-items-center gap-2 me-3">
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
        <!-- Bagian Kartu Kategori Konten -->
        <h2>Kategori Konten</h2>
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-light-pink text-dark h-100">
                    <div class="card-body">
                        <h5><span class="text-danger">❤</span> Pernikahan Anak</h5>
                        <p class="text-end mb-0">{{ count(array_filter($articles, function($article) { return $article['articleType'] == 'pernikahan dini'; })) }} Artikel</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light-red text-dark h-100">
                    <div class="card-body">
                        <h5><span class="text-danger">✋</span> Kekerasan Anak</h5>
                        <p class="text-end mb-0">{{ count(array_filter($articles, function($article) { return $article['articleType'] == 'kekerasan anak'; })) }} Artikel</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light-orange text-dark h-100">
                    <div class="card-body">
                        <h5><span class="text-warning">😣</span> Bullying</h5>
                        <p class="text-end mb-0">{{ count(array_filter($articles, function($article) { return $article['articleType'] == 'bullying'; })) }} Artikel</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light-green text-dark h-100">
                    <div class="card-body">
                        <h5><span class="text-success">↑</span> Stunting</h5>
                        <p class="text-end mb-0">{{ count(array_filter($articles, function($article) { return $article['articleType'] == 'stunting'; })) }} Artikel</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian Daftar Artikel -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2>Daftar Artikel</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.articel.downloadList') }}" id="downloadListBtn" class="btn btn-success">
                        <i class="bi bi-download me-1"></i>Download List
                    </a>
                    <a href="{{ route('admin.articel.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>Tambah Artikel
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Cari artikel..." id="search-input">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <select class="form-select float-end" style="width: auto;" id="category-filter">
                            <option selected value="all">Semua Kategori</option>
                            <option value="pernikahan dini">Pernikahan Anak</option>
                            <option value="kekerasan anak">Kekerasan Anak</option>
                            <option value="bullying">Bullying</option>
                            <option value="stunting">Stunting</option>
                        </select>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Tata Letak Tabel Untuk Artikel -->
                <div class="table-responsive">
                    <table class="table table-hover" id="articles-table">
                        <thead>
                            <tr>
                                <th>Judul Artikel</th>
                                <th>Kategori</th>
                                <th>Tanggal Rilis</th>
                                <th>Tanggal Edit</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($articles as $article)
                                <tr class="article-row"
                                data-kategori="{{ $article['articleType'] }}"
                                data-description="{{ strtolower($article['description']) }}"
                                data-hashtags="{{ isset($article['hashtags']) ? strtolower($article['hashtags']) : '' }}">
                                    <td>{{ $article['title'] }}</td>
                                    <td>
                                        @php
                                            $badgeClass = '';
                                            switch($article['articleType']) {
                                                case 'pernikahan dini':
                                                    $badgeClass = 'bg-pernikahan';
                                                    $displayText = 'Pernikahan Anak';
                                                    break;
                                                case 'kekerasan anak':
                                                    $badgeClass = 'bg-kekerasan';
                                                    $displayText = 'Kekerasan Anak';
                                                    break;
                                                case 'bullying':
                                                    $badgeClass = 'bg-bullying';
                                                    $displayText = 'Bullying';
                                                    break;
                                                case 'stunting':
                                                    $badgeClass = 'bg-stunting';
                                                    $displayText = 'Stunting';
                                                    break;
                                                default:
                                                    $badgeClass = 'bg-secondary';
                                                    $displayText = ucfirst($article['articleType']);
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $displayText }}</span>
                                    </td>
                                    <td>
                                        @if(isset($article['releasedDate']))
                                            @php
                                                if ($article['releasedDate'] instanceof Illuminate\Support\Carbon) {
                                                    $date = $article['releasedDate']->format('d M Y');
                                                } elseif (is_string($article['releasedDate'])) {
                                                    $date = date('d M Y', strtotime($article['releasedDate']));
                                                } else {
                                                    $date = $article['releasedDate']->get()->format('d M Y');
                                                }
                                            @endphp
                                            {{ $date }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($article['updateDate']))
                                            @php
                                                if ($article['updateDate'] instanceof Illuminate\Support\Carbon) {
                                                    $updateDate = $article['updateDate']->format('d M Y');
                                                } elseif (is_string($article['updateDate'])) {
                                                    $updateDate = date('d M Y', strtotime($article['updateDate']));
                                                } else {
                                                    $updateDate = $article['updateDate']->get()->format('d M Y');
                                                }
                                            @endphp
                                            <span class="text-info">
                                                <i class="bi bi-pencil-square"></i> {{ $updateDate }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.articel.edit', $article['id']) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.articel.destroy', $article['id']) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger delete-article-btn" data-title="{{ $article['title'] }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Tidak ada artikel yang tersedia.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div id="paginationWrapper" class="pagination-wrapper d-none">
                    <div class="pagination-info" id="paginationInfo"></div>
                    <nav aria-label="Navigasi halaman artikel">
                        <ul class="pagination mb-0" id="paginationNav"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Eksternal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script Kustom -->
    <script>
        // Hamburger Menu Toggle
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

            if (hamburgerBtn) {
                hamburgerBtn.addEventListener('click', toggleSidebar);
            }

            if (overlay) {
                overlay.addEventListener('click', closeSidebar);
            }

            // Close sidebar when clicking on menu links (mobile)
            const menuLinks = document.querySelectorAll('.sidebar-menu a');
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) {
                        closeSidebar();
                    }
                });
            });
        });

        // Fungsi toggle dropdown
        function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = event.currentTarget.parentElement;
            dropdown.classList.toggle('active');
        }

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

        // Fungsi saat dokumen siap
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const categoryFilter = document.getElementById('category-filter');
            const rows = document.querySelectorAll('#articles-table tbody tr.article-row');

            const perPage = 10;
            let currentPage = 1;

            function applyFilters() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedCategory = categoryFilter.value;

                rows.forEach(row => {
                    const title = row.querySelector('td:first-child').textContent.toLowerCase();
                    const description = row.getAttribute('data-description') || '';
                    const hashtags = row.getAttribute('data-hashtags') || '';
                    const category = row.getAttribute('data-kategori');

                    const matchCategory = selectedCategory === 'all' || category === selectedCategory;
                    const matchSearch = title.includes(searchTerm) ||
                        description.includes(searchTerm) ||
                        hashtags.includes(searchTerm);

                    row.style.display = (matchCategory && matchSearch) ? '' : 'none';
                });

                const visibleRows = Array.from(rows).filter(r => r.style.display !== 'none');
                const totalVisible = visibleRows.length;
                const totalPages = Math.max(1, Math.ceil(totalVisible / perPage));
                if (currentPage > totalPages) currentPage = totalPages;
                const start = (currentPage - 1) * perPage;
                const end = start + perPage;
                visibleRows.forEach((row, i) => {
                    row.style.display = (i >= start && i < end) ? '' : 'none';
                });

                updatePaginationUI(totalVisible, totalPages);
                updateDownloadLink();
            }

            function updatePaginationUI(totalVisible, totalPages) {
                const wrapper = document.getElementById('paginationWrapper');
                const infoEl = document.getElementById('paginationInfo');
                const navEl = document.getElementById('paginationNav');
                if (!wrapper || !infoEl || !navEl) return;
                if (totalVisible === 0) {
                    wrapper.classList.add('d-none');
                    return;
                }
                wrapper.classList.remove('d-none');
                const start = (currentPage - 1) * perPage + 1;
                const end = Math.min(currentPage * perPage, totalVisible);
                infoEl.textContent = 'Menampilkan ' + start + '–' + end + ' dari ' + totalVisible + ' artikel';

                navEl.innerHTML = '';
                function addPageItem(label, pageNum, disabled, active) {
                    const li = document.createElement('li');
                    li.className = 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : '');
                    const a = document.createElement('a');
                    a.className = 'page-link';
                    a.href = '#';
                    a.textContent = label;
                    if (!disabled) {
                        a.addEventListener('click', function(e) {
                            e.preventDefault();
                            currentPage = pageNum;
                            applyFilters();
                        });
                    }
                    li.appendChild(a);
                    navEl.appendChild(li);
                }
                addPageItem('Sebelumnya', currentPage - 1, currentPage <= 1, false);
                const maxButtons = 5;
                let from = Math.max(1, currentPage - Math.floor(maxButtons / 2));
                let to = Math.min(totalPages, from + maxButtons - 1);
                if (to - from + 1 < maxButtons) from = Math.max(1, to - maxButtons + 1);
                for (let p = from; p <= to; p++) addPageItem(p, p, false, p === currentPage);
                addPageItem('Selanjutnya', currentPage + 1, currentPage >= totalPages, false);
            }

            function updateDownloadLink() {
                const baseUrl = '{{ route("admin.articel.downloadList") }}';
                const params = [];
                const cat = categoryFilter.value;
                if (cat && cat !== 'all') params.push('kategori=' + encodeURIComponent(cat));
                const searchVal = searchInput.value.trim();
                if (searchVal) params.push('search=' + encodeURIComponent(searchVal));
                const url = params.length ? baseUrl + '?' + params.join('&') : baseUrl;
                const btn = document.getElementById('downloadListBtn');
                if (btn) btn.setAttribute('href', url);
            }

            searchInput.addEventListener('input', function() {
                currentPage = 1;
                applyFilters();
            });
            categoryFilter.addEventListener('change', function() {
                currentPage = 1;
                applyFilters();
            });

            updateDownloadLink();
            applyFilters();

            // Konfirmasi hapus dengan SweetAlert
            const deleteButtons = document.querySelectorAll('.delete-article-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    const articleTitle = this.getAttribute('data-title');

                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        text: `Apakah Anda yakin ingin menghapus artikel "${articleTitle}"?`,
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

            // Pemeriksaan akses untuk link pengaturan
            const pengaturanLink = document.querySelector('a[href="/admin/pengaturan"]');
            if (pengaturanLink) {
                pengaturanLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Periksa apakah user adalah super_admin
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