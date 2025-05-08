<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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

        /* Badge colors */
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
            <li>
                <a href="admin/stunting/chart" class="active">
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
                    <div class="navbar-dashboard-title">Chart Persentase stunting</div>
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
        <h4 class="mb-4 fw-bold">Statistik Stunting per Kabupaten/Kota</h4>

        <div class="card shadow-sm p-4 mb-4 bg-white rounded">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                <div class="input-group w-100" style="max-width: 300px;">
                    <input type="text" id="citySearch" class="form-control" placeholder="Cari Kota/Kabupaten">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                </div>
            </div>

            <div style="overflow-x: auto;">
                <canvas id="stuntingChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const allData = @json($allCities);
        let chart;

        function buildChart(dataObj) {
            const cities = Object.keys(dataObj);

            const normalData = cities.map(city => dataObj[city].normal);
            const stuntedData = cities.map(city => dataObj[city].stunted);
            const severelyStuntedData = cities.map(city => dataObj[city].severely_stunted);

            const ctx = document.getElementById('stuntingChart').getContext('2d');
            if (chart) chart.destroy();

            chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: cities,
                    datasets: [
                        {
                            label: 'Normal (%)',
                            data: normalData,
                            backgroundColor: '#4CAF50'
                        },
                        {
                            label: 'Stunted (%)',
                            data: stuntedData,
                            backgroundColor: '#FFC107'
                        },
                        {
                            label: 'Severely Stunted (%)',
                            data: severelyStuntedData,
                            backgroundColor: '#F44336'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    indexAxis: 'y',
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw}%`;
                                }
                            }
                        },
                        legend: {
                            position: 'top'
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Persentase (%)'
                            }
                        },
                        y: {
                            ticks: { autoSkip: false }
                        }
                    }
                }
            });
        }

        // Default tampilkan 4 teratas
        buildChart(@json($topCities));

        // Pencarian kota
        document.getElementById('citySearch').addEventListener('input', function () {
            const query = this.value.toLowerCase();
            const filtered = Object.fromEntries(
                Object.entries(allData).filter(([city]) => city.toLowerCase().includes(query))
            );
            buildChart(filtered);
        });
    </script>

</body>
</html>
