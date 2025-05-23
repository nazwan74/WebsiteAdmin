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
        /* Chart Dropdown Style */
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
                <form method="POST" action="{{ route('admin.logout') }}" id="logoutForm">
                    @csrf
                    <button type="button" class="btn btn-outline-danger" onclick="confirmLogout()">
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
                <h3 class="fw-bold" id="totalUsers">1</h3>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="bg-white shadow-sm rounded p-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-file-earmark-text-fill text-warning me-2" style="font-size: 1.5rem;"></i>
                    <h6 class="mb-0 text-muted">Laporan Masuk</h6>
                </div>
                <h3 class="fw-bold" id="totalLaporan">1</h3>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="bg-white shadow-sm rounded p-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-check-circle-fill text-success me-2" style="font-size: 1.5rem;"></i>
                    <h6 class="mb-0 text-muted">Kasus Selesai</h6>
                </div>
                <h3 class="fw-bold" id="totalSelesai">1</h3>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="bg-white shadow-sm rounded p-3">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-book-fill me-2" style="font-size: 1.5rem; color: #8e44ad;"></i>
                    <h6 class="mb-0 text-muted">Artikel Edukasi</h6>
                </div>
                <h3 class="fw-bold" id="totalArticles">1</h3>
            </div>
        </div>
    </div>

        <!-- Bagian Chart Visualisasi -->
        <!-- Chart Section -->
        <div class="row g-3 mt-3">
            <!-- Top 4 Kategori Kasus -->
            <div class="col-md-4">
                <div class="bg-white shadow-sm rounded p-3" style="height: 350px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 small">Top 4 Kategori Kasus</h6>
                        <span class="badge bg-primary">{{ array_sum(array_values($topKategori)) }} Total Kasus</span>
                    </div>
                    <div style="height: 280px;">
                        <canvas id="kategoriChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Daerah & Kategori -->
            <div class="col-md-4">
                <div class="bg-white shadow-sm rounded p-3" style="height: 350px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 small">Top Daerah & Kategori Terbanyak</h6>
                        <span class="badge bg-success">{{ array_sum(array_map(function($item) { return $item['total']; }, $topDaerahKategori)) }} Total Laporan</span>
                    </div>
                    <div style="height: 280px;">
                        <canvas id="daerahKategoriChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Status Laporan -->
            <div class="col-md-4">
                <div class="bg-white shadow-sm rounded p-3" style="height: 350px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0 small">Status Laporan</h6>
                        <div class="d-flex gap-2">
                            <span class="badge bg-success">{{ $totalSelesai }} Selesai</span>
                            <span class="badge bg-warning">{{ $totalDiproses }} Diproses</span>
                            <span class="badge bg-secondary">{{ $totalBaru }} Baru</span>
                            <span class="badge bg-danger">{{ $totalDitolak }} Ditolak</span>
                        </div>
                    </div>
                    <div style="height: 280px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleDropdown(event) {
            event.preventDefault();
            const dropdown = event.currentTarget.parentElement;
            dropdown.classList.toggle('active');
        }

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

        // Add access check for pengaturan link
        document.addEventListener('DOMContentLoaded', function() {
            const pengaturanLink = document.querySelector('a[href="/admin/pengaturan"]');
            if (pengaturanLink) {
                pengaturanLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Check if user is super_admin
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

        // Add counting animation function
        function animateCount(element, target, duration = 2000) {
            let start = 1; // Changed from 0 to 1
            const increment = (target - 1) / (duration / 16); // Adjusted increment calculation
            const timer = setInterval(() => {
                start += increment;
                if (start >= target) {
                    element.textContent = Math.round(target);
                    clearInterval(timer);
                } else {
                    element.textContent = Math.round(start);
                }
            }, 16);
        }

        // Initialize counting animations when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Animate all statistics
            const stats = {
                'totalUsers': {{ $totalUsers }},
                'totalLaporan': {{ $totalLaporan }},
                'totalSelesai': {{ $totalSelesai }},
                'totalArticles': {{ $totalArticles }}
            };

            Object.entries(stats).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = "1"; // Set initial value to 1
                    animateCount(element, value);
                }
            });
        });
    </script>
    <!-- Script Chart -->
    <script>
        // === Kategori Chart ===
        new Chart(document.getElementById('kategoriChart'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($topKategori)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($topKategori)) !!},
                    backgroundColor: ['#4e79a7', '#f28e2c', '#e15759', '#76b7b2'],
                    hoverOffset: 8,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed * 100) / total).toFixed(1);
                                return `${context.label}: ${context.parsed} laporan (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '60%'
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
                    borderRadius: 6,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                return [
                                    `Laporan: ${laporanData[index]}`,
                                    `Kategori: ${kategoriData[index]}`
                                ];
                            }
                        }
                    },
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            stepSize: 1, 
                            precision: 0,
                            font: {
                                size: 11
                            }
                        },
                        title: {
                            display: true,
                            text: 'Jumlah Laporan',
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            display: true,
                            drawBorder: false
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Nama Daerah',
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });

        // === Status Chart ===
        window.statusChart = new Chart(document.getElementById('statusChart'), {
            type: 'pie',
            data: {
                labels: ['Selesai', 'Diproses', 'Baru', 'Ditolak'],
                datasets: [{
                    data: [{{ $totalSelesai }}, {{ $totalDiproses }}, {{ $totalBaru }}, {{ $totalDitolak }}],
                    backgroundColor: ['#28a745', '#ffc107', '#6c757d', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 11
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed * 100) / total).toFixed(1);
                                return `${context.label}: ${context.parsed} laporan (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    </script>

</body>
</html>