<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    </style>
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
                <a href="/admin/dashboard" class="active">
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
                    <div class="navbar-dashboard-title">Dashboard Overview</div>
                    <div class="navbar-dashboard-subtitle">Welcome back, Admin</div>
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
        <div class="row">
        <div class="col-md-3 mb-4">
            <div class="bg-white shadow-sm rounded p-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-people-fill text-primary me-2" style="font-size: 1.5rem;"></i>
                    <h6 class="mb-0 text-muted">Total Pengguna</h6>
                </div>
                <h3 class="fw-bold">{{ $totalUsers }}</h3>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="bg-white shadow-sm rounded p-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-file-earmark-text-fill text-warning me-2" style="font-size: 1.5rem;"></i>
                    <h6 class="mb-0 text-muted">Laporan Masuk</h6>
                </div>
                <h3 class="fw-bold">{{ $totalLaporan }}</h3>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="bg-white shadow-sm rounded p-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-check-circle-fill text-success me-2" style="font-size: 1.5rem;"></i>
                    <h6 class="mb-0 text-muted">Kasus Selesai</h6>
                </div>
                <h3 class="fw-bold">{{ $totalSelesai }}</h3>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="bg-white shadow-sm rounded p-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-book-fill me-2" style="font-size: 1.5rem; color: #8e44ad;"></i>
                    <h6 class="mb-0 text-muted">Artikel Edukasi</h6>
                </div>
                <h3 class="fw-bold">{{ $totalArticles }}</h3>
            </div>
        </div>
    </div>

        <!-- Bagian Chart Visualisasi -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="bg-white shadow-sm rounded p-3">
                    <h6 class="fw-bold mb-3">Top 4 Kategori Kasus</h6>
                    <canvas id="kategoriChart" height="300"></canvas>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="bg-white shadow-sm rounded p-3">
                    <h6 class="fw-bold mb-3">Top Daerah & Kategori Terbanyak</h6>
                    <canvas id="daerahKategoriChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Chart -->
    <script>
        // === Kategori Chart ===
        new Chart(document.getElementById('kategoriChart'), {
            type: 'pie',
            data: {
                labels: {!! json_encode(array_keys($topKategori)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($topKategori)) !!},
                    backgroundColor: ['#4e79a7', '#f28e2c', '#e15759', '#76b7b2'],
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.parsed} laporan`;
                            }
                        }
                    }
                }
            }
        });

        // === Daerah & Kategori Chart ===
        const daerahData = @json($topDaerahKategori);
        const daerahLabels = Object.keys(daerahData);
        const laporanData = daerahLabels.map(d => daerahData[d].total);
        const kategoriData = daerahLabels.map(d => daerahData[d].kategori_terbanyak);

        new Chart(document.getElementById('daerahKategoriChart'), {
            type: 'bar',
            data: {
                labels: daerahLabels,
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: laporanData,
                    backgroundColor: '#59a14f',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                return `Laporan: ${laporanData[index]}, Kategori: ${kategoriData[index]}`;
                            }
                        }
                    },
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, precision: 0 },
                        title: {
                            display: true,
                            text: 'Jumlah Laporan'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Nama Daerah'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>