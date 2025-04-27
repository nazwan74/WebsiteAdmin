<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Artikel</title>
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
        .btn-outline-primary {
            color: #3498db;
            border-color: #3498db;
        }
        .btn-outline-danger {
            color: #e74c3c;
            border-color: #e74c3c;
        }
    </style>
    <!-- Script untuk pencarian dan filter -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const categoryFilter = document.getElementById('category-filter');
        const rows = document.querySelectorAll('#articles-table tbody tr.article-row');
        
        // Fungsi untuk filter tabel
        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedCategory = categoryFilter.value;
            
            rows.forEach(row => {
                const title = row.querySelector('td:first-child').textContent.toLowerCase();
                const category = row.getAttribute('data-kategori');
                
                const matchCategory = selectedCategory === 'all' || category === selectedCategory;
                const matchSearch = title.includes(searchTerm);
                
                if (matchCategory && matchSearch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
        
        // Event listeners
        searchInput.addEventListener('input', filterTable);
        categoryFilter.addEventListener('change', filterTable);
    });
</script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-logo">
                <img src="{{ URL::to('Images/Gesa_Logo.png')}}" alt="Logo GESA" style="height: 80px;">
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="/admin/dashboard" >
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
            <li>
                <a href="#">
                    <i class="bi bi-bar-chart"></i>
                    Stunting
                </a>
            </li>
            <li>
                <a href="/admin/pengaturan">
                    <i class="bi bi-gear"></i>
                    Pengaturan
                </a>
            </li>
        </ul>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <div class="ms-3">
                    <div class="navbar-dashboard-title">Manajemen Artikel</div>
                    <div class="navbar-dashboard-subtitle">Admin</div>
                </div>
            </div>
            <div class="ms-auto d-flex align-items-center gap-2 me-3">
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
        <!-- Kategori Konten Cards Section -->
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
                    <h5><span class="text-danger">✋</span> Kekerasan pada Anak</h5>
                    <p class="text-end mb-0">{{ count(array_filter($articles, function($article) { return $article['articleType'] == 'kekerasan pada anak'; })) }} Artikel</p>
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
    <!-- Daftar Artikel Section -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Daftar Artikel</h2>
            <a href="{{ route('admin.articel.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Tambah Artikel
            </a>
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
                        <option value="kekerasan pada anak">Kekerasan pada Anak</option>
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

            <!-- Tabel Layout Untuk Articles -->
            <div class="table-responsive">
                <table class="table table-hover" id="articles-table">
                    <thead>
                        <tr>
                            <th>Judul Artikel</th>
                            <th>Kategori</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                            <tr class="article-row" data-kategori="{{ $article['articleType'] }}">
                                <td>{{ $article['title'] }}</td>
                                <td>
                                    @php
                                        $badgeClass = '';
                                        switch($article['articleType']) {
                                            case 'pernikahan dini':
                                                $badgeClass = 'bg-pernikahan';
                                                $displayText = 'Pernikahan Anak';
                                                break;
                                            case 'kekerasan pada anak':
                                                $badgeClass = 'bg-kekerasan';
                                                $displayText = 'Kekerasan pada Anak';
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
                                    <a href="{{ route('admin.articel.edit', $article['id']) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.articel.destroy', $article['id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus artikel ini?')">
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
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>