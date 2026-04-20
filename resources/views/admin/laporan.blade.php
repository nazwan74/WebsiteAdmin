@extends('admin.layouts.app', [
    'activePage' => 'laporan',
    'navbarTitle' => 'Manajemen Laporan',
    'navbarSubtitle' => 'Admin'
])

@section('title', 'Laporan')

@section('head-scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection

@section('styles')
    <style>
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
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h2>Daftar Laporan</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.laporan.refresh') }}" class="btn btn-outline-secondary" title="Refresh Data">
                    <i class="bi bi-arrow-clockwise"></i>
                </a>
                <a href="{{ route('admin.laporan.downloadList') }}" id="downloadListBtn" class="btn btn-success">
                    <i class="bi bi-download me-2"></i>Download List
                </a>
            </div>
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
                            <th>Nama Pelapor</th>
                            <th>Tipe Kasus</th>
                            <th>Tanggal Kejadian</th>
                            <th>Status</th>
                            <th>Tanggal Buat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($laporan as $item)
                            @php
                                $kategori = strtolower($item['case_type'] ?? ($item['kategori'] ?? 'lainnya'));
                                $kategoriBadgeClass = match($kategori) {
                                    'pernikahan anak' => 'bg-pernikahan',
                                    'kekerasan anak' => 'bg-kekerasan',
                                    'bullying' => 'bg-bullying',
                                    'stunting' => 'bg-stunting',
                                    default => 'bg-secondary',
                                };
                                $kategoriDisplay = $item['case_type'] ?? ($item['kategori'] ?? '-');

                                $rawStatus = $item['report_status'] ?? ($item['status'] ?? 'baru');
                                $status = strtolower(trim($rawStatus));
                                if (in_array($status, ['belum ditangani', 'belum_ditangani', 'pending', ''])) {
                                    $status = 'baru';
                                }
                                $badgeColor = match($status) {
                                    'selesai' => 'success',
                                    'diproses' => 'warning',
                                    'ditolak' => 'danger',
                                    default => 'secondary',
                                };
                                $tanggalKejadian = $item['incident_date'] ?? null;
                                $tanggalBuat = $item['created_date'] ?? ($item['create_at'] ?? null);
                                $parsedKejadian = null;
                                $parsedBuat = null;
                                if ($tanggalKejadian !== null && $tanggalKejadian !== '') {
                                    try {
                                        $parsedKejadian = $tanggalKejadian instanceof \DateTimeInterface
                                            ? \Carbon\Carbon::instance($tanggalKejadian)
                                            : \Carbon\Carbon::parse($tanggalKejadian);
                                    } catch (\Throwable $e) {
                                        try {
                                            $parsedKejadian = \Carbon\Carbon::createFromLocaleFormat('d M Y', 'id', $tanggalKejadian);
                                        } catch (\Throwable $e2) {
                                            $parsedKejadian = null;
                                        }
                                    }
                                }
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
                            <tr class="laporan-row" 
                                data-report-id="{{ $item['id'] }}"
                                data-kategori="{{ $kategori }}"
                                data-daerah="{{ $item['daerah'] ?? '' }}"
                                data-tanggal="{{ $parsedBuat ? $parsedBuat->format('Y-m-d') : '' }}"
                                data-status="{{ $status }}">
                                <td>{{ $item['user_name'] ?? ($item['nama'] ?? '-') }}</td>
                                <td><span class="badge {{ $kategoriBadgeClass }}">{{ $kategoriDisplay }}</span></td>
                                <td class="text-nowrap">{{ $parsedKejadian ? $parsedKejadian->locale('id')->translatedFormat('d M Y') : ($tanggalKejadian ?: '-') }}</td>
                                @php
                                    $displayStatus = $status === 'baru' ? 'Belum Ditangani' : ucfirst($status);
                                @endphp
                                <td><span class="badge bg-{{ $badgeColor }}">{{ $displayStatus }}</span></td>
                                <td class="text-nowrap">{{ $parsedBuat ? $parsedBuat->locale('id')->translatedFormat('d M Y, H:i') : ($tanggalBuat ?: '-') }}</td>
                                <td>
                                    <button
                                        class="btn btn-primary btn-sm"
                                        onclick="openDetailLaporan('{{ $item['id'] }}')">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div id="paginationWrapper" class="pagination-wrapper d-none">
                <div class="pagination-info" id="paginationInfo"></div>
                <nav aria-label="Navigasi halaman laporan">
                    <ul class="pagination mb-0" id="paginationNav"></ul>
                </nav>
            </div>
        </div>
    </div>


@endsection

@section('modals')
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
                            <ul id="kategoriFilterContainer" class="list-group list-group-flush" style="max-height: 200px; overflow-y: auto;">
                                @forelse($kategoriList ?? [] as $kategori)
                                    @php
                                        $kategoriValue = is_string($kategori) ? strtolower($kategori) : $kategori;
                                        $kategoriId = 'kategori-' . \Illuminate\Support\Str::slug($kategoriValue);
                                    @endphp
                                    <li class="list-group-item py-2 px-3 border-0">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="{{ $kategoriId }}" name="kategoriFilter" value="{{ $kategoriValue }}">
                                            <label class="form-check-label" for="{{ $kategoriId }}">{{ $kategori }}</label>
                                        </div>
                                    </li>
                                @empty
                                    <li class="list-group-item text-muted small border-0">Tidak ada kategori dalam data laporan.</li>
                                @endforelse
                            </ul>
                        </div>

                        <!-- Filter Status -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Status</label>
                            <div class="d-flex flex-wrap gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="statusBaru" value="baru">
                                    <label class="form-check-label" for="statusBaru">Belum Ditangani</label>
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
@endsection
@section('scripts')
    <script>
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

            // Pagination
            const perPage = 10;
            let currentPage = 1;
            
            // Terapkan filter pencarian
            searchInput.addEventListener('input', function() {
                currentPage = 1;
                applyFilters();
            });
            
            // Tombol terapkan filter
            document.getElementById('applyFilters').addEventListener('click', function() {
                // Ambil filter daerah
                activeFilters.daerah = Array.from(daerahFilter.selectedOptions).map(option => option.value);
                
                // Ambil filter range tanggal
                activeFilters.dateStart = document.getElementById('startDate').value;
                activeFilters.dateEnd = document.getElementById('endDate').value;
                
                // Ambil filter kategori (dinamis)
                activeFilters.kategori = [];
                document.querySelectorAll('#kategoriFilterContainer input[name="kategoriFilter"]:checked').forEach(checkbox => {
                    activeFilters.kategori.push(checkbox.value);
                });
                
                // Ambil filter status
                activeFilters.status = [];
                document.querySelectorAll('input[id^="status"]:checked').forEach(checkbox => {
                    activeFilters.status.push(checkbox.value);
                });
                
                // Update UI dan terapkan filter
                updateActiveFiltersUI();
                currentPage = 1;
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
                currentPage = 1;
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
                
                // Tambah filter kategori (dinamis)
                activeFilters.kategori.forEach(kategori => {
                    const displayKategori = kategori.charAt(0).toUpperCase() + kategori.slice(1);
                    addFilterBadge('Kategori: ' + displayKategori, () => {
                        activeFilters.kategori = activeFilters.kategori.filter(k => k !== kategori);
                        const container = document.getElementById('kategoriFilterContainer');
                        if (container) {
                            container.querySelectorAll('input[name="kategoriFilter"]').forEach(cb => {
                                if (cb.value === kategori) cb.checked = false;
                            });
                        }
                        updateActiveFiltersUI();
                        applyFilters();
                    });
                    filterCount++;
                });
                
                // Tambah filter status
                activeFilters.status.forEach(status => {
                    const statusLabelMap = {
                        'baru': 'Belum Ditangani',
                        'diproses': 'Diproses',
                        'selesai': 'Selesai',
                        'ditolak': 'Ditolak'
                    };
                    const statusLabel = statusLabelMap[status] || (status.charAt(0).toUpperCase() + status.slice(1));
                    addFilterBadge('Status: ' + statusLabel, () => {
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

                // Terapkan pagination pada baris yang tampil
                const visibleRows = Array.from(rows).filter(r => r.style.display !== 'none');
                const totalVisible = visibleRows.length;
                const totalPages = Math.max(1, Math.ceil(totalVisible / perPage));
                if (currentPage > totalPages) currentPage = totalPages;
                const start = (currentPage - 1) * perPage;
                const end = start + perPage;
                visibleRows.forEach((row, i) => {
                    row.style.display = (i >= start && i < end) ? '' : 'none';
                });

                // Perbarui UI pagination dan URL download
                updatePaginationUI(totalVisible, totalPages);
                updateDownloadLink();
            }

            // Update tampilan pagination (info + navigasi)
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
                infoEl.textContent = `Menampilkan ${start}–${end} dari ${totalVisible} laporan`;

                navEl.innerHTML = '';
                const addPageItem = (label, pageNum, disabled, active) => {
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
                };

                addPageItem('Sebelumnya', currentPage - 1, currentPage <= 1, false);
                const maxButtons = 5;
                let from = Math.max(1, currentPage - Math.floor(maxButtons / 2));
                let to = Math.min(totalPages, from + maxButtons - 1);
                if (to - from + 1 < maxButtons) from = Math.max(1, to - maxButtons + 1);
                for (let p = from; p <= to; p++) {
                    addPageItem(p, p, false, p === currentPage);
                }
                addPageItem('Selanjutnya', currentPage + 1, currentPage >= totalPages, false);
            }
            
            // Perbarui link Download List dengan filter + pencarian saat ini
            function updateDownloadLink() {
                const baseUrl = '{{ route("admin.laporan.downloadList") }}';
                const params = [];
                if (activeFilters.daerah.length) params.push('daerah=' + encodeURIComponent(activeFilters.daerah.join(',')));
                if (activeFilters.dateStart) params.push('date_start=' + encodeURIComponent(activeFilters.dateStart));
                if (activeFilters.dateEnd) params.push('date_end=' + encodeURIComponent(activeFilters.dateEnd));
                if (activeFilters.kategori.length) params.push('kategori=' + encodeURIComponent(activeFilters.kategori.join(',')));
                if (activeFilters.status.length) params.push('status=' + encodeURIComponent(activeFilters.status.join(',')));
                const searchVal = searchInput.value.trim();
                if (searchVal) params.push('search=' + encodeURIComponent(searchVal));
                const url = params.length ? baseUrl + '?' + params.join('&') : baseUrl;
                const btn = document.getElementById('downloadListBtn');
                if (btn) btn.setAttribute('href', url);
            }
            
            // Setup awal UI filter dan pagination
            updateActiveFiltersUI();
            updateDownloadLink();
            applyFilters();

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
                document.querySelectorAll('input[name="kategoriFilter"]').forEach(checkbox => {
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

        let detailModal = null;
        function openDetailLaporan(id) {
            const modalEl = document.getElementById('detailLaporanModal');
            if (!detailModal) {
                detailModal = new bootstrap.Modal(modalEl);
            }
            
            // Reset content to spinner
            document.getElementById('detailLaporanContent').innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <div class="mt-2">Memuat data...</div>
                </div>
            `;
            
            detailModal.show();

            fetch(`/admin/laporan/${id}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                document.getElementById('detailLaporanContent').innerHTML = html;
            })
            .catch(err => {
                console.error(err);
                document.getElementById('detailLaporanContent').innerHTML = `
                    <div class="alert alert-danger">Gagal memuat data laporan.</div>
                `;
            });
        }

        /* =========================
        EVENT DELEGATION (AMAN)
        ========================= */
        document.addEventListener('submit', function(e) {
            /* UPDATE STATUS */
            if (e.target.id === 'statusForm') {
                e.preventDefault();

                const form = e.target;
                const url = form.action;
                const data = new FormData(form);
                const selectedStatus = data.get('status');
                const chatUrl = form.getAttribute('data-chat-url');

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    Swal.fire({
                        title: 'Berhasil',
                        text: res.message,
                        icon: 'success'
                    }).then(() => {
                        if (chatUrl) {
                            if (selectedStatus === 'ditolak') {
                                window.location.href = chatUrl + '?from=reject';
                            } else if (selectedStatus === 'selesai') {
                                window.location.href = chatUrl + '?from=done';
                            } else {
                                location.reload();
                            }
                        } else {
                            location.reload();
                        }
                    });
                });
            }
        });

        /* DELETE LAPORAN */
        document.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('#delete-laporan');
            if (deleteBtn) {
                const url = deleteBtn.dataset.url;

                Swal.fire({
                    title: 'Yakin?',
                    text: 'Laporan akan dihapus permanen',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus'
                }).then(result => {
                    if (result.isConfirmed) {
                        fetch(url, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(() => location.reload());
                    }
                });
            }
        });
    </script>
@endsection