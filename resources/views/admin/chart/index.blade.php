<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualisasi Data Kasus per Daerah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        .main-content {
            margin-left: 180px;
            margin-top: 70px;
            padding: 20px;
        }
        .chart-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 20px;
        }
        .chart-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
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
            <li class="dropdown active">
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
            <li>
                <a href="/admin/pengaturan">
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
                    <div class="navbar-dashboard-title">Visualisasi Data Kasus</div>
                    <div class="navbar-dashboard-subtitle">Admin</div>
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
        <div class="chart-container">
            <div class="chart-title">Distribusi Kasus per Daerah dan Kategori</div>
            <canvas id="kasusChart"></canvas>
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

        // Inisialisasi Chart
        const ctx = document.getElementById('kasusChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($daerahLabels),
                datasets: @json($datasets)
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Distribusi Kasus per Daerah dan Kategori'
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        title: {
                            display: true,
                            text: 'Daerah'
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Kasus'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html> 