<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan</title>
    
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
        
        /* Gaya Badge Kategori */
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
        
        /* Gaya Filter Badge */
        .filter-badge {
            background-color: #e6edff;
            color: #4361ee;
            padding: 5px 10px;
            margin: 5px 3px;
            border-radius: 15px;
            display: inline-flex;
            align-items: center;
            font-size: 0.85rem;
        }
        
        .filter-badge i {
            margin-left: 5px;
            cursor: pointer;
        }
        
        .filter-count {
            background-color: #6c757d;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            margin-left: 5px;
        }
        
        /* Gaya Tombol Filter */
        .btn-filter {
            border: 1px solid #dee2e6;
            background-color: #f8f9fa;
            color: #6c757d;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-filter:hover {
            background-color: #e9ecef;
            border-color: #ced4da;
            color: #495057;
        }
        
        .btn-filter i {
            font-size: 1rem;
        }
        
        .active-filters {
            margin-top: 10px;
            margin-bottom: 15px;
        }
        
        .date-range-inputs {
            display: flex;
            gap: 10px;
        }
        
        /* Gaya Modal Filter */
        #filterModal .modal-header {
            background: linear-gradient(135deg, #3b6efb, #5a8dfb);
            color: white;
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
            padding: 1.25rem 1.5rem;
        }

        #filterModal .modal-title {
            font-weight: bold;
            font-size: 1.25rem;
        }

        #filterModal .btn-close {
            filter: brightness(0) invert(1);
        }

        #filterModal .modal-content {
            border-radius: 1rem;
            overflow: hidden;
            border: none;
        }

        #filterModal .mb-3 {
            background: #f8faff;
            padding: 1rem;
            border-radius: 0.75rem;
            margin-bottom: 1.25rem;
            border: 1px solid #e2e8f0;
        }

        #filterModal .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        #filterModal input[type="date"] {
            border-radius: 0.5rem;
        }

        /* Gaya Checkbox Filter */
        #filterModal .form-check {
            position: relative;
            display: inline-block;
        }

        #filterModal .form-check-input {
            display: none;
        }

        #filterModal .form-check-label {
            display: inline-block;
            padding: 0.4rem 0.9rem;
            border-radius: 2rem;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease-in-out;
        }

        /* Warna Kategori Checkbox */
        #kategoriPernikahan + .form-check-label { 
            background-color: #fde2e4; 
            color: #b12c2c; 
        }
        
        #kategoriKekerasan + .form-check-label { 
            background-color: #ffe2e2; 
            color: #c0392b; 
        }
        
        #kategoriBullying + .form-check-label { 
            background-color: #fff3cd; 
            color: #8a6d3b; 
        }
        
        #kategoriStunting + .form-check-label { 
            background-color: #d4edda; 
            color: #2e7d32; 
        }

        /* Warna Status Checkbox */
        #statusBaru + .form-check-label {
            background-color: #e2e3e5;
            color: #383d41;
        }

        #statusDiproses + .form-check-label {
            background-color: #fff3cd;
            color: #856404;
        }

        #statusSelesai + .form-check-label {
            background-color: #d4edda;
            color: #155724;
        }

        #statusDitolak + .form-check-label {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Saat Checkbox Dipilih */
        #filterModal .form-check-input:checked + .form-check-label {
            box-shadow: 0 0 0 2px #1e88e5 inset;
            font-weight: bold;
        }

        /* Gaya Footer Modal */
        #filterModal .modal-footer {
            padding: 1.25rem;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            border-bottom-left-radius: 1rem;
            border-bottom-right-radius: 1rem;
        }

        #filterModal .btn {
            border-radius: 0.75rem;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease-in-out;
        }

        #filterModal .btn-primary {
            background: linear-gradient(to right, #3b6efb, #5a8dfb);
            border: none;
            color: white;
        }

        #filterModal .btn-primary:hover {
            background: linear-gradient(to right, #2c5be2, #467ef6);
        }

        #filterModal .btn-secondary {
            background-color: #f1f3f5;
            color: #333;
            border: 1px solid #ced4da;
        }

        #filterModal .btn-secondary:hover {
            background-color: #e2e6ea;
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
                <a href="/admin/laporan" class="active">
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
            <div class="d-flex align-items-center">
                <div class="ms-3">
                    <div class="navbar-dashboard-title">Manajemen Laporan</div>
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

    <!-- Konten Utama -->
    <div class="main-content">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>Daftar Laporan</h2>
            </div>
            <div class="container mt-4">
                <!-- Filter dan Pencarian -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Cari laporan..." id="search-input">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="bi bi-search"></i>
                            </button>
                            <button class="btn btn-filter ms-2" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
                                <i class="bi bi-funnel"></i> Filter <span class="filter-count" id="filterCount">0</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Area Filter Aktif -->
                <div class="active-filters" id="activeFilters"></div>

                <!-- Tabel Laporan -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nama Pelapor</th>
                                <th>Daerah</th>
                                <th>Role</th>
                                <th>Waktu Lapor</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($laporan as $item)
                                @php
                                    $kategori = strtolower($item['kategori'] ?? 'lainnya');
                                    $kategoriBadgeClass = match($kategori) {
                                        'pernikahan anak' => 'bg-pernikahan',
                                        'kekerasan anak' => 'bg-kekerasan',
                                        'bullying' => 'bg-bullying',
                                        'stunting' => 'bg-stunting',
                                        default => 'bg-secondary',
                                    };
                                    $kategoriDisplay = match($kategori) {
                                        'pernikahan anak' => 'Pernikahan Anak',
                                        'kekerasan anak' => 'Kekerasan Anak',
                                        'bullying' => 'Bullying',
                                        'stunting' => 'Stunting',
                                        default => ucfirst($kategori),
                                    };
                                    $status = strtolower($item['status'] ?? 'baru');
                                    $badgeColor = match($status) {
                                        'selesai' => 'success',
                                        'diproses' => 'warning',
                                        'ditolak' => 'danger',
                                        default => 'secondary',
                                    };
                                @endphp
                                <tr class="laporan-row" 
                                    data-kategori="{{ $kategori }}"
                                    data-daerah="{{ $item['daerah'] ?? '' }}"
                                    data-tanggal="{{ \Carbon\Carbon::parse($item['create_at'])->format('Y-m-d') }}"
                                    data-status="{{ $status }}">
                                    <td>{{ $item['id'] ?? '-' }}</td>
                                    <td>{{ $item['nama'] ?? '-' }}</td>
                                    <td>{{ $item['daerah'] ?? '-' }}</td>
                                    <td>{{ $item['role'] ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item['create_at'])->format('d M Y, H:i') ?? '-' }}</td>
                                    <td><span class="badge {{ $kategoriBadgeClass }}">{{ $kategoriDisplay }}</span></td>
                                    <td><span class="badge bg-{{ $badgeColor }}">{{ ucfirst($status) }}</span></td>
                                    <td>
                                        <a href="{{ url('/admin/laporan/'.$item['id']) }}" class="btn btn-sm btn-primary">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Filter -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Pilih Filter Laporan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <!-- Filter Daerah -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Daerah</label>
                            <select class="form-select" id="daerahFilter" multiple size="3">
                                <!-- Opsi akan diisi secara dinamis dengan JavaScript -->
                            </select>
                        </div>

                        <!-- Filter Waktu -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Waktu Lapor</label>
                            <div class="date-range-inputs">
                                <input type="date" class="form-control" id="startDate" placeholder="Tanggal Awal">
                                <input type="date" class="form-control" id="endDate" placeholder="Tanggal Akhir">
                            </div>
                        </div>

                        <!-- Filter Kategori -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Kategori</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="kategoriPernikahan" value="pernikahan anak">
                                    <label class="form-check-label" for="kategoriPernikahan">Pernikahan Anak</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="kategoriKekerasan" value="kekerasan anak">
                                    <label class="form-check-label" for="kategoriKekerasan">Kekerasan Anak</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="kategoriBullying" value="bullying">
                                    <label class="form-check-label" for="kategoriBullying">Bullying</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="kategoriStunting" value="stunting">
                                    <label class="form-check-label" for="kategoriStunting">Stunting</label>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Status -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Status</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="statusBaru" value="baru">
                                    <label class="form-check-label" for="statusBaru">Baru</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="statusDiproses" value="diproses">
                                    <label class="form-check-label" for="statusDiproses">Diproses</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="statusSelesai" value="selesai">
                                    <label class="form-check-label" for="statusSelesai">Selesai</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="statusDitolak" value="ditolak">
                                    <label class="form-check-label" for="statusDitolak">Ditolak</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="resetFilters">Reset Filter</button>
                    <button type="button" class="btn btn-primary" id="applyFilters" data-bs-dismiss="modal">Terapkan Filter</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Eksternal -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script Kustom -->
    <script>
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
            const rows = document.querySelectorAll('table tbody tr.laporan-row');
            const daerahFilter = document.getElementById('daerahFilter');
            const activeFiltersContainer = document.getElementById('activeFilters');
            const filterCountBadge = document.getElementById('filterCount');
            
            // Populate daerah options secara dinamis
            const daerahSet = new Set();
            rows.forEach(row => {
                const daerah = row.getAttribute('data-daerah');
                if (daerah && daerah !== '-' && daerah !== '') {
                    daerahSet.add(daerah);
                }
            });
            
            daerahSet.forEach(daerah => {
                const option = document.createElement('option');
                option.value = daerah;
                option.textContent = daerah;
                daerahFilter.appendChild(option);
            });

            // Inisialisasi state filter
            let activeFilters = {
                daerah: [],
                dateStart: '',
                dateEnd: '',
                kategori: [],
                status: []
            };
            
            // Terapkan filter pencarian
            searchInput.addEventListener('input', function() {
                applyFilters();
            });
            
            // Tombol terapkan filter
            document.getElementById('applyFilters').addEventListener('click', function() {
                // Ambil filter daerah
                activeFilters.daerah = Array.from(daerahFilter.selectedOptions).map(option => option.value);
                
                // Ambil filter range tanggal
                activeFilters.dateStart = document.getElementById('startDate').value;
                activeFilters.dateEnd = document.getElementById('endDate').value;
                
                // Ambil filter kategori
                activeFilters.kategori = [];
                document.querySelectorAll('input[id^="kategori"]:checked').forEach(checkbox => {
                    activeFilters.kategori.push(checkbox.value);
                });
                
                // Ambil filter status
                activeFilters.status = [];
                document.querySelectorAll('input[id^="status"]:checked').forEach(checkbox => {
                    activeFilters.status.push(checkbox.value);
                });
                
                // Update UI dan terapkan filter
                updateActiveFiltersUI();
                applyFilters();
            });
            
            // Reset filter
            document.getElementById('resetFilters').addEventListener('click', function() {
                // Reset form
                document.getElementById('filterForm').reset();
                
                // Clear active filters
                activeFilters = {
                    daerah: [],
                    dateStart: '',
                    dateEnd: '',
                    kategori: [],
                    status: []
                };
                
                // Update UI
                updateActiveFiltersUI();
                applyFilters();
            });
            
            // Fungsi update UI filter aktif
            function updateActiveFiltersUI() {
                activeFiltersContainer.innerHTML = '';
                let filterCount = 0;
                
                // Tambah filter daerah
                activeFilters.daerah.forEach(daerah => {
                    addFilterBadge('Daerah: ' + daerah, () => {
                        activeFilters.daerah = activeFilters.daerah.filter(d => d !== daerah);
                        // Reset dropdown filter daerah
                        Array.from(daerahFilter.options).forEach(option => {
                            option.selected = false;
                        });
                        updateActiveFiltersUI();
                        applyFilters();
                    });
                    filterCount++;
                });
                
                // Tambah filter range tanggal
                if (activeFilters.dateStart && activeFilters.dateEnd) {
                    const formattedStartDate = formatDate(activeFilters.dateStart);
                    const formattedEndDate = formatDate(activeFilters.dateEnd);
                    addFilterBadge(`Periode: ${formattedStartDate} - ${formattedEndDate}`, () => {
                        activeFilters.dateStart = '';
                        activeFilters.dateEnd = '';
                        document.getElementById('startDate').value = '';
                        document.getElementById('endDate').value = '';
                        updateActiveFiltersUI();
                        applyFilters();
                    });
                    filterCount++;
                }
                
                // Tambah filter kategori dengan mapping ID yang tepat
                activeFilters.kategori.forEach(kategori => {
                    let displayKategori = '';
                    let kategoriId = '';
                    
                    switch(kategori) {
                        case 'pernikahan dini':
                            displayKategori = 'Pernikahan Anak';
                            kategoriId = 'kategoriPernikahan';
                            break;
                        case 'kekerasan anak':
                            displayKategori = 'Kekerasan Anak';
                            kategoriId = 'kategoriKekerasan';
                            break;
                        case 'bullying':
                            displayKategori = 'Bullying';
                            kategoriId = 'kategoriBullying';
                            break;
                        case 'stunting':
                            displayKategori = 'Stunting';
                            kategoriId = 'kategoriStunting';
                            break;
                        default:
                            displayKategori = kategori;
                            kategoriId = 'kategori' + kategori.charAt(0).toUpperCase() + kategori.slice(1).replace(/\s+/g, '');
                    }
                    
                    addFilterBadge('Kategori: ' + displayKategori, () => {
                        activeFilters.kategori = activeFilters.kategori.filter(k => k !== kategori);
                        // Pastikan checkbox tidak tercentang
                        const checkbox = document.getElementById(kategoriId);
                        if (checkbox) {
                            checkbox.checked = false;
                        }
                        updateActiveFiltersUI();
                        applyFilters();
                    });
                    filterCount++;
                });
                
                // Tambah filter status
                activeFilters.status.forEach(status => {
                    const statusCapitalized = status.charAt(0).toUpperCase() + status.slice(1);
                    addFilterBadge('Status: ' + statusCapitalized, () => {
                        activeFilters.status = activeFilters.status.filter(s => s !== status);
                        const statusCheckbox = document.getElementById('status' + status.charAt(0).toUpperCase() + status.slice(1));
                        if (statusCheckbox) {
                            statusCheckbox.checked = false;
                        }
                        updateActiveFiltersUI();
                        applyFilters();
                    });
                    filterCount++;
                });
                
                // Update badge count filter
                filterCountBadge.textContent = filterCount;
                filterCountBadge.style.display = filterCount > 0 ? 'inline-flex' : 'none';
            }
            
            // Fungsi tambah filter badge
            function addFilterBadge(text, removeCallback) {
                const badge = document.createElement('span');
                badge.className = 'filter-badge';
                badge.innerHTML = text + ' <i class="bi bi-x-circle"></i>';
                badge.querySelector('i').addEventListener('click', removeCallback);
                activeFiltersContainer.appendChild(badge);
            }
            
            // Format tanggal untuk display
            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            }
            
            // Terapkan semua filter
            function applyFilters() {
                const searchTerm = searchInput.value.toLowerCase();
                
                rows.forEach(row => {
                    const namaPelapor = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    const daerah = row.getAttribute('data-daerah');
                    const tanggal = row.getAttribute('data-tanggal');
                    const kategori = row.getAttribute('data-kategori');
                    const status = row.getAttribute('data-status');
                    
                    // Cek search term
                    const matchSearch = namaPelapor.includes(searchTerm);
                    
                    // Cek filter daerah
                    const matchDaerah = activeFilters.daerah.length === 0 || 
                                        activeFilters.daerah.includes(daerah);
                    
                    // Cek range tanggal
                    let matchDate = true;
                    if (activeFilters.dateStart && activeFilters.dateEnd) {
                        matchDate = tanggal >= activeFilters.dateStart && 
                                tanggal <= activeFilters.dateEnd;
                    }
                    
                    // Cek kategori
                    const matchKategori = activeFilters.kategori.length === 0 || 
                                        activeFilters.kategori.includes(kategori);
                    
                    // Cek status
                    const matchStatus = activeFilters.status.length === 0 || 
                                    activeFilters.status.includes(status);
                    
                    // Tampilkan atau sembunyikan row berdasarkan semua filter
                    row.style.display = (matchSearch && matchDaerah && matchDate && 
                                        matchKategori && matchStatus) ? '' : 'none';
                });
            }
            
            // Setup awal UI filter
            updateActiveFiltersUI();

            // Event listener untuk modal show
            document.getElementById('filterModal').addEventListener('show.bs.modal', function () {
                // Reset dropdown filter daerah berdasarkan active filters
                Array.from(daerahFilter.options).forEach(option => {
                    option.selected = activeFilters.daerah.includes(option.value);
                });

                // Reset input tanggal
                document.getElementById('startDate').value = activeFilters.dateStart;
                document.getElementById('endDate').value = activeFilters.dateEnd;

                // Reset checkbox kategori
                document.querySelectorAll('input[id^="kategori"]').forEach(checkbox => {
                    const value = checkbox.value;
                    checkbox.checked = activeFilters.kategori.includes(value);
                });

                // Reset checkbox status
                document.querySelectorAll('input[id^="status"]').forEach(checkbox => {
                    const value = checkbox.value;
                    checkbox.checked = activeFilters.status.includes(value);
                });
            });
        });

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