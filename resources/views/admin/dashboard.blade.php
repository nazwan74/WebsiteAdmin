<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <!-- CSS Eksternal -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- JavaScript Eksternal -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Gaya Kustom -->
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f4f6f9;
        }
        
        /* Gaya Sidebar - z-index tinggi agar di atas navbar & tabel */
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
                box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15);
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
        
        /* Gaya Konten Utama - z-index rendah agar di bawah sidebar saat dibuka */
        .main-content {
            margin-left: 180px;
            margin-top: 70px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            position: relative;
            z-index: 1;
        }
        
        /* Gaya Card Statistik */
        .bg-white.shadow-sm.rounded {
            transition: all 0.3s ease;
        }
        
        .bg-white.shadow-sm.rounded:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
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

    <!-- Konten Utama -->
    <div class="main-content">
        <!-- Bagian Statistik -->
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="bg-white shadow-sm rounded p-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-people-fill text-primary me-2" style="font-size: 1.5rem;"></i>
                        <h6 class="mb-0 text-muted">Total Pengguna Aplikasi</h6>
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

        <!-- Tren Laporan per Periode + Laporan Terbaru (satu baris) -->
        <div class="row mt-3 g-3">
            <!-- Grafik Tren -->
            <div class="col-lg-7">
                <div class="bg-white shadow-sm rounded p-3 h-100">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h6 class="fw-bold mb-0">Tren Laporan per Periode</h6>
                        <div class="d-flex flex-wrap align-items-center gap-2">
                            <form method="GET" action="{{ route('admin.dashboard') }}" id="filterTrenForm" class="d-flex flex-wrap align-items-center gap-2">
                                <label class="form-label mb-0 small text-muted">Tahun:</label>
                                <select name="tahun" class="form-select form-select-sm" style="width: auto; min-width: 120px;" onchange="document.getElementById('filterTrenForm').submit();">
                                    <option value="">12 bulan terakhir</option>
                                    @foreach($tahunList ?? [] as $y)
                                        <option value="{{ $y }}" {{ (request('tahun') == $y || ($filterTahun ?? '') == $y) ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                                <label class="form-label mb-0 small text-muted ms-1">Bulan:</label>
                                <select name="bulan" class="form-select form-select-sm" style="width: auto; min-width: 140px;" onchange="document.getElementById('filterTrenForm').submit();" {{ empty($filterTahun) ? 'disabled' : '' }}>
                                    <option value="">Semua bulan</option>
                                    @php
                                        $bulanNama = ['1' => 'Januari', '2' => 'Februari', '3' => 'Maret', '4' => 'April', '5' => 'Mei', '6' => 'Juni', '7' => 'Juli', '8' => 'Agustus', '9' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
                                    @endphp
                                    @foreach($bulanNama as $num => $nama)
                                        <option value="{{ $num }}" {{ (request('bulan') == $num || ($filterBulan ?? '') == $num) ? 'selected' : '' }}>{{ $nama }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Terapkan</button>
                            </form>
                            <span class="badge bg-primary">{{ $trenPeriodLabel ?? '12 bulan terakhir' }}</span>
                        </div>
                    </div>
                    <div style="height: 280px;">
                        <canvas id="trenLaporanChart"></canvas>
                    </div>
                </div>
            </div>
            <!-- Tabel Laporan Terbaru -->
            <div class="col-lg-5">
                <div class="bg-white shadow-sm rounded p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">Laporan Terbaru</h6>
                        <a href="{{ route('admin.laporan') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="table-responsive" style="max-height: 320px; overflow-y: auto;">
                        <table class="table table-hover table-sm align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Kategori</th>
                                    <th>Daerah</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($laporanTerbaru ?? [] as $item)
                                    @php
                                        $status = strtolower($item['status'] ?? 'baru');
                                        $badgeColor = match($status) {
                                            'selesai' => 'success',
                                            'diproses' => 'warning',
                                            'ditolak' => 'danger',
                                            default => 'secondary',
                                        };
                                        $tanggalBuat = $item['created_date'] ?? ($item['create_at'] ?? null);
                                        $parsedBuat = null;
                                        if ($tanggalBuat !== null && $tanggalBuat !== '') {
                                            try {
                                                $parsedBuat = $tanggalBuat instanceof \DateTimeInterface
                                                    ? \Carbon\Carbon::instance($tanggalBuat)
                                                    : \Carbon\Carbon::parse($tanggalBuat);
                                            } catch (\Throwable $e) {
                                                try {
                                                    $parsedBuat = \Carbon\Carbon::createFromLocaleFormat('d M Y H:i', 'id', $tanggalBuat);
                                                } catch (\Throwable $e2) {
                                                    try {
                                                        $parsedBuat = \Carbon\Carbon::createFromLocaleFormat('d M Y', 'id', $tanggalBuat);
                                                    } catch (\Throwable $e3) {
                                                        $parsedBuat = null;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="text-nowrap">{{ $parsedBuat ? $parsedBuat->locale('id')->translatedFormat('d M Y, H:i') : ($tanggalBuat ?: '-') }}</td>
                                        <td>{{ $item['kategori'] ?? '-' }}</td>
                                        <td>{{ $item['daerah'] ?? '-' }}</td>
                                        <td><span class="badge bg-{{ $badgeColor }}">{{ ucfirst($status) }}</span></td>
                                        <td>
                                        <button
                                            class="btn btn-primary btn-sm"
                                            onclick="openDetailLaporan('{{ $item['id'] }}')">
                                            Detail
                                        </button>
                                    </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada laporan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

            <!-- Modal Detail Laporan -->
        <div class="modal fade" id="detailLaporanModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Detail Laporan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" id="detailLaporanContent">
                        <div class="text-center py-5">
                            <div class="spinner-border"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Eksternal -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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

        // Fungsi animasi penghitungan
        function animateCount(element, target, duration = 2000) {
            let start = 1;
            const increment = (target - 1) / (duration / 16);
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

        // Fungsi saat dokumen siap
        document.addEventListener('DOMContentLoaded', function() {
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

            // Inisialisasi animasi penghitungan statistik
            const stats = {
                'totalUsers': {{ $totalUsers }},
                'totalLaporan': {{ $totalLaporan }},
                'totalSelesai': {{ $totalSelesai }},
                'totalArticles': {{ $totalArticles }}
            };

            Object.entries(stats).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = "1";
                    animateCount(element, value);
                }
            });
        });

        //Script Chart: Tren Laporan per Periode
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('trenLaporanChart');
            if (!ctx) return;

            const labels = @json($trenLaporanLabels);
            const data = @json($trenLaporanData);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Laporan',
                        data: data,
                        borderColor: '#4361ee',
                        backgroundColor: 'rgba(67, 97, 238, 0.15)',
                        fill: true,
                        tension: 0.3,
                        borderWidth: 2,
                        pointBackgroundColor: '#4361ee',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 1,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Laporan: ' + context.parsed.y + ' kasus';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0,
                                font: { size: 11 }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                precision: 0,
                                font: { size: 11 }
                            },
                            title: {
                                display: true,
                                text: 'Jumlah Laporan',
                                font: { size: 11 }
                            },
                            grid: {
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        });

        function openDetailLaporan(id) {
            const modal = new bootstrap.Modal(document.getElementById('detailLaporanModal'));
            modal.show();

            fetch(`/admin/laporan/${id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                document.getElementById('detailLaporanContent').innerHTML = html;
            });
        }
    </script>

    
</body>
</html>